@extends('layouts.master')

<!--?php
header('X-Frame-Options: GOFORIT');
?-->

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
            <!-- Submit genes -->
            <div id="newquery" class="sidePanel container" style="padding-top:50px;">
                {{ html()->form('POST', '/gene2func/submit')->acceptsFiles()->novalidate()->open() }}
                <!-- <h3>Input list of genes</h3> -->
                <div class="row">
                    <div class="col-md-6 col-xs-6 col-sm-6">
                        <div class="card">
                            <div class="card-body" style="padding-bottom: 0;">
                                <h4>Genes of interest</h4>
                                <p class="info"><i class="fa fa-info"></i> Paste or upload a file that contains
                                    gene-symbols.
                                    Priority is given to the text box if both fields are used.
                                </p>
                                1. Paste genes
                                <a class="infoPop" data-bs-toggle="popover" title="Gene of interest input"
                                    data-bs-content="Please paste one gene per line. ENSG ID, entrez ID or gene symbols are accepted.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </a>
                                <br>
                                <textarea id="genes" name="genes" rows="12" cols="50"
                                    placeholder="Please enter one gene per line here." onkeyup="window.checkInput()"
                                    oninput="window.checkInput()"></textarea><br>
                                <br>
                                2. Upload file
                                <a class="infoPop" data-bs-toggle="popover" title="Gene of interest file formatting"
                                    data-bs-content="The first column should be the genes without header. Extra columns will be ignored. ENSG ID, entrez ID or gene symbols are accepted.">
                                    <i class="fa-regular fa-circle-question"></i></a>
                                <input class="form-control-file" type="file" name="genesfile" id="genesfile"
                                    onchange="window.checkInput()" />
                                <br>
                                <div id="GeneCheck" class="mt-2" style="padding-bottom: 0;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6 col-sm-6">
                        <div id="backgroundGenes"></div>
                        <div class="card">
                            <div class="card-body" style="padding-bottom: 0;">
                                <h4>Background genes</h4>
                                <p class="info"><i class="fa fa-info"></i>
                                    Specify background gene-set. This will be used in the hypergeometric test.
                                </p>
                                1. Select background genes by gene-type <a id="bkgeneSelectClear" class="clear">Clear</a><br>
                                <span class="info"><i class="fa fa-info"></i>
                                    Multiple gene-types can be selected.
                                </span>
                                <select class="form-control" multiple size="5" name="genetype[]" id="genetype"
                                    onchange="window.checkInput();">
                                    <option value="all">All</option>
                                    <option value="protein_coding">Protein coding</option>
                                    <option
                                        value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">
                                        lncRNA</option>
                                    <option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA</option>
                                    <option
                                        value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">
                                        Processed transcripts</option>
                                    <option
                                        value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">
                                        Pseudogene</option>
                                    <option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes</option>
                                    <option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes</option>
                                </select>
                                <br>
                                2. Paste custom list of background genes
                                <a class="infoPop" data-bs-toggle="popover" title="Background gene input"
                                    data-bs-content="Please paste one gene per line. ENSG ID, entrez ID and gene symbol are acceptable.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </a><br>
                                <textarea id="bkgenes" name="bkgenes" rows="5" cols="50"
                                    placeholder="Please enter each gene per line here." onkeyup="window.checkInput();"
                                    oninput="window.checkInput()"></textarea><br>
                                <br>
                                3. Upload a file with a custom list of background genes
                                <a class="infoPop" data-bs-toggle="popover" title="Background gene file formatting"
                                    data-bs-content="The first column should be the genes without header. Extra columns will be ignored. ENSG ID, entrez ID and gene symbol are acceptable.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </a>
                                <input class="form-control-file" type="file" name="bkgenesfile" id="bkgenesfile"
                                    onchange="window.checkInput()" />
                                <br>
                                <div id="bkGeneCheck" class="mt-2" style="padding-bottom: 0;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" style="padding:10;">
                        <h4>Other optional parameters</h4>
                        <div class="row mb-1">
                            <label for="ensembl" class="col-sm-5 col-form-label">
                                Ensembl version:</label>
                            <div class="col-sm-1">
                                <select class="form-control" id="ensembl" name="ensembl">
                                    <option selected value="v110">v110</option>
                                    <option selected value="v102">v102</option>
                                    <!-- REMOVED: no longer supported by biomart option value="v92">v92</option-->
                                    <!-- REMOVED: no longer supported by biomart option value="v85">v85</option-->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="gsFileAdd" class="col-sm-5 col-form-label">
                            Custom gene set files:
                            </label>
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-default btn-xs" id="gsFileAdd">add
                                    file
                                </button>
                            </div>
                            <input type="hidden" value="0" id="gsFileN" name="gsFileN">
                            <div class="col-sm-4">
                            <span class="info"><i class="fa fa-info"></i>&nbsp;File is required to have GMT format with an
                                extension ".gmt" and the gene IDs must be entrez ID.</span>
                            </div>
                        </div>
                        <span id="gsFiles"></span>

                        <div class="row mb-1">
                            <label for="gene_exp" class="col-sm-5 col-form-label">
                                Gene expression data sets:</label>
                            <div class="col-sm-5">
                                <select multiple class="form-control" name="gene_exp[]" id="gene_exp">
                                    <option selected value="GTEx/v8/gtex_v8_ts_avg_log2TPM">GTEx v8: 54 tissue
                                        types</option>
                                    <option selected value="GTEx/v8/gtex_v8_ts_general_avg_log2TPM">GTEx v8: 30
                                        general tissue types</option>
                                    <option value="GTEx/v7/gtex_v7_ts_avg_log2TPM">GTEx v7: 53 tissue types
                                    </option>
                                    <option value="GTEx/v7/gtex_v7_ts_general_avg_log2TPM">GTEx v7: 30 general
                                        tissue types</option>
                                    <option value="GTEx/v6/gtex_v6_ts_avg_log2RPKM">GTEx v6: 53 tissue types
                                    </option>
                                    <option value="GTEx/v6/gtex_v6_ts_general_avg_log2RPKM">GTEx v6: 30 general
                                        tissue types</option>
                                    <option value="BrainSpan/bs_age_avg_log2RPKM">BrainSpan: 29 different ages
                                        of brain samples</option>
                                    <option value="BrainSpan/bs_dev_avg_log2RPKM">BrainSpan: 11 general
                                        developmental stages of brain samples</option>
                                </select>
                            </div>
                        </div>
                        <!-- <input type="checkbox" id="Xchr" name="Xchr">&nbsp;Execlude genes on X chromosome. <span style="color: #004d99">*Please check to EXCLUDE X chromosome.</span><br> -->
                        <input type="checkbox" id="MHC" name="MHC">&nbsp;Exclude the MHC
                        region.
                        <!-- <span class="info"><i class="fa fa-info"></i> Please check to EXCLUDE genes in MHC region.</span><br> -->
                        <div class="row mb-1">
                            <label for="adjPmeth" class="col-sm-5 col-form-label">
                                Desired multiple test correction method for gene-set enrichment
                                testing:
                            </label>
                            <div class="col-sm-3">
                                <select class="form-control" id="adjPmeth" name="adjPmeth" style="width:auto;">
                                    <option value="bonferroni">Bonferroni</option>
                                    <option value="sidak">Sidak</option>
                                    <option value="holm-sidak">Holm-Sidak</option>
                                    <option value="holm">Holm</option>
                                    <option value="simes-hochberg">Simes-Hochberg</option>
                                    <option value="hommel">Hommel</option>
                                    <option selected value="fdr_bh">Benjamini-Hochberg (FDR)</option>
                                    <option value="fdr_by">Benjamini-Yekutieli (FDR)</option>
                                    <option value="fdr_tsbh">two-step Benjamini-Hochberg (FDR)</option>
                                    <option value="fdr_tsbky">two-step Benjamini-Krieger-Yekuteieli
                                        (FDR)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="ensembl" class="col-sm-5 col-form-label">
                                Maximum adjusted P-value for gene set association (&lt;):
                            </label>
                            <div class="col-sm-3">
                                <input class="form-control" type="number" id="adjPcut" name="adjPcut" value="0.05" />
                            </div>
                            <div class="col-sm-1">
                                <a class="infoPop" data-bs-toggle="popover" title="Adjusted P-value cutoff"
                                    data-bs-content="Only gene sets significantly enriched at given adjusted P-value threshold will be reported.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </a>
                            </div>

                        </div>
                        <div class="row mb-1">
                            <label for="minOverlap" class="col-sm-5 col-form-label">
                            Minimum overlapping genes with gene-sets (&ge;): 
                            </label>
                            <div class="col-sm-1">
                                <input class="form-control" type="number"
                                    id="minOverlap" name="minOverlap" value="2" />
                            </div>
                            <div class="col-sm-1">
                                <a class="infoPop" data-bs-toggle="popover" title="Minimum overlapping genes with gene sets"
                                    data-bs-content="Only gene sets which overlapping with more than or equal to the given number of genes in the input genes will be reported.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="title" class="col-sm-5 col-form-label">
                            Title: 
                            </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                            <div class="col-sm-1">
                                <span class="info"><i class="fa fa-info"></i> Optional</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="checkGenes"></div>
                <div id="checkBkGenes"></div>
                <input type="submit" value="Submit" class="btn btn-primary mt-3" id="geneSubmit"
                    name="geneSubmit" /><br><br>
                {{ html()->form()->close() }}
            </div>

            <!-- job list -->
            <div id="queryhistory" class="sidePanel container" style="padding-top:50px;">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Gene query history <a id="refreshTable"><i class="fa fa-refresh"></i></a></div>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-default btn-sm" id="deleteJob" name="deleteJob"
                            style="float:right; margin-right:20px;">Delete selected jobs</button>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Job ID</th>
                                    <th>Title</th>
                                    <th>SNP2GENE job ID</th>
                                    <th>SNP2GENE title</th>
                                    <th>Submit date</th>
                                    <th>Link</td>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" style="Text-align:center;">Retrieving data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- results panel -->
            <div id="results">
                @include('gene2func.summary')
                @include('gene2func.exp_heat')
                @include('gene2func.DEG')
                @include('gene2func.genesets')
                @include('gene2func.geneTable')
            </div>
        </div>
    </div>
