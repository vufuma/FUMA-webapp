library(data.table)
library(dplyr)
library(argparse)
library(LAVA)

# Create argument parser
parser <- ArgumentParser(description = 'Run LAVA analysis')

# Add arguments
parser$add_argument("--filedir", required = TRUE)

args <- parser$parse_args()
filedir = args$filedir

# Get config parameters
curfile <- whereami::thisfile()
source(paste0(dirname(curfile), '/ConfigParser.R'))
config <- ConfigParser(file=paste0(dirname(curfile),'/app.config'))

lava_dir = config$data$lava

params <- ConfigParser(file=paste0(filedir, 'params.config'))

# Set up look up dictionary for sample sizes
# sample_size_vector (for GTEx datasets)
sample_sizes = c(714, 587, 295, 472, 268, 691, 77, 181, 233, 300, 277, 266, 270, 269, 255, 257, 285, 254, 204, 183, 514, 652, 327, 419, 479, 403, 614, 561, 461, 452, 104, 262, 604, 181, 818, 670, 193, 362, 313, 282, 651, 754, 207, 277, 407, 414, 684, 153, 170, 803, 33508)
names(sample_sizes) = c("Adipose_Subcutaneous",
                         "Adipose_Visceral_Omentum",
                         "Adrenal_Gland",
                         "Artery_Aorta",
                         "Artery_Coronary",
                         "Artery_Tibial",
                         "Bladder",
                         "Brain_Amygdala",
                         "Brain_Anterior_cingulate_cortex_BA24",
                         "Brain_Caudate_basal_ganglia",
                         "Brain_Cerebellar_Hemisphere",
                         "Brain_Cerebellum",
                         "Brain_Cortex",
                         "Brain_Frontal_Cortex_BA9",
                         "Brain_Hippocampus",
                         "Brain_Hypothalamus",
                         "Brain_Nucleus_accumbens_basal_ganglia",
                         "Brain_Putamen_basal_ganglia",
                         "Brain_Spinal_cord_cervical_c-1",
                         "Brain_Substantia_nigra",
                         "Breast_Mammary_Tissue",
                         "Cells_Cultured_fibroblasts",
                         "Cells_EBV-transformed_lymphocytes",
                         "Colon_Sigmoid",
                         "Colon_Transverse",
                         "Esophagus_Gastroesophageal_Junction",
                         "Esophagus_Mucosa",
                         "Esophagus_Muscularis",
                         "Heart_Atrial_Appendage",
                         "Heart_Left_Ventricle",
                         "Kidney_Cortex",
                         "Liver",
                         "Lung",
                         "Minor_Salivary_Gland",
                         "Muscle_Skeletal",
                         "Nerve_Tibial",
                         "Ovary",
                         "Pancreas",
                         "Pituitary",
                         "Prostate",
                         "Skin_Not_Sun_Exposed_Suprapubic",
                         "Skin_Sun_Exposed_Lower_leg",
                         "Small_Intestine_Terminal_Ileum",
                         "Spleen",
                         "Stomach",
                         "Testis",
                         "Thyroid",
                         "Uterus",
                         "Vagina",
                         "Whole_Blood", 
                         "Neurology") #TODO: make this into a config file


# create the input info file for LAVA
phenotype = params$params$phenotype
lavaGene = params$params$lavaGene
cases = params$params$cases
totalN = params$params$totalN

if (cases == "NA") {
  controls = "NA"
} else {
  cases = as.numeric(cases)
  totalN = as.numeric(totalN)
  controls = totalN - cases
}
filename="input.info.txt"
input_info = data.frame("phenotype" = phenotype,
                        "cases" = cases,
                        "controls" = controls,
                        "filename" = paste0(filedir, "locus.input"))
write.table(input_info, file=paste0(filedir, filename), row.names=F, quote=F, col.names=T)

#
chrom = params$params$chrom
start = params$params$start
end = params$params$end
datasets = params$params$datasets

gene_conversion_dir = config$data$geneConversion

results <- data.frame(matrix(ncol = 13, nrow = 0))
colnames(results) = c("locus", "chr", "phen1", "rho", "rho.lower", "rho.upper", "r2", "r2.lower", "r2.upper", "p", "p.adjust", "dataset", "symbol")


