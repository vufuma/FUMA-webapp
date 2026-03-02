import pandas as pd
import os
import subprocess
from sys import argv
import configparser
import argparse
import logging
import sys
import gzip

def parse_args():
    parser = argparse.ArgumentParser()
    parser.add_argument('--filedir', required=True, help="Path to input directory.")
    parser.add_argument('--s2gdir', required=True, help="Path to snp2gene directory")
    args = parser.parse_args()
    
    return args

def sanitize_gwas(filedir, logger):
    # check if the file is gzipped
    with open(os.path.join(filedir, "input.gwas.gz"), 'rb') as f:
        magic_number = f.read(2)
        if magic_number != b'\x1f\x8b':  # Check for gzip magic number
            logger.error("input.gwas.gz is not a valid gzip file.")
            sys.exit(7)
    
    # check if the first line is a header that begins with #
    with gzip.open(os.path.join(filedir, "input.gwas.gz"), 'rt') as f:
        first_line = f.readline()
        if not first_line.startswith("#"):
            logger.error("input.gwas.gz does not have a valid header line starting with #.")
            sys.exit(8)
        else:
            header = first_line[1:].rstrip("\n").split("\t")
            print("\t".join(header), file=open(os.path.join(filedir, "header.txt"), 'w'))
    
    
def tabix_gwas(filedir, logger):
    # tabix the input.gwas.gz file 
    cmd = f"tabix -p vcf input.gwas.gz"
    cmd = ["tabix", "-p", "vcf", os.path.join(filedir, "input.gwas.gz")]

    try:
        result = subprocess.run(
            cmd,
            check=True,
            capture_output=True,
            text=True
        )
        logger.info("tabix finished successfully: %s", " ".join(cmd))
    except subprocess.CalledProcessError as e:
        logger.error("tabix failed with exit code %s", e.returncode)
        logger.error("stdout: %s", e.stdout)
        logger.error("stderr: %s", e.stderr)
        sys.exit(1)
        
def add_header(header_path, outfp):
    """This function adds header to the file loci/locus_{locname}.txt"""
    """header_path is the path to the header.txt file"""
    
    outfile = open(outfp, "w")
    
    with open(header_path) as f: #TODO: add handling for file not found
        header = f.readline().rstrip("\n").split("\t")
        print("\t".join(header), file=outfile)
    outfile.close()
    
