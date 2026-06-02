# this script converts the input gwas sumstat for the snp2gene module from grch38 to grch37 using liftover
# expect input in the gwas catalog format
import os
import argparse
import shutil
import sys
import configparser

def prepare_for_liftover(filedir):

    gwas_ori = os.path.join(filedir, "input.gwas")
    gwas_dest = os.path.join(filedir, "input.gwas.grch38")
    dest = shutil.copyfile(gwas_ori, gwas_dest)
    
    

    gwas_for_liftover = open(os.path.join(filedir, "input.gwas.grch38.for_liftover"), "w")

    with open(os.path.join(filedir, 'input.gwas.grch38'), "r") as f:
        header = f.readline().rstrip("\n").split("\t")
        
        if header[:2] != ["chromosome", "base_pair_location"]:
            print("Error: input gwas file must have 'chromosome' and 'base_pair_location' as the first two columns.")
            sys.exit(1)
            
        for line in f:
            if line.startswith("chromosome"):
                continue
            items = line.rstrip("\n").split("\t")
            chrom = items[0]
            pos = items[1]
            print("\t".join(["chr"+chrom, str(int(pos)-1), pos] + items[2:]), file=gwas_for_liftover)
            
def run_liftover(filedir, liftover_dir):
    command = "./liftOver -bedPlus=3 " + filedir + "input.gwas.grch38.for_liftover " +  liftover_dir + "/hg38ToHg19.over.chain.gz " + filedir + "input.gwas.grch37 " + filedir + "unMapped"
    print(command)

    os.system(command)
    
def format_post_liftover(filedir):
    gwas_post_liftover = open(os.path.join(filedir, "input.gwas"), "w")
    
    with open(os.path.join(filedir, "input.gwas.grch38"), "r") as f:
        header = f.readline().rstrip("\n").split("\t")
        print("\t".join(header), file=gwas_post_liftover)

    with open(os.path.join(filedir, 'input.gwas.grch37'), "r") as f:
        for line in f:
            items = line.rstrip("\n").split("\t")
            chrom = items[0]
            pos = items[2]
            print("\t".join([chrom.replace("chr", ""), pos] + items[3:]), file=gwas_post_liftover)
            
def main(args):
    # Setting up parameters
    filedir = args.filedir

    cfg = configparser.ConfigParser()
    cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))

    liftover_dir = os.path.join(cfg.get('data', 'liftover'))
    
    # Prepare for liftover
    prepare_for_liftover(filedir)
    
    # Run liftover
    run_liftover(filedir, liftover_dir)
    
    # Format post liftover
    format_post_liftover(filedir)
    
def parse_params():
    parser = argparse.ArgumentParser()
    parser.add_argument('--filedir', required=True, help="Path to input directory.")
    return parser.parse_args()

if __name__ == "__main__":
    main(parse_params())


    
    
    
