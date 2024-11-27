@extends('layouts.master')


@section('content')
	<div id="wrapper" class="active">
		<div id="sidebar-wrapper">
			<ul class="sidebar-nav" id="sidebar-menu">
				<li class="sidebar-brand"><a id="menu-toggle"><i id="main_icon" class="fa fa-chevron-left"></i></a></li>
			</ul>
			<ul class="sidebar-nav" id="sidebar">
				<li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
				<li><a href="#geneMap">Redo gene mapping<i class="sub_icon fa fa-repeat"></i></a></li>
				<li><a href="#joblist-panel">My Jobs<i class="sub_icon fa fa-search"></i></a></li>
				<li id="GWplotSide"><a href="#genomePlots">Genome-wide plots<i class="sub_icon fa fa-bar-chart"></i></a></li>
				<li id="Error5Side"><a href="#error5">ERROR:005<i class="sub_icon fa fa-exclamation-triangle"></i></a></li>
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
	
@push('page_scripts')
    {{-- Web (via npm) resources --}}
	<script type="module">
		console.log("In init page")
		window.setS2GPageState(
			"",
			"{{ $status }}",
			"{{ $id }}",
			"{{ $page }}",
			"{{ Auth::check() }}"
		);
		await import("{{ Vite::appjs('pages/page_s2g.js') }}");
	</script>
@endpush
