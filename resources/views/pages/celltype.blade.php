@extends('layouts.master')

@section('stylesheets')
    <link href="{{ asset('/css/tree_multiselect.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div id="wrapper" class="active">
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav" id="sidebar-menu">
                <li class="sidebar-brand"><a id="menu-toggle">
                        <tab><i id="main_icon" class="fa fa-chevron-left"></i>
                    </a></li>
            </ul>
            <ul class="sidebar-nav" id="sidebar">
                <li class="active"><a href="#newJob">New Job<i class="sub_icon fa fa-upload"></i></a></li>
                <li class="active"><a href="#joblist">My Jobs<i class="sub_icon fa fa-history"></i></a></li>
                <!-- <li class="active"><a href="#DIY">Do It Yourself<i class="sub_icon fa fa-wrench"></i></a></li> -->
                <div id="resultSide">
                    <!-- <li><a href="#Summary">Summary<i class="sub_icon fa fa-table"></i></a></li> -->
                    <li><a href="#result">Results<i class="sub_icon fa fa-bar-chart"></i></a></li>
                </div>
            </ul>
        </div>

        <div id="page-content-wrapper">
            <div class="page-content inset">
                <div id="newJob" class="sidePanel container" style="padding-top:50px;">
                    {{ html()->form('POST', '/celltype/submit')->acceptsFiles()->novalidate()->open() }}
                    <div class="card">
                        <div class="card-body">
                            <h4>MAGMA gene analysis result</h4>
                            1. Select from existing SNP2GENE job<br>
                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                You can only select one of the successful SNP2GENE jobs in your account.<br>
                                When you select a job ID, FUMA will automatically check if MAGMA was performed in the
                                selected job.
                            </span>
                            <select class="form-select" id="s2gID" name="s2gID" onchange="window.CheckInput();">
                            </select>
                            <br>
                            2. Upload your own genes.raw file<br>
                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                You can only upload a file with extension "genes.raw"
                                which is an output of MAGMA gene analysis.
                            </span>
                            <div class="row mb-1">
                                <div class="col-sm-1">
                                    <input type="file" class="form-control-file" name="genes_raw" id="genes_raw"
                                        onchange="window.CheckInput();" />
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-sm-5">
                                    <input type="checkbox" checked class="form-check-input" name="ensg_id" i="ensg_id">
                                    &nbsp;: Ensembl gene ID is used in the provided file. &nbsp;
                                    <a class="infoPop" data-bs-toggle="popover" title="Ensembl dene ID"
                                        data-bs-content="Please UNCHECK this option if you used different gene ID than Ensembl gene ID
								in your uploaded MAGMA output. In that case, provided genes will be mapped to Ensembl gene ID.">
                                        <i class="fa-regular fa-circle-question"></i>
                                    </a>
                                    </input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body" style="padding-bottom: 10;">
                            <h4>Single-cell expression data sets</h4>
                            Select single-cell expression data sets to perform MAGMA gene-property analysis<br>
                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                You should not select all datasets if you want to perform step 2 and 3 of the workflow
                                due to the duplicated cell types in multiple datasets from the same data resource.
                                For example, Tabula Muris FACS data have one dataset with all cell types from all tissues
                                and other datasets for each tissue separately. Therefore, "endothelial cell" in Lung sample in the dataset with all tissues is
                                exactly the same as "endothelial cell" in Lung dataset. This applies to data resource with multiple levels, where level 1 cell types include level 2
                                cell types.
                                In addition, step 2 is only performed after multiple testing correction across all the cell
                                types tested in the step 1
                                regardless of duplications of the cell types.
                                It is strongly recommended to carefully select datasets to test beforehand.
                            </span> <br>
                            <div class="alert alert-info">
			                    <strong>Data structure:</strong> 
                                <br> 
                                The data are organized by tissue types in alphabetical order. Species are separated out within each tisseue (currently data for human and mouse are available). Within the human brain,
                                the data are further categorized spatially (different regions of the brain) and temporally (different developmental timepoint). 
                                <br>
                                If you would like to add a new scRNAseq dataset that is not currently available here, please email us. 
                                
                            </div>

                            <div>
                                <select multiple="multiple" class="form-control" style="display: none;" id="cellDataSets"
                                    name="cellDataSets[]" onchange="window.CheckInput();">
                                    @include('celltype.celltype_options.aorta_options')
                                    @include('celltype.celltype_options.bladder_options')
                                    @include('celltype.celltype_options.blood_options')
                                    @include('celltype.celltype_options.boneMarrow_options')
                                    @include('celltype.celltype_options.brain_human_allocortex_options')
                                    @include('celltype.celltype_options.brain_human_brain_options')
                                    @include('celltype.celltype_options.brain_human_cerebellum_options')
                                    @include('celltype.celltype_options.brain_human_cerebralGyriAndLobules_options')
                                    @include('celltype.celltype_options.brain_human_cerebralNuclei_options')
                                    @include('celltype.celltype_options.brain_human_cingulateNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_diencephalon_options')
                                    @include('celltype.celltype_options.brain_human_dorsolateralPrefrontalCortex_options')
                                    @include('celltype.celltype_options.brain_human_forebrain_options')
                                    @include('celltype.celltype_options.brain_human_frontalNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_hindbrain_options')
                                    @include('celltype.celltype_options.brain_human_hippocampalGyrusFormation_options')
                                    @include('celltype.celltype_options.brain_human_hypothalamus_options')
                                    @include('celltype.celltype_options.brain_human_insularNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_medulla_options')
                                    @include('celltype.celltype_options.brain_human_meninges_options')
                                    @include('celltype.celltype_options.brain_human_midbrain_options')
                                    @include('celltype.celltype_options.brain_human_middleTemporalGyrus_options')
                                    @include('celltype.celltype_options.brain_human_myelencephalon_options')
                                    @include('celltype.celltype_options.brain_human_neocortex_options')
                                    @include('celltype.celltype_options.brain_human_occipitalNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_orbitalFrontalCortex_options')
                                    @include('celltype.celltype_options.brain_human_parietalNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_periallocortex_options')
                                    @include('celltype.celltype_options.brain_human_pons_options')
                                    @include('celltype.celltype_options.brain_human_prefrontalCortex_options')
                                    @include('celltype.celltype_options.brain_human_primaryAuditoryCortex_options')
                                    @include('celltype.celltype_options.brain_human_primaryMotorCortex_options')
                                    @include('celltype.celltype_options.brain_human_primarySomatosensoryCortex_options')
                                    @include('celltype.celltype_options.brain_human_primaryVisualCortex_options')
                                    @include('celltype.celltype_options.brain_human_telencephalon_options')
                                    @include('celltype.celltype_options.brain_human_temporalNeocortex_options')
                                    @include('celltype.celltype_options.brain_human_thalamus_options')
                                    @include('celltype.celltype_options.brain_human_transientStructuresOfForebrain_options')
                                    @include('celltype.celltype_options.brain_human_unspecifiedRegions_options')
                                    @include('celltype.celltype_options.brain_human_vagalNucleus_options')
                                    @include('celltype.celltype_options.brain_human_ventrolateralPrefrontalCortex_options')
                                    @include('celltype.celltype_options.brain_human_whiteMatter_options')
                                    @include('celltype.celltype_options.brain_mouse_options')
                                    @include('celltype.celltype_options.breast_options')
                                    @include('celltype.celltype_options.diaphram_options')
                                    @include('celltype.celltype_options.embryo_options')
                                    @include('celltype.celltype_options.epithelial_options')
                                    @include('celltype.celltype_options.fat_options')
                                    @include('celltype.celltype_options.heart_options')
                                    @include('celltype.celltype_options.intestine_options')
                                    @include('celltype.celltype_options.kidney_options')
                                    @include('celltype.celltype_options.liver_options')
                                    @include('celltype.celltype_options.lung_options')
                                    @include('celltype.celltype_options.lymphNode_options')
                                    @include('celltype.celltype_options.muscle_options')
                                    @include('celltype.celltype_options.other_options')
                                    @include('celltype.celltype_options.ovary_options')
                                    @include('celltype.celltype_options.pancreas_options')
                                    @include('celltype.celltype_options.placenta_options')
                                    @include('celltype.celltype_options.prostate_options')
                                    @include('celltype.celltype_options.ribs_options')
                                    @include('celltype.celltype_options.skeletalMuscle_options')
                                    @include('celltype.celltype_options.skin_options')
                                    @include('celltype.celltype_options.spinalCord_options')
                                    @include('celltype.celltype_options.spleen_options')
                                    @include('celltype.celltype_options.stemCell_options')
                                    @include('celltype.celltype_options.stomach_options')
                                    @include('celltype.celltype_options.testis_options')
                                    @include('celltype.celltype_options.thymus_options')
                                    @include('celltype.celltype_options.tongue_options')
                                    @include('celltype.celltype_options.trachea_options')
                                    @include('celltype.celltype_options.uterus_options')
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body" style="padding-bottom: 10;">
                            <h4>Other options</h4>
                            <div class="row mb-1">
                                <label for="adjPmeth" class="col-sm-5 col-form-label">
                                    Multiple test correction method:</label>
                                <div class="col-sm-1">
                                    <select class="form-select" id="adjPmeth" name="adjPmeth" style="width:auto;">
                                        <option selected value="bonferroni">Bonferroni</option>
                                        <option value="BH">Benjamini-Hochberg (FDR)</option>
                                        <option value="BY">Benjamini-Yekutieli</option>
                                        <option value="holm">Holm</option>
                                        <option value="hochberg">Hochberg</option>
                                        <option value="hommel">Hommel</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <input type="checkbox" id="step2" name="step2"> Perform step 2 (per dataset conditional
                            analysis)
                            if there is more then one significant cell type per dataset.
                            <a class="infoPop" data-bs-toggle="popover"
                                data-bs-content="Step 2 in the workflow is per dataset conditional analysis.
							When there are more than one significant cell types from the same dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
							significant cell types within the dataset. Based on this, forward selection will be performed to identify independent signals.
							See tutorial for details.">
                                <i class="fa-regular fa-circle-question"></i>
                            </a>
                            <br>
                            <input type="checkbox" id="step3" name="step3"> Perform step 3 (cross-datasets
                            conditional analysis)
                            if there is significant cell types from more than one dataset.
                            <a class="infoPop" data-bs-toggle="popover"
                                data-bs-content="Step 3 in the workflow is cross-datasets conditional analysis.
							When there are significant cell types from more than one dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
							significant cell types across datasets. See tutorial for details.">
                                <i class="fa-regular fa-circle-question"></i>
                            </a>
                            <br>
                            <span class="info"><i class="fa fa-info fa-sm"></i>
                                Step 2 and 3 options are disabled when all scRNA datasets are selected.
                            </span>
                            <br>
                            <div class="row mb-1">
                                <label for="title" class="col-sm-5 col-form-label">
                                    Title:
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" id="title" name="title" />
                                </div>
                                <div class="col-sm-1">
                                    <span class="info"><i class="fa fa-info fa-sm"></i> Optional</span>
                                    <div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="CheckInput" class="mt-2"></div>
                    <input type="submit" value="Submit" class="btn btn-primary" id="cellSubmit"
                        name="cellSubmit" /><br><br>
                    {{ html()->form()->close() }}
                </div>
                <div>
                    @include('celltype.joblist')
                    <div id="DIY" class="sidePanel container" style="padding-top:50px;">
                        <h4>Do It Yourself</h4>
                    </div>
                    @include('celltype.result')
                </div>
            </div>
        @endsection

        {{-- Vite imports from the project --}}
        @push('vite')
            @vite(['resources/js/utils/cell_results.js', 'resources/js/utils/celltype.js', 'resources/js/utils/sidebar.js'])
        @endpush

        @push('page_scripts')
            {{-- Web (via npm) resources --}}
            {{-- Hand written ones --}}
            <script type="module">
                window.loggedin = "{{ Auth::check() }}";
                console.log(`Page {{ $page }} LoggedIn ${window.loggedin}`)
                window.setCelltypePageState(
                    "{{ $status }}",
                    "{{ $id }}",
                    "{{ $prefix }}",
                    "{{ $page }}",
                    "",
                    "{{ Auth::check() }}"
                );
            </script>
            {{-- Imports from the project using Vite alias macro --}}
            <script type="module">
                import {
                    CheckAll
                } from "{{ Vite::appjs('utils/NewJobParameters.js') }}";
                window.CheckAll = CheckAll;
                import {
                    CellTypeSetup
                } from "{{ Vite::appjs('utils/celltype.js') }}";
                import {
                    CheckInput
                } from "{{ Vite::appjs('utils/celltype.js') }}";
                window.CheckInput = CheckInput;
                import {
                    SidebarSetup
                } from "{{ Vite::appjs('utils/sidebar.js') }}";
                import {
                    ImgDownDS,
                    ImgDown,
                    updatePerDatasetPlot
                } from "{{ Vite::appjs('utils/cell_results.js') }}";
                window.ImgDownDS = ImgDownDS;
                window.ImgDown = ImgDown;
                window.updatePerDatasetPlot = updatePerDatasetPlot;
                $(function() {
                    SidebarSetup();
                    CellTypeSetup();
                });
            </script>

            <script type="module">
                $("select#cellDataSets").treeMultiselect({
                    searchable: true,
                    searchParams: ['section', 'text'],
                    hideSidePanel: true,
                    startCollapsed: true
                });
            </script>
        @endpush
