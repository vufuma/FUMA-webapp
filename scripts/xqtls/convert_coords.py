# this script converts start end coordinates of the genomic locus from GRCh37 to GRCh38
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

liftover_dir = os.path.join(cfg.get('data', 'liftover'))

chrom=param.get('params','chrom')
start=param.get('params','start')
end=param.getint('params','end')

print("\t".join(["chr"+chrom, start, str(end+1)]), file=open(os.path.join(filedir, "locus_range_grch37.txt"), "w"))

command = "./liftOver -bedPlus=3 " + filedir + "locus_range_grch37.txt " +  liftover_dir + "/hg19ToHg38.over.chain.gz " + filedir + "locus_range_grch38.txt " + filedir + "unMapped"
print(command)

os.system(command)

# sanity check the conversion
# expect just 1 line

n_lines = 0
if not os.path.exists(os.path.join(filedir, "locus_range_grch38.txt")):
    sys.exit(1)
    
with open(os.path.join(filedir, "locus_range_grch38.txt"), "r") as f:
    for line in f:
        n_lines += 1
        items = line.rstrip("\n").split("\t")
        if items[0] != "chr" + chrom:
            sys.exit(3)
        
if n_lines > 1:
    sys.exit(2)
    
    
    
