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
rejected_snps = filedir / "rejected.snps"
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

tmp = gwas_file_df.copy(deep=True) # create a copy of the gwas_file_df dataframe, this will be used later to return the actual rows that were not accepted based on the index of the non-accepted rows

##### Set rows with p-values that are not float to NaN and exclude the rows with p-values that are not between 0 and 1 #####
# TODO: make it function
pcol = col['pcol'].index # get the index of the p-value column
gwas_file_df.iloc[:, pcol] = gwas_file_df.iloc[:, pcol].apply(pd.to_numeric, errors='coerce') # convert the p-value column to float non-convertible values will be converted to NaN
gwas_file_df = gwas_file_df[(gwas_file_df.iloc[:, pcol] > 0) & (gwas_file_df.iloc[:, pcol] <= 1)] # get the rows with p-values that are between 0 and 1, 1 is included, 0 is not included

##### Parse chromosome column <chrcol> #####
if col['chrcol'].found:
	gwas_file_df = bh.parse_chrcol(gwas_file_df, col['chrcol'])

gwas_file_df = gwas_file_df.dropna() # drop all rows with NA values

##### Turn columns to uppercase #####
# This sgould be placed adter the dropna because you don't want to convert nan to uppercase, if that happened, the new string uppercase nans would not be dropped
if col['eacol'].found: 
	gwas_file_df = bh.turn_column_to_uppercase(gwas_file_df, col['eacol'])
if col['neacol'].found:
	gwas_file_df = bh.turn_column_to_uppercase(gwas_file_df, col['neacol'])

##### Convert columns to specific data types #####
gwas_file_df = gwas_file_df.astype({
	# TODO: make this dynamic, only convert the columns that are found to specific data types 
	col['pcol'].hardcoded_name: 'float64',
	col['chrcol'].hardcoded_name: 'int64',
	col['poscol'].hardcoded_name: 'int64',
	})

gwas_file_df = gwas_file_df.sort_values([col['chrcol'].hardcoded_name, col['poscol'].hardcoded_name], ascending=[True, True]) # sort the dataframe by chromosome and position in ascending order

rejected_rows = tmp[~tmp.index.isin(gwas_file_df.index)] # get the actual rows that were not accepted based on the index of the non-accepted rows


# when all columns are provided
# In this case, if the rsID columns is wrongly labeled, it will be problem later (not checked here)
if col['chrcol'].found and col['poscol'].found and col['rsIDcol'].found and col['eacol'].found and col['neacol'].found:
	# There is nothing else to do here, go to the end (writing files and final checks)
	pass

# if both chr and pos are provided
elif col['chrcol'].found and col['poscol'].found:
	##### init variables #####
	##### read input.gwas line by line #####
		# do many things here and prepare the variables that will be passed to tabix
		# for each line in the input.gwas file calculate things and construct the variables that will be passed to tabix
	

		# Do some checks and if they satisfy the conditions, call tabix
		# Tabix(cur_chr, minpos, maxpos, temp)
	
	# There is nothing else to do here, go to the end (writing files and final checks)
	pass

# if either chr or pos is not procided, use rsID to extract position
elif not col['chrcol'].found or not col['poscol'].found:
	pass



gwas_file_df.to_csv(outSNPs, sep='\t', index=False) # write the gwas_file_df dataframe to a file
rejected_rows.to_csv(rejected_snps, sep='\t', index=False) # write the rejected_rows dataframe to a file

# TODO: check how many snps left in the gwas_file_df, if 0, exit with error message
if len(gwas_file_df.index) == 0:
	sys.exit("No SNPs left after filtering. Please check your input file.")


print(rejected_rows)
print(gwas_file_df)
print(gwas_file_df.dtypes)


# printing the results
for input_col_index, input_col in col.items():
	print(input_col_index , '-->' , input_col.name, input_col.index, input_col.found, input_col.regex, input_col.hardcoded_name)
