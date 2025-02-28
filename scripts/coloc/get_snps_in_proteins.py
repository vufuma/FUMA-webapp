import time
import sys
import os
import pandas as pd
import numpy as np

# this script gets the list of snps that fall within a protein region

# genomic risk loci: 23

def main():
    start = time.time()
    filedir = sys.argv[1]
    
    # read in files
    loci = pd.read_csv(os.path.join(filedir, "GenomicRiskLoci.txt"), sep="\t", usecols=[0, 3, 6, 7], header=0) #GenomicLocus, chr, start, end
    snps = pd.read_csv(os.path.join(filedir, "snps.txt"), sep="\t", usecols=[0, 2, 3, 6, 8, 9], header=0) #uniqID, chr, pos, maf, or, se
    
    loci_chrom = 3
    loci_start = 52217088
    loci_end = 53175017
    
    for locus in range(len(loci)):
        chrom = loci.iloc[locus,1]
        start = loci.iloc[locus,2]
        end = loci.iloc[locus,3]
        snps_in_locus = []
        for snp in range(len(snps)):
            snp_chrom = snps.iloc[snp, 1]
            snp_pos = snps.iloc[snp, 2]
            if snp_chrom == chrom and snp_pos <= end and snp_pos >= start:
                snps_in_locus.append(snp)
        pqtls_in_locus = []
        for pqtl in range(len(pqtls)):
            pqtl_chrom = pqtls.iloc[pqtl, 4]
            pqtl_pos = pqtls.iloc[pqtl, 5]
            if pqtl_chrom == chrom and pqtl_pos <= end and pqtl_pos >= start:
                pqtls_in_locus.append(pqtl)
        if len(snps_in_locus) > 0 and len(pqtls_in_locus) > 0:
            # print(f"For locus {locus} chrom {chrom}:{start}-{end}:")
            # print("SNPs: ")
            # print(snps_in_locus)
            # print("pQTLs")
            # print(pqtls_in_locus)
            snps_out = open(os.path.join(filedir, "locus_" + str(locus) + "_snps.txt"), "w")
            header = ["snp", "chr", "pos", "maf", "or", "se"]
            print("\t".join(header), file=snps_out)
            for i in snps_in_locus:
                out = [snps.iloc[i, 0], snps.iloc[i, 1].astype(str), snps.iloc[i, 2].astype(str), snps.iloc[i, 3].astype(str), snps.iloc[i, 4].astype(str), snps.iloc[i, 5].astype(str)]
                print("\t".join(out), file=snps_out)
                
            pqtls_out = open(os.path.join(filedir, "locus_" + str(locus) + "_pqtls.txt"), "w")
            header = ["snp", "chr", "pos", "maf", "beta", "se"]
            print("\t".join(header), file=pqtls_out)
            for i in pqtls_in_locus:
                out = [pqtls.iloc[i, 0], pqtls.iloc[i, 4].astype(str), pqtls.iloc[i, 5].astype(str), pqtls.iloc[i, 1].astype(str), pqtls.iloc[i, 2].astype(str), pqtls.iloc[i, 3].astype(str)]
                print("\t".join(out), file=pqtls_out)
            
        
    print(f"Processing time: {time.time()-start}")
    
if __name__ == '__main__':
    main()