@extends('layouts.master')


@section('content')
	<div id="wrapper" class="active">
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav" id="sidebar-menu">
				<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
			</ul>
			<ul class="sidebar-nav" id="sidebar">
				<li><a href="#GwasList">GWAS list<i class="sub_icon fa fa-search"></i></a></li>
				<span style="padding-left:10px;font-size:14px;"><b>Example input page</b></span>
				<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
				<div id="resultsSide">
					<span style="padding-left:10px;font-size:14px;"><b>SNP2GENE</b></span>
					<li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
					<li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
					<li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
					<li><a href="#downloads">Download<i class="sub_icon fa fa-download"></i></a></li>
				</div>
				<div id="resultsSideG2F">
					<span style="padding-left:10px;font-size:14px;"><b>GENE2FUNC</b></span>
					<li><a href="#g2f_summaryPanel">Summary<i class="sub_icon fa fa-table"></i></a></li>
					<li><a href="#expPanel">Heatmap<i class="sub_icon fa fa-th"></i></a></li>
					<li><a href="#tsEnrichBarPanel">Tissue specificity<i class="sub_icon fa fa-bar-chart"></i></a></li>
					<li><a href="#GeneSetPanel">Gene sets<i class="sub_icon fa fa-bar-chart"></i></a></li>
					<li><a href="#GeneTablePanel">Gene table<i class="sub_icon fa fa-table"></i></a></li>
				</div>
			</ul>
		</div>

		<canvas id="canvas" style="display:none;"></canvas>

		<div id="page-content-wrapper">
			<div class="page-content inset">
				@include('browse.gwaslist')
				@include('snp2gene.newjob')

				<!-- SNP2GENE result page -->
				@include('snp2gene.gwPlot')
				@include('snp2gene.summary')
				@include('snp2gene.result_tables')
				@include('snp2gene.filedown')

				<!-- GENE2FUNC result page -->
				@include('gene2func.summary')
				@include('gene2func.exp_heat')
				@include('gene2func.DEG')
				@include('gene2func.genesets')
				@include('gene2func.geneTable')
			</div>
		</div>
	</div>
@endsection

{{-- This projectsown javascript resources - in the header stylesheets section --}}
@push('vite')
    @vite([
        'resources/js/utils/sidebar.js',
        'resources/js/utils/browse.js',
		'resources/js/utils/s2g_results.js'])
@endpush

@push('page_scripts')

	{{-- Init page state --}}
	<script type = module>
		window.loggedin = "{{ Auth::check() }}";
		window.setBrowsePageState(
            "{{ $id }}",
            "{{ $page }}",
            "",
            "{{ Auth::check() }}"			
		);
	</script>

    <script type="module">
		import { ImgDown, circosDown, Chr15Select, expImgDown } from "{{ Vite::appjs('utils/s2g_results.js') }}";
		window.ImgDown = ImgDown;
		window.circosDown = circosDown;
		window.Chr15Select = Chr15Select;
		window.expImgDown = expImgDown;
        import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { BrowseSetup } from "{{ Vite::appjs('utils/browse.js') }}";
        $(function(){
            SidebarSetup();
            BrowseSetup();
        })
    </script>

@endpush
