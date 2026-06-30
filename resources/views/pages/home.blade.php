@extends('layouts.master')

@section('content')
	<div class="col-md-8 offset-md-2" style="padding-top:50px;">
		<div style="text-align: center;">
			<h2>FUMA GWAS</h2>
			<h2>Functional Mapping and Annotation of Genome-Wide Association Studies</h2>
		</div>
		<br>

		</p>
		<strong style="font-size: large;">Announcements</strong><br>

		<div class="alert alert-info">
			<strong>June 30 2026: </strong>
			<p> FUMA has been updated to version 2.1.6.</p>
			<p> Each user can have up to <strong>10 QUEUED or RUNNING jobs</strong> per module. This limit applies separately to the SNP2GENE, GENE2FUNC, CellType, FLAMES, and QTLs Analysis modules. A warning will be displayed if you reach the limit when attempting to submit a new job.</p>
		</div>

		<div>
			<strong>June 26 2026: </strong>
			<p> FUMA has been updated to version 2.1.5.</p>
			<p> In uploading of pre-defined lead SNPs, you can now submit either a file with 3 columns (rsID, chr, pos in GRCh37) or a file with 1 column (rsID). Files need to have a header. See <a target="_blank" href="https://fuma-docs.readthedocs.io/en/latest/snp2gene/prepare_input_files.html#pre-defined-lead-snps">Documentation</a> for more details.</p>
		</div>

		<div>
			<strong>June 8 2026: </strong>
			<p> FUMA has been updated to version 2.1.0.</p>
			<p> Support for GRCh38 has been added and logs for formatting of input GWAS sumstat in SNP2GENE job has been added.</p>
		</div>

		<div>
			<strong>May 26 2026: </strong>
			<p> FUMA has been updated to version 2.0.0.</p>
			<p> Please navigate to <a href="{{ Config::get('app.subdir') }}/wiki">the wiki page</a> for an overview of the updates.</p>
			<p> <strong>IMPORTANT:</strong> Please make sure to read the documentation carefully before submiting your jobs. If you need assistance, please post your questions on <a target="_blank" href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a> with your job type (snp2gene, gene2func, celltype, flames, or xqtls) and your jobID.</p>
			<p> As this is a major update, expect that FUMA will be down periodically for bug fixes. Please be sure to always download your files after the analysis is done.</p>

		</div>

		<div>
			<strong>May 13 2026: </strong>
			<p> 1. We will perform an update and maintenance on FUMA starting from <strong>Sunday May 17 2026</strong>. During the maintenance, FUMA is not accessible. Expect up to 2 weeks of down time. Please make sure to download files needed for your analyses from FUMA before this date. Any QUEUED jobs would be stopped before the maintenance.</p>
			<p> 2. Any SNP2GENE jobs that were created prior to Jan 01 2023 will be removed from the FUMA server during the maintenance. This does not apply to public jobs. 

		</div>

		<div>
			<strong>April 17 2026: </strong> We will perform an update and maintenance on FUMA starting from Sunday May 17 2026. During the maintenance, FUMA is not accessible. Expect 1 to 2 weeks down time. Please make sure to download files needed for your analyses from FUMA before this date.  
		</div>

		<div>
			<strong>March 23 2026: </strong> The versions of FUMA and MAGMA have been updated in the donwloaded <i>Parameter settings</i> file. For future reference, please refer to the version as recorded in the <i>i</i> icon in the top right of the page or goes to the <i>Updates</i> page for the version history for the correct information. 
		</div>
		<div>
			<strong>February 9 2026: </strong> New SNP2GENE jobs retention policy: each user can store a maximum of 100 SNP2GENE jobs on the FUMA server. Please keep continuing to delete old jobs that you do not need anymore. You can download all the files and associated images for future use. 
		</div>
		<p>

		<p>
			<strong style="font-size: large;">About FUMA</strong><br>
			FUMA is a platform that can be used to annotate, prioritize, visualize and interpret GWAS results.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a> module takes GWAS summary statistics as an input,
			and provides extensive functional annotation for all SNPs in genomic areas identified by lead SNPs.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a> module takes a list of gene IDs (as identified by SNP2GENE or as provided manually)
			and annotates genes in biological contexts.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/celltype">Cell Type</a> module takes MAGMA gene analysis result (as an output from SNP2GENE or as provided manually) and predicts relevant cell types.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/flames">FLAMES</a> module identifies effector genes per genomic risk locus defined from SNP2GENE outputs.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/xqtls">QTLs Analysis</a> module investigates the potential functional mechanisms underlying GWAS associations by integrating with various QTLs.
			<br/>
			To submit your own GWAS, login is required for security reason.
			If you have not registered yet, you can do so from <a href="{{ Config::get('app.subdir') }}/register">here</a>.
			<br/>
			You can browse public results of FUMA (including example jobs) from <a href="{{ Config::get('app.subdir') }}/browse">Browse Public Results</a> without registration or login.
		</p>
		<p>
			Please post any questions, suggestions and bug reports on Google Forum: <a target="_blank" href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a>.<br>
		</p>
		
		<p>
			<strong style="font-size: large;">Citation</strong><br>
			<strong> When using SNP2GENE or GENE2FUNC modules, please cite the following: </strong> <br>
			K. Watanabe, E. Taskesen, A. van Bochoven and D. Posthuma. Functional mapping and annotation of genetic associations with FUMA. <i>Nat. Commun.</i> <b>8</b>:1826. (2017).<br><a href="{{ Config::get('app.subdir') }}/links">links</a>
			<a target="_blank" href="https://www.nature.com/articles/s41467-017-01261-5">https://www.nature.com/articles/s41467-017-01261-5</a>
			<br>
			<strong>When using the Cell Type module, please cite the following:</strong><br>
			K. Watanabe, M. Umicevic Mirkov, C. de Leeuw, M. van den Heuvel and D. Posthuma. Genetic mapping of cell type specificity for complex traits. <i>Nat. Commun.</i> <b>10</b>:3222. (2019).<br>
			<a target="_blank" href="https://www.nature.com/articles/s41467-019-11181-1">https://www.nature.com/articles/s41467-019-11181-1</a>
			<br>
			Phung T, Seoane S, Li W, Brouwer R, Posthuma D. Identification of cell types associated with 14 brain phenotypes from more than 10 million single cells. [Preprint]. 2025 December 09. DOI: 10.64898/2025.12.05.692533
			<br>
			<strong>When using the FLAMES module, please cite the following:</strong><br>
			Schipper, M., de Leeuw, C.A., Maciel, B.A.P.C. et al. Prioritizing effector genes at trait-associated loci using multimodal evidence. Nat Genet 57, 323–333 (2025). https://doi.org/10.1038/s41588-025-02084-7
			<br>
			<strong>Depending on which results you are going to report, please also cite the original study of data sources/tools used in FUMA
			(references are available at <a href="{{ Config::get('app.subdir') }}/links">links</a> or
			<a href="{{ Config::get('app.subdir') }}/tutorial#celltype">tutorial for the cell type specificity analysis</a> for scRNA-seq data). </strong>
		</p>
		<br>

		<div class="row">
			<div class="col-md-6 col-xs-6 col-sm-6" style="text-align:center; padding: 20px;">
				<div style="background-color: #dfdfdf; padding-top:20px; padding-bottom:20px;">
					<!-- <h4 class="blinking" style="color:#000099">Start from here with GWAS summary statistics</h4> -->
					<button id="snp2genebtn" class="btn btn-primary">SNP2GENE</button>
					<br><br>
					<img src="{{ URL::asset('/image/homeSNP2GENE.png') }}" style="width:90%;" alt="SNP2GENE example pages">
				</div>
			</div>
			<div class="col-md-6 col-xs-6 col-sm-6" style="text-align:center; padding: 20px;">
				<div style="background-color: #dfdfdf; padding-top:20px; padding-bottom:20px;">
					<!-- <h4 class="blinking" style="color:#000099">Start from here with a list of genes</h4> -->
					<button id="gene2funcbtn" class="btn btn-success">GENE2FUNC</button>
					<br><br>
					<img src="{{ URL::asset('/image/homeGENE2FUNC.png') }}" style="width:90%;" alt="GENE2FUNC example pages">
				</div>
			</div>
		</div>
	</div>
	<br>
@endsection

@section('scripts')
	{{-- Imports from the web --}}

	{{-- Imports from the project --}}

	{{-- Hand written ones need module type because of vite jquery async loading--}}
	<script type="module">
		import "{{ Vite::appjs('app.js') }}";
		var loggedin = "{{ Auth::check() }}";
		$(function(){
			$('#snp2genebtn').on('click', function(){
				window.location.href="{{ Config::get('app.subdir') }}/snp2gene";
			});

			$('#gene2funcbtn').on('click', function(){
				window.location.href="{{ Config::get('app.subdir') }}/gene2func";
			});
		});
	</script>

@endsection
