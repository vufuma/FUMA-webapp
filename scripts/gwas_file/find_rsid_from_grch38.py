# this script looks up rsid from chromome, position, effect allele and other allele
# expect input in the gwas catalog format
import os
import argparse
import shutil
import sys
import configparser
import tabix
from collections import defaultdict
import logging

cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

tb_cache = {}

def process_header(filedir, logger):
    with open(os.path.join(filedir, 'input.gwas.grch38'), "r") as f:
        header = f.readline().strip("\n").split("\t")
        
        if "chromosome" not in header or "base_pair_location" not in header:
            logger.error("Columns chromosome and base_pair_location are required in the input file.")
            sys.exit(1)
        
        if "beta" in header: 
            if "effect_allele" not in header or "other_allele" not in header:
                logger.error("Columns effect_allele and other_allele are required when beta column is present.")
                sys.exit(2)
            
            if header != ["chromosome", "base_pair_location", "effect_allele", "other_allele", "beta", "p_value"]:
                logger.error("When beta column is present, the header must be: chromosome, base_pair_location, effect_allele, other_allele, beta, p_value")
                sys.exit(3)
            
            return "process_beta"
        else:
            if header != ["chromosome", "base_pair_location", "p_value"]:
                logger.error("When beta column is not present, the header must be: chromosome, base_pair_location, p_value")
                sys.exit(4)
            return "process_no_beta"
                
                
def process_window_beta(chrom, start, end, snps, outfile, unmatch_outfile):

    tb = tb_cache.get(chrom)
    if tb is None:
        tb = tabix.open(
            os.path.join(
                cfg.get('data', 'dbSNP'),
                "dbSNP_v157",
                f"dbSNP157.chr{chrom}.vcf.gz"
            )
        )
        tb_cache[chrom] = tb

    try:
        queried_results = tb.querys(f"{chrom}:{start}-{end}")
    except Exception:
        return

    # Build lookup:
    # pos -> list of (ref, alts, rsid)
    db_lookup = {}

    for row in queried_results:
        pos = int(row[1])

        db_lookup.setdefault(pos, []).append(
            (
                row[3],              # ref
                row[4].split(','),   # alts
                row[2]               # rsid
            )
        )

    out_lines = []
    unmatched_count = 0
    unmatch_out_lines = []

    for snp in snps:

        pos = int(snp["pos"])
        effect_allele = snp["effect_allele"]
        other_allele = snp["other_allele"]
        beta = snp["beta"]
        p_value = snp["p_value"]

        matches = db_lookup.get(pos)

        if matches is None:
            unmatched_count += 1
            unmatch_out_lines.append("\t".join([chrom, str(snp["pos"]), snp["effect_allele"], snp["other_allele"], snp["beta"], snp["p_value"]]))
            continue

        found = False

        for db_ref, db_alt_alleles, rsid in matches:

            if (
                effect_allele in db_alt_alleles
                and other_allele == db_ref
            ):
                out_lines.append(
                    "\t".join([
                        effect_allele,
                        other_allele,
                        rsid,
                        p_value,
                        beta
                    ])
                )

                found = True
                break

            if (
                other_allele in db_alt_alleles
                and effect_allele == db_ref
            ):
                out_lines.append(
                    "\t".join([
                        effect_allele,
                        other_allele,
                        rsid,
                        p_value,
                        str(-float(beta))
                    ])
                )

                found = True
                break

        if not found:
            unmatched_count += 1
            unmatch_out_lines.append("\t".join([chrom, str(snp["pos"]), snp["effect_allele"], snp["other_allele"], snp["beta"], snp["p_value"]]))

    outfile.write("\n".join(out_lines))
    outfile.write("\n")
    
    unmatch_outfile.write("\n".join(unmatch_out_lines))
    unmatch_outfile.write("\n")

    return unmatched_count
    
