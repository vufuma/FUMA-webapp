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
sample_sizes = c(181, 233, 300, 277, 266, 270, 269, 255, 257, 285, 254, 204, 183)
names(sample_sizes) = c("Brain_Amygdala", 
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
                        "Brain_Substantia_nigra")

# create the input info file for LAVA
phenotype = params$params$phenotype
cases = as.numeric(params$params$cases)
controls = as.numeric(params$params$controls)
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
out.fname="lava_results"

## process input
input = process.input(input.info.file = paste0(filedir, filename),
                      sample.overlap.file=NULL,
                      ref.prefix = paste0(lava_dir, "/g1000_eur"),
                      phenos=c(phenotype))

for (dataset in unlist(strsplit(datasets, ":"))) {
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

    for (i in 1:length(input$current.genes)) {
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
    write.table(do.call(rbind,b), paste0(filedir, out.fname,".bivar.lava"), row.names=F,quote=F,col.names=T)

    print(paste0("Done! Analysis output written to ",out.fname,".*.lava"))
}
