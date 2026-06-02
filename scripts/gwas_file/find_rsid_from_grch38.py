# this script looks up rsid from chromome, position, effect allele and other allele
# expect input in the gwas catalog format
import os
import argparse
import shutil
import sys
import configparser
import tabix

cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

# # --- Load dbSNP ---
# tb = tabix.open(os.path.join(os.path.join(cfg.get('data', 'dbSNP')), f"dbSNP146.chr3.vcf.gz")) #TODO: need to divide by chromosomes

tb_cache = {}

def check_alleles_cached(chrom, pos, effect_allele, other_allele, beta):

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
        queried_results = tb.querys(f"{chrom}:{pos}-{pos}")
    except Exception:
        return (effect_allele, other_allele, "NA", 1, beta)

    for query in queried_results:
        db_ref = query[3]
        db_alt_alleles = query[4].split(',')
        rsid = query[2]

        if effect_allele in db_alt_alleles and other_allele == db_ref:
            return (effect_allele, other_allele, rsid, 0, beta)

        if other_allele in db_alt_alleles and effect_allele == db_ref:
            return (
                effect_allele,
                other_allele,
                rsid,
                0,
                str(-float(beta))
            )

    return (effect_allele, other_allele, "NA", 1, beta)
            
def main(args):
    # Setting up parameters
    filedir = args.filedir

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))
    

    
    # rename
    gwas_ori = os.path.join(filedir, "input.gwas")
    gwas_dest = os.path.join(filedir, "input.gwas.grch38")
    dest = shutil.copyfile(gwas_ori, gwas_dest)
    
    outfile = open(os.path.join(filedir, "input.gwas"), "w")
    
    header = ["effect_allele", "other_allele", "rsid", "p_value", "beta"]
    print("\t".join(header), file=outfile)
    
    #
    with open(os.path.join(filedir, 'input.gwas.grch38'), "r") as f:
        for line in f:
            if line.startswith("chromosome"):
                continue
            items = line.rstrip("\n").split("\t")
            chrom = items[0]
            pos = items[1]
            effect_allele = items[2]
            other_allele = items[3]
            beta = items[4]
            p_value = items[6]
            
            effect_allele, other_allele, rsid, skip, beta = check_alleles_cached(
                chrom, pos, effect_allele, other_allele, beta)

            if skip:
                # skipped_snps += 1
                continue
            
            new_out = [str(effect_allele), str(other_allele), str(rsid), str(p_value), str(beta)]

            print("\t".join(new_out), file=outfile)
    
    
def parse_params():
    parser = argparse.ArgumentParser()
    parser.add_argument('--filedir', required=True, help="Path to input directory.")
    return parser.parse_args()

if __name__ == "__main__":
    main(parse_params())


    
    
    
