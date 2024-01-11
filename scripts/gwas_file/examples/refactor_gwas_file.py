#!/usr/bin/python
# refactoring gwas_file.py
# still in python2 (because of the tabix library)
from __future__ import print_function
import sys
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


def detect_delim(header):
    """
    Detect file delimiter from the header
    """
    if re.match(r'.*\s\s.*', header) is not None:
        return '\s+'
    sniffer = csv.Sniffer()
    dialect = sniffer.sniff(header)
    return dialect.delimiter


def float_safe(s):
    """
    Return the float of a string if true, otherwise return empty
    """
    try:
        return float(s)
    except ValueError:
        return


def write_col_header(orcol_idx, becol_idx, secol_idx, Ncol_idx):
    """
    Return a list of or, beta, se, and N if any of these are present
    This function is used for formatting the header
    """
    out_list = []
    if str(orcol_idx):
        out_list.append("or")
    if str(becol_idx):
        out_list.append("beta")
    if str(secol_idx):
        out_list.append("se")
    if str(Ncol_idx):
        out_list.append("N")
    return out_list


def input_sanitation(line, delim, nheader, chrcol_idx, poscol_idx, pcol_idx, rmSNPs, rsIDcol_idx, header):
    """
    Performs input sanitation per line. The line is from the input gwas.
    rmSNPS is the file storing the lines that are removed.
    """
    sanitized_line = []
    if line.startswith("#"):
        rmSNPs.write(line.rstrip("\n") + "| Ignore: line starts with #.\n")
    else:
        items = line.strip().split(delim)
        if len(items) < nheader:
            rmSNPs.write(line.rstrip("\n") + "| Ignore: Number of columns in this line is less than the number of columns in the header.\n")
        else:
            if items[pcol_idx].upper() == header[pcol_idx]:
                rmSNPs.write(line.rstrip("\n") + "| Ignore: This line is mostly a header.\n")
                sanitized_line = None
            elif not float_safe(items[pcol_idx]):
                rmSNPs.write(line.rstrip("\n") + "| Ignore: String in the p value column. Number is expected.\n")
                sanitized_line = None
            elif float(items[pcol_idx]) < 0 or float(items[pcol_idx]) > 1:
                rmSNPs.write(line.rstrip("\n") + "| Ignore: P value out of range (<0 or >1).\n")
                sanitized_line = None
            elif float(items[pcol_idx]) == 0 and re.match("^0", items[pcol_idx]):
                rmSNPs.write(line.rstrip("\n") + "| Ignore: P value is 0.\n")
                sanitized_line = None
            else:
                sanitized_line = items
            if sanitized_line is not None:
                if str(chrcol_idx):
                    items[chrcol_idx] = items[chrcol_idx].replace("chr", "").replace("CHR", "") #strip chr
                    if re.match("x", items[chrcol_idx], re.IGNORECASE): #convert x to 23
                        items[chrcol_idx] = '23'
                    if not items[chrcol_idx].isdigit(): #check if chrcol is a digit
                        rmSNPs.write(
                        line.rstrip("\n") + "| Ignore: Value for the chromosome column is not a digit.\n")
                        sanitized_line = None
                    elif int(items[chrcol_idx]) not in range(1, 24):
                        rmSNPs.write(
                        line.rstrip("\n") + "| Ignore: Value for the chromosome column is not between 1 and 23.\n")
                        sanitized_line = None
                if sanitized_line is not None:
                    if str(poscol_idx):
                        if not items[poscol_idx].isdigit():
                            rmSNPs.write(
                                    line.rstrip("\n") + "| Ignore: Value for the position column is not a digit.\n")
                            sanitized_line = None
                    if sanitized_line is not None:
                        if str(rsIDcol_idx):
                            if not items[rsIDcol_idx].startswith("rs"):
                                if ":" in items[rsIDcol_idx]:
                                    print(line.rstrip("\n") + "| WARNING: the rsid column does not start with rs. The colon (:) is detected so it seems to be in the UID format (chr:pos or chr:pos:a1:a2). This might be a problem with database look up.")
                                    sanitized_line = items
                                else:
                                    rmSNPs.write(
                                        line.rstrip("\n") + "| Ignore: In the rsID column, rsID does not seem to be in the correct format\n")
                                    sanitized_line = None
                            else:
                                sanitized_line = items
    return sanitized_line


