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
            <div id="pageData" data-page-data="{}"></div>
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
        'resources/js/s2g_results'])
@endpush

@push('page_scripts')
    {{-- Web (via npm) resources --}}
	<script>
		window.loggedin = "{{ Auth::check() }}";
        console.log(`Page {{ $page }} LoggedIn ${window.loggedin}`)
        const pageData = document.querySelector('#pageData');
        pageData.setAttribute('data-page-data', `{
            "status": "{{ $status }}",
            "id": "{{ $id }}",
            "page": "{{ $page }}",
            "subdir": "",
            "loggedin": "{{ Auth::check() }}"
        }`);

	</script>

    {{-- Imports from the project using Vite alias macro --}}
    <script type="module">
        console.log("Loading modules");
        debugger;
        import CheckAll from "{{ Vite::appjs('NewJobParameters.js') }}";
        window.CheckAll = CheckAll;
        import NewJobSetup from "{{ Vite::appjs('NewJobParameters.js') }}";
        import Snp2GeneSetup from "{{ Vite::appjs('snp2gene.js') }} ";
        import CellTypeSetup from "{{ Vite::appjs('celltype.js') }}";
        import GeneMapSetup from "{{ Vite::appjs('geneMapParameters.js') }}";
        import SidebarSetup from "{{ Vite::appjs('sidebar.js') }}"
        $(function(){
            SidebarSetup();
            NewJobSetup();
            CellTypeSetup();
            GeneMapSetup();
        });
    </script>

	{{-- Imports from the project --}}
    <!--script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script-->
    <!--script type="text/javascript" src="{!! URL::asset('js/NewJobParameters.js') !!}?136"></script-->
    <!--script type="text/javascript" src="{!! URL::asset('js/geneMapParameters.js') !!}?135"></script-->
    <!--script type="text/javascript" src="{!! URL::asset('js/s2g_results.js') !!}?135"></script-->
    <!--script type="text/javascript" src="{!! URL::asset('js/snp2gene.js') !!}?135a"></script-->

@endpush
