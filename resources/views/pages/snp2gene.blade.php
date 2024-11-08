@extends('layouts.master')


@section('content')
	<div id="wrapper" class="active">
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav" id="sidebar-menu">
				<li class="sidebar-brand"><a id="menu-toggle"><tab><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
			</ul>
			<ul class="sidebar-nav" id="sidebar">
				<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
				<li><a href="#geneMap">Redo gene mapping<i class="sub_icon fa fa-repeat"></i></a></li>
				<li><a href="#joblist-panel">My Jobs<i class="sub_icon fa fa-search"></i></a></li>
				<div id="GWplotSide">
					<li><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
				</div>
				<div id="Error5Side">
					<li><a href="#error5">ERROR:005<i class="sub_icon fa fa-exclamation-triangle"></i></a></li>
				</div>
				<div id="resultsSide">
					<li><a href="#summaryTable">Summary of results<i class="sub_icon fa fa-bar-chart"></i></a></li>
					<li><a href="#tables">Results<i class="sub_icon fa fa-table"></i></a></li>
					<li><a href="#downloads">Download<i class="sub_icon fa fa-download"></i></a></li>
				</div>
			</ul>
	</div>

		<div id="page-content-wrapper">
			<div class="page-content inset">
				@include('snp2gene.newjob')
				@include('snp2gene.geneMap')
				@include('snp2gene.joblist')

				@include('snp2gene.gwPlot')
				@include('snp2gene.error5')
				@include('snp2gene.summary')
				@include('snp2gene.result_tables')
				@include('snp2gene.filedown')
			</div>
		</div>
	</div>
@endsection

{{-- This projectsown javascript resources - in the header stylesheets section --}}
@push('vite')
    @vite([
        'resources/js/NewJobParameters.js',
        'resources/js/snp2gene.js',
        'resources/js/celltype.js',
        'resources/js/sidebar.js',
        'resources/js/geneMapParameters.js',
        'resources/js/s2g_results.js'])
@endpush

@push('page_scripts')
    {{-- Web (via npm) resources --}}
	<script type="module">
		window.loggedin = "{{ Auth::check() }}";
        console.log(`Page {{ $page }} LoggedIn ${window.loggedin}`)
		import { setPageState } from "{{ Vite::appjs('snp2gene.js') }}";
        setPageState(
			"",
            "{{ $status }}",
            "{{ $id }}",
            "{{ $page }}",
            "{{ Auth::check() }}"
		);

	</script>

    {{-- Imports from the project using Vite alias macro --}}
    <script type="module">
        console.log("Loading modules");
        import { CheckAll, loadParams } from "{{ Vite::appjs('NewJobParameters.js') }}";
        window.CheckAll = CheckAll;
		window.loadParams = loadParams;
		import { ImgDown, circosDown, Chr15Select, expImgDown } from "{{ Vite::appjs('s2g_results.js') }}"
		window.ImgDown = ImgDown;
		window.circosDown = circosDown;
		window.Chr15Select = Chr15Select;
		window.expImgDown = expImgDown;
		import { loadGeneMap } from "{{ Vite::appjs('geneMapParameters.js') }}";
		window.loadGeneMap = loadGeneMap;
		import { g2fbtn, checkPublish, checkPublishInput } from "{{ Vite::appjs('snp2gene.js') }} ";
		window.g2fbtn = g2fbtn;
		window.checkPublish = checkPublish;
		window.checkPublishInput = checkPublishInput;

        import { NewJobSetup } from "{{ Vite::appjs('NewJobParameters.js') }}";
        import { Snp2GeneSetup } from "{{ Vite::appjs('snp2gene.js') }} ";
        import { GeneMapSetup } from "{{ Vite::appjs('geneMapParameters.js') }}";
        import { SidebarSetup } from "{{ Vite::appjs('sidebar.js') }}"
        $(function(){
            SidebarSetup();
            NewJobSetup();
            Snp2GeneSetup();
            GeneMapSetup();
        });
    </script>
@endpush