def get_user_params(param):
    """returns a dictionary where:
    key: user defined value
    value: name of the column such as chrcol, rsIDcol, poscol, eacol, neacol, etc..."""
    out_dict = {}
    columns = ["chrcol", "poscol", "neacol", "eacol", "rsIDcol", "pcol", "orcol", "becol",
               "secol"]  # these are the columns under [inputfiles] while Ncol is under [params]
    for i in columns:
        out_dict[i] = param.get('inputfiles', i).upper()
    out_dict["Ncol"] = param.get('params', 'Ncol').upper()
    out_dict_inverse = {v: k for (k, v) in out_dict.items() if v != "NA"}
    return out_dict_inverse


def resolve_default_names(col_name):
    defaults = {
        "CHR": "chrcol",
        "CHROMOSOME": "chrcol",
        "CHROM": "chrcol",
        "SNP": "rsIDcol",
        "MARKERNAME": "rsIDcol",
        "RSID": "rsIDcol",
        "SNPID": "rsIDcol",
        "BP": "poscol",
        "POS": "poscol",
        "POSITION": "poscol",
        "A1": "eacol",
        "EFFECT_ALLELE": "eacol",
        "ALLELE1": "eacol",
        "ALLELEB": "eacol",
        "A2": "neacol",
        "NON_EFFECT_ALLELE": "neacol",
        "ALLELE2": "neacol",
        "ALLELEA": "neacol",
        "P": "pcol",
        "PVAL": "pcol",
        "PVALUE": "pcol",
        "P-VALUE": "pcol",
        "P_VALUE": "pcol",
        "P.VALUE": "pcol",
        "OR": "orcol",
        "BETA": "becol",
        "BE": "becol",
        "SE": "secol",
        "N": "Ncol"
    }
    return defaults.get(col_name, "NA")


def check_fmt_predefined_files(file):
    """Check format of the lead file and the region file
    """
    tmp = pd.read_csv(file, delim_whitespace=True)
    tmp = tmp.as_matrix()
    if len(tmp) == 0 or len(tmp[0]) < 3:
        return "Not enough columns"


def write_col(l, orcol_idx, becol_idx, secol_idx, Ncol_idx):
    """
    Return the list of values for orcol, becol, secol, and Ncol if any of these exists
    """
    out_list = []
    if str(orcol_idx):
        out_list.append(l[orcol_idx])
    if str(becol_idx):
        out_list.append(l[becol_idx])
    if str(secol_idx):
        out_list.append(l[secol_idx])
    if str(Ncol_idx):
        out_list.append(l[Ncol_idx])
    return out_list


def database_lookup(items, snpd, chrcol_idx, poscol_idx, neacol_idx, eacol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx):
    """This function replaces Tabix function"""
    uid = ":".join(
        [items[chrcol_idx], items[poscol_idx]] + sorted([items[neacol_idx].upper(), items[eacol_idx].upper()]))
    id_val = snpd.get(uid, uid)
    updated_out = [items[chrcol_idx], items[poscol_idx], items[neacol_idx].upper(), items[eacol_idx].upper(), id_val, items[pcol_idx]] + write_col(items, orcol_idx, becol_idx, secol_idx, Ncol_idx)
    return updated_out


