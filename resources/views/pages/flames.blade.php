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
                            <p> Implementation of FLAMES (10.1038/s41588-025-02084-7) </p>
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

                                <table class="table table-bordered inputTable" id="flames" style="width: auto; border: 1px solid black;">
                                    <tr>
                                        <td>
                                            <h5>GWAS summary statistics</h5>
                                            <a class="infoPop" data-bs-toggle="popover" title="GWAS summary statistics input file"
                                                data-bs-content="Upload a tab-delimited text file with header containing GWAS summary statistics with the following columns in this specific order: CHR, POS, REF, ALT, N, BETA, P, MAF. Check the documentation on how to prepare the summary statistics file.">
                                                <i class="fa-regular fa-circle-question fa-lg"></i>
                                            </a>
                                        </td>

                                        <td><input type="file" class="form-control-file" name="gwasSumstat" id="gwasSumstat" />
                                        </td>

                                        <td>
                                        <div id="gwasInputCheck" class="mt-2" style="padding-bottom: 0;"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                        <h5>MAGMA gene analysis result</h5>
                                        </td>
                                        <td>
                                        1. Select from existing SNP2GENE job<br>
                                        <span class="info"><i class="fa fa-info fa-sm"></i>
                                            You can only select one of the successful SNP2GENE jobs in your account.<br>
                                            When you select a job ID, FUMA will automatically check if MAGMA was performed in the
                                            selected job.
                                        </span>
                                        <select class="form-select" id="s2gID" name="s2gID">
                                        </select>
                                        <br>
                                        2. Upload your own genes.raw file<br>
                                        <span class="info"><i class="fa fa-info fa-sm"></i>
                                            You can only upload a file with extension "genes.raw"
                                            which is an output of MAGMA gene analysis.
                                        </span>
                                        <div class="row mb-1">
                                            <div class="col-sm-1">
                                                <input type="file" class="form-control-file" name="genes_raw" id="genes_raw" />
                                            </div>
                                        </div>
                                        <br>
                                        3. Input the SNP2GENE jobID
                                        <span class="inputSpan">SNP2GENE JobID: <input type="text" class="form-control"
                                                        id="snp2geneID" name="snp2geneID"></span>

                                        </td>

                                        <td>
                                        </td>
                                        
                                    </tr>

                                    <tr>
                                        <td>
                                        <h5>PoPS output upload (for testing only)</h5>
                                        </td>

                                        <td>
                                        Upload your own .preds file<br>
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
                                            <span class="inputSpan">Sample size: <input type="text" class="form-control"
                                            id="totalN" name="totalN" ></span>
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



        </div>

    </div>
            

</div>


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
        import { FLAMESSetup} from "{{ Vite::appjs('utils/flames.js') }}";
        $(function(){
            SidebarSetup();
            BrowseSetup();
            FLAMESSetup();
        })
    </script>

@endpush