</div>
@endsection

@push('vite')
@vite([
'resources/js/utils/sidebar.js',
'resources/js/utils/gene2func.js',
'resources/js/utils/g2f_results.js'])
@endpush

@push('page_scripts')

<!-- type="module" is used to keep the prder of execution 
        this relies on app.js being imported first-->
<script type="module">
    window.loggedin = "{{ Auth::check() }}";
        window.setG2FPageState(
            "{{ URL::asset('/image/ajax-loader2.gif') }}",
            "<?php echo storage_path(); ?>",
            "",
            "{{ Config::get('app.jobdir') }}",
            "{{ $status }}",
            "{{ $id }}",
            "{{ $page }}",
            "{{ Auth::check() }}",
            "gene2func"
        );
</script>

{{-- Imports from the project using Vite alias macro --}}
<script type="module">
    import { SidebarSetup } from "{{ Vite::appjs('utils/sidebar.js') }}";
        import { Gene2FuncSetup, checkInput, ImgDown, gsFileDel, gsFileCheck } from "{{ Vite::appjs('utils/gene2func.js') }}";
        import { DEGImgDown, GSImgDown, GeneSetTable, GeneSetPlot } from "{{ Vite::appjs('utils/g2f_results.js') }}";
        window.checkInput = checkInput;
        window.ImgDown = ImgDown;
        window.GSImgDown = GSImgDown;
        window.GeneSetTable = GeneSetTable;
        window.GeneSetPlot = GeneSetPlot;
        window.DEGImgDown = DEGImgDown;
        window.gsFileDel = gsFileDel;
        window.gsFileCheck = gsFileCheck;
        // document initialization
        $(function(){
            SidebarSetup();
            Gene2FuncSetup();
        });
</script>

<script>
    window.fumaJS = {{ Js::from(isset($data) ? $data : null)}};
</script>

@endpush