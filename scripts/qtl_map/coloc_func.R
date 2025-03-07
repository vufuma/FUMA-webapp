#! /usr/bin/Rscript

library(coloc)
library(data.table)
library(dplyr)

args <- commandArgs(trailingOnly = TRUE)
snps_fp = args[1]
pqtls_fp = args[2]
outfile = args[3]
genomic_locus = as.numeric(args[4])
ds = args[5]
gene = args[6]
sample_size = as.numeric(args[7])
cases_prop = as.numeric(args[8])

snps = fread(snps_fp)
pqtls = fread(pqtls_fp)

print("### Processing coloc_func.R")

snps_filtered = snps %>%
  filter(pos %in% pqtls$pos)

# snps_filtered$logOR = log(snps_filtered$or) 

snps_filtered = snps_filtered %>%
  distinct(pos, .keep_all = TRUE)

pqtls_filtered = pqtls %>%
  filter(pos %in% snps_filtered$pos) %>%
  distinct(pos, .keep_all = TRUE)

# 34,241 cases and 45,604 controls

dataset_gwas <- list(
  snp = as.character(snps_filtered$pos),
  position = snps_filtered$pos,
  # beta = snps_filtered$logOR,    # Use log odds ratio (logOR)
  beta = snps_filtered$or,    # Use log odds ratio (logOR)
  varbeta = snps_filtered$se^2,  # Variance of logOR (SE squared)
  MAF = snps_filtered$maf,       # Minor allele frequency
  P = snps_filtered$p,
  N = sample_size,           # Total sample size (cases + controls)
  s = cases_prop,  # Proportion of cases
  type = "cc"                # "cc" for case-control
)

dataset_pqtl <- list(
  snp = as.character(pqtls_filtered$pos),
  position=pqtls_filtered$pos,
  beta = pqtls_filtered$beta,       # Effect size
  varbeta = pqtls_filtered$se^2,    # Variance of effect size (SE squared)
  MAF = pqtls_filtered$maf,         # Minor allele frequency
  N = pqtls_filtered$N,             # Sample size
  type = "quant"               # "quant" for continuous traits
)

# Perform colocalization analysis
coloc_results <- coloc.abf(dataset_gwas, dataset_pqtl)

# Print results
# print(coloc_results)

# Extract Posterior Probabilities (PP)
# print(coloc_results$summary)

# plot_dataset(dataset_gwas)
# plot_dataset(dataset_pqtl)

out = data.frame(GenomicLocus=genomic_locus,
                 Dataset=ds,
                 Protein=gene,
                 nsnps=coloc_results$summary[1],
                 PP.H0.abf=coloc_results$summary[2],
                 PP.H1.abf=coloc_results$summary[3],
                 PP.H2.abf=coloc_results$summary[4],
                 PP.H3.abf=coloc_results$summary[5],
                 PP.H4.abf=coloc_results$summary[6])
write.table(out, outfile, row.names = FALSE, quote = FALSE, sep = "\t", append = TRUE)