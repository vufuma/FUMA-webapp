<h3 id="table-columns">Table Columns</h3>
<ul>
  <li><p>Genomic risk loci</p>
  <ul>
    <li><strong>Genomic locus</strong> : Index of genomic rick loci.</li>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>rsID</strong> : rsID of the top lead SNP based on dbSNP build 146.</li>
    <li><strong>chr</strong> : chromosome of top lead SNP</li>
    <li><strong>pos</strong> : position of top lead SNP on hg19</li>
    <li><strong>P-value</strong> : P-value of top lead SNP (from the input file).</li>
    <li><strong>start</strong> : Start position of the locus.</li>
    <li><strong>end</strong> : End postion of the locus.</li>
    <li><strong>nSNPs</strong> : Number of the unique canidate SNPs in the genomic locus, including non-GWAS-tagged SNPs (which are extracted from 1000G).
      Candidate SNPs are all SNPs that are in LD (give user-defined r<sup>2</sup>) with any of independent significant SNPs and either have a P-value below the user defined threshold or are only available in 1000G.
    </li>
    <li><strong>nGWASSNPs</strong> : The number of unique GWAS-tagged candidate SNPs in the genomic locus which is available in the GWAS summarys statistics input file. This is a subset of "nSNPs".</li>
    <li><strong>nIndSigSNPs</strong> : Number of the independent (at user defined r<sup>2</sup>) significant SNPs in the genomic locus.</li>
    <li><strong>IndSigSNPs</strong> : rsID of the independent significant SNPs in the genomic locus.</li>
    <li><strong>nLeadSNPs</strong> : Number of lead SNPs in the genomic locus.
      Lead SNPs are are subset of independent significant SNPs at r<sup>2</sup> 0.1.</li>
    <li><strong>LeadSNPs</strong> : rsID of lead SNPs in the genomic locus.</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>lead SNPs</p>
  <ul>
    <li><strong>No</strong> : Index of lead SNPs</li>
    <li><strong>Genomic Locus</strong> : Index of assigned genomic locus matched with "Genomic risk loci" table.
      Multiple lead SNPs can be assigned to the same genomic locus.</li>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>rsID</strong> : rsID based on dbSNP build 146.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : position on hg19</li>
    <li><strong>P-value</strong> : P-value (from the input file).</li>
    <li><strong>nIndSigSNPs</strong> : Number of independent significant SNPs which are in LD with the lead SNP at r<sup>2</sup> 0.1.</li>
    <li><strong>IndSigSNPs</strong> : rsID of independent significant SNPs which are in LD with the lead SNP at r<sup>2</sup> 0.1.</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>independent significant SNPs (Independent significant SNPs)</p>
  <p>All independent lead SNPs identified by FUMA.</p>
  <ul>
    <li><strong>No</strong> : Index of lead SNPs</li>
    <li><strong>Genomic Locus</strong> : Index of assigned genomic locus matched with "Genomic risk loci" table.
      Multiple independent lead SNPs can be assigned to the same genomic locus.</li>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>rsID</strong> : rsID based on dbSNP build 146.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : position on hg19</li>
    <li><strong>P-value</strong> : P-value (from the input file).</li>
    <li><strong>nSNPs</strong> : Number of SNPs in LD with the lead SNP given r<sup>2</sup>, including non-GWAS-tagged SNPs (which are extracted from 1000G).</li>
    <li><strong>nGWASSNPs</strong> : Number of GWAS-tagged SNPs in LD with the lead SNP given r<sup>2</sup>. This is a subset of "nSNPs".</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>SNPs</p>
  <p>All candidate SNPs (SNPs which are in LD of any independent lead SNPs) with annotations.
    Note that depending on your mapping criterion, not all candidate SNPs displaying in this table are mapped to genes.</p>
  <ul>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>rsID</strong> : rsID based on dbSNP build 146.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : position on hg19</li>
    <li><strong>ref</strong> : Reference allele. Non-effect allele if it is provided in the input GWAS summary statistics file. If not, this is the reference (major) allele in 1000G.</li>
    <li><strong>alt</strong> : Alternative allele. Effect allele if it is provided in the input GWAS summary statistics file. If not, this is the alternative (minor) allele in 1000G.</li>
    <li><strong>MAF</strong> : Minor allele frequency computed based on 1000G.</li>
    <li><strong>gwasP</strong> : P-value provided in the input GWAS summary statistics file.
      Non-GWAS tagged SNPs (which do not exist in input file but are extracted from the reference panel) have "NA" instead.
    </li>
    <li><strong>or</strong> : Odds ratio provided in the input GWAS summary statistics file if available.
      Non-GWAS tagged SNPs (which do not exist in input file but are extracted from the reference panel) have "NA" instead.
    </li>
    <li><strong>beta</strong> : Beta provided in the input GWAS summary statistics file if available.
      Non-GWAS tagged SNPs (which do not exist in input file but are extracted from the reference panel) have "NA" instead.
    </li>
    <li><strong>se</strong> : Standard error provided in the input GWAS summary statistics file if available.
      Non-GWAS tagged SNPs (which do not exist in input file but are extracted from the reference panel) have "NA" instead.
    </li>
    <li><strong>r2</strong> : The maximum r2 of the SNP with one of the independent significant SNPs.</li>
    <li><strong>IndSigSNP</strong> : rsID of the independent significant SNP which has the maximum r2 with the SNP.</li>
    <li><strong>Genomic locus</strong> : Index of the genomic risk loci matching with "Genomic risk loci" table.</li>
    <li><strong>nearestGene</strong> : The nearest Gene of the SNP. Genes are ecoded in symbol, if it is available otherwise Ensembl ID.
      Genes here include all transcripts from Ensembl gene build 85 includeing non-protein coding genes and RNAs.</li>
    <li><strong>dist</strong> : Distance to the nearest gene.</li>
    <li><strong>func</strong> : Functional consequence of the SNP on the gene obtained from ANNOVAR. For exonic SNPs, detailed annotation (e.g. non-synonymous, stop gain and so on) is available in the ANNOVAR table (annov.txt).</li>
    <li><strong>CADD</strong> : CADD score which is computed based on 67 annotations. The higher the score, the more deleterious the SNP is. 12.37 is the suggested threshold by Kicher et al (2014).</li>
    <li><strong>RDB</strong> : RegulomeDB score which is a categorical score (from 1a to 7). 1a is the highest score for SNPs with the most biological evidence to be a regulatory element.</li>
    <li><strong>minChrState</strong> : The minimum 15-core chromatin state across 127 tissue/cell type.</li>
    <li><strong>commonChrState</strong> : The most common 15-core chromatin state across 127 tissue/cell types.</li>
  </ul>
  </li>
  <span class="info"><i class="fa fa-info"></i>
    Complete annotations of 15-core chromatin state (for every 127 epigenomes) are available in the "annot.txt" from download.
  </span><br/>
