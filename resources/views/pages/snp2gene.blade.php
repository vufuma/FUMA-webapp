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

		<!-- <canvas id="canvas" style="display:none;"></canvas> -->

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

@section('scripts')

    {{-- Web (via npm) resources --}}
    @vite(['resources/js/app.js']);
	<script type="text/javascript">
 		$.ajaxSetup({
			headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
		});
		var status = "{{ $status }}";
		var id = "{{ $id }}";
		var page = "{{ $page }}";
		var subdir = "{{ Config::get('app.subdir') }}";
		var loggedin = "{{ Auth::check() }}";
	</script>
    {{-- This projectsown javascript resources --}}
    @vite([
        'resources/js/NewJobParameters.js',
        'resources/js/snp2gene.js',
        'resources/js/fuma.js',
        'resources/js/celltype.js',
        'resources/js/sidebar.js',
        'resources/js/geneMapParameters.js'])
    <script type="module">
        import CheckAll from "{{ Vite::asset('resources/js/NewJobParameters.js') }}";
        window.CheckAll = CheckAll
    </script>

	{{-- Imports from the project --}}
		<!--script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script-->


		<!--script type="text/javascript" src="{!! URL::asset('js/NewJobParameters.js') !!}?136"></script-->
		<!--script type="text/javascript" src="{!! URL::asset('js/geneMapParameters.js') !!}?135"></script-->
		<script type="text/javascript" src="{!! URL::asset('js/s2g_results.js') !!}?135"></script>
		<!--script type="text/javascript" src="{!! URL::asset('js/snp2gene.js') !!}?135a"></script-->

@endsection
