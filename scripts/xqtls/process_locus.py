# this script sanitizes and process locus file
import tabix
import os
import argparse
import sys
import configparser


parser = argparse.ArgumentParser()
parser.add_argument('--filedir', required=True, help="Path to input directory.")
args = parser.parse_args()

# Setting up parameters
filedir = args.filedir

cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = configparser.RawConfigParser()
param.optionxform = str
param.read(os.path.join(filedir, 'params.config'))

chrom = param.get('params','chrom')
build = param.get('params','build').lower()

dbsnp_dir = os.path.join(cfg.get('data', 'dbSNP'), "dbSNP_v157", build)

total_snps = 0
skipped_snps = 0

# prepare output file
outfile = open(os.path.join(filedir, 'locus.input'), "w")
header = ["RSID", "ALT", "REF", "N", "BETA", "P", "MAF"]
print("\t".join(header), file=outfile)


# get the tabix file
tb = tabix.open(os.path.join(dbsnp_dir, "dbSNP157.chr" + chrom + ".vcf.gz"))

with open(os.path.join(filedir, 'locus.input.orig'), 'r') as f:
    header = [i.lower() for i in f.readline().strip().split("\t")]
    # Process the header and file contents here
    if header != ['chr', 'pos', 'ref', 'alt', 'n', 'beta', 'p', 'maf']:
        print("Error: The header of locus.input file is not in the expected format.")
        sys.exit(1)
        
    for line in f:
        if line.startswith("CHR".lower()):
            continue  # skip header line
        total_snps += 1
        chrom, pos, ref, alt, n, beta, p, maf = line.rstrip("\n").split("\t")
        
        # check against dbSNP to confirm alleles
        query_region = str(chrom) + ":" + str(pos) + "-" + str(pos)
        queried_results = tb.querys(query_region)
        
        skip_counter = 0
        
        for query in queried_results:
            db_chrom = query[0]
            db_pos = query[1]
            if db_pos != pos or db_chrom != chrom:
                continue  # position or chromosome do not match
            
            
            db_rsid = query[2]
            db_ref = query[3]
            db_alt_alleles = query[4].split(',')
            
            if ref == db_ref and alt in db_alt_alleles:
                skip_counter = 0
                break
            elif ref in db_alt_alleles and alt == db_ref:
                # alleles are flipped
                print("Warning: Alleles are flipped for " + chrom + ":" + pos + ". Input REF: " + ref + ", ALT: " + alt + ". Flipping alleles and effect size.")
                ref, alt = alt, ref  # flip alleles
                beta = str(-float(beta))  # flip effect size
                skip_counter = 0
                break
            else: 
                # no match
                print("Warning: Alleles do not match dbSNP for " + chrom + ":" + pos + ". Skipping this SNP.")
                skip_counter += 1
                continue
            
        if skip_counter > 0: 
            skipped_snps += 1

        out = [db_rsid, alt, ref, n, beta, p, maf]
        print("\t".join(out), file=outfile)
outfile.close()
print("Total SNPs processed: " + str(total_snps))
print("SNPs skipped: " + str(skipped_snps))