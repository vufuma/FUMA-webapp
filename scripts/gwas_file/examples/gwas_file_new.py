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
import basic_helpers as bh
from Column import Column

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

# store the column names in a dictionary as col['variable_name'] = [column_name]
col = {}
col['chrcol'] = Column(param.get('inputfiles', 'chrcol').upper(), 'chr', "CHR$|^chromosome$|^chrom$")
col['poscol'] = Column(param.get('inputfiles', 'poscol').upper(), 'bp', "^BP$|^pos$|^position$")
col['rsIDcol'] = Column(param.get('inputfiles', 'rsIDcol').upper(), 'rsID', "SNP$|^MarkerName$|^rsID$|^snpid$")
col['pcol'] = Column(param.get('inputfiles', 'pcol').upper(), 'p', "^P$|^pval$|^pvalue$|^p-value$|^p_value$")
col['neacol'] = Column(param.get('inputfiles', 'neacol').upper(), 'non_effect_allele', "^A2$|^Non_Effect_allele$|^allele2$|^alleleA$")
col['eacol'] = Column(param.get('inputfiles', 'eacol').upper(), 'effect_allele', "^A1$|^Effect_allele$|^allele1$|^alleleB$")
col['orcol'] = Column(param.get('inputfiles', 'orcol').upper(), 'or', "^or$")
col['becol'] = Column(param.get('inputfiles', 'becol').upper(), 'beta', "^beta$")
col['secol'] = Column(param.get('inputfiles', 'secol').upper(), 'se', "^se$")
col['Ncol'] = Column(param.get('params', 'Ncol').upper(), 'N', "^N$")

GRCh38 = param.get('params', 'GRCh38')
N = param.get('params', 'N')

##### check format of pre-defined lead SNPS and genomic regions if files are provided #####
if leadfile != "NA":
	leadfile = filedir / "input.lead"
	header = bh.get_header_of_file(leadfile) # get the header of the lead SNPs file
	delimiter = bh.DetectDelim(header) # detect the delimiter of the lead SNPs file
	leadfile_df = pd.read_csv(leadfile, comment='#', sep=delimiter) # read the lead SNPs file
	leadfile_df_num_of_columns = len(leadfile_df.columns) # get the number of columns of the lead SNPs file
	if leadfile_df_num_of_columns == 0 or leadfile_df_num_of_columns < 3: # check if the lead SNPs file has at least 3 columns
		sys.exit("Input lead SNPs file does not have enought columns.")
	# TODO: add more checks regarding the format of the lead SNPs file

if regionfile != "NA":
	regionfile = filedir / "input.regions"
	header = bh.get_header_of_file(regionfile) # get the header of the genomic region file
	delimiter = bh.DetectDelim(header) # detect the delimiter of the genomic region file
	regionfile_df = pd.read_csv(regionfile, comment='#', sep=delimiter) # read the genomic region file
	regionsfile_df_num_of_columns = len(regionfile_df.columns) # get the number of columns of the genomic region file
	if regionsfile_df_num_of_columns == 0 or regionsfile_df_num_of_columns < 3: # check if the genomic region file has at least 3 columns
		sys.exit("Input genomic region file does not have enought columns.")
	# TODO: add more checks regarding the format of the genomic region file

##### Read gwas file #####
header = bh.get_header_of_file(gwas) # get the header of the gwas file
delimiter = bh.DetectDelim(header) # detect the delimiter of the gwas file
gwas_file_df = pd.read_csv(gwas, comment='#', sep=delimiter) # read the gwas file
gwas_file_df_columns = list(gwas_file_df.columns) # get the columns only of the gwas file in a list
gwas_file_df_num_of_columns = len(gwas_file_df_columns) # get the number of columns of the gwas file
gwas_file_df_columns = [x.upper() for x in gwas_file_df_columns] # make sure the columns are in upper case

##### Run GRCh38 #####
if GRCh38 == '1': # if GRCh38 is selected
	if col['chrcol'].name != "NA" and col['poscol'].name != "NA" and col['eacol'].name != "NA" and col['neacol'].name != "NA":
		command = "Rscript "+os.path.dirname(os.path.realpath(__file__))+"/giversID.R "+col['chrcol'].name+" "+col['poscol'].name+" "+col['eacol'].name+" "+col['neacol'].name+" "+filedir+" "+col['rsIDcol'].name
		Rsuc = os.system(command)
		col['chrcol'].name = "NA"
		col['poscol'].name = "NA"
		col['rsIDcol'].name = "RSID"
		bh.grcg38_errors(Rsuc)
	else:
		sys.exit("You selected GRCh38 but did not specify chromosome, position, effect allele, or non effect allele")

##### detect column index #####
# user defined colum name - simply check if the column name is in the gwas file
for i, value in enumerate(gwas_file_df_columns): # loop through the columns of the gwas file
	for input_col_index, input_col in col.items(): # loop through the columns of the params.config file
		if input_col.name == value: # if the column name of the params.config file is found in the gwas file
			col[input_col_index].index = i # set the value of the corresponding variable of the params.config file to the column index number of the gwas file
			col[input_col_index].found = True # set the found variable of the corresponding variable of the params.config file to True

# find out which user defined columns were not undetected
# user defined variable which are still undetected will be those that have not been found and are not NA, because NA means that the user did not define the column
undetected_user_defined_columns = []
for input_col_index, input_col in col.items():
	if input_col.name != 'NA' and input_col.found == False:
		undetected_user_defined_columns.append(input_col.name) # add the column name to the undetected_user_defined_columns list
