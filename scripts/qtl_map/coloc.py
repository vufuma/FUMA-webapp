import pandas as pd
from collections import defaultdict
import os, sys, time
import tabix
from config_helpers import Configuration
import subprocess

def prepare_coloc_inputs(filedir, locus, locus_chrom, locus_start, locus_end, gene, config_class, ds, tissue):
    pqtls_tb_fp = os.path.join(config_class._qtldir, "pQTL", ds, "full_sumstats", tissue, gene + "_chr" + str(locus_chrom) + ".tsv.gz")
    
    # check if the gene tabix file exists
    if os.path.exists(pqtls_tb_fp):        
        # read in files
        snps_tb = tabix.open(os.path.join(filedir, "input.gwas.gz"))
        pqtls_tb = tabix.open(pqtls_tb_fp)
        
        # locus_chrom = 3
        # locus_start = 52217088 - 500000
        # locus_end = 53175017 + 500000
        locus_start = locus_start - 500000
        locus_end = locus_end + 500000
        
        region=str(locus_chrom)+":"+str(locus_start)+"-"+str(locus_end)
        
        snps = snps_tb.querys(region)
        pqtls = pqtls_tb.querys(region)
        
        snps_out = open(os.path.join(filedir, "locus_" + str(locus) + "_" + tissue + "_" + gene + "_snps_for_coloc.txt"), "w")
        print("\t".join(["chr", "pos", "snp", "ref", "alt", "maf", "or", "se", "p"]), file=snps_out)
        
        pqtls_out = open(os.path.join(filedir, "locus_" + str(locus) + "_" + tissue + "_" + gene + "_pqtls_out.txt"), "w")
        # print("\t".join(["chr", "pos", "beta", "p", "se", "N", "maf"]), file=pqtls_out)
        print("\t".join(["chr", "pos", "ref", "alt", "beta", "se", "maf", "P", "N"]), file=pqtls_out)
        
        for i in snps:
            print("\t".join(i), file=snps_out)
        for i in pqtls:
            print("\t".join(i), file=pqtls_out)
    else:
        print(f"File {pqtls_tb_fp} does not exist.")

def main():
    start = time.time()
    filedir = sys.argv[1]
    
    config_class = Configuration(filedir=filedir) #create a config class
    
    sample_size = config_class._N
    cases_prop = config_class._cases_prop
    
    pqtl_df = pd.read_csv(os.path.join(filedir, "pqtl.txt"), sep="\t")
    snps_df = pd.read_csv(os.path.join(filedir, "snps.txt"), sep="\t", usecols=[0,2,3,12], header=0) #uniqID, chr, pos, GenomicLocus
    loci_df = pd.read_csv(os.path.join(filedir, "GenomicRiskLoci.txt"), sep="\t", usecols=[0,3,6,7], header=0) #GenomicLocus, chr, start, end

    pqtl_merged_snps = pqtl_df.merge(snps_df, on=["chr", "pos"], how="inner") #merge pqtl_df and snps_df to get the GenomicLocus
    print(f"Before merging, the dataframe pqtl has {pqtl_df.shape[0]} rows.")
    print(f"After merging, the dataframe pqtl has {pqtl_merged_snps.shape[0]} rows.")

    pqtl_merged_snps_merged_loci = pqtl_merged_snps.merge(loci_df, on=["GenomicLocus", "chr"], how="inner") #merge with loci_df to get the chr, start, and end of the GenomicLocus
    print(f"Before merging, the dataframe pqtl has {pqtl_merged_snps.shape[0]} rows.")
    print(f"After merging, the dataframe pqtl has {pqtl_merged_snps_merged_loci.shape[0]} rows.")

    loci_mappedGenes = defaultdict(set) # for each genomic locus, list the (db, protein)
    for index, row in pqtl_merged_snps_merged_loci.iterrows():
        loci_mappedGenes[(row["GenomicLocus"], row["chr"], row["start"], row["end"])].add((row["db"], row["tissue"], row["protein"]))
    
    # initialize an output file for coloc results
    coloc_out_fn = os.path.join(filedir, "coloc_results.txt")
        
    for loci, data in loci_mappedGenes.items():
        for i in data:
            genomic_locus = loci[0]
            genomic_locus_chr = loci[1]
            genomic_locus_start = loci[2]
            genomic_locus_end = loci[3]
            ds = i[0]
            tissue = i[1]
            gene = i[2]
            
            # prepare inputs for coloc
            prepare_coloc_inputs(filedir=filedir, locus=genomic_locus, locus_chrom=genomic_locus_chr, locus_start=genomic_locus_start, locus_end=genomic_locus_end, gene=gene, config_class=config_class, ds=ds, tissue=tissue)
            
            # run coloc
            snps_fn = os.path.join(filedir, "locus_" + str(genomic_locus) + "_" + tissue + "_" + gene + "_snps_for_coloc.txt")
            pqtls_fn = os.path.join(filedir, "locus_" + str(genomic_locus) + "_" + tissue + "_" + gene + "_pqtls_out.txt")
            if os.path.exists(snps_fn) and os.path.exists(pqtls_fn):
                coloc_log_fp = os.path.join(filedir, "locus_" + str(genomic_locus) + "_" + tissue + "_" + gene + "_coloc_log.txt")
                with open(coloc_log_fp, "w") as coloc_log:
                    subprocess.run(["Rscript", os.path.join(os.path.dirname(os.path.realpath(__file__)), "coloc_func.R"), snps_fn, pqtls_fn, coloc_out_fn, str(genomic_locus), tissue, gene, sample_size, cases_prop], stdout=coloc_log, stderr=subprocess.STDOUT)
            
    
    print(f"Processing time: {time.time()-start}")
    
if __name__ == '__main__':
    main()

