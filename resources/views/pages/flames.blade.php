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
            <li class="active"><a href="#newquery">New Job<i class="sub_icon fa fa-upload"></i></a></li>
            <li ><a href="#queryhistory">My Jobs<i class="sub_icon fa fa-history"></i></a></li>
            <div id="resultSide">
                <li><a href="#flamesResults">Results<i class="sub_icon fa fa-table"></i></a></li>
            </div>
        </ul>
    </div>

    <div id="page-content-wrapper">
        <div class="page-content inset">  
            <div id="newquery" class="sidePanel container" style="padding-top:50px;">
                <div class ="col">
                    <div class="container" style="padding-top:50px;">
                        <div style="text-align: center;">
                            <h3>GWAS Gene Prioritization with FLAMES</h3>

                            <div class="alert alert-primary">
                                Implementation of FLAMES (fine-mapped locus accessment model of effector genes). <br> 
                                <a href="https://www.nature.com/articles/s41588-025-02084-7" target="_blank">Link</a> to paper. <br>
                                Please read the instructions carefully when submitting a FLAMES job. Please check the <a href="https://fuma-docs.readthedocs.io/en/latest/flames.html" target="_blank">documentation</a> for more details. 
                            </div>
                            
                        </div>
                        <div>
                            <div id="uploadData">
                                {{ html()->form('POST', '/flames/submit')->attribute('enctype', 'multipart/form-data')->open() }}

                                <div class="row">
                                    <div class="col-sm-2" style="font-weight: bold; padding-top:7px;">
                                        <b>Job name (optional):</b>
                                    </div>
                                    <div class="col-sm">
                                        <input type="text" class="form-control" style="border: 1px solid black;" id="title" name="title" />
                                    </div>
                                </div>

                                <br>

                                <table class="table table-bordered inputTable" id="flames" style="width: auto; border: 1px solid #A9A9A9">

                                    <tr>
                                        <td>
                                        <h5>SNP2GENE jobID</h5>
                                        </td>
                                        <td>
                                            <b>Select from existing SNP2GENE job</b><br>
                                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                                You can only select one of the successful SNP2GENE jobs in your account.<br>
                                                When you select a job ID, FUMA will automatically check if MAGMA was performed in the
                                                selected job and if the required files for running FLAMES were generated in your SNP2GENE job.
                                            </span>
                                            <select class="form-select" id="s2gID" name="s2gID" style="border: 1px solid black;" onchange="window.CheckInput();">
                                            </select>
                                            <br>
                                        </td>

                                        <td>
                                            <div id="CheckInput" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                        
                                    </tr>

                                    <tr>
                                        <td>
                                            <h5>GWAS summary statistics</h5>
                                            
                                        </td>

                                        <td>
                                            <b>Upload your GWAS summary statistics</b><br>
                                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                                Please upload the same GWAS summary statistics that you used in the SNP2GENE job specified above. <br>
                                                FLAMES module expects the input GWAS summary statistics to have the following column names: chr, bp, A2, A1, rsID, p, beta. <br>
                                                The header file needs to start with "#" and the columns need to be separated by tab. <br>
                                                The file also needs to be bgzipped and have extension ".gz". <br>
                                            </span>
                                            <input type="file" class="form-control-file" name="gwasSumstat" id="gwasSumstat" onchange="window.CheckInput();" />
                                        </td>

                                        <td>
                                            <div id="gwasInputCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td>
                                        <h5>PoPS output upload (for testing only)</h5>
                                        </td>

                                        <td>
                                        <b>Upload your own .preds file</b><br>
                                        <span class="info"><i class="fa fa-info fa-sm"></i>
                                            You can only upload a file with extension ".preds" which is an output of PoPS.
                                        </span>
                                        <div class="row mb-1">
                                            <div class="col-sm-1">
                                                <input type="file" class="form-control-file" name="preds" id="preds" />
                                            </div>
                                        </div>
                                        </td>
                                        
                                        <td>
                                            <div id="popsInputCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <h5>Sample size:</h5>
                                        </td>
                                        <td>
                                            <class="inputSpan"><input type="text" class="form-control" style="border: 1px solid black;" id="totalN" name="totalN" onchange="window.CheckInput();"/>
                                        </td>

                                        <td>
                                        <div id="otherParamsCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>
                                    
                                </table>

                                <input type="submit" value="Submit" class="btn btn-primary mt-3" id="flamesSubmit" name="flamesSubmit" /><br><br>
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
                            <h5 style="color: #00004d"> Review your past FLAMES queries. </h5>
                            <div>
                            <button type="button" class="btn btn-primary" id="refreshTable" name="refreshTable"
                            style="margin-right:20px;">Refresh query table</button>
                            <button  type="button" class="btn btn-danger" id="deleteJob" name="deleteJob"
                            style="margin-right:20px;">Delete selected jobs</button>
                            </div>
                            <div id="historyData">
                                <table class="table table-bordered inputTable" id="flamesHistory" style="width: auto;">
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

            <div id="flamesResults" class="sidePanel container" style="padding-top:50px; display: none">
                <div class="card"><div class="card-body">
                    <h4 style="color: #00004d">Result tables</h4>
                    FLAMES output's FLAMES_scores.pred are displayed in the result tables. 
                    
                    <!-- Define navigation tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active " href="#predResultsTable" id="predResults-tab" data-bs-toggle="tab">PRED</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade show active" id="predResultsTable" aria-labelledby="predResults-tab">
                            <table id="predTable" class="table table-striped table-sm display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
                                <thead>
                                    <tr>
                                        <th>Locus</th><th>Gene Symbol</th><th>Ensemble</th><th>FLAMES scaled</th><th>FLAMES raw</th><th>Estimated cumulative precision</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card"><div class="card-body">
                    <h4 style="color: #00004d">Download Results: </h4>
                    <div class="clickable" onclick='tutorialDownloadVariant("flamesResultsRaw")'> FLAMES_scores.raw
                        <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
                    </div>
                    <div class="clickable" onclick='tutorialDownloadVariant("flamesResultsPred")'> FLAMES_scores.pred
                        <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

<form method="post" target="_blank" action="/flames/downloadResults">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="jobID" value="<?php echo $id;?>"/>
    <input type="hidden" name="variant_code" id="tutorialDownloadVariantCode" value="" />
    <input type="submit" id="tutorialDownloadVariantSubmit" class="ImgDownSubmit" style="display: none;" />
</form>


@endsection

@push('vite')
    @vite([
        'resources/js/utils/sidebar.js',
        'resoures/js/utils/flames.js',
        'resources/js/utils/browse.js',
        'resources/js/utils/tutorial_utils.js'])
@endpush

@push('page_scripts')
    <script type="module">
        window.loggedin = "{{ Auth::check() }}";
        window.setFlamesPageState(
            "{{ $status }}",
            "{{ $id }}",
            "flames",
            "{{ $page }}",
            "",
            "{{ Auth::check() }}"
        );
    </script>

    <script type="module">
        import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { BrowseSetup } from "{{ Vite::appjs('utils/browse.js') }}";
        import { FLAMESSetup, CheckInput} from "{{ Vite::appjs('utils/flames.js') }}";
        import tutorialDownloadVariant from "{{ Vite::appjs('utils/tutorial_utils.js') }}";
        window.CheckInput = CheckInput;
        window.tutorialDownloadVariant = tutorialDownloadVariant;
        $(function(){
            SidebarSetup();
            BrowseSetup();
            FLAMESSetup();
        })
    </script>

@endpush