# ## process input
for (dataset in unlist(strsplit(datasets, ":"))) {
  ## process input
  input = process.input(input.info.file = paste0(filedir, filename),
                      sample.overlap.file=NULL,
                      ref.prefix = paste0(lava_dir, "/g1000_eur"),
                      phenos=c(phenotype))

  ## set up output file names
  out.fname = paste0("lava_results_", dataset)

  qtl_type = unlist(strsplit(dataset, "-"))[1]
  dataset_origin = unlist(strsplit(dataset, "-"))[2]
  tissue = unlist(strsplit(dataset, "-"))[3]
  sample_size = sample_sizes[[tissue]]
  qtl_fn = paste0(filedir, dataset, "_", "[CHR]", "-", start, "-", end, ".sumstats.txt")
  process.eqtl.input(input, qtl_fn, chromosomes=chrom, sample.size=sample_size)

  ### Set univariate pvalue threshold
  univ.p.thresh = 1e-4

  ### Run LAVA analysis
  u=b=list()

  if (tolower(lavaGene) == "all") {
    genes_to_test <- input$current.genes
  } else {
    genes_to_test <- unlist(strsplit(lavaGene, ","))
    genes_to_test <- trimws(genes_to_test)
    genes_to_test <- intersect(genes_to_test, input$current.genes)
  }

  # ### if a gene is specified, filter the input to only include that gene
  # if (lavaGene != "NA") {
  #   i = lavaGene
  #   locus = process.eqtl.locus(i, input) # process locus
  
  #   # It is possible that the locus cannot be defined for various reasons (e.g. too few SNPs), so the !is.null(locus) check is necessary before calling the analysis functions.
  #   if (!is.null(locus)) {
  #       # extract some general locus info for the output
  #       loc.info = data.frame(locus = locus$id, chr = locus$chr)
        
  #       # run the univariate and bivariate tests
  #       loc.out = run.univ.bivar(locus, univ.thresh = univ.p.thresh)
  #       u[[i]] = cbind(loc.info, loc.out$univ)
  #       if(!is.null(loc.out$bivar)) b[[i]] = cbind(loc.info, loc.out$bivar)
  #   }
  #   else {
  #     print(paste0("Locus for gene ", lavaGene, " could not be defined. Skipping LAVA analysis for this gene."))
  #     quit(status=3) #TODO: check this functionality works as intended
  #   }
  # } else {
    for (i in genes_to_test) {
      locus = process.eqtl.locus(i, input)                                      # process locus
      
      # It is possible that the locus cannot be defined for various reasons (e.g. too few SNPs), so the !is.null(locus) check is necessary before calling the analysis functions.
      if (!is.null(locus)) {
          # extract some general locus info for the output
          loc.info = data.frame(locus = locus$id, chr = locus$chr)
          
          # run the univariate and bivariate tests
          loc.out = run.univ.bivar(locus, univ.thresh = univ.p.thresh)
          u[[i]] = cbind(loc.info, loc.out$univ)
          if(!is.null(loc.out$bivar)) b[[i]] = cbind(loc.info, loc.out$bivar)
      }
  }
  # }

  

  # save the output
  write.table(do.call(rbind,u), paste0(filedir, out.fname,".univ.lava"), row.names=F,quote=F,col.names=T)
  write.table(do.call(rbind,b), paste0(filedir, out.fname,".bivar.tmp.lava"), row.names=F,quote=F,col.names=T)

  print(paste0("Done! Analysis output written to ",out.fname,".*.lava"))

  # add bonferroni corrected p value
  bivar_results = fread(paste0(filedir, out.fname,".bivar.tmp.lava"))
  bivar_results = bivar_results %>%
    select(-c("phen2"))
  bivar_results$dataset = dataset
  bivar_results$p.adjust = p.adjust(bivar_results$p, method = "bonferroni")

  # add gene symbol column
  gene_conversion_orig = fread(paste0(gene_conversion_dir, "/gencode.v39.gene_name_conversion.tsv")) #TODO: make this dynamic
  gene_conversion = gene_conversion_orig %>%
    select("gene_id", "gene_name") %>% unique()
  colnames(gene_conversion) = c("locus", "symbol")
  bivar_results = bivar_results %>%
    left_join(gene_conversion, by="locus")

  write.table(bivar_results, paste0(filedir, out.fname,".bivar.lava"), row.names=F, quote=F, col.names=T)

  # combine results from different datasets
  results = rbind(results, as.data.frame(bivar_results))

  rm(input)
}

write.table(results, file=paste0(filedir, "lava_bivar_results_all_datasets.txt"), row.names=F, quote=F, col.names=T, sep="\t")

results <- results %>%
  filter(p.adjust < 0.05)
write.table(results, file=paste0(filedir, "lava_bivar_results_all_datasets_significant.txt"), row.names=F, quote=F, col.names=T, sep="\t")