if len(undetected_user_defined_columns) > 0: # return error if there is at least one user defined column that is not found
	sys.exit("The following header(s) was/were not detected in your input file: " + ", ".join(undetected_user_defined_columns))

# then automatic detection
for input_col_index, input_col in col.items(): # loop through the columns of the params.config file
	if input_col.found == False: # if the column name of the params.config file is not found in the gwas file
		for i, value in enumerate(gwas_file_df_columns): # loop through the columns of the gwas file
			# check if there is a regex defined, if the column name is NA (meaning that the user hasn't defined this column), and if the regex matches the column name of the gwas file
			if input_col.regex is not None and input_col.name == 'NA' and re.match(input_col.regex, value, re.IGNORECASE): # if the column name ish of the params.config file is found in the gwas file
				col[input_col_index].name = value # set the value of the corresponding variable of the params.config file to the column index number of the gwas file
				col[input_col_index].index = i # set the value of the corresponding variable of the params.config file to the column index number of the gwas file
				col[input_col_index].found = True # set the found variable of the corresponding variable of the params.config file to True

# rename the columns that are still NA to None so that they can be ignored later, for consistency
for input_col in col.values():
	if input_col.name == 'NA':
		input_col.name = None

# The above 3 loops (user defined detection, automatic detection, renaming) can be combined into one loop, but I think this is more readable

#### allele column check #####
# if only one allele is defined, this has to be alt (effect) allele
col['eacol'], col['neacol'] = bh.only_one_allele_defined(col['eacol'], col['neacol'])

##### Mandatory header check #####
if not col['pcol'].found:
    sys.exit("P-value column was not found")
if (not col['chrcol'].found or not col['poscol'].found) and not col['rsIDcol'].found:
    sys.exit("Chromosome or position and rsID column was not found")

##### Rewrite params.config if optional headers were detected #####
if param.get('inputfiles', 'orcol') == "NA" and col['orcol'].found:
	param.set('inputfiles', 'orcol', 'or')
if param.get('inputfiles', 'becol') == "NA" and col['becol'].found:
	param.set('inputfiles', 'becol', 'beta')
if param.get('inputfiles', 'secol') == "NA" and col['secol'].found:
	param.set('inputfiles', 'secol', 'se')

##### Uncomment this if you want to write the params.config file #####
# paramout = open(filedir / "params.config", 'w+')
# param.write(paramout)
# paramout.close()

##### Rename gwas file columns #####
# if the column name is found in the gwas file, rename the column to the hardcoded column name
for input_col in col.values():
	if input_col.found:
		gwas_file_df.columns.values[input_col.index] = input_col.hardcoded_name # rename the column to the hardcoded column name

##### Drop rows with NA values #####
na_free = gwas_file_df.dropna() # drop rows with NA values
only_na = gwas_file_df[~gwas_file_df.index.isin(na_free.index)] # get rows with NA values by getting the rows that are not in the na_free dataframe
gwas_file_df = na_free # set the gwas_file_df to the na_free dataframe

##### Drop rows with p-values that are not float #####
pcol = col['pcol'].index # get the index of the p-value column
only_floats = gwas_file_df.iloc[:, pcol].apply(pd.to_numeric, errors='coerce').dropna() # get the rows with p-values that are floats
only_nonfloats = gwas_file_df[~gwas_file_df.index.isin(only_floats.index)] # get the rows with p-values that are not floats by getting the rows that are not in the only_floats dataframe
gwas_file_df = gwas_file_df[gwas_file_df.index.isin(only_floats.index)].astype({col['pcol'].hardcoded_name: 'float64'}) # set the gwas_file_df to the only_floats dataframe and convert the p-value column to float64

##### Drop rows with p-values that are not between 0 and 1 #####
accepted_p_values_only = gwas_file_df[(gwas_file_df.iloc[:, pcol] > 0) & (gwas_file_df.iloc[:, pcol] <= 1)] # get the rows with p-values that are between 0 and 1, 1 is included, 0 is not included
non_accepted_p_values_only = gwas_file_df[~gwas_file_df.index.isin(accepted_p_values_only.index)] # get the rows with p-values that are not between 0 and 1 by getting the rows that are not in the accepted_p_values_only dataframe
gwas_file_df = accepted_p_values_only # set the gwas_file_df to the accepted_p_values_only dataframe

##### Delete chr and CHR strings from chrcol column #####
if col['chrcol'].found:
	chrcol = col['chrcol'].index # get the index of the chr column
	gwas_file_df = gwas_file_df.astype({col['chrcol'].hardcoded_name: 'str'}) # convert the chrcol column to string
	gwas_file_df.iloc[:, chrcol] = gwas_file_df.iloc[:, chrcol].str.replace('chr', '', case = False) # replace chr/CHR with nothing
	gwas_file_df.iloc[:, chrcol] = gwas_file_df.iloc[:, chrcol].apply(pd.to_numeric, errors='coerce').dropna()





print(gwas_file_df)
print(gwas_file_df.dtypes)
# print(gwas_file_df.dropna())



# printing the results
# for input_col_index, input_col in col.items():
# 	print(input_col_index , '-->' , input_col.name, input_col.index, input_col.found, input_col.regex, input_col.hardcoded_name)
