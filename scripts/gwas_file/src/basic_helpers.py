import re
import csv
import sys
import pandas as pd
import numpy as np
import tabix
from Column import Column

# This function is used to detect the delimiter of a CSV file.
# The function takes a header parameter, which is assumed to be the first row of a CSV file.
# It uses a regular expression (re.match) to check if there are two or more consecutive whitespace characters in the header.
# If this condition is true, it returns the regular expression r'\s+', which represents one or more whitespace characters. This suggests that the function is assuming the file is space-separated.
# If the first condition is not met, it uses the csv.Sniffer() class to automatically detect the delimiter in the CSV file.
# The sniffer.sniff(header) call analyzes the input header and returns a Dialect object that contains information about the CSV format, including the detected delimiter.
# Finally, it returns the detected delimiter using dialect.delimiter.
def DetectDelim(header):
	if re.match(r'.*\s\s.*', header) is not None:
		return r'\s+'
	sniffer = csv.Sniffer()
	dialect = sniffer.sniff(header)
	return dialect.delimiter

def get_header_of_file(file, comment_char="#"):
	with open(file, 'r') as f:
		for line in f:
			if not line.startswith(comment_char):
				return line

# this function is used to check if a variable is a float
def is_float(s):
    return isinstance(s, float)

def grcg38_errors(err_code):
	if err_code == 512:
		sys.exit("chr_10001, pos_10001, allele_10001, allele_20001 are in input. Please rename columns to something else.")
	elif err_code == 768:
		sys.exit("not all specified columns match input file")
	elif err_code == 1024:
		sys.exit("Some column names are duplicated in the input file")
	elif err_code != 0:
		sys.exit("Something went wrong when converting GRCh38 to rsID. Please contact the developer.")
	return err_code

def only_one_allele_defined(eacol: Column, neacol: Column):
	if neacol.found and not eacol.found:
		eacol.name = neacol.name
		eacol.index = neacol.index
		eacol.found = neacol.found

		neacol.name = None
		neacol.index = None
		neacol.found = False

	return eacol, neacol

def turn_column_to_uppercase(df, col):
	index = col.index # get the index of the column
	df = df.astype({col.hardcoded_name: 'str'}) # convert the column to string
	df.iloc[:, index] = df.iloc[:, index].str.upper() # convert the column to uppercase
	return df

def parse_chrcol(df, chrcol):
	index = chrcol.index # get the index of the chr column
	df = df.astype({chrcol.hardcoded_name: 'str'}) # convert the chrcol column to string
	df.iloc[:, index] = df.iloc[:, index].str.replace('chr', '', case = False) # replace chr/CHR with nothing
	df.iloc[:, index] = df.iloc[:, index].str.replace('x', '23', case = False) # replace x/X with 23
	df.iloc[:, index] = df.iloc[:, index].apply(pd.to_numeric, errors='coerce') # convert the chrcol column to float non-convertible values will be converted to NaN
	df[(df.iloc[:, index] < 1) | (df.iloc[:, index] > 23)] = np.nan # set values that are not between 1 and 23 to NaN
	return df

def Tabix(chrom, start ,end, snps):

	# when rsID is the only missing column, keep all SNPs in input file and assign rsID or uniqID from the selected reference panel
	if neacol is not None and eacol is not None:
		tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".rsID.gz"
		assigned_rsID_from_the_selected_reference_panel(tbfile, chrom, start ,end, snps)

	# when one of the alleles need to be extracted, get from the selected population
	else:
		extract_allele_from_the_selected_population()

	return


def assigned_rsID_from_the_selected_reference_panel(tbfile, chrom, start ,end, snps):
	# assigned rsID from the selected reference panel
	# if rsID is not available, replace with uniqID
	# eacol and neacol are not None, rsID is None
	tb = tabix.open(tbfile)
	refSNP = []
	for l in tb.querys(str(chrom)+":"+str(start)+"-"+str(end)):
		refSNP.append(l)
	if len(refSNP)>0:
		refSNP = np.array(refSNP)
		poss = set(refSNP[:,1].astype(int))
		pos = refSNP[:,1].astype(int)
		for l in snps:
			uid = ":".join([l[chrcol], l[poscol]]+sorted([l[neacol].upper(), l[eacol].upper()]))
			if int(l[poscol]) in poss:
				j = bisect_left(pos, int(l[poscol]))
				while refSNP[j,1] == int(l[poscol]):
					if uid == refSNP[j,2]: break
					j += 1
				if uid == refSNP[j,2]:
					out.write("\t".join([refSNP[j,0], refSNP[j,1], l[neacol].upper(), l[eacol].upper(), refSNP[j,3], l[pcol]]))
				else:
					out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
			else:
				out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
	else:
		for l in snps:
			uid = ":".join([l[chrcol], l[poscol]]+sorted([l[neacol].upper(), l[eacol].upper()]))
			out.write("\t".join([l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]))
	return

def extract_allele_from_the_selected_population():
	# one of eacol and neacol is None or 
	# both eacol and neacol are None
	# rsID could be None or not None (we don't know)

	tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".frq.gz"
	tb = tabix.open(tbfile)
	temp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
	for l in temp:
		if int(l[1]) in poss:
			j = bisect_left(pos, int(l[1]))
			if snps[j,pcol] is None:
				continue
			if eacol is not None:
				if snps[j,eacol].upper()==l[3] or snps[j,eacol].upper()==l[4]:
					a = "NA"
					if snps[j,eacol]==l[3]:
						a = l[4]
					else:
						a = l[3]
					if rsIDcol is None:
						out.write("\t".join([l[0],l[1], a, snps[j,eacol].upper(), l[2], snps[j,pcol]]))
					else:
						out.write("\t".join([l[0],l[1], a, snps[j,eacol].upper(), snps[j,rsIDcol], snps[j,pcol]]))
			else:
				if rsIDcol is None:
					out.write("\t".join([l[0],l[1], l[3], l[4], l[2], snps[j,pcol]]))
				else:
					out.write("\t".join([l[0],l[1], l[3], l[4], snps[j,rsIDcol], snps[j,pcol]]))
	return
