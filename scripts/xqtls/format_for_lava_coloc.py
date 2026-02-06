# this script prepares the QTLs for LAVA and coloc format
import tabix
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

qtl_dir = cfg.get('data', 'QTL')
dbsnp_dir = cfg.get('data', 'dbSNP')

chrom=param.get('params','chrom')
build = param.get('params','build').lower()
if build == "grch37":
    with open(os.path.join(filedir, "locus_range_grch38.txt"), "r") as f:
        for line in f:
            items = line.rstrip("\n").split("\t")
            start = int(items[1])
            end = int(items[2])-1
            print(f"After conversion of genomic locus from GRCh37 to GRCh38, new start is {start}, new end is {end}")
            break
else:
    start=param.getint('params','start')
    end=param.getint('params','end')
    
# update param file
param_path = os.path.join(filedir, "params.config")
updated_lines = []

with open(param_path, "r") as f:
    for line in f:
        if line.startswith("start="):
            updated_lines.append(f"start={start}\n")
        elif line.startswith("end="):
            updated_lines.append(f"end={end}\n")
        else:
            updated_lines.append(line)

with open(param_path, "w") as f:
    f.writelines(updated_lines)
    


# lavaGene=param.get('params','lavaGene')

datasets=param.get('params','datasets').split(":")

# # Setting up
# tissue_sample_sizes = {"Adipose_Subcutaneous": 714,
#             "Adipose_Visceral_Omentum": 587,
#             "Adrenal_Gland": 295,
#             "Artery_Aorta": 472,
#             "Artery_Coronary": 268,
#             "Artery_Tibial": 691,
#             "Bladder": 77,
#             "Brain_Amygdala": 181,
#             "Brain_Anterior_cingulate_cortex_BA24": 233,
#             "Brain_Caudate_basal_ganglia": 300,
#             "Brain_Cerebellar_Hemisphere": 277,
#             "Brain_Cerebellum": 266,
#             "Brain_Cortex": 270,
#             "Brain_Frontal_Cortex_BA9": 269,
#             "Brain_Hippocampus": 255,
#             "Brain_Hypothalamus": 257,
#             "Brain_Nucleus_accumbens_basal_ganglia": 285,
#             "Brain_Putamen_basal_ganglia": 254,
#             "Brain_Spinal_cord_cervical_c-1": 204,
#             "Brain_Substantia_nigra": 183,
#             "Breast_Mammary_Tissue": 514,
#             "Cells_Cultured_fibroblasts": 652,
#             "Cells_EBV-transformed_lymphocytes": 327,
#             "Colon_Sigmoid": 419,
#             "Colon_Transverse": 479,
#             "Esophagus_Gastroesophageal_Junction": 403,
#             "Esophagus_Mucosa": 614,
#             "Esophagus_Muscularis": 561,
#             "Heart_Atrial_Appendage": 461,
#             "Heart_Left_Ventricle": 452,
#             "Kidney_Cortex": 104,
#             "Liver": 262,
#             "Lung": 604,
#             "Minor_Salivary_Gland": 181,
#             "Muscle_Skeletal": 818,
#             "Nerve_Tibial": 670,
#             "Ovary": 193,
#             "Pancreas": 362,
#             "Pituitary": 313,
#             "Prostate": 282,
#             "Skin_Not_Sun_Exposed_Suprapubic": 651,
#             "Skin_Sun_Exposed_Lower_leg": 754,
#             "Small_Intestine_Terminal_Ileum": 207,
#             "Spleen": 277,
#             "Stomach": 407,
#             "Testis": 414,
#             "Thyroid": 684,
#             "Uterus": 153,
#             "Vagina": 170,
#             "Whole_Blood": 803}  #TODO: add more tissues

# # get the tabix file for dbSNP
# dbsnp_tb = tabix.open(os.path.join(dbsnp_dir, "dbSNP_v157", "dbSNP157.chr" + chrom + ".vcf.gz")) #TODO: check the grch37 versus grch38

for dataset in datasets:
    qtl_type = dataset.split("-")[0]
    dataset_origin = dataset.split("-")[1]
    tissue = dataset.split("-")[2]
    # sample_size = tissue_sample_sizes[tissue]
    infile = os.path.join(qtl_dir, qtl_type, dataset_origin, "processed_files", qtl_type + "_" + dataset_origin + "_" + tissue + ".chr" + chrom + ".txt.gz") #TODO: modify the path to have the same naming convention
    
    #setting up output file
    outfile_fn = os.path.join(filedir, dataset + "_" + str(chrom) + "-" + str(start) + "-" + str(end) + ".sumstats.txt")
    outfile = open(outfile_fn, "w")
    header = ["RSID", "ALT", "REF", "N", "BETA", "P", "GENE", "MAF"]
    print("\t".join(header), file=outfile)
    
    if not os.path.exists(infile):
        sys.exit("Input file " + infile + " not found.")
    tb = tabix.open(infile)
    
    query_region = str(chrom)+":"+str(start)+"-"+str(end)

    querried_results = tb.querys(query_region)
    
    # # setting up rsid dict
    # id_rsid_map = {}
    # if not os.path.exists(os.path.join(dbsnp_dir, "dbSNP_v157", "dbSNP157.chr" + chrom + ".vcf.gz")):
    #     sys.exit("dbSNP file for chromosome " + chrom + " not found.")
    # rsid_tb = tabix.open(os.path.join(dbsnp_dir, "dbSNP_v157", "dbSNP157.chr" + chrom + ".vcf.gz"))
    # rsid_querried_results = rsid_tb.querys(query_region)
    # for querry in rsid_querried_results:
    #     chrom = querry[0]
    #     pos = querry[1]
    #     rsid = querry[2]
    #     ref = querry[3]
    #     if ',' not in querry[4]:
    #         alt = querry[4]
    #         id_key = chrom + ":" + pos + ":" + ref + ":" + alt
    #         id_rsid_map[id_key] = rsid
    #     else:
    #         alt_alleles = querry[4].split(',')
    #         for alt in alt_alleles:
    #             id_key = chrom + ":" + pos + ":" + ref + ":" + alt
    #             id_rsid_map[id_key] = rsid

    # total_snps = 0
    # skipped_snps = 0
    

    for query in querried_results:
        # total_snps += 1
        chrom = query[0]
        pos = query[1]
        ref = query[2]
        alt = query[3]
        rsid = query[4]
        geneid = query[5]
        p = query[6]
        beta = query[7]
        maf = query[8]
        n = query[9]
        
        # if lavaGene != "all":
        #     if geneid != lavaGene:
        #         skipped_snps += 1
        #         continue
        
        print("\t".join([rsid, alt, ref, n, beta, p, geneid, maf]), file=outfile)

    outfile.close()
    
    
    

