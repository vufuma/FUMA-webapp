library(coloc)
library(data.table)
library(dplyr)



# snps = fread("snps_out.txt")
# pqtls = fread("pqtls_out.txt")

# snps_filtered = snps %>%
#   filter(pos %in% pqtls$pos)

# snps_filtered$logOR = log(snps_filtered$or) 

# snps_filtered = snps_filtered %>%
#   distinct(pos, .keep_all = TRUE)

# pqtls_filtered = pqtls %>%
#   filter(pos %in% snps_filtered$pos) %>%
#   distinct(pos, .keep_all = TRUE)

# # 34,241 cases and 45,604 controls

# dataset_gwas <- list(
#   snp = as.character(snps_filtered$pos),
#   position = snps_filtered$pos,
#   beta = snps_filtered$logOR,    # Use log odds ratio (logOR)
#   varbeta = snps_filtered$se^2,  # Variance of logOR (SE squared)
#   MAF = snps_filtered$maf,       # Minor allele frequency
#   P = snps_filtered$p,
#   N = 79845,           # Total sample size (cases + controls)
#   s = 0.43,  # Proportion of cases
#   type = "cc"                # "cc" for case-control
# )

# dataset_pqtl <- list(
#   snp = as.character(pqtls_filtered$pos),
#   position=pqtls_filtered$pos,
#   beta = pqtls_filtered$beta,       # Effect size
#   varbeta = pqtls_filtered$se^2,    # Variance of effect size (SE squared)
#   MAF = pqtls_filtered$maf,         # Minor allele frequency
#   N = pqtls_filtered$N,             # Sample size
#   type = "quant"               # "quant" for continuous traits
# )

# # Perform colocalization analysis
# coloc_results <- coloc.abf(dataset_gwas, dataset_pqtl)

# # Print results
# print(coloc_results)

# # Extract Posterior Probabilities (PP)
# print(coloc_results$summary)

# plot_dataset(dataset_gwas)
# plot_dataset(dataset_pqtl)