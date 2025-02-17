import time
import os
import os
import pandas as pd
from gene_map_helpers import Configuration, do_eqtl_mapping


def main():
    start = time.time()
    filedir="/home/tnphung/FUMA-dev/refactor_geteQTL/218399/"
    config_class = Configuration(filedir=filedir)
    if config_class._eqtlMap == 1:
        eqtl_fp = os.path.join(filedir, "eqtl.txt")
        snps_fp = os.path.join(filedir, "snps.txt")
        eqtl = do_eqtl_mapping(config_class, eqtl_fp, snps_fp)
        eqtl.to_csv(os.path.join(filedir, "eqtl.txt"), sep='\t', encoding='utf-8', index=False, header=True)
    
    
    print(f"Processing time: {time.time()-start}")
if __name__ == '__main__':
    main()