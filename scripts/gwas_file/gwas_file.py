#!/usr/bin/python
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
import logging
import shutil

##### detect file delimiter from the header #####
def DetectDelim(header):
    if re.match(r'.*\s\s.*', header) is not None:
        return r'\s+'
    sniffer = csv.Sniffer()
    dialect = sniffer.sniff(header)
    return dialect.delimiter

##### check float #####
def is_float(s):
    try:
        float(s)
        return True
    except ValueError:
        return False

##### check argument #####
if len(sys.argv)<2:
    sys.exit('ERROR: not enough arguments\nUSAGE ./gwas_file.py <filedir>')

##### start time #####
start = time.time()


filedir = sys.argv[1]
 
# Setting up the log file
logging.basicConfig(
    filename=os.path.join(filedir, "user.log"),
    filemode="a",
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
)
logger = logging.getLogger(__name__)

##### config variables #####
cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = configparser.RawConfigParser()
param.optionxform = str
param.read(os.path.join(filedir, 'params.config'))

##### check format of pre-defined lead SNPS and genomic regions if provided #####
leadfile = param.get('inputfiles', 'leadSNPsfile')
regionfile = param.get('inputfiles', 'regionsfile')
if leadfile != "NA":
    leadfile = pd.read_csv(os.path.join(filedir, "input.lead"), comment="#", sep=r"\s+", dtype=str)
    if leadfile.shape[0] == 0:
        logger.error("You submitted a predefined lead SNPs file. However, there is no data in this file.")
        sys.exit("Input lead SNPs file does not have any data.")
    number_cols = len(leadfile.columns)
    if number_cols == 1: 
        logger.info("There is only 1 column detected in your predefined lead SNPs file. FUMA assumes that this column is the rsID. chrosome and position in GRCh37 will be extracted from dbSNP v146.")
        lead_snps = leadfile.to_numpy()
        lead_snps = lead_snps[lead_snps[:,0].argsort()]

        ##### write header of input.snps #####
        out = open(os.path.join(filedir, "input.lead"), 'w')
        print("\t".join(["rsID", "chr", "pos"]), file=out)

        ##### update rsID to dbSNP 146 #####
        rsIDs = set(list(lead_snps[:, 0]))
        rsID = list(lead_snps[:, 0])
        dbSNPfile = cfg.get('data', 'dbSNP')
        rsID146 = open(dbSNPfile+"/RsMerge146.txt", 'r')
        for l in rsID146:
            l = l.strip().split()
            if l[0] in rsIDs:
                j = bisect_left(rsID, l[0])
                lead_snps[j,0] = l[1]
        rsID146.close()

        ##### sort input snps by rsID for bisect_left #####
        lead_snps = lead_snps[lead_snps[:,0].argsort()]
        rsIDs = set(list(lead_snps[:, 0]))
        rsID = list(lead_snps[:, 0])
        checked = []

        ##### process per chromosome #####
        for chrom in range(1,24):
            print("start chr"+str(chrom))
            for chunk in pd.read_csv(dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz", header=None, sep="\t", dtype=str, chunksize=10000):
                chunk = np.array(chunk)
                for l in chunk:
                    if l[2] in rsIDs:
                        checked.append(l[2])
                        print("\t".join([l[2], str(chrom), str(l[1])]), file=out)

            if len(lead_snps)==len(checked):
                break
        out.close()
    elif number_cols == 3:
        logger.info("3 columns were detected in your predefined lead SNPs file. FUMA assumes that the columns are in this order: rsID, chr, pos (GRCh37)")
    else: 
        logger.error("You submitted a predefined lead SNPs file. FUMA expects this file to have either (1) a single rsID column or (2) 3 colums of rsID, chromosome, and position in GRCh37. Your file has an incorrect number of columns.")
        sys.exit("Input lead SNPs file does not have enough columns.")
    
    # tmp = pd.read_csv(leadfile, sep=r"\s+")
    # tmp = tmp.to_numpy()
    # if len(tmp)==0 or len(tmp[0])<3:
    #     logger.error("Input lead SNPs file does not have enough columns.")
    #     sys.exit("Input lead SNPs file does not have enough columns.")

if regionfile != "NA":
    regionfile = os.path.join(filedir, "input.regions")
    tmp = pd.read_csv(regionfile, sep=r"\s+")
    tmp = tmp.to_numpy()
    if len(tmp)==0 or len(tmp[0])<3:
        logger.error("Input genomic region file does not have enough columns.")
        sys.exit("Input genomic region file does not have enough columns.")

##### prepare parameters #####
gwas = os.path.join(filedir, cfg.get('inputfiles', 'gwas'))
outSNPs = os.path.join(filedir, "input.snps")

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
N = param.get('params', 'N')

users_specified = {'chrcol': chrcol, 'poscol': poscol, 'rsIDcol': rsIDcol, 'pcol': pcol, 'neacol': neacol, 'eacol': eacol, 'orcol': orcol, 'becol': becol, 'secol': secol, 'Ncol': Ncol, 'N': N}
for k, v in users_specified.items():
    if users_specified[k] != "NA":
        logger.info(f"User input for {k}: {v}")

##### get header of sum stats #####
fin = open(gwas, 'r')
header = fin.readline()
while re.match("^#", header):
    header = fin.readline()
fin.close()
delim = DetectDelim(header)
header = re.split(delim, header.strip())
nheader = len(header)

logger.info(f"Input file header: {header}")

##### detect column index #####
# prioritize user defined colum name
# then automatic detection
checkedheader = []
for i in range(0,len(header)):
    if chrcol == header[i].upper():
        chrcol = i
        checkedheader.append(chrcol)
    elif rsIDcol == header[i].upper():
        rsIDcol = i
        checkedheader.append(rsIDcol)
    elif poscol == header[i].upper():
        poscol = i
        checkedheader.append(poscol)
    elif eacol == header[i].upper():
        eacol = i
        checkedheader.append(eacol)
    elif neacol == header[i].upper():
        neacol = i
        checkedheader.append(neacol)
    elif pcol == header[i].upper():
        pcol = i
        checkedheader.append(pcol)
    elif orcol == header[i].upper():
        orcol = i
        checkedheader.append(orcol)
    elif becol == header[i].upper():
        becol = i
        checkedheader.append(becol)
    elif secol == header[i].upper():
        secol = i
        checkedheader.append(secol)
    elif Ncol == header[i].upper():
        Ncol = i
        checkedheader.append(Ncol)
for i in range(0, len(header)):
    if i in checkedheader:
        continue
    if chrcol == "NA" and re.match("CHR$|^chromosome$|^chrom$", header[i], re.IGNORECASE):
        chrcol = i
    elif rsIDcol == "NA" and re.match("SNP$|^MarkerName$|^rsID$|^snpid$", header[i], re.IGNORECASE):
        rsIDcol = i
    elif poscol == "NA" and re.match("^BP$|^pos$|^position$|^base_pair_location$", header[i], re.IGNORECASE):
        poscol = i
    elif eacol == "NA" and re.match("^A1$|^Effect_allele$|^allele1$|^alleleB$", header[i], re.IGNORECASE):
        eacol = i
    elif neacol == "NA" and re.match("^A2$|^Non_Effect_allele$|^allele2$|^alleleA$", header[i], re.IGNORECASE):
        neacol = i
    elif pcol == "NA" and re.match("^P$|^pval$|^pvalue$|^p-value$|^p_value$", header[i], re.IGNORECASE):
        pcol = i
    elif orcol == "NA" and re.match("^or$", header[i], re.IGNORECASE):
        orcol = i
    elif becol == "NA" and re.match("^beta$", header[i], re.IGNORECASE):
        becol = i
    elif secol == "NA" and re.match("^se$", header[i], re.IGNORECASE):
        secol = i
    elif Ncol == "NA" and N=="NA" and re.match("^N$", header[i], re.IGNORECASE):
        Ncol = i

user_header = []
if chrcol=="NA":
    chrcol = None
else:
    user_header.append(chrcol)
if rsIDcol=="NA":
    rsIDcol = None
else:
    user_header.append(rsIDcol)
if poscol=="NA":
    poscol = None
else:
    user_header.append(poscol)
if neacol=="NA":
    neacol = None
else:
    user_header.append(neacol)
if eacol=="NA":
    eacol = None
else:
    user_header.append(eacol)
if pcol=="NA":
    pcol = None
else:
    user_header.append(pcol)
if orcol=="NA":
    orcol = None
else:
    user_header.append(orcol)
if secol=="NA":
    secol = None
else:
    user_header.append(secol)
if becol=="NA":
    becol = None
else:
    user_header.append(becol)
if Ncol=="NA":
    Ncol = None
else:
    user_header.append(Ncol)

##### Undetected header #####
# return error only if any of the user input colum names does not exits and not automatically detected
if not all([type(x) is int for x in user_header]):
    bl = [type(x) is not int for x in user_header]
    user_header = ", ".join([user_header[i] for i,x in enumerate(bl) if x])
    logger.error("The following header(s) was not detected in the input file: "+user_header)
    sys.exit("The following header(s) was not detected in your input file: "+user_header)

##### allele column check #####
# if only one allele is defined, this has to be alt (effect) allele
if neacol is not None and eacol is None:
    eacol = neacol
    neacol = None
    logger.warning("Non-effect allele column was detected but effect allele column was not detected. The detected non-effect allele column will be treated as effect allele column and non-effect allele will be set to NA.")

##### Mandatory header check #####
if pcol is None:
    logger.error("P-value column was not found")
    sys.exit("P-value column was not found")
if (chrcol is None or poscol is None) and rsIDcol is None:
    logger.error("Chromosome, position or rsID column was not found")
    sys.exit("Chromosome, position or rsID column was not found")

##### Rewrite params.config if optional headers were detected #####
if param.get('inputfiles', 'orcol')=="NA" and orcol is not None:
    param.set('inputfiles', 'orcol', 'or')
if param.get('inputfiles', 'becol')=="NA" and becol is not None:
    param.set('inputfiles', 'becol', 'beta')
if param.get('inputfiles', 'secol')=="NA" and secol is not None:
    param.set('inputfiles', 'secol', 'se')

paramout = open(os.path.join(filedir, "params.config"), 'w+')
param.write(paramout)
paramout.close()

def log_skip(reason, line):
    logger.warning(f"SKIPPED ({reason}): {line}")
    
def write_row(out, values, snp_row):
    out.write("\t".join(values))
    if orcol is not None:
        out.write("\t" + snp_row[orcol])
    if becol is not None:
        out.write("\t" + snp_row[becol])
    if secol is not None:
        out.write("\t" + snp_row[secol])
    if Ncol is not None:
        out.write("\t" + snp_row[Ncol])
    out.write("\n")

def basic_sanitize(filedir, pcol, chrcol, poscol, header):
    "basic sanitation of chr, pos, and pvalue"
    # rename
    gwas_ori = os.path.join(filedir, "input.gwas")
    gwas_dest = os.path.join(filedir, "input.gwas.unclean")
    dest = shutil.copyfile(gwas_ori, gwas_dest)
    
    gwasIn = open(os.path.join(filedir, "input.gwas.unclean"), 'r')
    gwasIn.readline()
    n_variants = 0
    n_skipped = 0
    outfile = open(os.path.join(filedir, "input.gwas"), 'w')
    outfile.write("\t".join(header)+"\n")
    for l in gwasIn:
        if re.match("^#", l):
            next
        n_variants += 1
        l = l.replace("nan", "")
        l = [item.strip() for item in re.split(delim, l.strip())]
        if len(l) < nheader:
            n_skipped += 1
            log_skip("not enough columns", l)
            continue
        if not is_float(l[pcol]):
            n_skipped += 1
            log_skip("invalid P-value", l)
            continue
        if float(l[pcol])<0 or float(l[pcol])>1:
            n_skipped += 1
            log_skip("P-value out of range (0-1)", l)
            continue
        if float(l[pcol])==0 and re.match("^0", l[pcol]):
            n_skipped += 1
            log_skip("P-value of 0", l)
            continue
        if chrcol is not None:
            l[chrcol] = l[chrcol].replace("chr", "").replace("CHR", "")
            if re.match("x", l[chrcol], re.IGNORECASE):
                l[chrcol] = '23'
            if not l[chrcol].isdigit():
                n_skipped += 1
                log_skip("non-numeric chromosome", l)
                continue
            if int(l[chrcol]) not in range(1,24):
                n_skipped += 1
                log_skip("chromosome out of range (1-23)", l)
                continue
        
        if poscol is not None:
            pos = l[poscol].strip()

            # reject scientific notation
            if "e" in pos.lower():
                n_skipped += 1
                log_skip("scientific notation position", l)
                continue

            # must be pure integer
            if not pos.isdigit():
                n_skipped += 1
                logger.warning("Non-integer position skipped: " + str(l))
                continue
        outfile.write("\t".join(l)+"\n")
    outfile.close()
    logger.info(f"Basic file check, total number of variants in the input file: {n_variants}")
    logger.info(f"Basic file check, total number of variants skipped due to formatting issues: {n_skipped}")
    with open(os.path.join(filedir, "input.gwas")) as f:
        logger.info(f"Basic file check, total number of variants passing formatting checks: {sum(1 for line in f) - 1}")
    
# sanitize
try:
    basic_sanitize(filedir, pcol, chrcol, poscol, header)
except Exception as e:
    logger.exception("Error occurred during basic_sanitize")
    raise

##### Process input gwas sum stats #####
# when all columns are provided
# In this case, if the rsID columns is wrongly labeled, it will be problem later (not checked here)
if chrcol is not None and poscol is not None and rsIDcol is not None and eacol is not None and neacol is not None:
    
    logger.info("All of the following columns were detected in the input file: chr, pos, rsID, effect allele, non-effect allele, and p-value. The input file will be processed directly without extracting information from reference panel.")

    out = open(outSNPs, 'w')
    out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
    if orcol is not None:
        out.write("\tor")
    if becol is not None:
        out.write("\tbeta")
    if secol is not None:
        out.write("\tse")
    if Ncol is not None:
        out.write("\tN")
    out.write("\n")

    gwasIn = open(gwas, 'r')
    gwasIn.readline()
    for l in gwasIn:
        l = l.strip('\n').split('\t')
        values = [l[chrcol], l[poscol], l[neacol].upper(), l[eacol].upper(), l[rsIDcol], l[pcol]]
        write_row(out, values, l)
    gwasIn.close()
    out.close()
    tempfile = os.path.join(filedir, "temp.txt")
    os.system("sort -k 1n -k 2n "+outSNPs+" > "+tempfile)
    os.system("mv "+tempfile+" "+outSNPs)


# if both chr and pos are provided
elif chrcol is not None and poscol is not None:
    logger.info("Either rsID, effect allele or non effect allele is missing. Look up from dbSNP will be performed")
    dbSNPfile = cfg.get('data', 'dbSNP')
    refpanel = cfg.get('data', 'refgenome')+"/"+param.get('params', 'refpanel')
    pop = param.get('params', 'pop')

    ##### tabix refpanel to get rsID and alleles #####
    def Tabix (chrom, start ,end, snps):
        snps = np.array(snps)

        poss = set(snps[:, poscol].astype(int))
        pos = snps[:, poscol].astype(int)
  
        temp_pos = set()

        out = open(outSNPs, 'a+')

        # when rsID is the only missing column, keep all SNPs in input file
        # assigned rsID from the selected reference panel
        # if rsID is not available, replace with uniqID
        if neacol is not None and eacol is not None:
            tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".rsID.gz"
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
                            values = [
                                refSNP[j, 0],
                                refSNP[j, 1],
                                l[neacol].upper(),
                                l[eacol].upper(),
                                refSNP[j, 3],
                                l[pcol],
                                ]
                            write_row(out, values, l)
                        else:
                            values = [l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]
                            write_row(out, values, l)
                    else:
                        values = [l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]
                        write_row(out, values, l)
            else:
                for l in snps:
                    uid = ":".join([l[chrcol], l[poscol]]+sorted([l[neacol].upper(), l[eacol].upper()]))
                    values = [l[chrcol],l[poscol], l[neacol].upper(), l[eacol].upper(), uid, l[pcol]]
                    write_row(out, values, l)

        # when one of the alleles need to be extracted, get from the selected population
        else:
            tbfile = refpanel+"/"+pop+"/"+pop+".chr"+str(chrom)+".frq.gz"
            tb = tabix.open(tbfile)
            temp = tb.querys(str(chrom)+":"+str(start)+"-"+str(end))
            for l in temp:
                if int(l[1]) not in poss:
                    continue
                temp_pos.add(int(l[1]))
                j = bisect_left(pos, int(l[1]))
                if snps[j,pcol] is None:
                    log_skip("missing p-value", l)
                    continue
                if eacol is not None:
                    if snps[j,eacol].upper()!=l[3] and snps[j,eacol].upper()!=l[4]:
                        log_skip("allele mismatch", snps[j])
                        continue
                    a = "NA"
                    if snps[j,eacol]==l[3]:
                        a = l[4]
                    else:
                        a = l[3]
                    if rsIDcol is None:
                        values = [l[0],l[1], a, snps[j,eacol].upper(), l[2], snps[j,pcol]]
                    else:
                        values = [l[0],l[1], a, snps[j,eacol].upper(), snps[j,rsIDcol], snps[j,pcol]]
                    write_row(out, values, snps[j])
                else:
                    if rsIDcol is None:
                        values = [l[0],l[1], l[3], l[4], l[2], snps[j,pcol]]
                    else:
                        values = [l[0],l[1], l[3], l[4], snps[j,rsIDcol], snps[j,pcol]]
                    write_row(out, values, snps[j])
            missing = []
            for i, l in enumerate(snps):
                if int(l[poscol]) not in temp_pos:
                    missing.append(l)
                    log_skip("SNP not found in reference database for the selected population", l)
        out.close()


        return
        ##### end def Tabix() #####

    ##### sort input sum stats #####
    # input.gwas will be overwrited
    tmp = pd.read_csv(gwas, comment="#", sep="\t", dtype=str)
    head = list(tmp.columns.values)
    tmp = np.array(tmp)
    # tmp[:,chrcol] = [x.replace("chr", "").replace("CHR", "") for x in tmp[:,chrcol]]
    # tmp[:,chrcol] = [x.replace("x", "23").replace("X", "23") for x in tmp[:,chrcol]]
    tmp = tmp[np.lexsort((tmp[:,poscol].astype(int), tmp[:,chrcol].astype(int)))]
    with open(gwas, 'w') as o:
        o.write("\t".join(head)+"\n")
    with open(gwas, 'a+') as o:
        np.savetxt(o, tmp, delimiter='\t', fmt='%s')

    ##### init variables #####
    cur_chr = 1
    minpos = 0
    maxpos = 0
    temp = []

    ##### write header of input.snps #####
    out = open(outSNPs, 'w')
    out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
    if orcol is not None:
        out.write("\tor")
    if becol is not None:
        out.write("\tbeta")
    if secol is not None:
        out.write("\tse")
    if Ncol is not None:
        out.write("\tN")
    out.write("\n")
    out.close()

    ##### read input.gwas line by line #####
    gwasIn = open(gwas, 'r')
    gwasIn.readline()
    for l in gwasIn:
        l = l.strip('\n').split('\t')
        if int(float(l[chrcol])) == cur_chr:
            if minpos==0:
                minpos = int(float(l[poscol]))
            if int(float(l[poscol]))-minpos<=1000000:
                maxpos = int(float(l[poscol]))
                temp.append(l)
            else:
                if str(cur_chr) in [str(x) for x in range(1,24)]:
                    Tabix(cur_chr, minpos, maxpos, temp)
                minpos = int(float(l[poscol]))
                maxpos = int(float(l[poscol]))
                temp = []
                temp.append(l)
        else:
            if minpos!=0 and maxpos!=0:
                if str(cur_chr) in [str(x) for x in range(1,24)]:
                    Tabix(cur_chr, minpos, maxpos, temp)
            cur_chr = int(l[chrcol])
            minpos = int(float(l[poscol]))
            maxpos = int(float(l[poscol]))
            temp = []
            temp.append(l)
    if str(cur_chr) in [str(x) for x in range(1,24)]:
        Tabix(cur_chr, minpos, maxpos, temp)

# if either chr or pos is not procided, use rsID to extract position
elif chrcol is None or poscol is None:
    logger.info("Either chromosome or position column is missing. Look up from dbSNP will be performed to extract the missing information.")
    ##### read input file #####
    gwas = pd.read_csv(gwas, comment="#", sep="\t", dtype=str)
    gwas = gwas.to_numpy()
    gwas = gwas[gwas[:,rsIDcol].argsort()]

    ##### write header of input.snps #####
    out = open(outSNPs, 'w')
    out.write("chr\tbp\tnon_effect_allele\teffect_allele\trsID\tp")
    if orcol is not None:
        out.write("\tor")
    if becol is not None:
        out.write("\tbeta")
    if secol is not None:
        out.write("\tse")
    if Ncol is not None:
        out.write("\tN")
    out.write("\n")

    ##### update rsID to dbSNP 146 #####
    rsIDs = set(list(gwas[:, rsIDcol]))
    rsID = list(gwas[:, rsIDcol])
    dbSNPfile = cfg.get('data', 'dbSNP')
    rsID146 = open(dbSNPfile+"/RsMerge146.txt", 'r')
    for l in rsID146:
        l = l.strip().split()
        if l[0] in rsIDs:
            j = bisect_left(rsID, l[0])
            gwas[j,rsIDcol] = l[1]
    rsID146.close()

    ##### sort input snps by rsID for bisect_left #####
    gwas = gwas[gwas[:,rsIDcol].argsort()]
    rsIDs = set(list(gwas[:, rsIDcol]))
    rsID = list(gwas[:, rsIDcol])
    checked = []

    ##### process per chromosome #####
    for chrom in range(1,24):
        print("start chr"+str(chrom))
        for chunk in pd.read_csv(dbSNPfile+"/dbSNP146.chr"+str(chrom)+".vcf.gz", header=None, sep="\t", dtype=str, chunksize=10000):
            chunk = np.array(chunk)
            for l in chunk:
                alt = l[4].split(",")
                if l[2] in rsIDs:
                    checked.append(l[2])
                    j = bisect_left(rsID, l[2])
                    # if not is_float(gwas[j,pcol]):
                    # 	continue
                    # if float(gwas[j,pcol])<0 or float(gwas[j,pcol])>1:
                    # 	continue
                    # if float(gwas[j,pcol])==0 and re.match("^0", gwas[j,pcol]):
                    # 	continue
                    # if(gwas[j,pcol]<1e-308):
                    #     gwas[j,pcol]=1e-308
                    if eacol is not None and neacol is not None:
                        if (gwas[j,eacol].upper()==l[3] and gwas[j,neacol].upper() in alt) or gwas[j,eacol].upper() in alt and gwas[j,neacol].upper()==l[3]:
                            values = [str(chrom), str(l[1]), gwas[j,neacol].upper(), gwas[j,eacol].upper(), l[2], str(gwas[j,pcol])]
                            write_row(out, values, gwas[j])
                    elif eacol is not None:
                        if gwas[j,eacol].upper()==l[3] or gwas[j,eacol].upper() in alt:
                            if len(alt)>1:
                                log_skip("multiple alternate alleles in reference panel, cannot determine effect allele", l)
                                continue
                            a = "NA"
                            if gwas[j,eacol].upper()==l[3]:
                                a=l[4]
                            else:
                                a=l[3]

                            values = [str(chrom), str(l[1]), a, gwas[j,eacol].upper(), l[2], str(gwas[j,pcol])]
                            write_row(out, values, gwas[j])
                    else:
                        if len(alt)>1:
                            log_skip("multiple alternate alleles in reference panel, cannot determine effect allele", l)
                            continue
                        values = [str(chrom), str(l[1]), l[3], l[4], l[2], str(gwas[j,pcol])]
                        write_row(out, values, gwas[j])
                        # if orcol is not None:
                        # 	out.write("\t"+str(gwas[j,orcol]))
                        # if becol is not None:
                        # 	out.write("\t"+str(gwas[j,becol]))
                        # if secol is not None:
                        # 	out.write("\t"+str(gwas[j,secol]))
                        # if Ncol is not None:
                        # 	out.write("\t"+str(gwas[j,Ncol]))
                        # out.write("\n")

        if len(gwas)==len(checked):
            break
    out.close()

##### check output file #####
wc = int(subprocess.check_output("wc -l "+ outSNPs, shell=True).split()[0])
if wc < 2:
    sys.exit("There was no SNPs remained after formatting the input summary statistics.")

##### total time #####
print(time.time()-start)