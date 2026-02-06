library(data.table)
library(dplyr)
library(argparse)
library(coloc)

# Create argument parser
parser <- ArgumentParser(description = 'Run coloc analysis between GWAS and eQTL data')

# Add arguments
parser$add_argument("--filedir", required = TRUE)

args <- parser$parse_args()
filedir = args$filedir

# Get config parameters
curfile <- whereami::thisfile()
source(paste0(dirname(curfile), '/ConfigParser.R'))
config <- ConfigParser(file=paste0(dirname(curfile),'/app.config'))

params <- ConfigParser(file=paste0(filedir, 'params.config'))

gene_conversion_dir = config$data$geneConversion

#
chrom = params$params$chrom
start = params$params$start
end = params$params$end
datasets = params$params$datasets
pp4 = params$params$pp4
colocGene = params$params$colocGene
cases = params$params$cases
totalN = params$params$totalN
if (cases == "NA" && totalN == "NA") {
  stop("The total sample size (totalN) must be provided for GWAS summary statistics and cannot be NA")
} else if (cases == "NA") {
  print("Cases were set to NA, treat this as a quantitative trait")
  type_gwas = "quant"
} else {
  type_gwas = "cc"
  cases = as.numeric(cases)
  totalN = as.numeric(totalN)
  cases_prop = cases / totalN
}

out_fn = paste0(filedir, "coloc_results.txt")
filtered_out_fn = paste0(filedir, "coloc_results_filtered.txt")

results <- data.frame(matrix(ncol = 9, nrow = 0))
colnames(results) = c("tissue", "gene", "nsnps", "PP.H0.abf", "PP.H1.abf", "PP.H2.abf", "PP.H3.abf", "PP.H4.abf", "symbol")

for (dataset in unlist(strsplit(datasets, ":"))) {
  qtl_type = unlist(strsplit(dataset, "-"))[1]
  dataset_origin = unlist(strsplit(dataset, "-"))[2]
  tissue = unlist(strsplit(dataset, "-"))[3]

  snps_fn = paste0(filedir, "locus.input")
  qtl_fn = paste0(filedir, dataset, "_", chrom, "-", start, "-", end, ".sumstats.txt")


  # analysis
  snps_orig = fread(snps_fn)
  snps = snps_orig %>%
    filter(!is.na(RSID))
  colnames(snps) = c("RSID", "ALT_SNP", "REF_SNP", "N_SNP", "BETA_SNP", "P_SNP", "MAF_SNP")

  qtls_full = fread(qtl_fn)
  colnames(qtls_full) = c("RSID", "ALT_QTL", "REF_QTL", "N_QTL", "BETA_QTL", "P_QTL", "GENE_QTL", "MAF_QTL")
  sample_size = unique(qtls_full$N_QTL)


  if (tolower(colocGene) == "all") {
    genes_to_test <- unique(qtls_full$GENE_QTL)
  } else {
    genes_to_test <- unlist(strsplit(colocGene, ","))
    genes_to_test <- trimws(genes_to_test)
    genes_to_test <- intersect(genes_to_test, unique(qtls_full$GENE_QTL))
  }

  if (length(genes_to_test) == 0) {
    print("No genes found in the locus for colocalization analysis.")
    quit(status=1)
  }

  for (i in genes_to_test) {
    qtls = qtls_full %>%
      filter(GENE_QTL == i) %>%
      filter(!is.na(P_QTL))
    
    merged_data = merge(snps, qtls, by="RSID")
    
    merged_data = merged_data %>%
      distinct(RSID, .keep_all = TRUE)
    
    if (type_gwas == "cc") {
      dataset1 = list(snp=merged_data$RSID, pvalues=merged_data$P_SNP, type='cc', s=cases_prop, N=as.numeric(totalN), MAF = merged_data$MAF_SNP)
    } else {
      dataset1 = list(snp=merged_data$RSID, pvalues=merged_data$P_SNP, type='quant', N=as.numeric(totalN), MAF = merged_data$MAF_SNP)
    }
    dataset2 = list(snp=merged_data$RSID, pvalues=merged_data$P_QTL, type='quant', N=sample_size, MAF = merged_data$MAF_QTL)
    
    coloc_results <- coloc.abf(dataset1, dataset2)
    
    result = data.frame("tissue" = tissue, "gene" = i, "nsnps" = coloc_results$summary[[1]], "PP.H0.abf" = coloc_results$summary[[2]], "PP.H1.abf" = coloc_results$summary[[3]], "PP.H2.abf" = coloc_results$summary[[4]], "PP.H3.abf" = coloc_results$summary[[5]], "PP.H4.abf" = coloc_results$summary[[6]])
    results = rbind(results, result)
  }
}

# add gene symbol column
gene_conversion_orig = fread(paste0(gene_conversion_dir, "/gencode.v39.gene_name_conversion.tsv")) #TODO: make this dynamic
gene_conversion = gene_conversion_orig %>%
  select("gene_id", "gene_name") %>% unique()
colnames(gene_conversion) = c("gene", "symbol")
results = results %>%
  left_join(gene_conversion, by="gene")

write.table(results, file=out_fn, quote=FALSE, sep="\t", row.names=FALSE, col.names=TRUE)

# filter results based on PP4 threshold
filtered_results = results %>%
  filter(PP.H4.abf >= as.numeric(pp4))
write.table(filtered_results, file=filtered_out_fn, quote=FALSE, sep="\t", row.names=FALSE, col.names=TRUE)



