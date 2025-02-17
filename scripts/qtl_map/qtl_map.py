import time
import sys
import os
import pandas as pd
import numpy as np
# from scripts.helpers import Configuration
# from scripts.qtl_map.qtl_map_helpers import process_qtl, do_eqtl_mapping
from temp import Configuration
from qtl_map_helpers import process_qtl, do_eqtl_mapping

def main():
    start = time.time()
    filedir="/home/tnphung/FUMA-dev/refactor_geteQTL/3/"
    config_class = Configuration(filedir=filedir)
    
    fsnps = os.path.join(filedir, "snps.txt")
    floci = os.path.join(filedir, "GenomicRiskLoci.txt")
    out_fp = os.path.join(filedir, "eqtl.txt")
    fout = open(out_fp, "w")
    print("\t".join(["uniqID", "db", "tissue", "gene", "testedAllele", "p", "signed_stats", "FDR", "RiskIncAllele", "alignedDirection"]), file=fout)
    
    ##### Process per locus #####
    loci = pd.read_csv(floci, sep="\t", usecols=[0,3,6,7], header=0) #GenomicLocus, chr, start, end
    snps = pd.read_csv(fsnps, sep="\t", usecols=[0,2,3], header=0) #uniqID, chr, pos
    
    if config_class._eqtlMap == 1:
        for feqtl in config_class._eqtlds:
            process_qtl(fqtl=feqtl, config_class=config_class, loci=loci, snps=snps, fout=fout)
        fout.close()
        eqtl = do_eqtl_mapping(config_class, out_fp, fsnps)
        eqtl.to_csv(out_fp, sep='\t', encoding='utf-8', index=False, header=True)

    
    print(f"Processing time: {time.time()-start}")
    
if __name__ == '__main__':
    main()