args = commandArgs(trailingOnly=TRUE)
#usage Rscript giversID.R chromosome position effectallele noneffectallele path2inputfile

print("Using GRCh38 position to get rsIDs")

#get file names
library(kimisc)
curfile <- thisfile()
source(paste(dirname(curfile), '/ConfigParser.R', sep=""))
config <- ConfigParser(file=paste(dirname(curfile),'/app.config', sep=""))

library(data.table)

#read in user file
a<-fread(paste(args[5],"input.gwas",sep=""),header=T)

#set chromosome, position, and allele columns to a specific name
#chr_10001, pos_10001, allele_10001, allele_20001
#check if these columns already exist
if (any(c("chr_10001","pos_10001","allele_10001","allele_20001") %in% colnames(a))) {
	print("chr_10001, pos_10001, allele_10001, allele_20001 are in input. Please rename columns to something else.")
	quit(status=2)
}

#stop if they give incorrect column names
if (any(!args[1:4] %in% colnames(a))) {
	print("not all specified columns match input file")
	quit(status=3)
}

#check if the colnames match multiple columns
if (length(colnames(a)[duplicated(colnames(a))])>0) {
	print("Some column names are duplicated in the input file")
	quit(status=4)
}


#make new columns with uniform names
tmp<-which(colnames(a)==args[1])
a$CHR_10001<-a[,..tmp]
tmp<-which(colnames(a)==args[2])
a$POS_10001<-a[,..tmp]
tmp<-which(colnames(a)==args[3])
a$ALLELE_10001<-a[,..tmp]
tmp<-which(colnames(a)==args[4])
a$ALLELE_20001<-a[,..tmp]