def process_window_nobeta(chrom, start, end, snps, outfile):

    tb = tb_cache.get(chrom)
    if tb is None:
        tb = tabix.open(
            os.path.join(
                cfg.get('data', 'dbSNP'),
                "dbSNP_v157",
                f"dbSNP157.chr{chrom}.vcf.gz"
            )
        )
        tb_cache[chrom] = tb

    try:
        queried_results = tb.querys(f"{chrom}:{start}-{end}")
    except Exception:
        return

    # pos -> first dbSNP record at that position
    db_lookup = {}

    for row in queried_results:
        pos = int(row[1])

        if pos not in db_lookup:
            db_lookup[pos] = (
                row[3],              # ref
                row[4].split(',')[0],  # first alt allele
                row[2]               # rsid
            )

    out_lines = []
    unmatched_count = 0

    for snp in snps:
        pos = int(snp["pos"])
        p_value = snp["p_value"]

        match = db_lookup.get(pos)
        if match is None:
            unmatched_count += 1
            continue

        ref, alt, rsid = match

        out_lines.append(
            "\t".join([
                alt,
                ref,
                rsid,
                p_value
            ])
        )

    if out_lines:
        outfile.write("\n".join(out_lines))
        outfile.write("\n")
    
    return unmatched_count
        
def main(args):
    # Setting up parameters
    filedir = args.filedir

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))
    
    # Setting up the log file
    logging.basicConfig(
        filename=os.path.join(filedir, "user.log"),
        filemode="a",
        level=logging.INFO,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
    )
    logger = logging.getLogger(__name__)
    
    # rename
    gwas_ori = os.path.join(filedir, "input.gwas")
    gwas_dest = os.path.join(filedir, "input.gwas.grch38")
    dest = shutil.copyfile(gwas_ori, gwas_dest)
    
    outfile = open(os.path.join(filedir, "input.gwas"), "w")
    unmatch_outfile = open(os.path.join(filedir, "unmatched_snps.tsv"), "w")
    
    type_of_process = process_header(filedir, logger)
    
    n_rows = 0
    n_unmatched = 0
    
    if type_of_process == "process_beta":
        logger.info("Processing with beta column.")
        header = ["effect_allele", "other_allele", "rsid", "p_value", "beta"]
        print("\t".join(header), file=outfile)
        
        #
        snps_by_chr = defaultdict(list)
        with open(os.path.join(filedir, 'input.gwas.grch38'), "r") as f:
            next(f) # skip header
            for line in f:
                n_rows += 1
                items = line.rstrip("\n").split("\t")
                snps_by_chr[items[0]].append({"pos": items[1], 
                                        "effect_allele": items[2], 
                                        "other_allele": items[3], 
                                        "beta": items[4], 
                                        "p_value": items[5]})
                
        window = 1000000 # 1Mb window
        for chrom in snps_by_chr:
            snps_by_chr[chrom].sort(key=lambda x: int(x["pos"]))
            
            snps = snps_by_chr[chrom]
            start = int(snps[0]["pos"])
            end = int(start)

            buffer = []

            for snp in snps:
                if int(snp["pos"]) - int(start) <= window:
                    buffer.append(snp)
                    end = int(snp["pos"])
                else:
                    n_unmatched += process_window_beta(chrom, start, end, buffer, outfile, unmatch_outfile)
                    start = int(snp["pos"])
                    end = int(snp["pos"])
                    buffer = [snp]

            n_unmatched += process_window_beta(chrom, start, end, buffer, outfile, unmatch_outfile)
    else:
        logger.info("Processing without beta column.")
        header = ["rsid", "p_value"]
        print("\t".join(header), file=outfile)
        
        #
        snps_by_chr = defaultdict(list)
        with open(os.path.join(filedir, 'input.gwas.grch38'), "r") as f:
            next(f) # skip header
            for line in f:
                items = line.rstrip("\n").split("\t")
                snps_by_chr[items[0]].append({"pos": items[1],
                                        "p_value": items[2]})
                
        window = 1000000 # 1Mb window
        for chrom in snps_by_chr:
            snps_by_chr[chrom].sort(key=lambda x: int(x["pos"]))
            
            snps = snps_by_chr[chrom]
            start = int(snps[0]["pos"])
            end = int(start)

            buffer = []

            for snp in snps:
                if int(snp["pos"]) - int(start) <= window:
                    buffer.append(snp)
                    end = int(snp["pos"])
                else:
                    n_unmatched += process_window_nobeta(chrom, start, end, buffer, outfile)
                    start = int(snp["pos"])
                    end = int(snp["pos"])
                    buffer = [snp]

            n_unmatched += process_window_nobeta(chrom, start, end, buffer, outfile)
    
    logger.info(f"Total number of rows processed: {n_rows}")
    logger.info(f"Number of unmatched SNPs: {n_unmatched}")


def parse_params():
    parser = argparse.ArgumentParser()
    parser.add_argument('--filedir', required=True, help="Path to input directory.")
    return parser.parse_args()

if __name__ == "__main__":
    main(parse_params())


    
    
    
