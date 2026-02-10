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

def check_alleles(tb, chrom, pos, ref, alt, beta, maf):
    """Check if the alleles match, are flipped, or do not match."""
    query_region = f"{chrom}:{pos}-{pos}"
    # print(f"Querying dbSNP for region: {query_region}")
    queried_results = tb.querys(query_region)
    
    db_rsid = "NA"
    
    beta_f = float(beta)
    maf_f = float(maf)
    
    for query in queried_results:
        db_ref = query[3]
        db_alt_alleles = set(query[4].split(','))
        rsid = query[2]
        
        if ref == db_ref and alt in db_alt_alleles:
            # alleles match
            return ref, alt, rsid, beta, maf, 0
        
        if ref in db_alt_alleles and alt == db_ref:
            # alleles are flipped
            # print(f"Warning: Alleles are flipped for {chrom}:{pos}. Input REF: {ref}, ALT: {alt}. Flipping alleles and effect size.")
            return (
                alt,
                ref,
                rsid,
                str(-beta_f),
                1.0 - maf_f,
                0,
            )
    # else:
    #     # no match found
    #     print(f"Warning: Alleles do not match dbSNP for {chrom}:{pos}. Skipping this SNP.")
    #     return ref, alt, db_rsid, beta, maf, 1
    
    return ref, alt, db_rsid, beta, maf, 1

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
        
        ref, alt, db_rsid, beta, maf, skip_count = check_alleles(tb, chrom, pos, ref, alt, beta, maf)
        if skip_count > 0:
            skipped_snps += 1
            continue  # skip this SNP

        out = [str(db_rsid), str(alt), str(ref), str(n), str(beta), str(p), str(maf)]
        print("\t".join(out), file=outfile)
outfile.close()
print("Total SNPs processed: " + str(total_snps))
print("SNPs skipped: " + str(skipped_snps))