import time
import sys
import os
import pandas as pd
import numpy as np
from config_helpers import Configuration
from qtl_map_helpers import process_xqtls, do_xqtls_mapping
from collections import defaultdict

def main():
    start = time.time()
    filedir = sys.argv[1]
    
    config_class = Configuration(filedir=filedir) #create a config class
    
    # read in the GenomicRiskLoci.txt and snps.txt files
    loci = pd.read_csv(os.path.join(filedir, "GenomicRiskLoci.txt"), sep="\t", usecols=[0,3,6,7], header=0) #GenomicLocus, chr, start, end
    snps = pd.read_csv(os.path.join(filedir, "snps.txt"), sep="\t", usecols=[0,2,3], header=0) #uniqID, chr, pos
    
    if config_class._xqtlsMap == 1:
        out_fp = os.path.join(filedir, "xqtls.txt")
        tmp_out = open(os.path.join(filedir, "xqtls_tmp.txt"), "w")
        print("\t".join(["uniqID", "db", "tissue", "protein", "testedAllele", "beta", "P", "type", "qtl_type", "genomicriskloci"]), file=tmp_out)
        for fxqtl in config_class._xqtlsMapdss:
            process_xqtls(fqtl=fxqtl, config_class=config_class, loci=loci, snps=snps, fout=tmp_out)
        tmp_out.close()

        xqtls = do_xqtls_mapping(config_class, os.path.join(filedir, "xqtls_tmp.txt"), snps)
        try:
            xqtls = xqtls[["uniqID", "db", "tissue", "protein", "type", "qtl_type", "genomicriskloci", "ensemble_id"]]
            xqtls.to_csv(out_fp, sep='\t', encoding='utf-8', index=False, header=True)
        except:
            with open(out_fp, 'w') as f:
                f.write("uniqID\tdb\ttissue\tprotein\ttype\tqtl_type\tgenomicriskloci\tensemble_id\n")
            print("Nothing to print out.")
            
        # make the qtls_hits.tsv file for the upset plot
        outfile = open(os.path.join(filedir, "qtls_hits.tsv"), "w")
        print("\t".join(["gene", "type"]), file=outfile)
        type_gene_dict = defaultdict(set)
        with open(os.path.join(filedir, "xqtls.txt"), "r") as f:
            for  line in f:
                if line.startswith("uniqID"):
                    continue
                else:
                    line = line.strip().split("\t")
                    type_gene_dict[line[5]].add(line[3])
        for type, genes in type_gene_dict.items():
            for gene in genes:
                print("\t".join([gene, type]), file=outfile)
        outfile.close()

    print(f"Processing time: {time.time()-start}")
    
if __name__ == '__main__':
    main()