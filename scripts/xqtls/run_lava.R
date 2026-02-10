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

input_common = process.input(input.info.file = paste0(filedir, filename),
                      sample.overlap.file=NULL,
                      ref.prefix = paste0(lava_dir, "/g1000_eur"),
                      phenos=c(phenotype))

# ## process input
for (dataset in unlist(strsplit(datasets, ":"))) {
  input = new.env()
  for (var in names(input_common)) input[[var]] = input_common[[var]]
  ## process input
  # copy input common
  # input = process.input(input.info.file = paste0(filedir, filename),
  #                     sample.overlap.file=NULL,
  #                     ref.prefix = paste0(lava_dir, "/g1000_eur"),
  #                     phenos=c(phenotype))
  # input = input_common
  

  ## set up output file names
  out.fname = paste0("lava_results_", dataset)

  qtl_type = unlist(strsplit(dataset, "-"))[1]
  dataset_origin = unlist(strsplit(dataset, "-"))[2]
  tissue = unlist(strsplit(dataset, "-"))[3]
  qtl_fn = paste0(filedir, dataset, "_", "[CHR]", "-", start, "-", end, ".sumstats.txt")

  qtls_full = fread(paste0(filedir, dataset, "_", chrom, "-", start, "-", end, ".sumstats.txt"))
  colnames(qtls_full) = c("RSID", "ALT_QTL", "REF_QTL", "N_QTL", "BETA_QTL", "P_QTL", "GENE_QTL", "MAF_QTL")
  sample_size = as.integer(mean(qtls_full$N_QTL))

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
  
  # save the output
  write.table(do.call(rbind,u), paste0(filedir, out.fname,".univ.lava"), row.names=F,quote=F,col.names=T)
  write.table(do.call(rbind,b), paste0(filedir, out.fname,".bivar.tmp.lava"), row.names=F,quote=F,col.names=T)

  print(paste0("Done! Analysis output written to ",out.fname,".*.lava"))

  # add bonferroni corrected p value
  tryCatch({
    bivar_results = fread(paste0(filedir, out.fname, ".bivar.tmp.lava"))
    # bivar_results = fread(paste0(filedir, out.fname,".bivar.tmp.lava"))
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
  },
  error = function(e) {
    message("Skipping file due to fread error: ", e$message)
  }
  )
}

write.table(results, file=paste0(filedir, "lava_bivar_results_all_datasets.txt"), row.names=F, quote=F, col.names=T, sep="\t")

results <- results %>%
  filter(p.adjust < 0.05)
write.table(results, file=paste0(filedir, "lava_bivar_results_all_datasets_significant.txt"), row.names=F, quote=F, col.names=T, sep="\t")