def run_database_lookup(tb, chrom, start, end, snps, chrcol_idx, poscol_idx, neacol_idx, eacol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, out):
    """ This function runs database look up """
    refSNP = np.array(list(tb.querys(str(chrom) + ":" + str(start) + "-" + str(end))))
    snps = np.array(snps)
    snpd = {refSNP[i, 2]: refSNP[i, 3] for i in range(refSNP.shape[0])}
    rsid_lookup = list(map(lambda l: database_lookup(items=l, snpd=snpd, chrcol_idx=chrcol_idx,
                                                     poscol_idx=poscol_idx, neacol_idx=neacol_idx,
                                                     eacol_idx=eacol_idx, pcol_idx=pcol_idx,
                                                     orcol_idx=orcol_idx, becol_idx=becol_idx,
                                                     secol_idx=secol_idx, Ncol_idx=Ncol_idx), snps))
    out.write('\n'.join(['\t'.join(s) for s in rsid_lookup]) + '\n')

def extract_alleles(tb, chrom, start, end, snps, out, poscol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, eacol_idx, rsIDcol_idx):
    snps = np.array(snps)
    poss = set(snps[:, poscol_idx].astype(int))
    pos = snps[:, poscol_idx].astype(int)
    query = tb.querys(str(chrom) + ":" + str(start) + "-" + str(end))
    for l in query:
        if int(l[1]) in poss:
            j = bisect_left(pos, int(l[1]))
            if eacol_idx:
                if snps[j, eacol_idx].upper() == l[3] or snps[j, eacol_idx].upper() == l[4]:
                    a = "NA"
                    if snps[j, eacol_idx] == l[3]:
                        a = l[4]
                    else:
                        a = l[3]
                    if not str(rsIDcol_idx):
                        print("INFO: No column for rsID, extracting from the database.")
                        updated_out = [l[0], l[1], a, snps[j, eacol_idx].upper(), l[2], snps[j, pcol_idx]] + write_col(snps[j], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                        out.write("\t".join(updated_out) + '\n')
                    else:
                        print("INFO: Column for rsID found. Use the user's rsID.")
                        updated_out = [l[0], l[1], a, snps[j, eacol_idx].upper(), snps[j, rsIDcol_idx], snps[j, pcol_idx]] + write_col(snps[j], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                        out.write("\t".join(updated_out) + '\n')
            else:
                if not str(rsIDcol_idx):
                    print("INFO: No column for rsID, extracting from the database.")
                    updated_out = [l[0], l[1], l[3], l[4], l[2], snps[j, pcol_idx]] + write_col(
                        snps[j], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                    out.write("\t".join(updated_out) + '\n')
                else:
                    print("INFO: Column for rsID found. Use the user's rsID.")
                    updated_out = [l[0], l[1], l[3], l[4], snps[j, rsIDcol_idx],
                                   snps[j, pcol_idx]] + write_col(snps[j], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                    out.write("\t".join(updated_out) + '\n')


def main():
    ##### check argument #####
    print("INFO: Checking argument.")
    if len(sys.argv) < 2:
        sys.exit('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>')

    ##### start time #####
    start_time = time.time()

    filedir = sys.argv[1] #parse input

    ##### config variables #####
    print("INFO: Parsing config variables.")
    cfg = configparser.ConfigParser()
    cfg.read(os.path.dirname(os.path.realpath(__file__)) + '/app.config')

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))

    ##### check format of pre-defined lead SNPS and genomic regions if provided #####
    leadfile = param.get('inputfiles', 'leadSNPsfile')
    regionfile = param.get('inputfiles', 'regionsfile')
    if leadfile != "NA":
        print("INFO: Lead SNPs file exists. Checking format.")
        leadfile = os.path.join(filedir, "input.lead")
        if check_fmt_predefined_files(leadfile) == "Not enough columns":
            sys.exit("ERROR: Input lead SNPs file does not have enough columns.")

    if regionfile != "NA":
        print("INFO: Genomic region file exists. Checking format.")
        regionfile = os.path.join(filedir, "input.regions")
        if check_fmt_predefined_files(regionfile) == "Not enough columns":
            sys.exit("ERROR: Input genomic region file does not have enough columns.")

    ##### prepare parameters #####
    gwas = os.path.join(filedir, cfg.get('inputfiles', 'gwas'))
    outSNPs = os.path.join(filedir, "input.snps")
    rmSNPs = os.path.join(filedir, "input.snps.rm")

    ##### get header of sum stats #####
    print("INFO: Parsing header.")
    fin = open(gwas, 'r')
    header = fin.readline()
    while re.match("^#", header):
        header = fin.readline()
    fin.close()
    print("INFO: FUMA detects this to be your header: ", header.rstrip("\n"))
    delim = detect_delim(header)
    header_original = re.split(delim, header.strip())
    header = [x.upper() for x in header_original]
    nheader = len(header)
    print("INFO: Number of columns in header: ", nheader)

    ##### detect column index #####
    # prioritize user defined colum name
    # then automatic detection
    user_defined_col = get_user_params(
        param)  # this is a dict where key is the user defined value and value is the name of the col such as chrcol, rsIDcol, poscol, eacol, neacol, etc...
    print("INFO: User defined columns: ", user_defined_col)

    user_header = user_defined_col.keys()
    bad_user_defined_col = list(set(user_header).difference(header))
    if bad_user_defined_col:
        bad_user_defined_col_str = ", ".join(bad_user_defined_col)
        sys.exit("ERROR: The following header(s) was not detected in your input file: " + bad_user_defined_col_str) #when the users specify a column name and if it does not exist in the actual header, throw an error. For example, if they put in `my_pval` for the P-value column but this name does not actually exist in the detected header, then it will throw an error.

    print("INFO: Resolving column names.")
    col_idx_dict_tmp = {user_defined_col.get(col, resolve_default_names(col)): idx for (idx, col) in enumerate(header)} #col is the value in the header. If it's in defined by the user, it will be obtained by the command user_defined_col.get(col). Otherwise, we will try to resolve it by the default names.
    col_idx_dict = {k: col_idx_dict_tmp[k] for k in col_idx_dict_tmp if k != "NA"}
    # col_idx_dict
    # {'rsIDcol': 0, 'chrcol': 1, 'poscol': 2, 'eacol': 3, 'orcol': 6, 'secol': 7, 'pcol': 11, 'neacol': 12}

    chrcol_idx = col_idx_dict.get("chrcol", "")
    poscol_idx = col_idx_dict.get("poscol", "")
    rsIDcol_idx = col_idx_dict.get("rsIDcol", "")
    eacol_idx = col_idx_dict.get("eacol", "")
    neacol_idx = col_idx_dict.get("neacol", "")
    orcol_idx = col_idx_dict.get("orcol", "")
    becol_idx = col_idx_dict.get("becol", "")
    secol_idx = col_idx_dict.get("secol", "")
    Ncol_idx = col_idx_dict.get("Ncol", "")
    pcol_idx = col_idx_dict.get("pcol", "")
    print("INFO: FUMA will use the following indices:")
    colname_indices = {"chromosome": chrcol_idx, "position": poscol_idx, "rsID": rsIDcol_idx, "effect_allele": eacol_idx, "non_effect_allele": neacol_idx, "or": orcol_idx, "be": becol_idx, "se": secol_idx, "Ncol": Ncol_idx, "pval": pcol_idx}
    for k, v in colname_indices.items():
        print("INFO: Index for ", k,  "column: ", str(v))

    ##### allele column check #####
    # if only one allele is defined, this has to be alt (effect) allele
    if neacol_idx and not eacol_idx:
        eacol_idx = neacol_idx
        neacol_idx = ""
        print("WARNING: the input file contains only one column for allele, FUMA will proceed with assumption that this is the effect allele.")

    ##### Mandatory header check #####
    if not str(pcol_idx): #convert to str because python interprets bool(0) as false
        sys.exit("ERROR: P-value column was not found. A column for P-value is mandatory.")
    if not str(chrcol_idx) and not str(rsIDcol_idx):
        sys.exit("ERROR: Both chromosome and rsID column was not found.\n FUMA requires chr and pos OR rsID.")
    if not str(poscol_idx) and not str(rsIDcol_idx):
        sys.exit("ERROR: Both position and rsID column was not found.\n FUMA requires chr and pos OR rsID.")

    ##### Rewrite params.config if optional headers were detected #####
    if param.get('inputfiles', 'orcol') == "NA" and orcol_idx:
        param.set('inputfiles', 'orcol', 'or')
    if param.get('inputfiles', 'becol') == "NA" and becol_idx:
        param.set('inputfiles', 'becol', 'beta')
    if param.get('inputfiles', 'secol') == "NA" and secol_idx:
        param.set('inputfiles', 'secol', 'se')

    paramout = open(filedir + "params.config", 'w+')
    param.write(paramout)
    paramout.close()

    # sanitize inputs
    print("INFO: Sanitize input.")
    gwas_sanitized_path = os.path.join(filedir, "input.gwas.sanitized")
    gwas_sanitized = open(gwas_sanitized_path, "w")
    print("\t".join(header_original), file=gwas_sanitized)
    rmSNPs = open(rmSNPs, "w")
    with open(gwas, "r") as gwasIn:
        new_line = list(map(lambda l: input_sanitation(line=l, delim=delim, nheader=nheader, chrcol_idx=chrcol_idx, poscol_idx=poscol_idx, rsIDcol_idx=rsIDcol_idx, pcol_idx=pcol_idx, rmSNPs=rmSNPs, header=header), gwasIn))
        for i in new_line:
            if i:
                print("\t".join(i), file=gwas_sanitized)
    gwas_sanitized.close()


    ##### sort input sum stats #####
    # input.gwas.sanitized will be overwritten
    if str(chrcol_idx) and str(poscol_idx):
        # sorting by chromosome and position
        print("INFO: Both chr and pos are present. input gwas is being sorted by chr and pos")
        tempfile = os.path.join(filedir, "input.gwas.sanitized.temp.txt")
        os.system("sort -k 1n -k 2n input.gwas.sanitized" + " > " + tempfile)
        os.system("mv " + tempfile + " " + "input.gwas.sanitized")

    ##### Process input file #####
    print("INFO: Processing the input file.")
    if str(chrcol_idx) and str(poscol_idx) and str(rsIDcol_idx) and str(eacol_idx) and str(neacol_idx):
        print("INFO: Chromosome, position, rsID, effect allele, and noneffect allele columns are all present.")

        out = open(outSNPs, "w")
        # format header
        header = ["chr", "bp", "non_effect_allele", "effect_allele", "rsID", "p"] + write_col_header(orcol_idx, becol_idx, secol_idx, Ncol_idx)
        out.write('\t'.join(header) + "\n")

        with open(gwas_sanitized_path, "r") as f:
            for line in f:
                if "chr" not in line:
                    s = line.rstrip("\n").split("\t")
                    updated_line =  [s[chrcol_idx], s[poscol_idx], s[neacol_idx].upper(), s[eacol_idx].upper(), s[rsIDcol_idx], s[pcol_idx]] + write_col(s, orcol_idx, becol_idx, secol_idx, Ncol_idx)
                    out.write('\t'.join(updated_line) + "\n")
        out.close()
        rmSNPs.close()

    elif str(chrcol_idx) and str(poscol_idx):
        print("INFO: Chromosome column and position columns are detected.")
        out = open(outSNPs, "w")

        # format header
        header = ["chr", "bp", "non_effect_allele", "effect_allele", "rsID", "p"] + write_col_header(orcol_idx, becol_idx, secol_idx, Ncol_idx)
        out.write('\t'.join(header) + "\n")

        refpanel = cfg.get('data', 'refgenome') + "/" + param.get('params', 'refpanel')
        pop = param.get('params', 'pop')

        with open(gwas_sanitized_path, "r") as gwasIn:
            if str(neacol_idx) and str(eacol_idx):
                print("INFO: Both column for effect allele and non-effect allele are present. \n Looking up rsID from database.")
                cur_chr = 1
                minpos = 0
                maxpos = 0
                temp = []
                for line in gwasIn:
                    if "chr" not in line:
                        s = line.rstrip("\n").split("\t")
                        if int(s[chrcol_idx]) == cur_chr:  # if it's the current chromosome
                            if minpos == 0:
                                minpos = int(s[poscol_idx])
                            if int(s[poscol_idx]) - minpos <= 1000000 and int(s[poscol_idx]) - minpos >= 0:
                                maxpos = int(s[poscol_idx])
                                temp.append(s)
                            else:
                                tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".rsID.gz")
                                run_database_lookup(tb, cur_chr, minpos, maxpos, temp, chrcol_idx, poscol_idx, neacol_idx, eacol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, out)
                                minpos = int(s[poscol_idx])
                                maxpos = int(s[poscol_idx])
                                temp = [s]
                        else:
                            if minpos != 0 and maxpos != 0:
                                tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".rsID.gz")
                                run_database_lookup(tb, cur_chr, minpos, maxpos, temp, chrcol_idx, poscol_idx, neacol_idx, eacol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, out)
                                cur_chr = int(s[chrcol_idx])
                                minpos = int(s[poscol_idx])
                                maxpos = int(s[poscol_idx])
                                temp = [s]
                if len(temp) > 0:
                    tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".rsID.gz")
                    run_database_lookup(tb, cur_chr, minpos, maxpos, temp, chrcol_idx, poscol_idx, neacol_idx, eacol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, out)
            else:
                print("INFO: Either the column for effect allele or non-effect allele is missing. Extract from the database")
                cur_chr = 1
                minpos = 0
                maxpos = 0
                temp = []
                for line in gwasIn:
                    if "chr" not in line:
                        s = line.rstrip("\n").split("\t")
                        if int(s[chrcol_idx]) == cur_chr:  # if it's the current chromosome
                            print("INFO: Processing: ", cur_chr)
                            if minpos == 0:
                                print("INFO: Updating minpos")
                                minpos = int(s[poscol_idx])
                            if int(s[poscol_idx]) - minpos <= 1000000 and int(s[poscol_idx]) - minpos >= 0:
                                maxpos = int(s[poscol_idx])
                                print("INFO: Updating maxpos. Go to the next line.")
                                temp.append(s)
                            else:
                                print("INFO: Extract alleles now.", cur_chr, minpos, maxpos)
                                tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".frq.gz")
                                extract_alleles(tb, cur_chr, minpos, maxpos, temp, out, poscol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, eacol_idx, rsIDcol_idx)
                                minpos = int(s[poscol_idx])
                                maxpos = int(s[poscol_idx])
                                temp = [s]
                        else:
                            print("INFO: Chromosome is changing. Extract allele & reset.", cur_chr, minpos, maxpos)
                            if minpos != 0 and maxpos != 0:
                                tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".frq.gz")
                                extract_alleles(tb, cur_chr, minpos, maxpos, temp, out, poscol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, eacol_idx, rsIDcol_idx)
                                cur_chr = int(s[chrcol_idx])
                                minpos = int(s[poscol_idx])
                                maxpos = int(s[poscol_idx])
                                temp = [s]
                if len(temp) > 0:
                    print("INFO: Run the left over.", cur_chr, minpos, maxpos)
                    tb = tabix.open(refpanel + "/" + pop + "/" + pop + ".chr" + str(cur_chr) + ".frq.gz")
                    extract_alleles(tb, cur_chr, minpos, maxpos, temp, out, poscol_idx, pcol_idx, orcol_idx, becol_idx, secol_idx, Ncol_idx, eacol_idx, rsIDcol_idx)
        out.close()
        rmSNPs.close()

    elif not chrcol_idx or not poscol_idx:
        print("INFO: Either chr or pos is not provided.")
        out = open(outSNPs, "w")

        # format header
        header = ["chr", "bp", "non_effect_allele", "effect_allele", "rsID", "p"] + write_col_header(orcol_idx, becol_idx, secol_idx, Ncol_idx)
        out.write('\t'.join(header) + "\n")


        ##### read input file #####
        gwas = pd.read_csv(gwas_sanitized_path, comment="#", sep=delim, dtype=str)
        print("INFO: After sanitizing input gwas, there are ", str(gwas.shape[0]), " variants left.")
        if gwas.shape[0] == 0:
            sys.exit("There are no variants left after sanitizing input gwas.")
        else:
            gwas = gwas.as_matrix()
            gwas = gwas[gwas[:, rsIDcol_idx].argsort()]

            ##### update rsID to dbSNP 146 #####
            rsID = list(gwas[:, rsIDcol_idx])
            rsIDs = set(rsID)
            dbSNPfile = cfg.get('data', 'dbSNP')
            rsID146 = open(dbSNPfile + "/RsMerge146.txt", 'r')
            for l in rsID146:
                l = l.strip().split()
                if l[0] in rsIDs:
                    j = bisect_left(rsID, l[0])
                    gwas[j, rsIDcol_idx] = l[1]
            rsID146.close()

            ##### sort input snps by rsID for bisect_left #####
            gwas = gwas[gwas[:, rsIDcol_idx].argsort()]
            rsID = list(gwas[:, rsIDcol_idx])
            rsIDs = set(rsID)
            checked = []

            ##### process per chromosome #####
            for chrom in range(1, 24):
                print("start chr" + str(chrom))
                for chunk in pd.read_csv(dbSNPfile + "/dbSNP146.chr" + str(chrom) + ".vcf.gz", header=None, sep="\t", dtype=str, chunksize=10000):
                    chunk = np.array(chunk)
                    for l in chunk:
                        alt = l[4].split(",")
                        if l[2] in rsIDs:
                            checked.append(l[2])
                            j = bisect_left(rsID, l[2])
                            if eacol_idx and neacol_idx:
                                if (gwas[j, eacol_idx].upper() == l[3] and gwas[j, neacol_idx].upper() in alt) or gwas[j, eacol_idx].upper() in alt and gwas[j, neacol_idx].upper() == l[3]:
                                    new_line = [str(chrom), str(l[1]), gwas[j,neacol_idx].upper(), gwas[j,eacol_idx].upper(), l[2], str(gwas[j,pcol_idx])] + write_col(gwas[j,], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                                    out.write('\t'.join(new_line) + "\n")
                            elif eacol_idx:
                                if gwas[j,eacol_idx].upper()==l[3] or gwas[j,eacol_idx].upper() in alt:
                                    if len(alt) > 1:
                                        continue
                                    if gwas[j, eacol_idx].upper() == l[3]:
                                        a = l[4]
                                    else:
                                        a = l[3]
                                    new_line = [str(chrom), str(l[1]), a, gwas[j,eacol_idx].upper(), l[2], str(gwas[j,pcol_idx])] + write_col(gwas[j,], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                                    out.write('\t'.join(new_line) + "\n")
                            else:
                                if len(alt) > 1:
                                    continue
                                new_line = [str(chrom), str(l[1]), l[3], l[4], l[2], str(gwas[j, pcol_idx])] + write_col(gwas[j,], orcol_idx, becol_idx, secol_idx, Ncol_idx)
                                out.write('\t'.join(new_line) + "\n")

                if len(gwas) == len(checked):
                    break
            out.close()

    ##### check output file #####

    print("INFO: Sorting by chromosome and position.")
    # sorting by chromosome and position
    tempfile = os.path.join(filedir, "temp.txt")
    os.system("sort -k 1n -k 2n " + outSNPs + " > " + tempfile)
    os.system("mv " + tempfile + " " + outSNPs)

    out_fp = os.path.join(filedir, "input.snps")
    wc = int(subprocess.check_output("wc -l " + out_fp, shell=True).split()[0])
    print("The number of lines in the file input.snps is: ", wc)
    if wc < 2:
        sys.exit("ERROR: There was no SNPs remained after formatting the input summary statistics.")

    ##### total time #####
    print(time.time() - start_time)


if __name__ == '__main__':
    main()
