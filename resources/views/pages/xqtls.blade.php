@extends('layouts.master')

@section('stylesheets')
@endsection

@section('content')
<div id="wrapper" class="active">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav" id="sidebar-menu">
            <li class="sidebar-brand"><a id="menu-toggle">
                    <i id="main_icon" class="fa fa-chevron-left"></i>
                </a></li>
        </ul>
        <ul class="sidebar-nav" id="sidebar">
            <li class="active"><a href="#newquery">New Query<i class="sub_icon fa fa-upload"></i></a></li>
            <li class="active"><a href="#queryhistory">Query History<i class="sub_icon fa fa-history"></i></a></li>
            <div id="resultSide">
                <li><a href="#xqtlsResultsTable">Summary<i class="sub_icon fa fa-table"></i></a></li>
            </div>
        </ul>
    </div>

    <div id="page-content-wrapper">
        <div class="page-content inset">  
            <div id="newquery" class="sidePanel container" style="padding-top:50px;">
                <div class ="col">
                    <div class="container" style="padding-top:50px;">
                        <div style="text-align: center;">
                            <h3>xQTLs Analysis</h3>
                            <p> Compute genetic correlation for a genomic region of interest (for example, a genomic risk loci) with various QTLs datasets. </p>
                            <div id="uploadData">
                                <table class="table table-bordered inputTable" id="xqtlsAnalysis" style="width: auto;">
                                    <tr>
                                        <td> GWAS summary statistics file: </td>
                                        <td><input type="file" class="form-control-file" name="GWASsummary" id="GWASsummary" /></td>
                                    </tr>
                                    <tr>
                                        <td> Information about the genomic region of interest: </td>
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
                                            <!-- eQTLs GTEx v8 -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlGtexv8">
                                                        eQTLs GTEx v8
                                                    </button>
                                                </h5>
                                                <div class="accordion-collapse collapse" id="eqtlGtexv8">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('snp2gene.xqtls_options.eqtls.eqtls_gtexv8_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- eQTL Catalog -->
                                            <div class="accordion-item" style="padding:0px; accordion-bg:gray;">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlCatalog">
                                                        eQTLs Catalog
                                                    </button>
                                                </h5>
                                                <div class="accordion-collapse collapse" id="eqtlCatalog">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="eqtlCatalogTs" name="eqtlCatalogTs[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('snp2gene.xqtls_options.eqtls.eqtls_eqtlcatalog_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- pQTLs Plasma -->
                                            <div class="accordion-item" style="padding:0px;">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#plasma">
                                                        pQTLs Plasma
                                                    </button>
                                                </h5>
                                                <div class="accordion-collapse collapse" id="plasma">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="pqtlPlasmaDs" name="pqtlPlasmaDs[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('snp2gene.xqtls_options._pqtl_plasma_options')
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

            <div id="queryhistory" class="sidePanel container" style="padding-top:50px;">
                <div class ="col">
                    <div class="container" style="padding-top:50px;">
                        <div style="text-align: center;">
                            <h3>Query History</h3>
                            <p> Review your past xQTLs analysis queries. </p>
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
                                            <td colspan="7" style="Text-align:center;">Retrieving data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="results">
                @include('xqtls.result')
            </div>

        </div>

    </div>
            

</div>


@endsection

@push('vite')
    @vite([
        'resources/js/utils/sidebar.js',
        'resources/js/utils/browse.js'])
@endpush

@push('page_scripts')

    <script type="module">
        import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { BrowseSetup } from "{{ Vite::appjs('utils/browse.js') }}";
        $(function(){
            SidebarSetup();
            BrowseSetup();
        })
    </script>

@endpush