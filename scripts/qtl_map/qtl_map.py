import time
import sys
import os
import pandas as pd
import numpy as np
# from scripts.helpers import Configuration
# from scripts.qtl_map.qtl_map_helpers import process_eqtl, do_eqtl_mapping
from config_helpers import Configuration
from qtl_map_helpers import process_eqtl, do_eqtl_mapping, process_pqtl, do_pqtl_mapping

def main():
    start = time.time()
    filedir = sys.argv[1]
    
    config_class = Configuration(filedir=filedir) #create a config class
    
    # read in the GenomicRiskLoci.txt and snps.txt files
    loci = pd.read_csv(os.path.join(filedir, "GenomicRiskLoci.txt"), sep="\t", usecols=[0,3,6,7], header=0) #GenomicLocus, chr, start, end
    snps = pd.read_csv(os.path.join(filedir, "snps.txt"), sep="\t", usecols=[0,2,3], header=0) #uniqID, chr, pos
    
    # comment out the eQTL mapping (date: feb 19 2025) because for now I will use this functionality for other QTLs that are not eQTLs
    # if config_class._eqtlMap == 1: 
    #     out_fp = os.path.join(filedir, "eqtl.txt")
    #     fout = open(out_fp, "w")
    #     print("\t".join(["uniqID", "db", "tissue", "gene", "testedAllele", "p", "signed_stats", "FDR", "RiskIncAllele", "alignedDirection"]), file=fout)
    #     for feqtl in config_class._eqtlds:
    #         process_eqtl(fqtl=feqtl, config_class=config_class, loci=loci, snps=snps, fout=fout)
    #     fout.close()
    #     eqtl = do_eqtl_mapping(config_class, out_fp, snps)
    #     eqtl.to_csv(out_fp, sep='\t', encoding='utf-8', index=False, header=True)
    
    if config_class._pqtlMap == 1:
        out_fp = os.path.join(filedir, "pqtl.txt")
        fout = open(out_fp, "w")
        print("\t".join(["uniqID", "db", "tissue", "protein", "testedAllele", "P", "type", "RiskIncAllele", "alignedDirection"]), file=fout)
        for fpqtl in config_class._pqtlMapdss:
            process_pqtl(fqtl=fpqtl, config_class=config_class, loci=loci, snps=snps, fout=fout)
        fout.close()
        for fpqtl in config_class._pqtlMapdss:
            pqtl = do_pqtl_mapping(config_class, out_fp, snps)
            pqtl.to_csv(out_fp, sep='\t', encoding='utf-8', index=False, header=True)
    
    print(f"Processing time: {time.time()-start}")
    
if __name__ == '__main__':
    main()