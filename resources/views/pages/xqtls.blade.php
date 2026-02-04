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
                        <div style="text-align: center;">
                            <h3>QTLs Analysis</h3>
                            <h5 style="color: #00004d"> Prioritizing genes within a genomic risk locus by integrating with QTLs datasets. </h5>
                            <p> Use the QTLs analysis to investigate the potential functional mechanisms underlying GWAS associations by integrating with various xQTLs datasets including eQTLs, sQTLs and apaQTLs. Upload your GWAS summary statistics for a specific genomic locus and select the xQTLs datasets of interest to perform colocalization and/or LAVA analysis. Check the documentation for more information on how to prepare the input files and interpret the results. Please select either colocalization and/or LAVA analysis. If you do not check either option, there is no error but there will be no results shown. </p>
                        </div>
                        <div>
                            <div id="uploadData">
                                {{ html()->form('POST', '/xqtls/submit')->attribute('enctype', 'multipart/form-data')->open() }}

                                <div class="row">
                                    <div class="col-sm-2" style="font-weight: bold; padding-top:7px;">
                                        <b>Job name (optional):</b>
                                    </div>
                                    <div class="col-sm">
                                        <input type="text" class="form-control" style="border: 1px solid black;" id="title" name="title" />
                                    </div>
                                </div>

                                <br>

                                <table class="table table-bordered inputTable" id="xqtlsAnalysis" style="width: auto; border: 1px solid black;">
                                    <tr>
                                        <td> Summary statistics file for a locus: 
                                            <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Upload a tab-delimited text file with header containing GWAS summary statistics with the following columns in this specific order: CHR, POS, REF, ALT, N, BETA, P, MAF. Check the documentation on how to prepare the summary statistics file.">
                                                <i class="fa-regular fa-circle-question"></i>
                                            </a>
                                        </td>
                                        <td><input type="file" class="form-control-file" name="locusSumstat" id="locusSumstat" onchange="window.CheckAll()" />
                                        <br>
                                        Build:
                                        <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Please select the human genome assembly version (GRCh37 or GRCh38) on which the genomic coordinates in your summary statistics file are based (mandatory).">
                                                <i class="fa-regular fa-circle-question"></i>
                                        </a>
                                        <br>  
                                        <input type="radio" id="grch37" name="build" value="GRCh37">
                                        <label for="grch37">GRCh37</label><br>
                                        <input type="radio" id="grch38" name="build" value="GRCh38">
                                        <label for="grch38">GRCh38</label><br>
                                        </input>
                                        </td>
                                        <td>
                                        <div id="locusInputCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td> Genomic Locus Information: 
                                            <a class="infoPop" data-bs-toggle="popover"
                                                data-bs-content="Provide the chromosome number, start and end position of the genomic region of interest. Coordinates have to be based on the GRCh38 human genome assembly.">
                                                <i class="fa-regular fa-circle-question"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="inputSpan">Chromosome: <input type="text" class="form-control"
                                                        id="chrom" name="chrom" onchange="window.CheckAll()"></span>
                                            <span class="inputSpan">Start: <input type="text" class="form-control"
                                                        id="locusStart" name="locusStart" onchange="window.CheckAll()"></span>
                                            <span class="inputSpan">End: <input type="text" class="form-control"
                                                        id="locusEnd" name="locusEnd" onchange="window.CheckAll()"></span>
                                        </td>
                                        <td>
                                        <div id="locusInputCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    <tr>
                                        <td>Perform colocalization
                                            <a class="infoPop" data-bs-toggle="popover" title="colocalization"
                                                data-bs-content="Check this option to perform colocalization analysis.">
                                                <i class="fa-regular fa-circle-question fa-lg"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="accordion-item" style="padding:0px;">
                                                <div class="accordion-header">
                                                    <input class="accordion-button collapsed accordion-highlight" data-bs-toggle="collapse" data-bs-target="#colocParams" type="checkbox" class="form-check-inline" name="coloc" id="coloc" onchange="window.CheckAll()">
                                                </div>

                                                <div class="accordion-collapse collapse" id="colocParams">
                                                    <div class="accordion-body">
                                                        <i>Parameters for colocalization:</i><br> 
                                                        <span class="inputSpan">PP4 threshold: <input type="number" class="form-control"
                                                        id="pp4" name="pp4" value="0.8" onchange="window.CheckAll()"></span>
                                                        <span class="inputSpan">Gene symbol (optional): 
                                                            <a class="infoPop" data-bs-toggle="popover"
                                                            data-bs-content="Input gene(s) to focus the colocalization analysis on that gene. If there are more than one gene, separate them with commas without space, for example: ENSG00000133805.16,ENSG00000133789.15. If left blank, colocalization will analyze all genes within the locus.">
                                                            <i class="fa-regular fa-circle-question"></i>
                                                            </a>
                                                            <input type="text" class="form-control" id="colocGene" name="colocGene">
                                                        </span> 
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        <div id="colocParamsCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Perform LAVA
                                            <a class="infoPop" data-bs-toggle="popover" title="LAVA"
                                                data-bs-content="Check this option to perform LAVA analysis.">
                                                <i class="fa-regular fa-circle-question fa-lg"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="accordion-item">
                                                <div class="accordion-header">
                                                    <input class="accordion-button collapsed accordion-highlight" data-bs-toggle="collapse" data-bs-target="#lavaParams" type="checkbox" class="form-check-inline" name="lava" id="lava" onchange="window.CheckAll()">
                                                </div>

                                                <div class="accordion-collapse collapse" id="lavaParams">
                                                    <div class="accordion-body">
                                                        <i>Parameters for LAVA:</i><br>
                                                        <span class="inputSpan">Phenotype: <input type="text" class="form-control"
                                                        id="phenotype" name="phenotype" onchange="window.CheckAll()"></span>
                                                        <span class="inputSpan">Gene symbol (optional): 
                                                            <a class="infoPop" data-bs-toggle="popover"
                                                            data-bs-content="Input a specific gene symbol to focus the LAVA analysis on that gene. If left blank, LAVA will analyze all genes within the locus.">
                                                            <i class="fa-regular fa-circle-question"></i>
                                                            </a>
                                                            <input type="text" class="form-control" id="lavaGene" name="lavaGene">
                                                        </span> 
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                        <div id="lavaParamsCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Other parameters:
                                            <a class="infoPop" data-bs-toggle="popover" title="otherParams"
                                                data-bs-content="Input other parameters for the analysis.">
                                                <i class="fa-regular fa-circle-question fa-lg"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="inputSpan">Cases: <input type="text" class="form-control"
                                            id="cases" name="cases" onchange="window.CheckAll()"></span>
                                            <span class="inputSpan">Sample size: <input type="text" class="form-control"
                                            id="totalN" name="totalN" onchange="window.CheckAll()"></span>
                                        </td>

                                        <td>
                                        <div id="otherParamsCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>

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
                                                            <select multiple class="form-select" id="eqtlGtexv10Ds" name="eqtlGtexv10Ds[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.eqtls.gtex_v10_options')
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
                                                            <select multiple class="form-select" id="sqtlGtexv10Ds" name="sqtlGtexv10Ds[]"
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

                                            <br>

                                            <h2 style="color: #00004d; font-size:16px;">pQTLs Datasets</h2>
                                            <div class="accordion-item" style="padding:0px;">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#pqtl9Sun2023">
                                                        9 sun 2023
                                                    </button>
                                                </h3>

                                                <div class="accordion-collapse collapse" id="pqtl9Sun2023">
                                                    <div class="accordion-body">
                                                        <span class="multiSelect">
                                                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                                            <select multiple class="form-select" id="pqtl9Sun2023Ds" name="pqtl9Sun2023Ds[]"
                                                                size="10" onchange="window.CheckAll();">
                                                                @include('xqtls.xqtls_options.pqtls.9_sun_2023_options')
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                        <div id="datasetCheck" class="mt-2" style="padding-bottom: 0;"></div>
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
                            <div>
                            <button type="button" class="btn btn-primary" id="refreshTable" name="refreshTable"
                            style="margin-right:20px;">Refresh query table</button>
                            <button  type="button" class="btn btn-danger" id="deleteJob" name="deleteJob"
                            style="margin-right:20px;">Delete selected jobs</button>
                            </div>
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
        import { XQTLSSetup, CheckAll } from "{{ Vite::appjs('utils/xqtls.js') }}";
        window.CheckAll = CheckAll;
        $(function(){
            SidebarSetup();
            BrowseSetup();
            XQTLSSetup();
        })
    </script>

@endpush