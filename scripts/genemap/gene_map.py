import time
import os
from gene_map_helpers import Configuration, do_eqtl_mapping


def main():
    start = time.time()
    filedir="/home/tnphung/FUMA-dev/refactor_geteQTL/218399/"
    config_class = Configuration(filedir=filedir)
    if config_class._eqtlMap == 1:
        eqtl_fp = os.path.join(filedir, "eqtl.txt")
        do_eqtl_mapping(config_class, eqtl_fp)
    
    
    print(f"Processing time: {time.time()-start}")
if __name__ == '__main__':
    main()