# parser.add_argument('--infile', required=True, help="Input file, e.g. GTEx_v10_apaQTL_Brain_Hippocampus_chr17_hg19_fmt.txt.gz")
# parser.add_argument('--type', required=True, help="Type of QTLs: eQTL, sQTL, apaQTL")
# parser.add_argument('--dataset', required=True, help="Dataset name, e.g. gtex_v10_brain_hippocampus")
# parser.add_argument('--chrom', required=True, help="Chromosome number, e.g. 17")
# parser.add_argument('--start', type=int, required=True, help="Start position of the locus, e.g. 42180244")
# parser.add_argument('--end', type=int, required=True, help="End position of the locus, e.g. 42874404")
# parser.add_argument('--tissue', required=True, help="tissue, e.g. Brain_Hippocampus")
# parser.add_argument('--rsid_infer', required=True, help="yes if inference of rsid is required.")
# parser.add_argument('--out_dir', required=True, help="Path to output directory.")
# args = parser.parse_args()

# infile = args.infile
# type = args.type
# dataset = args.dataset
# chrom = args.chrom
# start = args.start
# end = args.end
# rsid_infer = args.rsid_infer
# out_dir = args.out_dir
# tissue=args.tissue

# tissue_sample_sizes = {"Brain_Amygdala":181,
#                        "Brain_Anterior_cingulate_cortex_BA24":233,
#                        "Brain_Caudate_basal_ganglia":300,
#                        "Brain_Cerebellar_Hemisphere":277,
#                        "Brain_Cerebellum":266,
#                        "Brain_Cortex":270,
#                        "Brain_Frontal_Cortex_BA9":269,
#                        "Brain_Hippocampus":255,
#                        "Brain_Hypothalamus":257,
#                        "Brain_Nucleus_accumbens_basal_ganglia":285,
#                        "Brain_Putamen_basal_ganglia":254,
#                        "Brain_Spinal_cord_cervical_c-1":204,
#                        "Brain_Substantia_nigra":183}

# sample_size = tissue_sample_sizes[tissue]

# query_region = str(chrom)+":"+str(start)+"-"+str(end)

# if rsid_infer == 'yes':
#     print("rsid inference is enabled.")
#     id_rsid_map = {}
#     if not os.path.exists("/gpfs/work5/0/vusr0480/Reference_Data/fuma_reference_data/dbSNP157/dbSNP157.chr" + chrom + ".vcf.gz"):
#         sys.exit("dbSNP file for chromosome " + chrom + " not found.")
#     rsid_tb = tabix.open("/gpfs/work5/0/vusr0480/Reference_Data/fuma_reference_data/dbSNP157/dbSNP157.chr" + chrom + ".vcf.gz")
#     rsid_querried_results = rsid_tb.querys(query_region)
#     for querry in rsid_querried_results:
#         chrom = querry[0]
#         pos = querry[1]
#         rsid = querry[2]
#         ref = querry[3]
#         if ',' not in querry[4]:
#             alt = querry[4]
#             id_key = chrom + ":" + pos + ":" + ref + ":" + alt
#             id_rsid_map[id_key] = rsid
#         else:
#             alt_alleles = querry[4].split(',')
#             for alt in alt_alleles:
#                 id_key = chrom + ":" + pos + ":" + ref + ":" + alt
#                 id_rsid_map[id_key] = rsid

# outfile_fn = os.path.join(out_dir, type + "_" + dataset + "_" + str(chrom) + "-" + str(start) + "-" + str(end) + ".sumstats.txt")
# outfile = open(outfile_fn, "w")
# header = ["RSID", "ALT", "REF", "N", "BETA", "P", "GENE", "MAF"]
# print("\t".join(header), file=outfile)

# if not os.path.exists(infile):
#     sys.exit("Input file " + infile + " not found.")
# tb = tabix.open(infile)

# querried_results = tb.querys(query_region)

# n_snps = 0
# n_snps_with_rsid = 0

# for querry in querried_results:
#     n_snps += 1
#     chrom = querry[0]
#     pos = querry[1]
#     ref = querry[2]
#     alt = querry[3]
#     beta = querry[6]
#     p = querry[5]
#     n = str(sample_size)
#     geneid = querry[4]
#     maf = querry[7]
    
#     id_key = chrom + ":" + pos + ":" + ref + ":" + alt
#     if id_key in id_rsid_map:
#         rsid = id_rsid_map[id_key]
#         n_snps_with_rsid += 1
#         # continue
#     else:
#         print("rsid not found for " + id_key)
    
#     print("\t".join([rsid, alt, ref, n, beta, p, geneid, maf]), file=outfile)

# outfile.close()
# print(n_snps, " SNPs processed.")
# print(n_snps_with_rsid, " SNPs have rsid inferred.")