def subset_variants_per_locus(filedir, locname, outfp, logger):
    """ this function uses tabix to subset variants per locus"""
    cmd = ["tabix", os.path.join(filedir, "input.gwas.gz"), locname]
    
    try:
        with open(outfp, "a") as out:
            subprocess.run(cmd,
                        stdout=out,
                        stderr=subprocess.PIPE,
                        text=True,
                        check=True)
        logger.info("tabix locus %s successful", locname)
    except subprocess.CalledProcessError as e:
        logger.error(
            "tabix failed for locus %s (exit code %s)", locname, e.returncode
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(2)
        
def run_polyfun_munge(locus_txt, locus_pq, sample_size, logger, locname):
    try:
        munge_cmd = [
            "python",
            "/opt/polyfun/munge_polyfun_sumstats.py",
            "--sumstats", str(locus_txt),
            "--out", str(locus_pq),
            "--n", str(sample_size),
        ]

        logger.info("Running munge_polyfun_sumstats for locus %s", locname)

        subprocess.run(
            munge_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("Munging finished successfully for locus %s", locname)
    except subprocess.CalledProcessError as e:
        logger.error(
            "polyfun munge failed for locus %s (exit code %s)", locname, e.returncode
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(3)
        
def run_polyfun_finemapper(locus_pq, sample_size, susie_out, chrom, start, end, logger, locname):
    try:
        finemap_cmd = [
            "python",
            "/opt/polyfun/finemapper.py",
            "--sumstats", str(locus_pq),
            "--method", "susie",
            "--n", str(sample_size),
            "--out", str(susie_out),
            "--max-num-causal", "1",
            "--chr", str(chrom),
            "--start", str(start),
            "--end", str(end),
            "--non-funct",
        ]

        logger.info("Running finemapper for locus %s", locname)

        subprocess.run(
            finemap_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("Finemapping finished successfully for locus %s", locname)
    except subprocess.CalledProcessError as e:
        logger.error(
            "polyfun munge failed for locus %s (exit code %s)", locname, e.returncode
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(4)
        
def format_susie_results(filedir, locname):
    try:
        finemapped = pd.read_csv(f"{filedir}/loci/locus_{locname}.susie", sep="\t")
        print(finemapped)
        print(max(finemapped['PIP']))
        finemapped = finemapped.sort_values("PIP", ascending=True)
        if len(finemapped) > 1:
            finemapped['cumsum'] = finemapped['PIP'].cumsum()
            finemapped = finemapped[finemapped['cumsum'] > 0.05]
        finemapped['SNP'] = finemapped.apply(lambda row: f"{row['CHR']}:{row['BP']}:{row['A1']}:{row['A2']}", axis=1)
        finemapped = finemapped[['SNP', 'PIP']]
        finemapped = finemapped.sort_values("PIP", ascending=False)
        if len(finemapped) == 0:
            print(f"No finemapped SNPs for locus {locname}, skipping")
            return
        finemapped.to_csv(f"{filedir}/locus_{locname}.susie.finemapped", sep="\t", index=False)
    except: 
        sys.exit(5)
        
def make_indexfile(filedir, infile, locus_n):
    try:
        outfile = open(f"{filedir}/locus_{locus_n}.cred1", "w")
        print("\t".join(["index", "cred1", "prob1"]), file=outfile)

        
        count = 1
        with open(os.path.join(filedir, infile), "r") as f:
            for line in f:
                if line.startswith("SNP"):
                    continue
                items = line.rstrip("\n").split("\t")
                print("\t".join([str(count), items[0], items[1]]), file=outfile)
                
                
                count += 1
        outfile.close()
    except:
        sys.exit(6)
        
def run_pops(flames, logger):
    try:
        pops_cmd = [
            "python",
            "/opt/pops/pops.py",
            "--gene_annot_path", f"{flames}/pops_features_full_FUMA_compatible/gene_annots.txt",
            "--feature_mat_prefix", f"{flames}/pops_features_full_FUMA_compatible/features_munged/pops_features",
            "--num_feature_chunks", "116",
            "--magma_prefix", "magma",
            "--control_features", f"{flames}/pops_features_full_FUMA_compatible/control.features",
            "--out_prefix", "input",
        ]

        logger.info("Running PoPS")

        subprocess.run(
            pops_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("PoPS finished successfully")

    except subprocess.CalledProcessError as e:
        logger.error(
            "PoPS failed", e.returncode
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(11)
        

def run_flames(filedir, flames, s2gdir, s2g_id, logger):
    try:
        annotate_cmd = [
            "python",
            "/opt/FLAMES/FLAMES.py",
            "annotate",
            "-o", str(filedir),
            "-a", f"{flames}/Annotation_data",
            "-p", f"{filedir}/input.preds",
            "-m", f"{s2gdir}/{s2g_id}/magma.genes.out",
            "-mt", f"{s2gdir}/{s2g_id}/magma_exp_gtex_v8_ts_general_avg_log2TPM.gsa.out",
            "-id", f"{filedir}/indexfile.txt",
            "-t", "tabix",
            "-cf", f"{flames}/whole_genome_SNVs.tsv.gz",
            "-cv", "vep",
            "-vc", "/home/worker/.vep"
        ]

        logger.info("Running FLAMES annotate")

        subprocess.run(
            annotate_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("FLAMES annotate finished successfully")

        flames_cmd = [
            "python",
            "/opt/FLAMES/FLAMES.py",
            "FLAMES",
            "-o", str(filedir),
            "-id", f"{filedir}/indexfile.txt",
        ]

        logger.info("Running FLAMES main")

        subprocess.run(
            flames_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("FLAMES main finished successfully")
    except subprocess.CalledProcessError as e:
        logger.error(
            "FLAMES failed"
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(10)
        
def format_pred_output(filedir, logger):
    try:
        pred_input = pd.read_csv(os.path.join(filedir, "FLAMES_scores.pred"), sep="\t")
        pred_input = pred_input[["locus", "symbol", "ensg", "FLAMES_scaled", "FLAMES_raw", "estimated_cumulative_precision"]]
        pred_input[["locus"]] = pred_input[["locus"]] + 1
        pred_input.to_csv(os.path.join(filedir, "FLAMES_scores_fmt.pred"), sep="\t", index=False)
    except Exception as e:
        logger.error("Error formatting prediction output: %s", str(e))
        sys.exit(9)
    

def main():
    args = parse_args()
    
    # Setting up parameters
    filedir = args.filedir
    s2gdir = args.s2gdir

    cfg = configparser.ConfigParser()
    cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')
    flames = os.path.join(cfg.get('data', 'flames'))

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))

    # Setting up the log file
    logging.basicConfig(
        filename=os.path.join(filedir, "job.log"),
        filemode="a",
        level=logging.INFO,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
    )
    logger = logging.getLogger(__name__)

    # get the parameters from the params
    sample_size = param.get('params','sampleSize')
    s2g_id = param.get('params','snp2geneID')

    # create the loci directory
    if not os.path.exists(os.path.join(filedir, "loci")):
        os.makedirs(os.path.join(filedir, "loci"))
        
    # input gwas sumstat sanitization
    sanitize_gwas(filedir, logger)
    
    # tabix the input gwas sumstat
    tabix_gwas(filedir=filedir, logger=logger)
    
    
    # read in the genomic risk loci file and process each row
    loci = pd.read_csv(os.path.join(s2gdir, s2g_id, 'GenomicRiskLoci.txt'), sep='\t')
    for index, row in loci.iterrows():
        locname = row['GenomicLocus']
        chrom = row["chr"]
        start = row["start"]
        end = row["end"]
        if start == end:
            start = start - 25000
            end = end + 25000
        locname = f'{chrom}:{start}-{end}'
        
        if os.path.exists(f"{filedir}/locus_{locname}.susie.finemapped"):
            logger.info(f"locus_{locname}.susie.finemapped already exists, skipping")
            continue
        
        # add header
        outfp = os.path.join(filedir, "loci", "locus_" + locname + ".txt")
        
        # add header 
        add_header(header_path=os.path.join(filedir, "header.txt"), outfp=outfp)

        # get the variants per locus with tabix
        subset_variants_per_locus(filedir=filedir, locname=locname, outfp=outfp, logger=logger)
        
        locus_txt = os.path.join(filedir, "loci", f"locus_{locname}.txt")
        locus_pq = os.path.join(filedir, "loci", f"locus_{locname}.txt.pq")
        susie_out = os.path.join(filedir, "loci", f"locus_{locname}.susie")
        
        # run polyfun munge
        run_polyfun_munge(locus_txt, locus_pq, sample_size, logger, locname)
        
        # run polyfun finemapper
        run_polyfun_finemapper(locus_pq, sample_size, susie_out, chrom, start, end, logger, locname)
        
        # format susie results
        format_susie_results(filedir, locname)

        #done with looping through loci

    indexfile = open(f"{filedir}/indexfile.txt", "w")
    print("\t".join(["Filename", "GenomicLocus", "Annotfiles"]), file=indexfile)

    locus_n = 1
    for file in os.listdir(filedir):
        if file.endswith(".susie.finemapped"):
            make_indexfile(filedir, file, locus_n)
            # format(os.path.join(filedir, file), locus_n)
            indexfile_row = [f"{filedir}/locus_{locus_n}.cred1", f"{locus_n}", f"{filedir}/annots/annotated_locus_{locus_n}.txt"]
            print("\t".join(indexfile_row), file=indexfile)
            locus_n += 1
    indexfile.close()
    
    # run flames
    run_flames(filedir, flames, s2gdir, s2g_id, logger)
    
    # format
    format_pred_output(filedir, logger)

    
if __name__ == "__main__":
    main()