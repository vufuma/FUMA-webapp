import sys
sys.path.append("../src")

import os
import re
import pandas as pd
import numpy as np
import configparser
import time
from bisect import bisect_left
import tabix
import csv
import subprocess
import argparse
from pathlib import Path
import helpers.basic_helpers as bh


parser = argparse.ArgumentParser() # Create the parser
parser.add_argument('filedir', type=str, help='the file directory') # Add an argument
args = parser.parse_args() # Parse the argument
filedir = Path(args.filedir) # Parse the argument str as a Path object

##### config variables #####
cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__)) + '/app.config') # read the app.config file

param = configparser.RawConfigParser()
param.optionxform = str
param.read(filedir / 'params.config') # read the params.config file

##### prepare parameters #####
leadfile = param.get('inputfiles', 'leadSNPsfile')
regionfile = param.get('inputfiles', 'regionsfile')

gwas = filedir / cfg.get('inputfiles', 'gwas')
outSNPs = filedir / "input.snps"
outMAGMA = filedir / "magma.in"

chrcol = param.get('inputfiles', 'chrcol').upper()
poscol = param.get('inputfiles', 'poscol').upper()
rsIDcol = param.get('inputfiles', 'rsIDcol').upper()
pcol = param.get('inputfiles', 'pcol').upper()
neacol = param.get('inputfiles', 'neacol').upper()
eacol = param.get('inputfiles', 'eacol').upper()
orcol = param.get('inputfiles', 'orcol').upper()
becol = param.get('inputfiles', 'becol').upper()
secol = param.get('inputfiles', 'secol').upper()
Ncol = param.get('params', 'Ncol').upper()
GRCh38 = param.get('params', 'GRCh38')
N = param.get('params', 'N')

##### check format of pre-defined lead SNPS and genomic regions if files are provided #####
if leadfile != "NA":
	leadfile = filedir / "input.lead"
	delimiter = bh.DetectDelim(open(leadfile).readline())
	leadfile_df = pd.read_csv(leadfile, sep=delimiter)
	leadfile_df_num_of_columns = len(leadfile_df.columns)
	if leadfile_df_num_of_columns == 0 or leadfile_df_num_of_columns < 3:
		sys.exit("Input lead SNPs file does not have enought columns.")
	# TODO: add more checks regarding the format of the lead SNPs file

if regionfile != "NA":
	regionfile = filedir / "input.regions"
	delimiter = bh.DetectDelim(open(regionfile).readline())
	regionfile_df = pd.read_csv(regionfile, sep=delimiter)
	regionsfile_df_num_of_columns = len(regionfile_df.columns)
	if regionsfile_df_num_of_columns == 0 or regionsfile_df_num_of_columns < 3:
		sys.exit("Input genomic region file does not have enought columns.")
	# TODO: add more checks regarding the format of the genomic region file

##### Run GRCh38 #####
if GRCh38=='1':
	if chrcol is None or poscol is None or eacol is None or neacol is None:
		sys.exit("You selected GRCh38 but did not specify chromosome, position, effect allele, or non effect allele")
	if chrcol is not None and poscol is not None and eacol is not None and neacol is not None:
		command = "Rscript "+os.path.dirname(os.path.realpath(__file__))+"/giversID.R "+chrcol+" "+poscol+" "+eacol+" "+neacol+" "+filedir+" "+rsIDcol
		Rsuc=os.system(command)
		chrcol = "NA"
		poscol = "NA"
		rsIDcol = "RSID"
		bh.grcg38_errors(Rsuc)