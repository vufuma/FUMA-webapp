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

command = "./liftOver -bedPlus=3 " + filedir + "/locus_range_grch37.txt " +  liftover_dir + "/hg38ToHg19.over.chain.gz " + filedir + "/locus_range_grch38.txt " + filedir + "/unMapped"

os.system(command)
