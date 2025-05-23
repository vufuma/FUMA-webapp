@extends('layouts.simple')

@section('stylesheets')
<!--link href="https://cdn.datatables.net/v/dt/dt-1.13.4/b-2.3.6/sl-1.6.2/datatables.min.css" rel="stylesheet"/-->
@endsection


@section('content')
<canvas id="canvas" style="display:none;"></canvas>

<br><br>


<div class ="col">
<div class="container" >
	<div class ="row">
		<div class="col-md-9 col-xs-9 col-sm-9">
			<div id='title' style="text-align: center;">
				<h4>Regional plot</h4>
			</div>
			<span class="info"><i class="fa fa-info"></i>
				For SNPs colored grey in the plots of GWAS P-value, CADD, RegulomeDB score and eQTLs, please refer the
				legend at the bottom of the page.
			</span><br>
			<span class="info"><i class="fa fa-info"></i>
				For details of color-code of genes, please refer the legend at the bottom of the page.
			</span><br>
			<div class="float-end"><button id="plotclear" class="btn btn-light" style="right: 30px;">Clear</button></div>
			<br>
			Download the plot as
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("annotPlot","png");'>PNG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("annotPlot","jpeg");'>JPG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("annotPlot","svg");'>SVG</button>
			<button class="btn btn-default btn-xs ImgDown" onclick='ImgDown("annotPlot","pdf");'>PDF</button>

			<form method="post" target="_blank" action="imgdown">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dir" id="annotPlotDir" val="" />
				<input type="hidden" name="jobID" id="annotPlotID" val="" />
				<input type="hidden" name="data" id="annotPlotData" val="" />
				<input type="hidden" name="type" id="annotPlotType" val="" />
				<input type="hidden" name="fileName" id="annotPlotFileName" val="" />
				<input type="submit" id="annotPlotSubmit" class="ImgDownSubmit" />
			</form>
			<div id="annotPlot"></div>
			<br>
			<div id="load" style="text-align:center;"></div>
			<div>
				<h4>Regulome DB</h4>
				<div id="RDBlegend"></div>
				*External link to RegulomeDB from SNP table (when one of the SNPs is clicked) will open a new tab.
				rsID does not always match since RegulomeDB used dbSNP build 141 (the rsID in FUMA is dbSNP build 146).
				Genomic position (bp on hg19) shown in the link of RegulomeDB is the position shown in the SNP table -
				1, since RegulomeDB used 0 based coordinate.
			</div>
			<br>
			<div>
				<h4>15-core chromatin state</h4>
				<div id="EIDlegend"></div>
				*When 15-core chromatin state is included in the plot and >30 cell types are selected, the labels of
				Y-axis are omitted.
				The order of the cell types is same as the legend table.
			</div>
			<br>
			<div>
				<h4>eQTLs</h4>
				The color of eQTLs are arbitrary. When P-value is not available (i.e. for CMC eQTLs), -log10 FDR is
				plotted in stead of P-value.
			</div>
			<br>
			<div id="SNPlegend">
				<h4>SNPs colored grey in the plots</h4>
				<strong>GWAS P-value</strong>: SNPs which are not in LD of any of significant independent lead SNPs in
				the selected region are colored grey.<br>
				<strong>CADD score</strong>: Only SNPs which are in LD of any of significant independent lead SNPs are
				displayed in the plot.
				Of those SNPs, SNPs which did not used for mapping (SNPs that were filtered by user defined parameters)
				are colored grey.<br>
				When positional mapping is performed, SNPs used for positional mapping are always colored non-grey
				colors.<br>
				When eQTL mapping is performed and eQTLs are plotted, SNPs used for eQTL mapping are also colored
				non-grey colors.
				If the option of eQTLs is not selected for the plot, SNPs which are not used for other mappings are
				colored grey even if they are used for eQTL mapping.<br>
				When chromatin interaction mapping is performed and chromatin interactions are plotted, SNPs used for
				chromatin interaction mapping are also colored non-grey colors.
				If the option of chromatin interactions is not selected for the plot, SNPs which are not used for other
				mappings are colored grey even if they are used for chromatin interaction mapping.<br>
				<strong>RegulomeDB score</strong>: Same as CADD score.<br>
				<strong>eQTLs</strong>: When eQTL mapping was performed and if there is any eQTL in the selected region,
				all eQTLs with user defined P-value threshold and tissue types are displayed.
				Of those eQTLs, eQTLs which did not used for eQTL mapping (eQTLs that were filtered by user defined
				parameters) are colored grey.<br>
			</div>
			<br>
			<div id="GeneLegnd">
				<h4>Color-code for genes</h4>
				<strong>Red</strong> : Mapped genes. Genes mapped by positional mapping are always colored red.
				Genes mapped by eQTL mapping are colore red only when the option of eQTLs is selected for the plot,
				otherwise those genes are considered as non-mapped genes.
				Genes mapped by chromatin interaction are colored red only when the option of chromatin interactions is
				selected for the plot, otherwise those genes are considered as non-mapped genes.<br>
				<strong>Blue</strong> : Non-mapped protein-coding genes.<br>
				<strong>Dark grey</strong> : Non-mapped non-coding genes.<br>
			</div>
		</div>
		<div class="col-md-3 col-xs-3 col-sm-3" style="text-align: center;">
			<div><h4>SNP annotations</h4></div>
			<div id="annotTable">
				click any SNP on the plot<br>
			</div>
		</div>
	</div>
</div>
</div>

<br><br>
@endsection

@push('vite')
    @vite([
        'resources/js/utils/annotPlot.js'])
@endpush

@push('page_scripts')
<script type="module">
	import { ImgDown, AnnotPlotSetup } from "{{ Vite::appjs('utils/annotPlot.js') }}";
		window.loggedin = "{{ Auth::check() }}";
		window.ImgDown = ImgDown;
		
        window.setAnnotPlotPageState (
			"{{$jobID}}",
			"{{$page}}",
			"{{ Config::get('app.subdir') }}",
			"{{ Auth::check() }}"
		)

		$(function(){
			AnnotPlotSetup(
				"jobs",
				"{{$type}}",
				"{{$rowI}}",
				"{{$GWASplot}}",
				"{{$CADDplot}}",
				"{{$RDBplot}}",
				"{{$eqtlplot}}",
				"{{$ciplot}}",
				"{{$Chr15}}",
				"{{$Chr15cells}}"				
			);
		});
</script>

@endpush