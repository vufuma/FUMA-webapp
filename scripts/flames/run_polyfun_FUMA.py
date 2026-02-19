import pandas as pd
import os
import subprocess
import time
from sys import argv
import configparser
import argparse

parser = argparse.ArgumentParser()
parser.add_argument('--filedir', required=True, help="Path to input directory.")
parser.add_argument('--s2gdir', required=True, help="Path to snp2gene directory")
args = parser.parse_args()

# Setting up parameters
filedir = args.filedir
s2gdir = args.s2gdir

cfg = configparser.ConfigParser()
cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

param = configparser.RawConfigParser()
param.optionxform = str
param.read(os.path.join(filedir, 'params.config'))

# get the parameters from the params
N = param.get('params','sampleSize')
s2g_id = param.get('params','snp2geneID')

# 
loci = pd.read_csv(os.path.join(s2gdir, s2g_id, 'GenomicRiskLoci.txt'), sep='\t')
# N = argv[1]

if not os.path.exists(os.path.join(filedir, "loci")):
    os.makedirs(os.path.join(filedir, "loci"))
for index, row in loci.iterrows():
    locname = row['GenomicLocus']
    
    chrom = row["chr"]
    start = row["start"]
    end = row["end"]
    locname = f'{chrom}:{start}-{end}'
    if os.path.exists(f"{filedir}/locus_{locname}.susie.finemapped"):
        print(f"locus_{locname}.susie.finemapped already exists, skipping")
        continue

    cmd = f'cat {s2gdir}/{s2g_id}/header.txt > loci/locus_{locname}.txt' #from fuma: chr    bp      A2       A1   rsID    p       beta
    process = subprocess.Popen([cmd], close_fds=True, shell=True)
    process.wait()

    # if start == end:
    #     start = start - 25000
    #     end = end + 25000

    # cmd = f"tabix input.snps.gz {chrom}:{start}-{end} >> loci/locus_{locname}.txt"
    # process = subprocess.Popen([cmd], close_fds=True, shell=True)
    # process.wait()
    # time.sleep(2.5)
    
 
    # try:
    #     cmd = f'python ~/onedrive_documents/ctg/codes/polyfun/munge_polyfun_sumstats.py --sumstats loci/locus_{locname}.txt --out loci/locus_{locname}.txt.pq --n {N}'
    #     print(cmd)
    #     process = subprocess.Popen([cmd], close_fds=True, shell=True)
    #     process.wait()
    #     time.sleep(2.5)

    #     cmd = f'python ~/onedrive_documents/ctg/codes/polyfun/finemapper.py --sumstats loci/locus_{locname}.txt.pq --method susie --n {N} --out loci/locus_{locname}.susie --max-num-causal 1 --chr {chrom} --start {start} --end {end} --non-funct'
    #     process = subprocess.Popen([cmd], close_fds=True, shell=True)
    #     print(cmd)
    #     process.wait()
    #     time.sleep(2.5)
        
    #     finemapped = pd.read_csv(f"loci/locus_{locname}.susie", sep="\t")
    #     print(finemapped)
    #     print(max(finemapped['PIP']))
    #     finemapped = finemapped.sort_values("PIP", ascending=True)
    #     if len(finemapped) > 1:
    #         finemapped['cumsum'] = finemapped['PIP'].cumsum()
    #         finemapped = finemapped[finemapped['cumsum'] > 0.05]
    #     finemapped['SNP'] = finemapped.apply(lambda row: f"{row['CHR']}:{row['BP']}:{row['A1']}:{row['A2']}", axis=1)
    #     finemapped = finemapped[['SNP', 'PIP']]
    #     finemapped = finemapped.sort_values("PIP", ascending=False)
    #     if len(finemapped) == 0:
    #         print(f"No finemapped SNPs for locus {locname}, skipping")
    #         continue
    #     finemapped.to_csv(f"locus_{locname}.susie.finemapped", sep="\t", index=False)
    # except:
    #     print(f"Error in {locname}")
    # cmd = f'rm loci/locus_{locname}*'
    # process = subprocess.Popen([cmd], close_fds=True, shell=True)
    # process.wait(2.5)

