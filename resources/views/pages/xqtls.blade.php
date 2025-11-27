@extends('layouts.master')

@section('stylesheets')
@endsection


@section('content')
<style> 
.accordion-button.accordion-highlight {
background-color: #efeff8ff;
border-color: rgba(0,0,0,0.1);
}
</style>

<div id="wrapper" class="active">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav" id="sidebar-menu">
            <li class="sidebar-brand"><a id="menu-toggle">
                    <i id="main_icon" class="fa fa-chevron-left"></i>
                </a></li>
        </ul>
        <ul class="sidebar-nav" id="sidebar">
            <li class="active"><a href="#newquery">New Query<i class="sub_icon fa fa-upload"></i></a></li>
            <li ><a href="#queryhistory">Query History<i class="sub_icon fa fa-history"></i></a></li>
            <div id="resultSide">
                <li><a href="#xqtlTables">Summary<i class="sub_icon fa fa-table"></i></a></li>
            </div>
        </ul>
    </div>

    <div id="page-content-wrapper">
        <div class="page-content inset">  
            <div id="newquery" class="sidePanel container" style="padding-top:50px;">
                <div class ="col">
                    <div class="container" style="padding-top:50px;">
                        <div style="text-align: left;">
                            <h3>xQTLs Analysis</h3>
                            <h5 style="color: #00004d"> Compute genetic correlation for a genomic region of interest (for example, a genomic risk loci) with various QTLs datasets. </h5>
                            <div id="uploadData">
                                <table class="table table-bordered inputTable" id="xqtlsAnalysis" style="width: auto;">
                                    <tr>
                                        <td> GWAS summary statistics file: 
                                            <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Upload a tab-delimited text file with header containing GWAS summary statistics with the following columns in this specific order: CHR, POS, REF, ALT, BETA, P. The position can be in GRCh37 or GRCh38 coordinates. If in GRCh38 coordinates, please check the box below.">
                                                <i class="fa-regular fa-circle-question"></i>
                                            </a>
                                        </td>
                                        <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary" /></td>
                                    </tr>
                                    <tr>
                                        <td>Genome Build
                                            <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Select the genome build of your GWAS summary statistics file.">
                                                <i class="fa-regular fa-circle-question"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <div><input type="radio" name="build", value="grch37"><label>GRCh37</label></div>
                                            <div><input type="radio" name="build", value="grch38"><label>GRCh38</label></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Information about the genomic region of interest: 
                                            <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Provide the chromosome number, start and end position of the genomic region of interest.">
                                                <i class="fa-regular fa-circle-question"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="inputSpan">Chromosome: <input type="text" class="form-control"
                                                        id="chrcol" name="chr"></span>
                                            <span class="inputSpan">Start: <input type="text" class="form-control"
                                                        id="startpos" name="start"></span>
                                            <span class="inputSpan">End: <input type="text" class="form-control"
                                                        id="endpos" name="end"></span>
                                        </td>
                                    <tr>
                                        <td>Available datasets:</td>
                                        <td>
                                            <h2 style="color: #00004d; font-size:16px;">eQTLs Datasets</h2>

                                            <!-- GTEx v10 -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlGtexv10">
                                                        GTEx v10
                                                    </button>
                                                </h3>

                                                <div class="accordion-collapse collapse" id="eqtlGtexv10">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.eqtls.gtex_v10_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <p></p>

                                            <!-- eQTL catalog -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlCatalog">
                                                        eQTL Catalog
                                                    </button>
                                                </h3>

                                                <div class="accordion-collapse collapse" id="eqtlCatalog">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.eqtls.eqtl_catalog_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <h2 style="color: #00004d; font-size:16px;">sQTLs Datasets</h2>

                                            <!-- GTEx v10 -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlGtexv10">
                                                        GTEx v10
                                                    </button>
                                                </h3>

                                                <div class="accordion-collapse collapse" id="sqtlGtexv10">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.sqtls.gtex_v10_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <h2 style="color: #00004d; font-size:16px;">apaQTLs Datasets</h2>

                                            <!-- GTEx v10 -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#apaqtlGtexv10">
                                                        GTEx v10
                                                    </button>
                                                </h3>

                                                <div class="accordion-collapse collapse" id="apaqtlGtexv10">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.apaqtls.gtex_v10_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>

                                </table>

                                <input type="submit" value="Submit" class="btn btn-primary mt-3" id="xqtlsSubmit" name="xqtlsSubmit" /><br><br>
                                {{ html()->form()->close() }}
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="queryhistory" class="sidePanel container" style="padding-top:50px; display: none">
                <div class ="col">
                    <div class="container" style="padding-top:50px;">
                        <div style="text-align: center;">
                            <h3>Query History</h3>
                            <h5 style="color: #00004d"> Review your past xQTLs analysis queries. </h5>
                            <div id="historyData">
                                <table class="table table-bordered inputTable" id="xqtlsHistory" style="width: auto;">
                                    <thead>
                                        <tr>
                                            <th>Job ID</th>
                                            <th>Title</th>
                                            <th>Submit date</th>
                                            <th>Link</td>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historyBody">
                                        <tr>
                                            <td colspan="5" style="Text-align:center;">Retrieving data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('xqtls.result')

        </div>

    </div>
            

</div>


@endsection

@push('vite')
    @vite([
        'resources/js/utils/sidebar.js',
        'resoures/js/utils/xqtls.js',
        'resources/js/utils/browse.js'])
@endpush

@push('page_scripts')
    <script type="module">
        window.loggedin = "{{ Auth::check() }}";
            window.setXqtlsPageState(
                "{{ $status }}",
                "{{ $id }}",
                "xqtls",
                "{{ $page }}",
                "",
                "{{ Auth::check() }}"
            );
    </script>

    <script type="module">
        import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { BrowseSetup } from "{{ Vite::appjs('utils/browse.js') }}";
        import { XQTLSSetup } from "{{ Vite::appjs('utils/xqtls.js') }}";
        $(function(){
            SidebarSetup();
            BrowseSetup();
            XQTLSSetup();
        })
    </script>

@endpush