#set X chromosome to 23
a$CHR_10001[a$CHR_10001=="X"]<-23
#save rows that are dropped for later
badc<-a[!a$CHR_10001 %in% 1:23,]
badc<-badc[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001")]
fwrite(badc, file=paste(args[5],"GRCh38_droppedvariants.txt.gz",sep=""), sep="\t", col.names=T, row.names=F, na=NA, quote=F, append=T)

#remove chr not in 1:23
a<-a[a$CHR_10001 %in% 1:23,]

#remove columns that would match rsID and uniqID
tmp<-which(grepl("SNP", colnames(a),ignore.case=T))
if (length(tmp)>0) {
	a<-a[,-..tmp]
}

tmp<-which(grepl("SNPID", colnames(a),ignore.case=T))
if (length(tmp)>0) {
	a<-a[,-..tmp]
}

tmp<-which(grepl("MARKERNAME", colnames(a),ignore.case=T))
if (length(tmp)>0) {
	a<-a[,-..tmp]
}

tmp<-which(grepl("RSID", colnames(a),ignore.case=T))
if (length(tmp)>0) {
	a<-a[,-..tmp]
}

tmp<-which(grepl("UNIQID", colnames(a),ignore.case=T))
if (length(tmp)>0) {
	a<-a[,-..tmp]
}

#remove rsID column if name supplied
if (!args[6]=="NA") {
	tmp<-which(colnames(a)==args[5])
	a<-a[,-..tmp]
}

#recursively load in the conversion data and annotate with rsID
#subset to single chromosome
a1<-a[a$CHR_10001==unique(a$CHR_10001)[1],]

#make uniqID
a1$min<-apply(a1[,c("ALLELE_10001","ALLELE_20001")], 1, FUN = min)
a1$max<-apply(a1[,c("ALLELE_10001","ALLELE_20001")], 1, FUN = max)
a1$UNIQID<-paste(a1$CHR_10001,a1$POS_10001,a1$min,a1$max,sep=":")

#merge with reference file
ref<-fread(paste(config$data$GRCh38,unique(a$CHR_10001)[1],"_FUMA_DuplicatesResolved_dbSNP_v152_GRCh38.txt.gz",sep=""), header=F)
colnames(ref)<-c("UNIQID","rsID")
a1<-merge(a1,ref,by="UNIQID",all.x=T)

#remove NAs in rsID column
badc<-a1[is.na(a1$rsID),]
if (length(badc$CHR_10001)>0) {
	badc<-badc[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","rsID","min","max","UNIQID")]
	fwrite(badc, file=paste(args[5],"GRCh38_droppedvariants.txt.gz",sep=""), sep="\t", col.names=F, row.names=F, na=NA, quote=F, append=T)
	a1<-a1[!is.na(a1$rsID),]
}

#remove duplicates
badc<-a1[duplicated(a1$rsID) | duplicated(a1$rsID, fromLast=T),]
if (length(badc$CHR_10001)>0) {
	badc<-badc[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","rsID","min","max","UNIQID")]
	fwrite(badc, file=paste(args[5],"GRCh38_droppedvariants.txt.gz",sep=""), sep="\t", col.names=F, row.names=F, na=NA, quote=F, append=T)
	a1<-a1[!duplicated(a1$rsID) | duplicated(a1$rsID, fromLast=T),]
}

#remove chr and pos column, and the columns I added
a1<-a1[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","min","max","UNIQID")]
tmp<-which(colnames(a1)==args[1])
a1<-a1[,-..tmp]
tmp<-which(colnames(a1)==args[2])
a1<-a1[,-..tmp]

#save in file
fwrite(a1, file=paste(args[5],"input.gwas",sep=""), sep="\t", col.names=T, row.names=F, na=NA, quote=F, append=F)

#now do the other chromosomes
for (i in unique(a$CHR_10001)[-1]) {
	a1<-a[a$CHR_10001==i,]
	#make uniqID
	a1$min<-apply(a1[,c("ALLELE_10001","ALLELE_20001")], 1, FUN = min)
	a1$max<-apply(a1[,c("ALLELE_10001","ALLELE_20001")], 1, FUN = max)
	a1$UNIQID<-paste(a1$CHR_10001,a1$POS_10001,a1$min,a1$max,sep=":")
	#merge with reference file
	ref<-fread(paste(config$data$GRCh38,i,"_FUMA_DuplicatesResolved_dbSNP_v152_GRCh38.txt.gz",sep=""), header=F)
	colnames(ref)<-c("UNIQID","rsID")
	a1<-merge(a1,ref,by="UNIQID",all.x=T)
	#remove NAs in rsID column
	badc<-a1[is.na(a1$rsID),]
	if (length(badc$CHR_10001)>0) {
		badc<-badc[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","rsID","min","max","UNIQID")]
		fwrite(badc, file=paste(args[5],"GRCh38_droppedvariants.txt.gz",sep=""), sep="\t", col.names=F, row.names=F, na=NA, quote=F, append=T)
		a1<-a1[!is.na(a1$rsID),]
	}
	#remove duplicates
	badc<-a1[duplicated(a1$rsID) | duplicated(a1$rsID, fromLast=T),]
	if (length(badc$CHR_10001)>0) {
		badc<-badc[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","rsID","min","max","UNIQID")]
		fwrite(badc, file=paste(args[5],"GRCh38_droppedvariants.txt.gz",sep=""), sep="\t", col.names=F, row.names=F, na=NA, quote=F, append=T)
		a1<-a1[!duplicated(a1$rsID) | duplicated(a1$rsID, fromLast=T),]
	}
	#remove chr and pos column, and the columns I added
	a1<-a1[,-c("CHR_10001","POS_10001","ALLELE_10001","ALLELE_20001","min","max","UNIQID")]
	tmp<-which(colnames(a1)==args[1])
	a1<-a1[,-..tmp]
	tmp<-which(colnames(a1)==args[2])
	a1<-a1[,-..tmp]
	#save in file
	fwrite(a1, file=paste(args[5],"input.gwas",sep=""), sep="\t", col.names=F, row.names=F, na=NA, quote=F, append=T)
}