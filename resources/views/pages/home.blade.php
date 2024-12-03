@extends('layouts.master')

@section('content')
	<div class="container" style="padding-top:50px;">
		<div class="alert alert-danger">
			<p><strong>Here you can find a detailed list of the error codes. We kindly advise thorough consultation of this troubleshooting list prior to seeking assistance through Google groups. <a href="https://groups.google.com/g/fuma-gwas-users/c/N3HCEXBJ8Iw/m/utS6HxWoAAAJ">Troubleshooting List</a></strong></p>
		</div>

		<div style="text-align: center;">
			<h2>FUMA GWAS</h2>
			<h2>Functional Mapping and Annotation of Genome-Wide Association Studies</h2>
		</div>
		<br/>
		<p>
			<strong style="font-size: large;">About FUMA</strong><br/>
			FUMA is a platform that can be used to annotate, prioritize, visualize and interpret GWAS results.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/snp2gene">SNP2GENE</a> module takes GWAS summary statistics as an input,
			and provides extensive functional annotation for all SNPs in genomic areas identified by lead SNPs.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/gene2func">GENE2FUNC</a> module takes a list of gene IDs (as identified by SNP2GENE or as provided manually)
			and annotates genes in biological context.
			<br/>
			The <a href="{{ Config::get('app.subdir') }}/celltype">Cell Type</a> module takes MAGMA gene analysis result (as an output from SNP2GENE or as provided manually) and predicts relevant cell types.
			<br/>
			To submit your own GWAS, login is required for security reason.
			If you have not registered yet, you can do so from <a href="{{ Config::get('app.subdir') }}/register">here</a>.
			<br/>
			You can browse public results of FUMA (including example jobs) from <a href="{{ Config::get('app.subdir') }}/browse">Browse Public Results</a> without registration or login.
		</p>
		<p>
			Please post any questions, suggestions and bug reports on Google Forum: <a target="_blank" href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a>.<br/>
		</p>
		<p>
			<strong style="font-size: large;">Citation</strong><br/>
			When using FUMA, please cite the following.<br/>
			K. Watanabe, E. Taskesen, A. van Bochoven and D. Posthuma. Functional mapping and annotation of genetic associations with FUMA. <i>Nat. Commun.</i> <b>8</b>:1826. (2017).<br/><a href="{{ Config::get('app.subdir') }}/links">links</a>
			<a target="_blank" href="https://www.nature.com/articles/s41467-017-01261-5">https://www.nature.com/articles/s41467-017-01261-5</a>
			<br>
			When using cell type analysis, please cite the following.<br/>
			K. Watanabe, M. Umicevic Mirkov, C. de Leeuw, M. van den Heuvel and D. Posthuma. Genetic mapping of cell type specificity for complex traits. <i>Nat. Commun.</i> <b>10</b>:3222. (2019).<br/>
			<a target="_blank" href="https://www.nature.com/articles/s41467-019-11181-1">https://www.nature.com/articles/s41467-019-11181-1</a>
			<br>
			Depending on which results you are going to report, please also cite the original study of data sources/tools used in FUMA
			(references are available at <a href="{{ Config::get('app.subdir') }}/links">links</a> or
			<a href="{{ Config::get('app.subdir') }}/tutorial#celltype">tutorial for the cell type specificity analysis</a> for scRNA-seq data).
		</p>
		<br/>

		<div class="row">
			<div class="col-md-6 col-xs-6 col-sm-6" style="text-align:center; padding: 20px;">
				<div style="background-color: #dfdfdf; padding-top:20px; padding-bottom:20px;">
					<!-- <h4 class="blinking" style="color:#000099">Start from here with GWAS summary statistics</h4> -->
					<button id="snp2genebtn" class="btn btn-primary">SNP2GENE</button>
					<br/><br/>
					<img src="{{ URL::asset('/image/homeSNP2GENE.png') }}" align="middle" style="width:90%;">
				</div>
			</div>
			<div class="col-md-6 col-xs-6 col-sm-6" style="text-align:center; padding: 20px;">
				<div style="background-color: #dfdfdf; padding-top:20px; padding-bottom:20px;">
					<!-- <h4 class="blinking" style="color:#000099">Start from here with a list of genes</h4> -->
					<button id="gene2funcbtn" class="btn btn-success">GENE2FUNC</button>
					<br/><br/>
					<img src="{{ URL::asset('/image/homeGENE2FUNC.png') }}" align="middle" style="width:90%;">
				</div>
			</div>
		</div>
	</div>
	</br>
@endsection

@section('scripts')
	{{-- Imports from the web --}}

	{{-- Imports from the project --}}

	{{-- Hand written ones --}}
	<script type="text/javascript">
		var loggedin = "{{ Auth::check() }}";
		$(document).ready(function(){
			$('#snp2genebtn').on('click', function(){
				window.location.href="{{ Config::get('app.subdir') }}/snp2gene";
			});

			$('#gene2funcbtn').on('click', function(){
				window.location.href="{{ Config::get('app.subdir') }}/gene2func";
			});
		});
	</script>

@endsection