</ul>
<ul>
  <li><p>ANNOVAR</p>
  <p>Since one SNP can be annotated to multiple positional information, the table of ANNOVAR output is separated from SNPs table.
    This table contains unique SNP-annotation combinations.</p>
  <ul>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : position on hg19</li>
    <li><strong>Gene</strong> : ENSG ID</li>
    <li><strong>Symbol</strong> : Gene Symbol</li>
    <li><strong>Distance</strong> : Distance to the gene</li>
    <li><strong>Function</strong> : Positional annotation</li>
    <li><strong>Exonic function</strong> : Functional annotation of exonic SNPs</li>
    <li><strong>Exon</strong> : Index of exon</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>Mapped genes</p>
  <p>The genes which are mapped by SNPs in the SNPs table based on user-defined mapping parameters.
    Columns with posMap or eqtlMap in the parentheses are only available when positional or eQTL mapping is performed, respectively.
  </p>
  <ul>
    <li><strong>Gene</strong> : ENSG ID</li>
    <li><strong>Symbol</strong> : Gene Symbol</li>
    <li><strong>entrezID</strong> : entrez ID</li>
    <li><strong>Genomic locus</strong> : Index of genomic loci where mapped SNPs are from. This could contain more than one interval in the case that eQTLs are mapped to genes from distinct genomic risk loci.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>start</strong> : gene starting position</li>
    <li><strong>end</strong> : gene ending position</li>
    <li><strong>strand</strong> : strand of gene</li>
    <li><strong>status</strong> : status of gene from Ensembl</li>
    <li><strong>type</strong> : gene biotype from Ensembl</li>
    <li><strong>HUGO</strong> : HUGO (HGNC) gene symbol</li>
    <li><strong>posMapSNPs</strong> (posMap): Number of the SNPs mapped to gene based on positional mapping (after functional filtering if parameters are given).</li>
    <li><strong>posMapMaxCADD</strong> (posMap): The maximum CADD score of mapped SNPs by positional mapping.</li>
    <li><strong>eqtlMapSNPs</strong> (eqtlMap): Number of the SNPs mapped to the gene based on eQTL mapping.</li>
    <li><strong>eqtlMapminP</strong> (eqtlMap): The minimum eQTL P-value of mapped SNPs.</li>
    <li><strong>eqtlMapmin!</strong> (eqtlMap): The minimum eQTL FDR of mapped SNPs.</li>
    <li><strong>eqtlMapts</strong> (eqtlMap): Tissue types of mapped eQTL SNPs.</li>
    <li><strong>eqtlDirection</strong> (eqtlMap): consecutive direction of mapped eQTL SNPs.</li>
    <li><strong>minGwasP</strong> : The minimum P-value of mapped SNPs.</li>
    <li><strong>IndSigSNPs</strong> : rsID of the all independent significant SNPs of mapped SNPs.</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>eQTL</p>
  <p>This table is only shown wheneQTL mapping is performed.
   The table contains unique pairs of SNP-gene-tissue, therefore, a SNP could appear multiple times.</p>
  <ul>
    <li><strong>uniqID</strong> : Unique ID of SNPs consisting of chr:position:allele1:allele2 where alleles are alphabetically ordered.</li>
    <li><strong>chr</strong> : chromosome</li>
    <li><strong>pos</strong> : position on hg19</li>
    <li><strong>DB</strong> : Data source of eQTLs. Currently GTEx, BloodeQTL, BIOS and BRAINEAC are available. Please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTL</a> section for details.</li>
    <li><strong>tissue</strong> : tissue type</li>
    <li><strong>Gene</strong> : ENSG ID</li>
    <li><strong>Symbol</strong> : Gene symbol</li>
    <li><strong>P-value</strong> : P-value of eQTLs</li>
    <li><strong>FDR</strong> : FDR of eQTLs. Note that the method to compute FDR differs between data sources. Please refer to the <a href="{{ Config::get('app.subdir') }}/tutorial#eQTLs">eQTL</a> section for details.</li>
    <li><strong>tz</strong> : T-statistics or z score depending on data source.</li>
  </ul>
  </li>
