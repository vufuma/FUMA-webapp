<h3 id="g2fOutputs">Outputs of GENE2FUNC</h3>
<div style="padding-left: 40px;">
	<h4><strong>1. Summary of input genes and download files</strong></h4>
	<p>
		1) <strong>Summary of input genes</strong><br>
		The table summarised the input genes and background genes.
		Input genes which are not used in the GENE2FUNC analyses due to lack of matching gene ID
		are also listed.
		Since the primary gene ID of FUMA is Ensembl ID and not all Ensembl IDs are mapped to unique
		entrez ID (NCBI gene ID), the number of unique entrez ID can be smaller than the number of
		input genes with Ensembl ID.
		Ensembl ID is used for expression heatmap and tissue specificity analyses,
		and entrez ID is used for gene set enrichment analysis.
		<br>
		2) <strong>Download files</strong><br>
		Results of GENE2FUNC can be downloaded as text file from here.
		<br>
		3) <strong>Parameters</strong><br>
		The table contains input parameters. This can be also downloaded from the option above.
		<br><br>
	</p>
	<h4><strong>2. Gene Expression Heatmap</strong></h4>
	<p>
		The heatmap displays two expression values.<br>
		1) <strong>Average expression per label</strong><br>
		This is an averaged expression value per label (e.g. tissue types or developmental stage)
		per gene following to winsorization at 50 and log 2 transformation with pseudocount 1.
		The expression value depends on the data set, RPKM (Read Per Kilobase per Million)
		for GTEx v6 and BrainSapn, TPM (Transcripts Per Million) for GTEx v7.
		This allows for comparison across labels and genes.
		Hence, cells filled in red represent higher expression compared to cells filled in blue across genes and labels.<br>
		2) <strong>Average of normalized expression per label</strong><br>
		This is the average of normalized expression (zero mean across samples)
		following to winsorization at 50 and log 2 transformation of the expression value with pseudocount 1.
		This allows comparison of gene expression across labels (horizontal comparison) within a gene.
		Thus expression values of different genes within a label (vertical comparison) are not comparable.
		Hence, cells filled in red represents higher expression of the genes in
		a corresponding label compared to other labels, but it DOES NOT represent
		higher expression compared to other genes.
	</p>
	<p>
		Labels (columns) and genes (rows) can be ordered by alphabetically or cluster (hierarchical  clustering).
		Hierarchical  clustering is performed using python scipy package (using "average" method).<br>
		The heatmap is downloadable in several file formats. Note that the image will be downloaded as displayed.
	</p>
	<img src="{!! URL::asset('/image/gene2funcHeatmap.png') !!}" style="width:60%"/>
	<br><br>

	<h4><strong>3. Tissue specificity</strong></h4>
	<p>
		Tissue specificity is tested using the differentially expressed genes
		defined for each label of each expression data set<br>
		<br>
		<strong>Differentially Expressed Gene (DEG) Sets</strong><br>
		DEG sets were pre-calculated by performing two-sided t-test for any one of labels against all others.
		For this, expression values were normalized (zero-mean) following to a log 2 transformation of expression value (EPKM or TPM).
		Genes which with P-value &le; 0.05 after Bonferroni correction and absolute log fold change &ge; 0.58 were
		defined as differentially expressed genes in a given label compared to others.
		On top of DEG, up-regulated DEG and down-regulated DEG were also pre-calculated by taking sign of t-statistics into account.
		<br><br>
	</p>
	<p>
		Input genes were tested against each of the DEG sets using the hypergeometric test.
		The background genes are genes that have average expression value > 1 in at
		least one of the labels and exist in the user selected background genes.
		Significant enrichment at Bonferroni corrected P-value &le; 0.05 are coloured in red.<br>
		<span class="info"><i class="fa fa-info"></i>
		Note that for DEG sets, Bonferroni correction is performed for each of up-regulated, down-regulated and both-sided DEG sets separately.
		</span><br><br>
		Results and images are downloadable as text files and in several image file formats.
	</p>
	<img src="{!! URL::asset('/image/gene2funcTs.png') !!}" style="width:60%"/>
	<br><br>

	<h4><strong>4. Gene Sets</strong></h4>
	<p>
		Hypergeometric tests are performed to test if genes of interest are overrepresented in any of  the pre-defined gene sets.
		Multiple test correction is performed per category, (i.e. canonical pathways, GO biological processes and so on, separately).
		Gene sets were obtained from MSigDB, WikiPathways and reported genes from the GWAS-catalog.
		The MSigDB and WikiPathways data were downloaded with entrez IDs and included without modification.
		The GWAS catalog data was downloaded with gene symbols and then converted to entrez ID using biomaRt.
		If a single gene symbol matched multiple entrez IDs, then all matching entrez IDs were included in the geneset.
		<br>
		The following files were used to make the GENE2FUNC genesets:
		<br>
		<table class="table table-bordered table-hover" style="width:auto">
			<thead>
				<th>GENE2FUNC name</th>
				<th>File used</th>
			</thead>
			<tbody>
				<tr>
					<td>Hallmark gene sets (MsigDB h)</td>
					<td>h.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Positional gene sets (MsigDB c1)</td>
					<td>c1.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Curated_gene_sets</td>
					<td>c2.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Chemical and Genetic pertubation gene sets (MsigDB c2)</td>
					<td>c2.cgp.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>All Canonical Pathways (MsigDB c2)</td>
					<td>c2.cp.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>BioCarta (MsigDB c2)</td>
					<td>c2.cp.biocarta.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>KEGG (MsigDB c2)</td>
					<td>c2.cp.kegg.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Reactome (MsigDB c2)</td>
					<td>c2.cp.reactome.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>microRNA targets (MsigDB c3)</td>
					<td>c3.mir.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>TF targets (MsigDB c3)</td>
					<td>c3.tft.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>All computational gene sets (MsigDB c4)</td>
					<td>c4.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Cancer gene neighborhoods (MsigDB c4)</td>
					<td>c4.cgn.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Cancer gene modules (MsigDB c4)</td>
					<td>c4.cm.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>GO biological processes (MsigDB c5)</td>
					<td>c5.go.bp.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>GO cellular components (MsigDB c5)</td>
					<td>c5.go.cc.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>GO molecular functions (MsigDB c5)</td>
					<td>c5.go.mf.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Oncogenic signatures (MsigDB c6)</td>
					<td>c6.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Immunologic signatures (MsigDB c7)</td>
					<td>c7.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>WikiPathways</td>
					<td>c2.cp.wikipathways.v2023.1.Hs.entrez.gmt</td>
				</tr>
				<tr>
					<td>Cell_type_signature (MSigDB c8)</td>
					<td>c8.all.v2023.1.Hs.entrez.gmt</td>
				</tr>
			</tbody>
		</table>
	</p>
	<p>
		The genesets used in the GENE2FUNC module can be downloaded here:
		<br>
		<div class="clickable" onclick='tutorialDownloadVariant("GENE2FUNC1")'>
		Genesets used in FUMA version 1.3.5d to 1.5.5.
		<img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 22M
		</div>
		<br>
		<div class="clickable" onclick='tutorialDownloadVariant("GENE2FUNC2")'>
		Genesets used in FUMA version 1.5.6 onwards.
		<img class="fontsvg" src="{{URL::asset('/image/download.svg')}}"/> 29M
		</div>
	</p>
	<p>
		The full results are downloadable as a text file at the top of the page. <br>
		In each category, plot view and table view are selectable.
		In the plot view, images are downloadable in several file formats.
	</p>
	<img src="{!! URL::asset('/image/gene2funcGS.png') !!}" style="width:70%"/>
	<br><br>

	<h4><strong>5. Gene Table</strong></h4>
	<p>
		Input genes are mapped to OMIM ID, UniProt ID, Drug ID of DrugBank and links to GeneCards.
		Drug IDs are assigned if the UniProt ID of the gene is one of the targets of the drug.<br>
		Each link to OMIM, Drugbank and GeneCards will open in a new tab.
	</p>
	<img src="{!! URL::asset('/image/gene2funcGT.png') !!}" style="width:70%"/>
</div>