</ul>
<ul>
  <li><p>GWAScatalog</p>
    <p>List of SNPs reported in GWAScatalog which are candidate SNPs of your GWAS summary statistics. <br/>
      <span class="info"><i class="fa fa-info"></i>
        The table does not show all columns available. The complete table is available by downloading.
      </span>
    </p>
    <ul>
      <li><strong>Genomic locus</strong> : Index of genomic risk loci.</li>
      <li><strong>lead SNP</strong> : The lead SNP of the SNP in GWAScatalog.</li>
      <li><strong>chr</strong> : chromosome</li>
      <li><strong>bp</strong> : position on hg19</li>
      <li><strong>snp</strong> : rsID of reported SNP in GWAS catalog</li>
      <li><strong>PMID</strong> : PubMed ID</li>
      <li><strong>Trait</strong> : The trait reported in GWAScatalog</li>
      <li><strong>FirthAuth</strong> : First author reported in GWAScatalog</li>
      <li><strong>Date</strong> : Date added in GWAScatalog</li>
      <li><strong>P-value</strong> : Reported P-value</li>
    </ul>
  </li>
</ul>
<ul>
  <li><p>Parameters</p>
  <p>The table of input parameters.
    The downloadable file is a config file with INI format.
  </p>
  <ul>
    [jobinfo]
    <li><strong>created_at</strong> : Date of job created</li>
    <li><strong>title</strong> : Job title</li>
    [inputfiles]
    <li><strong>gwasfile</strong> : File name of GWAS summary statistics</li>
    <li><strong>leadSNPsfile</strong> : File name of pre-defined lead SNPs if provided.</li>
    <li><strong>addleadSNPs</strong> : 1 if option is checked, 0 otherwise. If pre-defined lead SNPs are not provided, it is always 1.</li>
    <li><strong>regionsfile</strong> : File name of pre-defined genetic regions if provided.</li>
    <li><strong>**col</strong> : The column names of input GWAS summary statistics file if provided.</li>
    [params]
    <li><strong>N</strong> : Sample size of GWAS</li>
    <li><strong>exMHC</strong> : 1 to exclude MHC region, 0 otherwise</li>
    <li><strong>extMHC</strong> : user defined MHC region if provided, NA otherwise</li>
    <li><strong>genetype</strong> : All selected gene type.</li>
    <li><strong>leadP</strong> : the maximum threshold of P-value to be lead SNP</li>
    <li><strong>r2</strong> : the minimum threshold for SNPs to ne in LD of the lead SNPs</li>
    <li><strong>gwasP</strong> : the maximum threshold of P-value to be candidate SNP</li>
    <li><strong>pop</strong> : The population of reference panel</li>
    <li><strong>MAF</strong> : the minimum minor allele frequency based on 1000 genome reference of given population</li>
    <li><strong>Incl1KGSNPs</strong> : 1 to include non-GWAS-tagged SNPs from reference panel, 0 otherwise</li>
    <li><strong>mergeDist</strong> : The maximum distance between LD blocks to merge into interval</li>
    [posMap]
    <li><strong>posMap</strong> : 1 to perform positional mapping, 0 otherwise</li>
    <li><strong>posMapWindowSize</strong> : If provided, this distance is used as the maximum distance between SNPs to genes. Otherwise "NA".</li>
    <li><strong>posMapAnnot</strong> : Functional consequences of SNPs on genes to map.</li>
    <li><strong>posMapCADDth</strong> : The minimum CADD score for SNP filtering</li>
    <li><strong>posMapRDBth</strong> : The minimum RegulomeDB score for SNP filtering</li>
    <li><strong>posMapChr15</strong> : Select tissue/cell types, NA otherwise</li>
    <li><strong>posMapChr15Max</strong> : The maximum 15-core chromatin state</li>
    <li><strong>posMapChr15Meth</strong> : The method of chromatin state filtering</li>
    [eqtlMap]
    <li><strong>eqtlMap</strong> : 1 to perform eQTL mapping, 0 otherwise</li>
    <li><strong>eqtlMaptss</strong> : Selected tissue typed for eQTL mapping</li>
    <li><strong>eqtlMapSig</strong> : 1 to use only significant snp-gene pairs, 0 otherwise</li>
    <li><strong>eqtlMapP</strong> : The P-value threshold for eQTLs if <code> eqtlMap significant only</code> is not selected.</li>
    <li><strong>eqtlMapCADDth</strong> : The minimum CADD score for SNP filtering</li>
    <li><strong>eqtlMapRDBth</strong> : The minimum RegulomeDB score for SNP filtering</li>
    <li><strong>eqtlMapChr15</strong> : Select tissue/cell types, NA otherwise</li>
    <li><strong>eqtlMapChr15Max</strong> : The maximum 15-core chromatin state</li>
    <li><strong>eqtlMapChr15Meth</strong> : The method of chromatin state filtering</li>
  </ul>
  </li>
</ul>