@extends('layouts.master')

@section('stylesheets')
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
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
                    {{ html()->form('POST', 'celltype/submit')->acceptsFiles()->novalidate()->open() }}
                    <div class="panel panel-default">
                        <div class="panel-body" style="padding-bottom: 10;">
                            <h4>MAGMA gene analysis result</h4>
                            1. Select from existing SNP2GENE job<br />
                            <span class="info"><i class="fa fa-info"></i>
                                You can only select one of the succeeded SNP2GENE jobs in your account.<br />
                                When you select a job ID, FUMA will automatically check if MAGMA was performed in the
                                selected job.
                            </span>
                            <select class="form-control" id="s2gID" name="s2gID" onchange="CheckInput();">
                            </select>
                            <br />
                            2. Upload your own genes.raw file<br />
                            <span class="info"><i class="fa fa-info"></i>
                                You can only upload a file with extension "genes.raw"
                                which is an output of MAGMA gene analysis.
                            </span>
                            <input type="file" class="form-control-file" name="genes_raw" id="genes_raw"
                                onchange="CheckInput();" />
                            <span class="form-inline">
                                <input type="checkbox" checked class="form-check-input" name="ensg_id" i="ensg_id" />
                                : Ensembl gene ID is used in the provided file.
                                <a class="infoPop" data-toggle="popover"
                                    data-content="Please UNCHECK this option if you used different gene ID than Ensembl gene ID
								in your uploaded MAGMA output. In that case, provided genes will be mapped to Ensembl gene ID.">
                                    <i class="fa fa-question-circle-o fa-lg"></i>
                                </a>
                            </span>
                            <br />
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body" style="padding-bottom: 10;">
                            <h4>Single-cell expression data sets</h4>
                            Select single-cell expression data sets to perform MAGMA gene-property analysis<br />
                            <span class="info"><i class="fa fa-info"></i>
                                You should not select all datasets if you want to perform step 2 and 3 of the workflow
                                due to the duplicated cell types in multiple datasets from the same data resource.
                                For example, Tabula Muris FACS data have one dataset with all cell types from all tissues
                                and
                                other datasets for each tissue separately.
                                Therefore, "endothelial cell" in Lung sample in the dataset with all tissues is
                                exactly the same as "endothelial cell" in Lung dataset.
                                This applies to data resource with multiple levels, where level 1 cell types include level 2
                                cell types.
                                In addition, step 2 is only performed after multiple testing correction across all the cell
                                types tested in the step 1
                                regardless of duplications of the cell types.
                                It is strongly recommended to carefully select datasets to test beforehand.
                            </span> <br>

                            <div>
                                <select multiple="multiple" class="form-control" style="display: none;" id="cellDataSets"
                                    name="cellDataSets[]" onchange="CheckInput();">
                                    <option value="TabulaMuris_FACS_Aorta" data-section="Aorta/Mouse" data-key="0">
                                        TabulaMuris_FACS_Aorta</option>
                                    <option value="MouseCellAtlas_Bladder" data-section="Bladder/Mouse" data-key="0">
                                        MouseCellAtlas_Bladder</option>
                                    <option value="TabulaMuris_FACS_Bladder" data-section="Bladder/Mouse" data-key="1">
                                        TabulaMuris_FACS_Bladder</option>
                                    <option value="TabulaMuris_droplet_Bladder" data-section="Bladder/Mouse" data-key="2">
                                        TabulaMuris_droplet_Bladder</option>
                                    <option value="GSE89232_Human_Blood" data-section="Blood/Human" data-key="0">
                                        GSE89232_Human_Blood</option>
                                    <option value="MouseCellAtlas_Peripheral_Blood" data-section="Blood/Mouse"
                                        data-key="0">MouseCellAtlas_Peripheral_Blood</option>
                                    <option value="MouseCellAtlas_Bone_Marrow" data-section="Bone Marrow/Mouse"
                                        data-key="0">MouseCellAtlas_Bone_Marrow</option>
                                    <option value="TabulaMuris_FACS_Marrow" data-section="Bone Marrow/Mouse"
                                        data-key="1">TabulaMuris_FACS_Marrow</option>
                                    <option value="TabulaMuris_droplet_Marrow" data-section="Bone Marrow/Mouse"
                                        data-key="2">TabulaMuris_droplet_Marrow</option>
                                    <option value="Allen_Human_LGN_level1" data-section="Brain/Human/Regions_Not_Specified" data-key="0">
                                        Allen_Human_LGN_level1</option>
                                    <option value="Allen_Human_LGN_level2" data-section="Brain/Human/Regions_Not_Specified" data-key="1">
                                        Allen_Human_LGN_level2</option>
                                    <option value="Allen_Human_MTG_level1" data-section="Brain/Human/Regions_Not_Specified" data-key="2">
                                        Allen_Human_MTG_level1</option>
                                    <option value="Allen_Human_MTG_level2" data-section="Brain/Human/Regions_Not_Specified" data-key="3">
                                        Allen_Human_MTG_level2</option>
                                    <option value="DroNc_Human_Hippocampus" data-section="Brain/Human/Regions_Not_Specified" data-key="4">
                                        DroNc_Human_Hippocampus</option>
                                    <option value="GSE104276_Human_Prefrontal_cortex_all_ages" data-section="Brain/Human/Regions_Not_Specified"
                                        data-key="5">GSE104276_Human_Prefrontal_cortex_all_ages</option>
                                    <option value="GSE104276_Human_Prefrontal_cortex_per_ages" data-section="Brain/Human/Regions_Not_Specified"
                                        data-key="6">GSE104276_Human_Prefrontal_cortex_per_ages</option>
                                    <option value="GSE67835_Human_Cortex" data-section="Brain/Human/Regions_Not_Specified" data-key="7">
                                        GSE67835_Human_Cortex</option>
                                    <option value="GSE67835_Human_Cortex_woFetal" data-section="Brain/Human/Regions_Not_Specified"
                                        data-key="8">GSE67835_Human_Cortex_woFetal</option>
                                    <option value="Linnarsson_GSE101601_Human_Temporal_cortex" data-section="Brain/Human/Regions_Not_Specified"
                                        data-key="9">Linnarsson_GSE101601_Human_Temporal_cortex</option>
                                    <option value="Linnarsson_GSE76381_Human_Midbrain" data-section="Brain/Human/Regions_Not_Specified"
                                        data-key="10">Linnarsson_GSE76381_Human_Midbrain</option>
                                    <option value="PsychENCODE_Developmental" data-section="Brain/Human/Regions_Not_Specified" data-key="11">
                                        PsychENCODE_Developmental</option>
                                    <option value="PsychENCODE_Adult" data-section="Brain/Human/Regions_Not_Specified" data-key="12">
                                        PsychENCODE_Adult</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Fetal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="13">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Fetal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Neonatal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="14">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Neonatal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Infancy"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="15">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Infancy</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Childhood"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="16">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Childhood</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Adolescence"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="17">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Adolescence</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level1_Adult"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="18">
                                        GSE168408_Human_Prefrontal_Cortex_level1_Adult</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Fetal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="19">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Fetal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Neonatal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="20">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Neonatal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Infancy"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="21">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Infancy</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Childhood"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="22">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Childhood</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Adolescence"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="23">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Adolescence</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level2_Adult"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="24">
                                        GSE168408_Human_Prefrontal_Cortex_level2_Adult</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Fetal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="25">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Fetal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Neonatal"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="26">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Neonatal</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Infancy"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="27">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Infancy</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Childhood"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="28">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Childhood</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Adolescence"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="29">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Adolescence</option>
                                    <option value="GSE168408_Human_Prefrontal_Cortex_level3_Adult"
                                        data-section="Brain/Human/Regions_Not_Specified" data-key="30">
                                        GSE168408_Human_Prefrontal_Cortex_level3_Adult</option>
                                    <option value="3_Gabitto_MTG_Human_2023_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="0">
                                        3_Gabitto_MTG_Human_2023_level1</option>
                                    <option value="3_Gabitto_MTG_Human_2023_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="1">
                                        3_Gabitto_MTG_Human_2023_level2</option>
                                    <option value="3_Gabitto_MTG_Human_2023_level3"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="2">
                                        3_Gabitto_MTG_Human_2023_level3</option>
                                    <option value="4_TorresFlores_Cerebellum_Human_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="0">
                                        4_TorresFlores_Cerebellum_Human_level1</option>
                                    <option value="5_Jakel_WhiteMatter_Human_2019_level1"
                                        data-section="Brain/Human/White Matter" data-key="0">
                                        5_Jakel_WhiteMatter_Human_2019_level1</option>
                                    <option value="5_Jakel_WhiteMatter_Human_2019_level2"
                                        data-section="Brain/Human/White Matter" data-key="1">
                                        5_Jakel_WhiteMatter_Human_2019_level2</option>
                                    <option value="7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="0">
                                        7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level1</option>
                                    <option value="7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="1">
                                        7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level2</option>
                                    <option value="8_Siletti_CerebralCortex.MTG_Human_2022_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="3">
                                        8_Siletti_CerebralCortex.MTG_Human_2022_level1</option>
                                    <option value="8_Siletti_CerebralCortex.MTG_Human_2022_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="4">
                                        8_Siletti_CerebralCortex.MTG_Human_2022_level2</option>
                                    <option value="9_Siletti_CerebralCortex.APH.MEC_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="0">
                                        9_Siletti_CerebralCortex.APH.MEC_Human_2022_level1</option>
                                    <option value="9_Siletti_CerebralCortex.APH.MEC_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="1">
                                        9_Siletti_CerebralCortex.APH.MEC_Human_2022_level2</option>
                                    <option value="10_Siletti_CerebralCortex.PaO.A43_Human_2022_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="0">
                                        10_Siletti_CerebralCortex.PaO.A43_Human_2022_level1</option>
                                    <option value="10_Siletti_CerebralCortex.PaO.A43_Human_2022_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="1">
                                        10_Siletti_CerebralCortex.PaO.A43_Human_2022_level2</option>
                                    <option value="11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="0">
                                        11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level1</option>
                                    <option value="11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="1">
                                        11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level2</option>
                                    <option value="12_Siletti_CerebralCortex.STG_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="2">
                                        12_Siletti_CerebralCortex.STG_Human_2022_level1</option>
                                    <option value="12_Siletti_CerebralCortex.STG_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="3">
                                        12_Siletti_CerebralCortex.STG_Human_2022_level2</option>
                                    <option value="13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="0">
                                        13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level1</option>
                                    <option value="13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level2"
                                        data-section="Brain/Human/Insular Neocortex" data-key="1">
                                        13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level2</option>
                                    <option value="14_Siletti_CerebralCortex.SMG_Human_2022_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="2">
                                        14_Siletti_CerebralCortex.SMG_Human_2022_level1</option>
                                    <option value="14_Siletti_CerebralCortex.SMG_Human_2022_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="3">
                                        14_Siletti_CerebralCortex.SMG_Human_2022_level2</option>
                                    <option value="15_Siletti_CerebralCortex.Ig_Human_2022_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="2">
                                        15_Siletti_CerebralCortex.Ig_Human_2022_level1</option>
                                    <option value="15_Siletti_CerebralCortex.Ig_Human_2022_level2"
                                        data-section="Brain/Human/Insular Neocortex" data-key="3">
                                        15_Siletti_CerebralCortex.Ig_Human_2022_level2</option>
                                    <option value="16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level1"
                                        data-section="Brain/Human/Ventrolateral Prefrontal Cortex" data-key="0">
                                        16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level1</option>
                                    <option value="16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level2"
                                        data-section="Brain/Human/Ventrolateral Prefrontal Cortex" data-key="1">
                                        16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level2</option>
                                    <option value="17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="0">
                                        17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level1</option>
                                    <option value="17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="1">
                                        17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level2</option>
                                    <option value="18_Siletti_CerebralCortex.SCG.A25_Human_2022_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="0">
                                        18_Siletti_CerebralCortex.SCG.A25_Human_2022_level1</option>
                                    <option value="18_Siletti_CerebralCortex.SCG.A25_Human_2022_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="1">
                                        18_Siletti_CerebralCortex.SCG.A25_Human_2022_level2</option>
                                    <option value="19_Siletti_CerebralCortex.TP.A38_Human_2022_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="0">
                                        19_Siletti_CerebralCortex.TP.A38_Human_2022_level1</option>
                                    <option value="19_Siletti_CerebralCortex.TP.A38_Human_2022_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="1">
                                        19_Siletti_CerebralCortex.TP.A38_Human_2022_level2</option>
                                    <option value="20_Siletti_CerebralCortex.Pir_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="0">
                                        20_Siletti_CerebralCortex.Pir_Human_2022_level1</option>
                                    <option value="20_Siletti_CerebralCortex.Pir_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="1">
                                        20_Siletti_CerebralCortex.Pir_Human_2022_level2</option>
                                    <option value="21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="2">
                                        21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level1</option>
                                    <option value="21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="3">
                                        21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level2</option>
                                    <option value="22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="4">
                                        22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level1</option>
                                    <option value="22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="5">
                                        22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level2</option>
                                    <option value="23_Siletti_CerebralCortex.FI_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="2">
                                        23_Siletti_CerebralCortex.FI_Human_2022_level1</option>
                                    <option value="23_Siletti_CerebralCortex.FI_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="3">
                                        23_Siletti_CerebralCortex.FI_Human_2022_level2</option>
                                    <option value="24_Siletti_CerebralCortex.POrG.A13_Human_2022_level1"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="0">
                                        24_Siletti_CerebralCortex.POrG.A13_Human_2022_level1</option>
                                    <option value="24_Siletti_CerebralCortex.POrG.A13_Human_2022_level2"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="1">
                                        24_Siletti_CerebralCortex.POrG.A13_Human_2022_level2</option>
                                    <option value="25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="4">
                                        25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level1</option>
                                    <option value="25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="5">
                                        25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level2</option>
                                    <option value="26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="0">
                                        26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level1</option>
                                    <option value="26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="1">
                                        26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level2</option>
                                    <option value="27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level1"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="0">
                                        27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level1</option>
                                    <option value="27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level2"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="1">
                                        27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level2</option>
                                    <option value="28_Siletti_CerebralCortex.AG.LEC_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="6">
                                        28_Siletti_CerebralCortex.AG.LEC_Human_2022_level1</option>
                                    <option value="28_Siletti_CerebralCortex.AG.LEC_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="7">
                                        28_Siletti_CerebralCortex.AG.LEC_Human_2022_level2</option>
                                    <option value="29_Siletti_CerebralCortex.V2_Human_2022_level1"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="0">
                                        29_Siletti_CerebralCortex.V2_Human_2022_level1</option>
                                    <option value="29_Siletti_CerebralCortex.V2_Human_2022_level2"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="1">
                                        29_Siletti_CerebralCortex.V2_Human_2022_level2</option>
                                    <option value="30_Siletti_CerebralCortex.ACC_Human_2022_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="4">
                                        30_Siletti_CerebralCortex.ACC_Human_2022_level1</option>
                                    <option value="30_Siletti_CerebralCortex.ACC_Human_2022_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="5">
                                        30_Siletti_CerebralCortex.ACC_Human_2022_level2</option>
                                    <option value="31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="4">
                                        31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level1</option>
                                    <option value="31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="5">
                                        31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level2</option>
                                    <option value="32_Siletti_CerebralCortex.AON_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="2">
                                        32_Siletti_CerebralCortex.AON_Human_2022_level1</option>
                                    <option value="32_Siletti_CerebralCortex.AON_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="3">
                                        32_Siletti_CerebralCortex.AON_Human_2022_level2</option>
                                    <option value="33_Siletti_CerebralCortex.MFG.A46_Human_2022_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="0">
                                        33_Siletti_CerebralCortex.MFG.A46_Human_2022_level1</option>
                                    <option value="33_Siletti_CerebralCortex.MFG.A46_Human_2022_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="1">
                                        33_Siletti_CerebralCortex.MFG.A46_Human_2022_level2</option>
                                    <option value="34_Siletti_CerebralCortex.SOG.A19_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="6">
                                        34_Siletti_CerebralCortex.SOG.A19_Human_2022_level1</option>
                                    <option value="34_Siletti_CerebralCortex.SOG.A19_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="7">
                                        34_Siletti_CerebralCortex.SOG.A19_Human_2022_level2</option>
                                    <option value="35_Siletti_CerebralCortex.ITG_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="8">
                                        35_Siletti_CerebralCortex.ITG_Human_2022_level1</option>
                                    <option value="35_Siletti_CerebralCortex.ITG_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="9">
                                        35_Siletti_CerebralCortex.ITG_Human_2022_level2</option>
                                    <option value="36_Siletti_CerebralCortex.Pro_Human_2022_level1"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="2">
                                        36_Siletti_CerebralCortex.Pro_Human_2022_level1</option>
                                    <option value="36_Siletti_CerebralCortex.Pro_Human_2022_level2"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="3">
                                        36_Siletti_CerebralCortex.Pro_Human_2022_level2</option>
                                    <option value="37_Siletti_CerebralCortex.RoG.A32_Human_2022_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="6">
                                        37_Siletti_CerebralCortex.RoG.A32_Human_2022_level1</option>
                                    <option value="37_Siletti_CerebralCortex.RoG.A32_Human_2022_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="7">
                                        37_Siletti_CerebralCortex.RoG.A32_Human_2022_level2</option>
                                    <option value="38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="8">
                                        38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level1</option>
                                    <option value="38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="9">
                                        38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level2</option>
                                    <option value="39_Siletti_CerebralCortex.A35.A35r_Human_2022_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="10">
                                        39_Siletti_CerebralCortex.A35.A35r_Human_2022_level1</option>
                                    <option value="39_Siletti_CerebralCortex.A35.A35r_Human_2022_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="11">
                                        39_Siletti_CerebralCortex.A35.A35r_Human_2022_level2</option>
                                    <option value="40_Siletti_CerebralCortex.ReG.A14_Human_2022_level1"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="2">
                                        40_Siletti_CerebralCortex.ReG.A14_Human_2022_level1</option>
                                    <option value="40_Siletti_CerebralCortex.ReG.A14_Human_2022_level2"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="3">
                                        40_Siletti_CerebralCortex.ReG.A14_Human_2022_level2</option>
                                    <option value="41_Siletti_Cerebellum.CBV_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="1">
                                        41_Siletti_Cerebellum.CBV_Human_2022_level1</option>
                                    <option value="41_Siletti_Cerebellum.CBV_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="2">
                                        41_Siletti_Cerebellum.CBV_Human_2022_level2</option>
                                    <option value="42_Siletti_Cerebellum.CbDN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="3">
                                        42_Siletti_Cerebellum.CbDN_Human_2022_level1</option>
                                    <option value="42_Siletti_Cerebellum.CbDN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="4">
                                        42_Siletti_Cerebellum.CbDN_Human_2022_level2</option>
                                    <option value="43_Siletti_Cerebellum.CBL_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="5">
                                        43_Siletti_Cerebellum.CBL_Human_2022_level1</option>
                                    <option value="43_Siletti_Cerebellum.CBL_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="6">
                                        43_Siletti_Cerebellum.CBL_Human_2022_level2</option>
                                    <option value="44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="0">
                                        44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level1</option>
                                    <option value="44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="1">
                                        44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level2</option>
                                    <option value="45_Siletti_CerebralNuclei.CEN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="2">
                                        45_Siletti_CerebralNuclei.CEN_Human_2022_level1</option>
                                    <option value="45_Siletti_CerebralNuclei.CEN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="3">
                                        45_Siletti_CerebralNuclei.CEN_Human_2022_level2</option>
                                    <option value="46_Siletti_CerebralNuclei.SEP_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="4">
                                        46_Siletti_CerebralNuclei.SEP_Human_2022_level1</option>
                                    <option value="46_Siletti_CerebralNuclei.SEP_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="5">
                                        46_Siletti_CerebralNuclei.SEP_Human_2022_level2</option>
                                    <option value="47_Siletti_CerebralNuclei.Cla_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="6">
                                        47_Siletti_CerebralNuclei.Cla_Human_2022_level1</option>
                                    <option value="47_Siletti_CerebralNuclei.Cla_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="7">
                                        47_Siletti_CerebralNuclei.Cla_Human_2022_level2</option>
                                    <option value="48_Siletti_CerebralNuclei.CMN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="8">
                                        48_Siletti_CerebralNuclei.CMN_Human_2022_level1</option>
                                    <option value="48_Siletti_CerebralNuclei.CMN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="9">
                                        48_Siletti_CerebralNuclei.CMN_Human_2022_level2</option>
                                    <option value="49_Siletti_CerebralNuclei.SI_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="10">
                                        49_Siletti_CerebralNuclei.SI_Human_2022_level1</option>
                                    <option value="49_Siletti_CerebralNuclei.SI_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="11">
                                        49_Siletti_CerebralNuclei.SI_Human_2022_level2</option>
                                    <option value="50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="12">
                                        50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level1</option>
                                    <option value="50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="13">
                                        50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level2</option>
                                    <option value="51_Siletti_CerebralNuclei.BNST_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="14">
                                        51_Siletti_CerebralNuclei.BNST_Human_2022_level1</option>
                                    <option value="51_Siletti_CerebralNuclei.BNST_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="15">
                                        51_Siletti_CerebralNuclei.BNST_Human_2022_level2</option>
                                    <option value="52_Siletti_CerebralNuclei.Pu_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="16">
                                        52_Siletti_CerebralNuclei.Pu_Human_2022_level1</option>
                                    <option value="52_Siletti_CerebralNuclei.Pu_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="17">
                                        52_Siletti_CerebralNuclei.Pu_Human_2022_level2</option>
                                    <option value="53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="18">
                                        53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level1</option>
                                    <option value="53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="19">
                                        53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level2</option>
                                    <option value="54_Siletti_CerebralNuclei.CaB_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="20">
                                        54_Siletti_CerebralNuclei.CaB_Human_2022_level1</option>
                                    <option value="54_Siletti_CerebralNuclei.CaB_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="21">
                                        54_Siletti_CerebralNuclei.CaB_Human_2022_level2</option>
                                    <option value="55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="22">
                                        55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level1</option>
                                    <option value="55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="23">
                                        55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level2</option>
                                    <option value="56_Siletti_CerebralNuclei.NAC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="24">
                                        56_Siletti_CerebralNuclei.NAC_Human_2022_level1</option>
                                    <option value="56_Siletti_CerebralNuclei.NAC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="25">
                                        56_Siletti_CerebralNuclei.NAC_Human_2022_level2</option>
                                    <option value="57_Siletti_CerebralNuclei.BLN.La_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="26">
                                        57_Siletti_CerebralNuclei.BLN.La_Human_2022_level1</option>
                                    <option value="57_Siletti_CerebralNuclei.BLN.La_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="27">
                                        57_Siletti_CerebralNuclei.BLN.La_Human_2022_level2</option>
                                    <option value="58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="28">
                                        58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level1</option>
                                    <option value="58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="29">
                                        58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level2</option>
                                    <option value="59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="4">
                                        59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level1</option>
                                    <option value="59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="5">
                                        59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level2</option>
                                    <option value="60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="6">
                                        60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level1</option>
                                    <option value="60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="7">
                                        60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level2</option>
                                    <option value="61_Siletti_Hippocampus.HiH.CA1_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="8">
                                        61_Siletti_Hippocampus.HiH.CA1_Human_2022_level1</option>
                                    <option value="61_Siletti_Hippocampus.HiH.CA1_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="9">
                                        61_Siletti_Hippocampus.HiH.CA1_Human_2022_level2</option>
                                    <option value="62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="10">
                                        62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level1</option>
                                    <option value="62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="11">
                                        62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level2</option>
                                    <option value="63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="12">
                                        63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level1</option>
                                    <option value="63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="13">
                                        63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level2</option>
                                    <option value="64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="14">
                                        64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level1</option>
                                    <option value="64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="15">
                                        64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level2</option>
                                    <option value="65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="16">
                                        65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level1</option>
                                    <option value="65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="17">
                                        65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level2</option>
                                    <option value="66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="18">
                                        66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level1</option>
                                    <option value="66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="19">
                                        66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level2</option>
                                    <option value="67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="20">
                                        67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level1</option>
                                    <option value="67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="21">
                                        67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level2</option>
                                    <option value="68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="22">
                                        68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level1</option>
                                    <option value="68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="23">
                                        68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level2</option>
                                    <option value="69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level1"
                                        data-section="Brain/Human/Allocortex" data-key="24">
                                        69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level1</option>
                                    <option value="69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level2"
                                        data-section="Brain/Human/Allocortex" data-key="25">
                                        69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level2</option>
                                    <option value="70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="0">
                                        70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level1</option>
                                    <option value="70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="1">
                                        70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level2</option>
                                    <option value="71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="2">
                                        71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level1</option>
                                    <option value="71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="3">
                                        71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level2</option>
                                    <option value="72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="4">
                                        72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level1</option>
                                    <option value="72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="5">
                                        72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level2</option>
                                    <option value="73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="6">
                                        73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level1</option>
                                    <option value="73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="7">
                                        73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level2</option>
                                    <option value="74_Siletti_Hypothalamus.HTHpo_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="8">
                                        74_Siletti_Hypothalamus.HTHpo_Human_2022_level1</option>
                                    <option value="74_Siletti_Hypothalamus.HTHpo_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="9">
                                        74_Siletti_Hypothalamus.HTHpo_Human_2022_level2</option>
                                    <option value="75_Siletti_Hypothalamus.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="10">
                                        75_Siletti_Hypothalamus.HTHtub_Human_2022_level1</option>
                                    <option value="75_Siletti_Hypothalamus.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="11">
                                        75_Siletti_Hypothalamus.HTHtub_Human_2022_level2</option>
                                    <option value="76_Siletti_Hypothalamus.HTHso_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="12">
                                        76_Siletti_Hypothalamus.HTHso_Human_2022_level1</option>
                                    <option value="76_Siletti_Hypothalamus.HTHso_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="13">
                                        76_Siletti_Hypothalamus.HTHso_Human_2022_level2</option>
                                    <option value="77_Siletti_Hypothalamus.HTHma_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="14">
                                        77_Siletti_Hypothalamus.HTHma_Human_2022_level1</option>
                                    <option value="77_Siletti_Hypothalamus.HTHma_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="15">
                                        77_Siletti_Hypothalamus.HTHma_Human_2022_level2</option>
                                    <option value="78_Siletti_Midbrain.SN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="0">
                                        78_Siletti_Midbrain.SN_Human_2022_level1</option>
                                    <option value="78_Siletti_Midbrain.SN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="1">
                                        78_Siletti_Midbrain.SN_Human_2022_level2</option>
                                    <option value="79_Siletti_Midbrain.SN-RN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="2">
                                        79_Siletti_Midbrain.SN-RN_Human_2022_level1</option>
                                    <option value="79_Siletti_Midbrain.SN-RN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="3">
                                        79_Siletti_Midbrain.SN-RN_Human_2022_level2</option>
                                    <option value="80_Siletti_Midbrain.PAG_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="4">
                                        80_Siletti_Midbrain.PAG_Human_2022_level1</option>
                                    <option value="80_Siletti_Midbrain.PAG_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="5">
                                        80_Siletti_Midbrain.PAG_Human_2022_level2</option>
                                    <option value="81_Siletti_Midbrain.IC_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="6">
                                        81_Siletti_Midbrain.IC_Human_2022_level1</option>
                                    <option value="81_Siletti_Midbrain.IC_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="7">
                                        81_Siletti_Midbrain.IC_Human_2022_level2</option>
                                    <option value="82_Siletti_Midbrain.SC_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="8">
                                        82_Siletti_Midbrain.SC_Human_2022_level1</option>
                                    <option value="82_Siletti_Midbrain.SC_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="9">
                                        82_Siletti_Midbrain.SC_Human_2022_level2</option>
                                    <option value="83_Siletti_Midbrain.PTR_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="10">
                                        83_Siletti_Midbrain.PTR_Human_2022_level1</option>
                                    <option value="83_Siletti_Midbrain.PTR_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="11">
                                        83_Siletti_Midbrain.PTR_Human_2022_level2</option>
                                    <option value="84_Siletti_Midbrain.PAG-DR_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="12">
                                        84_Siletti_Midbrain.PAG-DR_Human_2022_level1</option>
                                    <option value="84_Siletti_Midbrain.PAG-DR_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="13">
                                        84_Siletti_Midbrain.PAG-DR_Human_2022_level2</option>
                                    <option value="85_Siletti_Midbrain.RN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="14">
                                        85_Siletti_Midbrain.RN_Human_2022_level1</option>
                                    <option value="85_Siletti_Midbrain.RN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="15">
                                        85_Siletti_Midbrain.RN_Human_2022_level2</option>
                                    <option value="86_Siletti_Myelencephalon.MoAN_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="0">
                                        86_Siletti_Myelencephalon.MoAN_Human_2022_level1</option>
                                    <option value="86_Siletti_Myelencephalon.MoAN_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="1">
                                        86_Siletti_Myelencephalon.MoAN_Human_2022_level2</option>
                                    <option value="87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="2">
                                        87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level1</option>
                                    <option value="87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="3">
                                        87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level2</option>
                                    <option value="88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="4">
                                        88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level1</option>
                                    <option value="88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="5">
                                        88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level2</option>
                                    <option value="89_Siletti_Myelencephalon.MoSR_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="6">
                                        89_Siletti_Myelencephalon.MoSR_Human_2022_level1</option>
                                    <option value="89_Siletti_Myelencephalon.MoSR_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="7">
                                        89_Siletti_Myelencephalon.MoSR_Human_2022_level2</option>
                                    <option value="90_Siletti_Pons.PnRF_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="0">
                                        90_Siletti_Pons.PnRF_Human_2022_level1</option>
                                    <option value="90_Siletti_Pons.PnRF_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="1">
                                        90_Siletti_Pons.PnRF_Human_2022_level2</option>
                                    <option value="91_Siletti_Pons.PnEN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="2">
                                        91_Siletti_Pons.PnEN_Human_2022_level1</option>
                                    <option value="91_Siletti_Pons.PnEN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="3">
                                        91_Siletti_Pons.PnEN_Human_2022_level2</option>
                                    <option value="92_Siletti_Pons.PN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="4">
                                        92_Siletti_Pons.PN_Human_2022_level1</option>
                                    <option value="92_Siletti_Pons.PN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="5">
                                        92_Siletti_Pons.PN_Human_2022_level2</option>
                                    <option value="93_Siletti_Pons.XPnTg.DTg_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="6">
                                        93_Siletti_Pons.XPnTg.DTg_Human_2022_level1</option>
                                    <option value="93_Siletti_Pons.XPnTg.DTg_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="7">
                                        93_Siletti_Pons.XPnTg.DTg_Human_2022_level2</option>
                                    <option value="94_Siletti_Pons.PnRF.PB_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="8">
                                        94_Siletti_Pons.PnRF.PB_Human_2022_level1</option>
                                    <option value="94_Siletti_Pons.PnRF.PB_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="9">
                                        94_Siletti_Pons.PnRF.PB_Human_2022_level2</option>
                                    <option value="95_Siletti_Pons.PnAN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="10">
                                        95_Siletti_Pons.PnAN_Human_2022_level1</option>
                                    <option value="95_Siletti_Pons.PnAN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="11">
                                        95_Siletti_Pons.PnAN_Human_2022_level2</option>
                                    <option value="96_Siletti_Spinalcord.Spc_Human_2022_level1"
                                        data-section="Spinal Cord/Human" data-key="0">
                                        96_Siletti_Spinalcord.Spc_Human_2022_level1</option>
                                    <option value="96_Siletti_Spinalcord.Spc_Human_2022_level2"
                                        data-section="Spinal Cord/Human" data-key="1">
                                        96_Siletti_Spinalcord.Spc_Human_2022_level2</option>
                                    <option value="97_Siletti_Thalamus.PoN.LG_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="0">
                                        97_Siletti_Thalamus.PoN.LG_Human_2022_level1</option>
                                    <option value="97_Siletti_Thalamus.PoN.LG_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="1">
                                        97_Siletti_Thalamus.PoN.LG_Human_2022_level2</option>
                                    <option value="98_Siletti_Thalamus.LNC.Pul_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="2">
                                        98_Siletti_Thalamus.LNC.Pul_Human_2022_level1</option>
                                    <option value="98_Siletti_Thalamus.LNC.Pul_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="3">
                                        98_Siletti_Thalamus.LNC.Pul_Human_2022_level2</option>
                                    <option value="99_Siletti_Thalamus.ANC_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="4">
                                        99_Siletti_Thalamus.ANC_Human_2022_level1</option>
                                    <option value="99_Siletti_Thalamus.ANC_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="5">
                                        99_Siletti_Thalamus.ANC_Human_2022_level2</option>
                                    <option value="100_Siletti_Thalamus.LNC.VLN_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="6">
                                        100_Siletti_Thalamus.LNC.VLN_Human_2022_level1</option>
                                    <option value="100_Siletti_Thalamus.LNC.VLN_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="7">
                                        100_Siletti_Thalamus.LNC.VLN_Human_2022_level2</option>
                                    <option value="101_Siletti_Thalamus.LNC.LP_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="8">
                                        101_Siletti_Thalamus.LNC.LP_Human_2022_level1</option>
                                    <option value="101_Siletti_Thalamus.LNC.LP_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="9">
                                        101_Siletti_Thalamus.LNC.LP_Human_2022_level2</option>
                                    <option value="102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="10">
                                        102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level1</option>
                                    <option value="102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="11">
                                        102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level2</option>
                                    <option value="103_Siletti_Thalamus.ETH_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="12">
                                        103_Siletti_Thalamus.ETH_Human_2022_level1</option>
                                    <option value="103_Siletti_Thalamus.ETH_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="13">
                                        103_Siletti_Thalamus.ETH_Human_2022_level2</option>
                                    <option value="104_Siletti_Thalamus.MNC.MD_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="14">
                                        104_Siletti_Thalamus.MNC.MD_Human_2022_level1</option>
                                    <option value="104_Siletti_Thalamus.MNC.MD_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="15">
                                        104_Siletti_Thalamus.MNC.MD_Human_2022_level2</option>
                                    <option value="105_Siletti_Thalamus.STH_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="16">
                                        105_Siletti_Thalamus.STH_Human_2022_level1</option>
                                    <option value="105_Siletti_Thalamus.STH_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="17">
                                        105_Siletti_Thalamus.STH_Human_2022_level2</option>
                                    <option value="106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="18">
                                        106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level1</option>
                                    <option value="106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="19">
                                        106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level2</option>
                                    <option value="107_Siletti_Thalamus.PoN.MG_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="20">
                                        107_Siletti_Thalamus.PoN.MG_Human_2022_level1</option>
                                    <option value="107_Siletti_Thalamus.PoN.MG_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="21">
                                        107_Siletti_Thalamus.PoN.MG_Human_2022_level2</option>
                                    <option value="108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="22">
                                        108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level1</option>
                                    <option value="108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="23">
                                        108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level2</option>
                                    <option value="109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="24">
                                        109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level1</option>
                                    <option value="109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="25">
                                        109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level2</option>
                                    <option value="110_Siletti_Thalamus.LNC.VA_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="26">
                                        110_Siletti_Thalamus.LNC.VA_Human_2022_level1</option>
                                    <option value="110_Siletti_Thalamus.LNC.VA_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="27">
                                        110_Siletti_Thalamus.LNC.VA_Human_2022_level2</option>
                                    <option value="111_Siletti_Thalamus.LNC.VPL_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="28">
                                        111_Siletti_Thalamus.LNC.VPL_Human_2022_level1</option>
                                    <option value="111_Siletti_Thalamus.LNC.VPL_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="29">
                                        111_Siletti_Thalamus.LNC.VPL_Human_2022_level2</option>
                                    <option value="226_Jorstadetal2023_MTG_Human_2023_smartseq_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="5">
                                        226_Jorstadetal2023_MTG_Human_2023_smartseq_level1</option>
                                    <option value="226_Jorstadetal2023_MTG_Human_2023_smartseq_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="6">
                                        226_Jorstadetal2023_MTG_Human_2023_smartseq_level2</option>
                                    <option value="226_Jorstadetal2023_MTG_Human_2023_smartseq_level3"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="7">
                                        226_Jorstadetal2023_MTG_Human_2023_smartseq_level3</option>
                                    <option value="227_Jorstadetal2023_MTG_Human_2023_10x_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="8">
                                        227_Jorstadetal2023_MTG_Human_2023_10x_level1</option>
                                    <option value="227_Jorstadetal2023_MTG_Human_2023_10x_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="9">
                                        227_Jorstadetal2023_MTG_Human_2023_10x_level2</option>
                                    <option value="227_Jorstadetal2023_MTG_Human_2023_10x_level3"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="10">
                                        227_Jorstadetal2023_MTG_Human_2023_10x_level3</option>
                                    <option value="236_Sepp2023_Cerebellum_Human_2023_group1_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="7">
                                        236_Sepp2023_Cerebellum_Human_2023_group1_level1</option>
                                    <option value="237_Sepp2023_Cerebellum_Human_2023_group2_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="8">
                                        237_Sepp2023_Cerebellum_Human_2023_group2_level1</option>
                                    <option value="238_Sepp2023_Cerebellum_Human_2023_group3_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="9">
                                        238_Sepp2023_Cerebellum_Human_2023_group3_level1</option>
                                    <option value="239_Sepp2023_Cerebellum_Human_2023_group4_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="10">
                                        239_Sepp2023_Cerebellum_Human_2023_group4_level1</option>
                                    <option value="240_Sepp2023_Cerebellum_Human_2023_group5_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="11">
                                        240_Sepp2023_Cerebellum_Human_2023_group5_level1</option>
                                    <option value="241_Sepp2023_Cerebellum_Human_2023_group6_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="12">
                                        241_Sepp2023_Cerebellum_Human_2023_group6_level1</option>
                                    <option value="242_Sepp2023_Cerebellum_Human_2023_group7_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="13">
                                        242_Sepp2023_Cerebellum_Human_2023_group7_level1</option>
                                    <option value="243_Sepp2023_Cerebellum_Human_2023_group8_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="14">
                                        243_Sepp2023_Cerebellum_Human_2023_group8_level1</option>
                                    <option value="244_Sepp2023_Cerebellum_Human_2023_group9_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="15">
                                        244_Sepp2023_Cerebellum_Human_2023_group9_level1</option>
                                    <option value="245_Sepp2023_Cerebellum_Human_2023_group10_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="16">
                                        245_Sepp2023_Cerebellum_Human_2023_group10_level1</option>
                                    <option value="246_Sepp2023_Cerebellum_Human_2023_group11_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="17">
                                        246_Sepp2023_Cerebellum_Human_2023_group11_level1</option>
                                    <option value="247_Zhu2023_Neocortex_Human_2023_group1_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="0">
                                        247_Zhu2023_Neocortex_Human_2023_group1_level1</option>
                                    <option value="247_Zhu2023_Neocortex_Human_2023_group1_level2"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="1">
                                        247_Zhu2023_Neocortex_Human_2023_group1_level2</option>
                                    <option value="248_Zhu2023_Neocortex_Human_2023_group2_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="2">
                                        248_Zhu2023_Neocortex_Human_2023_group2_level1</option>
                                    <option value="248_Zhu2023_Neocortex_Human_2023_group2_level2"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="3">
                                        248_Zhu2023_Neocortex_Human_2023_group2_level2</option>
                                    <option value="249_Zhu2023_Neocortex_Human_2023_group3_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="2">
                                        249_Zhu2023_Neocortex_Human_2023_group3_level1</option>
                                    <option value="249_Zhu2023_Neocortex_Human_2023_group3_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="3">
                                        249_Zhu2023_Neocortex_Human_2023_group3_level2</option>
                                    <option value="250_Zhu2023_Neocortex_Human_2023_group4_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="4">
                                        250_Zhu2023_Neocortex_Human_2023_group4_level1</option>
                                    <option value="250_Zhu2023_Neocortex_Human_2023_group4_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="5">
                                        250_Zhu2023_Neocortex_Human_2023_group4_level2</option>
                                    <option value="251_Zhu2023_Neocortex_Human_2023_group5_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="6">
                                        251_Zhu2023_Neocortex_Human_2023_group5_level1</option>
                                    <option value="251_Zhu2023_Neocortex_Human_2023_group5_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="7">
                                        251_Zhu2023_Neocortex_Human_2023_group5_level2</option>
                                    <option value="252_Zhu2023_Neocortex_Human_2023_group6_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="8">
                                        252_Zhu2023_Neocortex_Human_2023_group6_level1</option>
                                    <option value="252_Zhu2023_Neocortex_Human_2023_group6_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="9">
                                        252_Zhu2023_Neocortex_Human_2023_group6_level2</option>
                                    <option value="253_Smith2021_MidgestationalNeocortex_Human_2021_ParietalLobe_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="10">
                                        253_Smith2021_MidgestationalNeocortex_Human_2021_ParietalLobe_level1</option>
                                    <option value="254_Smith2021_MidgestationalNeocortex_Human_2021_HippocampalFormation_level1"
                                        data-section="Brain/Human/Allocortex" data-key="26">
                                        254_Smith2021_MidgestationalNeocortex_Human_2021_HippocampalFormation_level1</option>
                                    <option value="255_Smith2021_MidgestationalNeocortex_Human_2021_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="2">
                                        255_Smith2021_MidgestationalNeocortex_Human_2021_PrimaryVisualCortex_level1</option>
                                    <option value="256_Smith2021_MidgestationalNeocortex_Human_2021_MedialGanglionicEminence_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="4">
                                        256_Smith2021_MidgestationalNeocortex_Human_2021_MedialGanglionicEminence_level1</option>
                                    <option value="257_Smith2021_MidgestationalNeocortex_Human_2021_CaudalGanglionicEminence_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="5">
                                        257_Smith2021_MidgestationalNeocortex_Human_2021_CaudalGanglionicEminence_level1</option>
                                    <option value="258_Smith2021_MidgestationalNeocortex_Human_2021_OrbitofrontalCortex_level1"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="4">
                                        258_Smith2021_MidgestationalNeocortex_Human_2021_OrbitofrontalCortex_level1</option>
                                    <option value="259_Smith2021_MidgestationalNeocortex_Human_2021_AnteriorCingulateCortex_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="8">
                                        259_Smith2021_MidgestationalNeocortex_Human_2021_AnteriorCingulateCortex_level1</option>
                                    <option value="260_Smith2021_InfantNeocortex_Human_2021_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="0">
                                        260_Smith2021_InfantNeocortex_Human_2021_PrefrontalCortex_level1</option>
                                    <option value="261_Smith2021_InfantNeocortex_Human_2021_TemporalLobe_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="11">
                                        261_Smith2021_InfantNeocortex_Human_2021_TemporalLobe_level1</option>
                                    <option value="262_Smith2021_InfantNeocortex_Human_2021_ParietalLobe_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="12">
                                        262_Smith2021_InfantNeocortex_Human_2021_ParietalLobe_level1</option>
                                    <option value="263_Smith2021_InfantNeocortex_Human_2021_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="3">
                                        263_Smith2021_InfantNeocortex_Human_2021_PrimaryVisualCortex_level1</option>
                                    <option value="264_Aldinger2021_PrenatalCerebellum_Human_2021_9wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="18">
                                        264_Aldinger2021_PrenatalCerebellum_Human_2021_9wpc_level1</option>
                                    <option value="264_Aldinger2021_PrenatalCerebellum_Human_2021_9wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="19">
                                        264_Aldinger2021_PrenatalCerebellum_Human_2021_9wpc_level2</option>
                                    <option value="265_Aldinger2021_PrenatalCerebellum_Human_2021_10wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="20">
                                        265_Aldinger2021_PrenatalCerebellum_Human_2021_10wpc_level1</option>
                                    <option value="265_Aldinger2021_PrenatalCerebellum_Human_2021_10wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="21">
                                        265_Aldinger2021_PrenatalCerebellum_Human_2021_10wpc_level2</option>
                                    <option value="266_Aldinger2021_PrenatalCerebellum_Human_2021_11wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="22">
                                        266_Aldinger2021_PrenatalCerebellum_Human_2021_11wpc_level1</option>
                                    <option value="266_Aldinger2021_PrenatalCerebellum_Human_2021_11wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="23">
                                        266_Aldinger2021_PrenatalCerebellum_Human_2021_11wpc_level2</option>
                                    <option value="267_Aldinger2021_PrenatalCerebellum_Human_2021_12wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="24">
                                        267_Aldinger2021_PrenatalCerebellum_Human_2021_12wpc_level1</option>
                                    <option value="267_Aldinger2021_PrenatalCerebellum_Human_2021_12wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="25">
                                        267_Aldinger2021_PrenatalCerebellum_Human_2021_12wpc_level2</option>
                                    <option value="268_Aldinger2021_PrenatalCerebellum_Human_2021_14wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="26">
                                        268_Aldinger2021_PrenatalCerebellum_Human_2021_14wpc_level1</option>
                                    <option value="268_Aldinger2021_PrenatalCerebellum_Human_2021_14wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="27">
                                        268_Aldinger2021_PrenatalCerebellum_Human_2021_14wpc_level2</option>
                                    <option value="269_Aldinger2021_PrenatalCerebellum_Human_2021_17wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="28">
                                        269_Aldinger2021_PrenatalCerebellum_Human_2021_17wpc_level1</option>
                                    <option value="269_Aldinger2021_PrenatalCerebellum_Human_2021_17wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="29">
                                        269_Aldinger2021_PrenatalCerebellum_Human_2021_17wpc_level2</option>
                                    <option value="270_Aldinger2021_PrenatalCerebellum_Human_2021_18wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="30">
                                        270_Aldinger2021_PrenatalCerebellum_Human_2021_18wpc_level1</option>
                                    <option value="270_Aldinger2021_PrenatalCerebellum_Human_2021_18wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="31">
                                        270_Aldinger2021_PrenatalCerebellum_Human_2021_18wpc_level2</option>
                                    <option value="271_Aldinger2021_PrenatalCerebellum_Human_2021_20wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="32">
                                        271_Aldinger2021_PrenatalCerebellum_Human_2021_20wpc_level1</option>
                                    <option value="271_Aldinger2021_PrenatalCerebellum_Human_2021_20wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="33">
                                        271_Aldinger2021_PrenatalCerebellum_Human_2021_20wpc_level2</option>
                                    <option value="272_Aldinger2021_PrenatalCerebellum_Human_2021_21wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="34">
                                        272_Aldinger2021_PrenatalCerebellum_Human_2021_21wpc_level1</option>
                                    <option value="272_Aldinger2021_PrenatalCerebellum_Human_2021_21wpc_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="35">
                                        272_Aldinger2021_PrenatalCerebellum_Human_2021_21wpc_level2</option>
                                    <option value="273_Bakken2021_AdultM1_Human_2021_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="2">
                                        273_Bakken2021_AdultM1_Human_2021_level1</option>
                                    <option value="273_Bakken2021_AdultM1_Human_2021_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="3">
                                        273_Bakken2021_AdultM1_Human_2021_level2</option>
                                    <option value="273_Bakken2021_AdultM1_Human_2021_level3"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="4">
                                        273_Bakken2021_AdultM1_Human_2021_level3</option>
                                    <option value="276_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="1">
                                        276_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrefrontalCortex_level1</option>
                                    <option value="276_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="2">
                                        276_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrefrontalCortex_level2</option>
                                    <option value="277_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="3">
                                        277_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrefrontalCortex_level1</option>
                                    <option value="277_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="4">
                                        277_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrefrontalCortex_level2</option>
                                    <option value="278_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="5">
                                        278_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrefrontalCortex_level1</option>
                                    <option value="278_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="6">
                                        278_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrefrontalCortex_level2</option>
                                    <option value="279_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="7">
                                        279_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrefrontalCortex_level1</option>
                                    <option value="279_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="8">
                                        279_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrefrontalCortex_level2</option>
                                    <option value="280_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="9">
                                        280_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrefrontalCortex_level1</option>
                                    <option value="280_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="10">
                                        280_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrefrontalCortex_level2</option>
                                    <option value="281_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="11">
                                        281_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrefrontalCortex_level1</option>
                                    <option value="281_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="12">
                                        281_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrefrontalCortex_level2</option>
                                    <option value="282_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="13">
                                        282_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrefrontalCortex_level1</option>
                                    <option value="282_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="14">
                                        282_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrefrontalCortex_level2</option>
                                    <option value="283_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="5">
                                        283_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryMotorCortex_level1</option>
                                    <option value="283_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="6">
                                        283_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryMotorCortex_level2</option>
                                    <option value="284_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="7">
                                        284_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryMotorCortex_level1</option>
                                    <option value="284_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="8">
                                        284_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryMotorCortex_level2</option>
                                    <option value="285_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="9">
                                        285_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryMotorCortex_level1</option>
                                    <option value="285_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="10">
                                        285_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryMotorCortex_level2</option>
                                    <option value="286_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="11">
                                        286_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryMotorCortex_level1</option>
                                    <option value="286_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="12">
                                        286_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryMotorCortex_level2</option>
                                    <option value="287_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="13">
                                        287_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryMotorCortex_level1</option>
                                    <option value="287_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="14">
                                        287_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryMotorCortex_level2</option>
                                    <option value="288_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="15">
                                        288_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryMotorCortex_level1</option>
                                    <option value="288_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="16">
                                        288_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryMotorCortex_level2</option>
                                    <option value="289_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="17">
                                        289_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryMotorCortex_level1</option>
                                    <option value="289_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="18">
                                        289_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryMotorCortex_level2</option>
                                    <option value="290_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="19">
                                        290_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimaryMotorCortex_level1</option>
                                    <option value="290_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimaryMotorCortex_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="20">
                                        290_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimaryMotorCortex_level2</option>
                                    <option value="291_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="6">
                                        291_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_ParietalCortex_level1</option>
                                    <option value="291_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="7">
                                        291_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_ParietalCortex_level2</option>
                                    <option value="292_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="8">
                                        292_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_ParietalCortex_level1</option>
                                    <option value="292_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="9">
                                        292_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_ParietalCortex_level2</option>
                                    <option value="293_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="10">
                                        293_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_ParietalCortex_level1</option>
                                    <option value="293_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="11">
                                        293_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_ParietalCortex_level2</option>
                                    <option value="294_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="12">
                                        294_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_ParietalCortex_level1</option>
                                    <option value="294_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="13">
                                        294_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_ParietalCortex_level2</option>
                                    <option value="295_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="14">
                                        295_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_ParietalCortex_level1</option>
                                    <option value="295_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="15">
                                        295_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_ParietalCortex_level2</option>
                                    <option value="296_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="16">
                                        296_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_ParietalCortex_level1</option>
                                    <option value="296_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="17">
                                        296_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_ParietalCortex_level2</option>
                                    <option value="297_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_ParietalCortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="18">
                                        297_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_ParietalCortex_level1</option>
                                    <option value="297_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_ParietalCortex_level2"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="19">
                                        297_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_ParietalCortex_level2</option>
                                    <option value="298_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="2">
                                        298_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="298_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="3">
                                        298_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="299_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="4">
                                        299_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="299_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="5">
                                        299_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="300_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="6">
                                        300_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="300_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="7">
                                        300_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="301_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="8">
                                        301_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="301_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="9">
                                        301_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="302_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="10">
                                        302_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="302_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="11">
                                        302_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="303_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="12">
                                        303_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="303_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="13">
                                        303_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="304_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="14">
                                        304_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="304_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="15">
                                        304_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="305_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimarySomatosensoryCortex_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="16">
                                        305_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimarySomatosensoryCortex_level1</option>
                                    <option value="305_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimarySomatosensoryCortex_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="17">
                                        305_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_PrimarySomatosensoryCortex_level2</option>
                                    <option value="306_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_TemporalCortex_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="2">
                                        306_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_TemporalCortex_level1</option>
                                    <option value="306_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_TemporalCortex_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="3">
                                        306_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_TemporalCortex_level2</option>
                                    <option value="307_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_TemporalCortex_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="4">
                                        307_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_TemporalCortex_level1</option>
                                    <option value="307_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_TemporalCortex_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="5">
                                        307_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_TemporalCortex_level2</option>
                                    <option value="308_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_TemporalCortex_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="6">
                                        308_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_TemporalCortex_level1</option>
                                    <option value="308_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_TemporalCortex_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="7">
                                        308_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_TemporalCortex_level2</option>
                                    <option value="309_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_TemporalCortex_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="8">
                                        309_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_TemporalCortex_level1</option>
                                    <option value="309_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_TemporalCortex_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="9">
                                        309_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_TemporalCortex_level2</option>
                                    <option value="310_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_TemporalCortex_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="10">
                                        310_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_TemporalCortex_level1</option>
                                    <option value="310_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_TemporalCortex_level2"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="11">
                                        310_Bhaduri2021_PrenatalNeocortex_Human_2021_25wpc_TemporalCortex_level2</option>
                                    <option value="311_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="4">
                                        311_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryVisualCortex_level1</option>
                                    <option value="311_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="5">
                                        311_Bhaduri2021_PrenatalNeocortex_Human_2021_14wpc_PrimaryVisualCortex_level2</option>
                                    <option value="312_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="6">
                                        312_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryVisualCortex_level1</option>
                                    <option value="312_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="7">
                                        312_Bhaduri2021_PrenatalNeocortex_Human_2021_16wpc_PrimaryVisualCortex_level2</option>
                                    <option value="313_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="8">
                                        313_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryVisualCortex_level1</option>
                                    <option value="313_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="9">
                                        313_Bhaduri2021_PrenatalNeocortex_Human_2021_17wpc_PrimaryVisualCortex_level2</option>
                                    <option value="314_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="10">
                                        314_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryVisualCortex_level1</option>
                                    <option value="314_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="11">
                                        314_Bhaduri2021_PrenatalNeocortex_Human_2021_18wpc_PrimaryVisualCortex_level2</option>
                                    <option value="315_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="12">
                                        315_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryVisualCortex_level1</option>
                                    <option value="315_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="13">
                                        315_Bhaduri2021_PrenatalNeocortex_Human_2021_19wpc_PrimaryVisualCortex_level2</option>
                                    <option value="316_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="14">
                                        316_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryVisualCortex_level1</option>
                                    <option value="316_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="15">
                                        316_Bhaduri2021_PrenatalNeocortex_Human_2021_20wpc_PrimaryVisualCortex_level2</option>
                                    <option value="317_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="16">
                                        317_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryVisualCortex_level1</option>
                                    <option value="317_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryVisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="17">
                                        317_Bhaduri2021_PrenatalNeocortex_Human_2021_22wpc_PrimaryVisualCortex_level2</option>
                                    <option value="318_Jorstad2023_M1_Human_2023_10x_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="21">
                                        318_Jorstad2023_M1_Human_2023_10x_level1</option>
                                    <option value="318_Jorstad2023_M1_Human_2023_10x_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="22">
                                        318_Jorstad2023_M1_Human_2023_10x_level2</option>
                                    <option value="318_Jorstad2023_M1_Human_2023_10x_level3"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="23">
                                        318_Jorstad2023_M1_Human_2023_10x_level3</option>
                                    <option value="319_Jorstad2023_S1_Human_2023_10x_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="18">
                                        319_Jorstad2023_S1_Human_2023_10x_level1</option>
                                    <option value="319_Jorstad2023_S1_Human_2023_10x_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="19">
                                        319_Jorstad2023_S1_Human_2023_10x_level2</option>
                                    <option value="319_Jorstad2023_S1_Human_2023_10x_level3"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="20">
                                        319_Jorstad2023_S1_Human_2023_10x_level3</option>
                                    <option value="320_Jorstad2023_A1_Human_2023_10x_level1"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="2">
                                        320_Jorstad2023_A1_Human_2023_10x_level1</option>
                                    <option value="320_Jorstad2023_A1_Human_2023_10x_level2"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="3">
                                        320_Jorstad2023_A1_Human_2023_10x_level2</option>
                                    <option value="320_Jorstad2023_A1_Human_2023_10x_level3"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="4">
                                        320_Jorstad2023_A1_Human_2023_10x_level3</option>
                                    <option value="321_Jorstad2023_V1_Human_2023_10x_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="18">
                                        321_Jorstad2023_V1_Human_2023_10x_level1</option>
                                    <option value="321_Jorstad2023_V1_Human_2023_10x_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="19">
                                        321_Jorstad2023_V1_Human_2023_10x_level2</option>
                                    <option value="321_Jorstad2023_V1_Human_2023_10x_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="20">
                                        321_Jorstad2023_V1_Human_2023_10x_level3</option>
                                    <option value="322_Jorstad2023_DFC_Human_2023_10x_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="10">
                                        322_Jorstad2023_DFC_Human_2023_10x_level1</option>
                                    <option value="322_Jorstad2023_DFC_Human_2023_10x_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="11">
                                        322_Jorstad2023_DFC_Human_2023_10x_level2</option>
                                    <option value="322_Jorstad2023_DFC_Human_2023_10x_level3"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="12">
                                        322_Jorstad2023_DFC_Human_2023_10x_level3</option>
                                    <option value="323_Jorstad2023_ACC_Human_2023_10x_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="9">
                                        323_Jorstad2023_ACC_Human_2023_10x_level1</option>
                                    <option value="323_Jorstad2023_ACC_Human_2023_10x_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="10">
                                        323_Jorstad2023_ACC_Human_2023_10x_level2</option>
                                    <option value="323_Jorstad2023_ACC_Human_2023_10x_level3"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="11">
                                        323_Jorstad2023_ACC_Human_2023_10x_level3</option>
                                    <option value="324_Jorstad2023_MTG_Human_2023_10x_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="11">
                                        324_Jorstad2023_MTG_Human_2023_10x_level1</option>
                                    <option value="324_Jorstad2023_MTG_Human_2023_10x_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="12">
                                        324_Jorstad2023_MTG_Human_2023_10x_level2</option>
                                    <option value="324_Jorstad2023_MTG_Human_2023_10x_level3"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="13">
                                        324_Jorstad2023_MTG_Human_2023_10x_level3</option>
                                    <option value="325_Jorstad2023_AnG_Human_2023_10x_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="13">
                                        325_Jorstad2023_AnG_Human_2023_10x_level1</option>
                                    <option value="325_Jorstad2023_AnG_Human_2023_10x_level2"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="14">
                                        325_Jorstad2023_AnG_Human_2023_10x_level2</option>
                                    <option value="325_Jorstad2023_AnG_Human_2023_10x_level3"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="15">
                                        325_Jorstad2023_AnG_Human_2023_10x_level3</option>
                                    <option value="326_Jorstad2023_M1_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="24">
                                        326_Jorstad2023_M1_Human_2023_Smartseq_level1</option>
                                    <option value="326_Jorstad2023_M1_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="25">
                                        326_Jorstad2023_M1_Human_2023_Smartseq_level2</option>
                                    <option value="326_Jorstad2023_M1_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="26">
                                        326_Jorstad2023_M1_Human_2023_Smartseq_level3</option>
                                    <option value="327_Jorstad2023_S1_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="21">
                                        327_Jorstad2023_S1_Human_2023_Smartseq_level1</option>
                                    <option value="327_Jorstad2023_S1_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="22">
                                        327_Jorstad2023_S1_Human_2023_Smartseq_level2</option>
                                    <option value="327_Jorstad2023_S1_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Primary Somatosensory Cortex" data-key="23">
                                        327_Jorstad2023_S1_Human_2023_Smartseq_level3</option>
                                    <option value="328_Jorstad2023_A1_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="5">
                                        328_Jorstad2023_A1_Human_2023_Smartseq_level1</option>
                                    <option value="328_Jorstad2023_A1_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="6">
                                        328_Jorstad2023_A1_Human_2023_Smartseq_level2</option>
                                    <option value="328_Jorstad2023_A1_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Primary Auditory Cortex" data-key="7">
                                        328_Jorstad2023_A1_Human_2023_Smartseq_level3</option>
                                    <option value="329_Jorstad2023_V1_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="21">
                                        329_Jorstad2023_V1_Human_2023_Smartseq_level1</option>
                                    <option value="329_Jorstad2023_V1_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="22">
                                        329_Jorstad2023_V1_Human_2023_Smartseq_level2</option>
                                    <option value="329_Jorstad2023_V1_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="23">
                                        329_Jorstad2023_V1_Human_2023_Smartseq_level3</option>
                                    <option value="330_Jorstad2023_ACC_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="12">
                                        330_Jorstad2023_ACC_Human_2023_Smartseq_level1</option>
                                    <option value="330_Jorstad2023_ACC_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="13">
                                        330_Jorstad2023_ACC_Human_2023_Smartseq_level2</option>
                                    <option value="330_Jorstad2023_ACC_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="14">
                                        330_Jorstad2023_ACC_Human_2023_Smartseq_level3</option>
                                    <option value="331_Jorstad2023_MTG_Human_2023_Smartseq_level1"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="14">
                                        331_Jorstad2023_MTG_Human_2023_Smartseq_level1</option>
                                    <option value="331_Jorstad2023_MTG_Human_2023_Smartseq_level2"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="15">
                                        331_Jorstad2023_MTG_Human_2023_Smartseq_level2</option>
                                    <option value="331_Jorstad2023_MTG_Human_2023_Smartseq_level3"
                                        data-section="Brain/Human/Middle Temporal Gyrus" data-key="16">
                                        331_Jorstad2023_MTG_Human_2023_Smartseq_level3</option>
                                    <option value="332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level1"
                                        data-section="Brain/Human/White Matter" data-key="2">
                                        332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level1</option>
                                    <option value="332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level2"
                                        data-section="Brain/Human/White Matter" data-key="3">
                                        332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level2</option>
                                    <option value="332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level3"
                                        data-section="Brain/Human/White Matter" data-key="4">
                                        332_Seeker2023_WhiteMatter_Human_2023_BA4_Young_level3</option>
                                    <option value="333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level1"
                                        data-section="Brain/Human/White Matter" data-key="5">
                                        333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level1</option>
                                    <option value="333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level2"
                                        data-section="Brain/Human/White Matter" data-key="6">
                                        333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level2</option>
                                    <option value="333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level3"
                                        data-section="Brain/Human/White Matter" data-key="7">
                                        333_Seeker2023_WhiteMatter_Human_2023_BA4_Old_level3</option>
                                    <option value="334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level1"
                                        data-section="Brain/Human/White Matter" data-key="8">
                                        334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level1</option>
                                    <option value="334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level2"
                                        data-section="Brain/Human/White Matter" data-key="9">
                                        334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level2</option>
                                    <option value="334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level3"
                                        data-section="Brain/Human/White Matter" data-key="10">
                                        334_Seeker2023_WhiteMatter_Human_2023_CB_Young_level3</option>
                                    <option value="335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level1"
                                        data-section="Brain/Human/White Matter" data-key="11">
                                        335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level1</option>
                                    <option value="335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level2"
                                        data-section="Brain/Human/White Matter" data-key="12">
                                        335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level2</option>
                                    <option value="335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level3"
                                        data-section="Brain/Human/White Matter" data-key="13">
                                        335_Seeker2023_WhiteMatter_Human_2023_CB_Old_level3</option>
                                    <option value="336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level1"
                                        data-section="Brain/Human/White Matter" data-key="14">
                                        336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level1</option>
                                    <option value="336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level2"
                                        data-section="Brain/Human/White Matter" data-key="15">
                                        336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level2</option>
                                    <option value="336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level3"
                                        data-section="Brain/Human/White Matter" data-key="16">
                                        336_Seeker2023_WhiteMatter_Human_2023_CSC_Young_level3</option>
                                    <option value="337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level1"
                                        data-section="Brain/Human/White Matter" data-key="17">
                                        337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level1</option>
                                    <option value="337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level2"
                                        data-section="Brain/Human/White Matter" data-key="18">
                                        337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level2</option>
                                    <option value="337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level3"
                                        data-section="Brain/Human/White Matter" data-key="19">
                                        337_Seeker2023_WhiteMatter_Human_2023_CSC_Old_level3</option>
                                    <option value="338_Ma2022_dlPFC_Human_2023_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="13">
                                        338_Ma2022_dlPFC_Human_2023_level1</option>
                                    <option value="338_Ma2022_dlPFC_Human_2023_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="14">
                                        338_Ma2022_dlPFC_Human_2023_level2</option>
                                    <option value="338_Ma2022_dlPFC_Human_2023_level3"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="15">
                                        338_Ma2022_dlPFC_Human_2023_level3</option>
                                    <option value="339_OteroGarcia_NFTs_Human_2022_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="15">
                                        339_OteroGarcia_NFTs_Human_2022_level1</option>
                                    <option value="341_Gittings_FrontalCortex_Human_2023_Part1_level1"
                                        data-section="Brain/Human/Frontal Neocortex" data-key="0">
                                        341_Gittings_FrontalCortex_Human_2023_Part1_level1</option>
                                    <option value="341_Gittings_FrontalCortex_Human_2023_Part1_level2"
                                        data-section="Brain/Human/Frontal Neocortex" data-key="1">
                                        341_Gittings_FrontalCortex_Human_2023_Part1_level2</option>
                                    <option value="342_Gittings_FrontalCortex_Human_2023_Part2_level1"
                                        data-section="Brain/Human/Frontal Neocortex" data-key="2">
                                        342_Gittings_FrontalCortex_Human_2023_Part2_level1</option>
                                    <option value="342_Gittings_FrontalCortex_Human_2023_Part2_level2"
                                        data-section="Brain/Human/Frontal Neocortex" data-key="3">
                                        342_Gittings_FrontalCortex_Human_2023_Part2_level2</option>
                                    <option value="343_Gittings_OccipitalCortex_Human_2023_Part1_level1"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="4">
                                        343_Gittings_OccipitalCortex_Human_2023_Part1_level1</option>
                                    <option value="343_Gittings_OccipitalCortex_Human_2023_Part1_level2"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="5">
                                        343_Gittings_OccipitalCortex_Human_2023_Part1_level2</option>
                                    <option value="344_Gittings_OccipitalCortex_Human_2023_Part2_level1"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="6">
                                        344_Gittings_OccipitalCortex_Human_2023_Part2_level1</option>
                                    <option value="344_Gittings_OccipitalCortex_Human_2023_Part2_level2"
                                        data-section="Brain/Human/Occipital Neocortex" data-key="7">
                                        344_Gittings_OccipitalCortex_Human_2023_Part2_level2</option>
                                    <option value="345_Nascimento_EntorhinalCortex_Human_Fetal_subpallial_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="12">
                                        345_Nascimento_EntorhinalCortex_Human_Fetal_subpallial_level1</option>
                                    <option value="345_Nascimento_EntorhinalCortex_Human_Fetal_subpallial_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="13">
                                        345_Nascimento_EntorhinalCortex_Human_Fetal_subpallial_level2</option>
                                    <option value="346_Nascimento_EntorhinalCortex_Human_Infant_subpallial_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="14">
                                        346_Nascimento_EntorhinalCortex_Human_Infant_subpallial_level1</option>
                                    <option value="346_Nascimento_EntorhinalCortex_Human_Infant_subpallial_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="15">
                                        346_Nascimento_EntorhinalCortex_Human_Infant_subpallial_level2</option>
                                    <option value="347_Nascimento_EntorhinalCortex_Human_Toddler_subpallial_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="16">
                                        347_Nascimento_EntorhinalCortex_Human_Toddler_subpallial_level1</option>
                                    <option value="347_Nascimento_EntorhinalCortex_Human_Toddler_subpallial_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="17">
                                        347_Nascimento_EntorhinalCortex_Human_Toddler_subpallial_level2</option>
                                    <option value="348_Nascimento_EntorhinalCortex_Human_Teen_subpallial_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="18">
                                        348_Nascimento_EntorhinalCortex_Human_Teen_subpallial_level1</option>
                                    <option value="348_Nascimento_EntorhinalCortex_Human_Teen_subpallial_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="19">
                                        348_Nascimento_EntorhinalCortex_Human_Teen_subpallial_level2</option>
                                    <option value="349_Nascimento_EntorhinalCortex_Human_Adult_subpallial_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="20">
                                        349_Nascimento_EntorhinalCortex_Human_Adult_subpallial_level1</option>
                                    <option value="349_Nascimento_EntorhinalCortex_Human_Adult_subpallial_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="21">
                                        349_Nascimento_EntorhinalCortex_Human_Adult_subpallial_level2</option>
                                    <option value="350_Nascimento_GanglionicEminence_Human_Fetal_subpallial_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="6">
                                        350_Nascimento_GanglionicEminence_Human_Fetal_subpallial_level1</option>
                                    <option value="350_Nascimento_GanglionicEminence_Human_Fetal_subpallial_level2"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="7">
                                        350_Nascimento_GanglionicEminence_Human_Fetal_subpallial_level2</option>
                                    <option value="355_Nascimento_EntorhinalCortex_Human_Fetal_interneuron_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="22">
                                        355_Nascimento_EntorhinalCortex_Human_Fetal_interneuron_level1</option>
                                    <option value="355_Nascimento_EntorhinalCortex_Human_Fetal_interneuron_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="23">
                                        355_Nascimento_EntorhinalCortex_Human_Fetal_interneuron_level2</option>
                                    <option value="356_Nascimento_EntorhinalCortex_Human_Infant_interneuron_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="24">
                                        356_Nascimento_EntorhinalCortex_Human_Infant_interneuron_level1</option>
                                    <option value="356_Nascimento_EntorhinalCortex_Human_Infant_interneuron_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="25">
                                        356_Nascimento_EntorhinalCortex_Human_Infant_interneuron_level2</option>
                                    <option value="357_Nascimento_EntorhinalCortex_Human_Toddler_interneuron_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="26">
                                        357_Nascimento_EntorhinalCortex_Human_Toddler_interneuron_level1</option>
                                    <option value="357_Nascimento_EntorhinalCortex_Human_Toddler_interneuron_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="27">
                                        357_Nascimento_EntorhinalCortex_Human_Toddler_interneuron_level2</option>
                                    <option value="358_Nascimento_EntorhinalCortex_Human_Teen_interneuron_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="28">
                                        358_Nascimento_EntorhinalCortex_Human_Teen_interneuron_level1</option>
                                    <option value="358_Nascimento_EntorhinalCortex_Human_Teen_interneuron_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="29">
                                        358_Nascimento_EntorhinalCortex_Human_Teen_interneuron_level2</option>
                                    <option value="359_Nascimento_EntorhinalCortex_Human_Adult_interneuron_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="30">
                                        359_Nascimento_EntorhinalCortex_Human_Adult_interneuron_level1</option>
                                    <option value="359_Nascimento_EntorhinalCortex_Human_Adult_interneuron_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="31">
                                        359_Nascimento_EntorhinalCortex_Human_Adult_interneuron_level2</option>
                                    <option value="360_Nascimento_GanglionicEminence_Human_Fetal_interneuron_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="8">
                                        360_Nascimento_GanglionicEminence_Human_Fetal_interneuron_level1</option>
                                    <option value="360_Nascimento_GanglionicEminence_Human_Fetal_interneuron_level2"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="9">
                                        360_Nascimento_GanglionicEminence_Human_Fetal_interneuron_level2</option>
                                    <option value="365_Nascimento_EntorhinalCortex_Human_Infant_ECstream_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="32">
                                        365_Nascimento_EntorhinalCortex_Human_Infant_ECstream_level1</option>
                                    <option value="365_Nascimento_EntorhinalCortex_Human_Infant_ECstream_level2"
                                        data-section="Brain/Human/Periallocortex" data-key="33">
                                        365_Nascimento_EntorhinalCortex_Human_Infant_ECstream_level2</option>
                                    <option value="366_Velmeshev2023_PrePostNatal_Human_2023_group1_cortex_level1"
                                        data-section="Brain/Human/Neocortex" data-key="0">
                                        366_Velmeshev2023_PrePostNatal_Human_2023_group1_cortex_level1</option>
                                    <option value="367_Velmeshev2023_PrePostNatal_Human_2023_group1_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="12">
                                        367_Velmeshev2023_PrePostNatal_Human_2023_group1_BA22_level1</option>
                                    <option value="368_Velmeshev2023_PrePostNatal_Human_2023_group1_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="16">
                                        368_Velmeshev2023_PrePostNatal_Human_2023_group1_BA9_level1</option>
                                    <option value="369_Velmeshev2023_PrePostNatal_Human_2023_group1_BA13_level1"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="5">
                                        369_Velmeshev2023_PrePostNatal_Human_2023_group1_BA13_level1</option>
                                    <option value="370_Velmeshev2023_PrePostNatal_Human_2023_group1_LGE_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="10">
                                        370_Velmeshev2023_PrePostNatal_Human_2023_group1_LGE_level1</option>
                                    <option value="371_Velmeshev2023_PrePostNatal_Human_2023_group1_CGE_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="11">
                                        371_Velmeshev2023_PrePostNatal_Human_2023_group1_CGE_level1</option>
                                    <option value="372_Velmeshev2023_PrePostNatal_Human_2023_group1_PFC_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="16">
                                        372_Velmeshev2023_PrePostNatal_Human_2023_group1_PFC_level1</option>
                                    <option value="373_Velmeshev2023_PrePostNatal_Human_2023_group1_temporal_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="13">
                                        373_Velmeshev2023_PrePostNatal_Human_2023_group1_temporal_level1</option>
                                    <option value="374_Velmeshev2023_PrePostNatal_Human_2023_group1_cingulate_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="15">
                                        374_Velmeshev2023_PrePostNatal_Human_2023_group1_cingulate_level1</option>
                                    <option value="375_Velmeshev2023_PrePostNatal_Human_2023_group1_ACC_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="16">
                                        375_Velmeshev2023_PrePostNatal_Human_2023_group1_ACC_level1</option>
                                    <option value="376_Velmeshev2023_PrePostNatal_Human_2023_group1_MGE_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="12">
                                        376_Velmeshev2023_PrePostNatal_Human_2023_group1_MGE_level1</option>
                                    <option value="377_Velmeshev2023_PrePostNatal_Human_2023_group1_GE_level1"
                                        data-section="Brain/Human/Transient Structures Of Forebrain" data-key="13">
                                        377_Velmeshev2023_PrePostNatal_Human_2023_group1_GE_level1</option>
                                    <option value="378_Velmeshev2023_PrePostNatal_Human_2023_group1_Frontoparietalcortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="20">
                                        378_Velmeshev2023_PrePostNatal_Human_2023_group1_Frontoparietalcortex_level1</option>
                                    <option value="379_Velmeshev2023_PrePostNatal_Human_2023_group1_BA9-46_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="17">
                                        379_Velmeshev2023_PrePostNatal_Human_2023_group1_BA9-46_level1</option>
                                    <option value="380_Velmeshev2023_PrePostNatal_Human_2023_group2_FC_level1"
                                        data-section="Brain/Human/Frontal Neocortex" data-key="4">
                                        380_Velmeshev2023_PrePostNatal_Human_2023_group2_FC_level1</option>
                                    <option value="381_Velmeshev2023_PrePostNatal_Human_2023_group2_cingulate_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="17">
                                        381_Velmeshev2023_PrePostNatal_Human_2023_group2_cingulate_level1</option>
                                    <option value="382_Velmeshev2023_PrePostNatal_Human_2023_group2_BA13_level1"
                                        data-section="Brain/Human/Orbital Frontal Cortex" data-key="6">
                                        382_Velmeshev2023_PrePostNatal_Human_2023_group2_BA13_level1</option>
                                    <option value="383_Velmeshev2023_PrePostNatal_Human_2023_group2_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="14">
                                        383_Velmeshev2023_PrePostNatal_Human_2023_group2_BA22_level1</option>
                                    <option value="384_Velmeshev2023_PrePostNatal_Human_2023_group2_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="18">
                                        384_Velmeshev2023_PrePostNatal_Human_2023_group2_BA24_level1</option>
                                    <option value="385_Velmeshev2023_PrePostNatal_Human_2023_group2_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="18">
                                        385_Velmeshev2023_PrePostNatal_Human_2023_group2_BA9_level1</option>
                                    <option value="386_Velmeshev2023_PrePostNatal_Human_2023_group2_PFC_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="17">
                                        386_Velmeshev2023_PrePostNatal_Human_2023_group2_PFC_level1</option>
                                    <option value="387_Velmeshev2023_PrePostNatal_Human_2023_group2_STG_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="16">
                                        387_Velmeshev2023_PrePostNatal_Human_2023_group2_STG_level1</option>
                                    <option value="388_Velmeshev2023_PrePostNatal_Human_2023_group2_temporal_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="15">
                                        388_Velmeshev2023_PrePostNatal_Human_2023_group2_temporal_level1</option>
                                    <option value="389_Velmeshev2023_PrePostNatal_Human_2023_group2_Frontoparietalcortex_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="21">
                                        389_Velmeshev2023_PrePostNatal_Human_2023_group2_Frontoparietalcortex_level1</option>
                                    <option value="390_Velmeshev2023_PrePostNatal_Human_2023_group3_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="19">
                                        390_Velmeshev2023_PrePostNatal_Human_2023_group3_BA9_level1</option>
                                    <option value="391_Velmeshev2023_PrePostNatal_Human_2023_group3_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="19">
                                        391_Velmeshev2023_PrePostNatal_Human_2023_group3_BA24_level1</option>
                                    <option value="392_Velmeshev2023_PrePostNatal_Human_2023_group3_INS_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="4">
                                        392_Velmeshev2023_PrePostNatal_Human_2023_group3_INS_level1</option>
                                    <option value="393_Velmeshev2023_PrePostNatal_Human_2023_group3_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="16">
                                        393_Velmeshev2023_PrePostNatal_Human_2023_group3_BA22_level1</option>
                                    <option value="394_Velmeshev2023_PrePostNatal_Human_2023_group3_BA8_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="20">
                                        394_Velmeshev2023_PrePostNatal_Human_2023_group3_BA8_level1</option>
                                    <option value="395_Velmeshev2023_PrePostNatal_Human_2023_group4_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="20">
                                        395_Velmeshev2023_PrePostNatal_Human_2023_group4_BA24_level1</option>
                                    <option value="396_Velmeshev2023_PrePostNatal_Human_2023_group4_INS_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="5">
                                        396_Velmeshev2023_PrePostNatal_Human_2023_group4_INS_level1</option>
                                    <option value="397_Velmeshev2023_PrePostNatal_Human_2023_group4_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="21">
                                        397_Velmeshev2023_PrePostNatal_Human_2023_group4_BA9_level1</option>
                                    <option value="398_Velmeshev2023_PrePostNatal_Human_2023_group4_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="17">
                                        398_Velmeshev2023_PrePostNatal_Human_2023_group4_BA22_level1</option>
                                    <option value="399_Velmeshev2023_PrePostNatal_Human_2023_group4_BA8_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="22">
                                        399_Velmeshev2023_PrePostNatal_Human_2023_group4_BA8_level1</option>
                                    <option value="400_Velmeshev2023_PrePostNatal_Human_2023_group5_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="23">
                                        400_Velmeshev2023_PrePostNatal_Human_2023_group5_BA9_level1</option>
                                    <option value="401_Velmeshev2023_PrePostNatal_Human_2023_group5_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="21">
                                        401_Velmeshev2023_PrePostNatal_Human_2023_group5_BA24_level1</option>
                                    <option value="402_Velmeshev2023_PrePostNatal_Human_2023_group5_INS_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="6">
                                        402_Velmeshev2023_PrePostNatal_Human_2023_group5_INS_level1</option>
                                    <option value="403_Velmeshev2023_PrePostNatal_Human_2023_group5_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="18">
                                        403_Velmeshev2023_PrePostNatal_Human_2023_group5_BA22_level1</option>
                                    <option value="405_Velmeshev2023_PrePostNatal_Human_2023_group6_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="22">
                                        405_Velmeshev2023_PrePostNatal_Human_2023_group6_BA24_level1</option>
                                    <option value="406_Velmeshev2023_PrePostNatal_Human_2023_group6_PFC_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="18">
                                        406_Velmeshev2023_PrePostNatal_Human_2023_group6_PFC_level1</option>
                                    <option value="407_Velmeshev2023_PrePostNatal_Human_2023_group6_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="24">
                                        407_Velmeshev2023_PrePostNatal_Human_2023_group6_BA9_level1</option>
                                    <option value="408_Velmeshev2023_PrePostNatal_Human_2023_group6_FIC_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="7">
                                        408_Velmeshev2023_PrePostNatal_Human_2023_group6_FIC_level1</option>
                                    <option value="409_Velmeshev2023_PrePostNatal_Human_2023_group6_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="19">
                                        409_Velmeshev2023_PrePostNatal_Human_2023_group6_BA22_level1</option>
                                    <option value="410_Velmeshev2023_PrePostNatal_Human_2023_group6_BA46_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="25">
                                        410_Velmeshev2023_PrePostNatal_Human_2023_group6_BA46_level1</option>
                                    <option value="411_Velmeshev2023_PrePostNatal_Human_2023_group7_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="23">
                                        411_Velmeshev2023_PrePostNatal_Human_2023_group7_BA24_level1</option>
                                    <option value="412_Velmeshev2023_PrePostNatal_Human_2023_group7_PFC_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="19">
                                        412_Velmeshev2023_PrePostNatal_Human_2023_group7_PFC_level1</option>
                                    <option value="413_Velmeshev2023_PrePostNatal_Human_2023_group7_BA46_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="26">
                                        413_Velmeshev2023_PrePostNatal_Human_2023_group7_BA46_level1</option>
                                    <option value="414_Velmeshev2023_PrePostNatal_Human_2023_group7_FIC_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="8">
                                        414_Velmeshev2023_PrePostNatal_Human_2023_group7_FIC_level1</option>
                                    <option value="415_Velmeshev2023_PrePostNatal_Human_2023_group7_BA22_level1"
                                        data-section="Brain/Human/Temporal Neocortex" data-key="20">
                                        415_Velmeshev2023_PrePostNatal_Human_2023_group7_BA22_level1</option>
                                    <option value="416_Velmeshev2023_PrePostNatal_Human_2023_group7_BA10_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="20">
                                        416_Velmeshev2023_PrePostNatal_Human_2023_group7_BA10_level1</option>
                                    <option value="417_Velmeshev2023_PrePostNatal_Human_2023_group7_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="27">
                                        417_Velmeshev2023_PrePostNatal_Human_2023_group7_BA9_level1</option>
                                    <option value="418_Velmeshev2023_PrePostNatal_Human_2023_group7_BA8_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="28">
                                        418_Velmeshev2023_PrePostNatal_Human_2023_group7_BA8_level1</option>
                                    <option value="419_Velmeshev2023_PrePostNatal_Human_2023_group8_BA24_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="24">
                                        419_Velmeshev2023_PrePostNatal_Human_2023_group8_BA24_level1</option>
                                    <option value="420_Velmeshev2023_PrePostNatal_Human_2023_group8_BA9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="29">
                                        420_Velmeshev2023_PrePostNatal_Human_2023_group8_BA9_level1</option>
                                    <option value="421_Velmeshev2023_PrePostNatal_Human_2023_group8_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="27">
                                        421_Velmeshev2023_PrePostNatal_Human_2023_group8_PrimaryMotorCortex_level1</option>
                                    <option value="422_Velmeshev2023_PrePostNatal_Human_2023_group8_BA8_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="30">
                                        422_Velmeshev2023_PrePostNatal_Human_2023_group8_BA8_level1</option>
                                    <option value="423_Velmeshev2023_PrePostNatal_Human_2023_group8_BA10_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="21">
                                        423_Velmeshev2023_PrePostNatal_Human_2023_group8_BA10_level1</option>
                                    <option value="424_Leng2021_Human_2021_EC_level1"
                                        data-section="Brain/Human/Periallocortex" data-key="34">
                                        424_Leng2021_Human_2021_EC_level1</option>
                                    <option value="425_Leng2021_Human_2021_SFG_level1"
                                        data-section="Brain/Human/Cerebral Gyri And Lobules" data-key="17">
                                        425_Leng2021_Human_2021_SFG_level1</option>
                                    <option value="426_Kamath2022_Human_2022_SN_level1"
                                        data-section="Brain/Human/Midbrain" data-key="16">
                                        426_Kamath2022_Human_2022_SN_level1</option>
                                    <option value="426_Kamath2022_Human_2022_SN_level2"
                                        data-section="Brain/Human/Midbrain" data-key="17">
                                        426_Kamath2022_Human_2022_SN_level2</option>
                                    <option value="427_Phan2024_Human_2024_CaudateNucleus_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="30">
                                        427_Phan2024_Human_2024_CaudateNucleus_level1</option>
                                    <option value="427_Phan2024_Human_2024_CaudateNucleus_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="31">
                                        427_Phan2024_Human_2024_CaudateNucleus_level2</option>
                                    <option value="428_Phan2024_Human_2024_Putamen_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="32">
                                        428_Phan2024_Human_2024_Putamen_level1</option>
                                    <option value="428_Phan2024_Human_2024_Putamen_level2"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="33">
                                        428_Phan2024_Human_2024_Putamen_level2</option>
                                    <option value="429_Wang2024_Human_2024_FirstTrimester_Neocortex_level1"
                                        data-section="Brain/Human/Neocortex" data-key="1">
                                        429_Wang2024_Human_2024_FirstTrimester_Neocortex_level1</option>
                                    <option value="429_Wang2024_Human_2024_FirstTrimester_Neocortex_level2"
                                        data-section="Brain/Human/Neocortex" data-key="2">
                                        429_Wang2024_Human_2024_FirstTrimester_Neocortex_level2</option>
                                    <option value="429_Wang2024_Human_2024_FirstTrimester_Neocortex_level3"
                                        data-section="Brain/Human/Neocortex" data-key="3">
                                        429_Wang2024_Human_2024_FirstTrimester_Neocortex_level3</option>
                                    <option value="430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="0">
                                        430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level1</option>
                                    <option value="430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level2"
                                        data-section="Brain/Human/Telencephalon" data-key="1">
                                        430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level2</option>
                                    <option value="430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level3"
                                        data-section="Brain/Human/Telencephalon" data-key="2">
                                        430_Wang2024_Human_2024_FirstTrimester_Telencephalon_level3</option>
                                    <option value="431_Wang2024_Human_2024_FirstTrimester_Forebrain_level1"
                                        data-section="Brain/Human/Forebrain" data-key="0">
                                        431_Wang2024_Human_2024_FirstTrimester_Forebrain_level1</option>
                                    <option value="431_Wang2024_Human_2024_FirstTrimester_Forebrain_level2"
                                        data-section="Brain/Human/Forebrain" data-key="1">
                                        431_Wang2024_Human_2024_FirstTrimester_Forebrain_level2</option>
                                    <option value="431_Wang2024_Human_2024_FirstTrimester_Forebrain_level3"
                                        data-section="Brain/Human/Forebrain" data-key="2">
                                        431_Wang2024_Human_2024_FirstTrimester_Forebrain_level3</option>
                                    <option value="432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="22">
                                        432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level1</option>
                                    <option value="432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="23">
                                        432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level2</option>
                                    <option value="432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level3"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="24">
                                        432_Wang2024_Human_2024_SecondTrimester_PrefrontalCortex_level3</option>
                                    <option value="433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="24">
                                        433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level1</option>
                                    <option value="433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="25">
                                        433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level2</option>
                                    <option value="433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="26">
                                        433_Wang2024_Human_2024_SecondTrimester_VisualCortex_level3</option>
                                    <option value="434_Wang2024_Human_2024_SecondTrimester_Neocortex_level1"
                                        data-section="Brain/Human/Neocortex" data-key="4">
                                        434_Wang2024_Human_2024_SecondTrimester_Neocortex_level1</option>
                                    <option value="434_Wang2024_Human_2024_SecondTrimester_Neocortex_level2"
                                        data-section="Brain/Human/Neocortex" data-key="5">
                                        434_Wang2024_Human_2024_SecondTrimester_Neocortex_level2</option>
                                    <option value="434_Wang2024_Human_2024_SecondTrimester_Neocortex_level3"
                                        data-section="Brain/Human/Neocortex" data-key="6">
                                        434_Wang2024_Human_2024_SecondTrimester_Neocortex_level3</option>
                                    <option value="435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="25">
                                        435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level1</option>
                                    <option value="435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="26">
                                        435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level2</option>
                                    <option value="435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level3"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="27">
                                        435_Wang2024_Human_2024_ThirdTrimester_PrefrontalCortex_level3</option>
                                    <option value="436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="28">
                                        436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level1</option>
                                    <option value="436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="29">
                                        436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level2</option>
                                    <option value="436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level3"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="30">
                                        436_Wang2024_Human_2024_ThirdTrimester_BrodmannArea10_level3</option>
                                    <option value="437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="27">
                                        437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level1</option>
                                    <option value="437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="28">
                                        437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level2</option>
                                    <option value="437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="29">
                                        437_Wang2024_Human_2024_ThirdTrimester_BrodmannArea17_level3</option>
                                    <option value="438_Wang2024_Human_2024_Infancy_BrodmannArea10_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="31">
                                        438_Wang2024_Human_2024_Infancy_BrodmannArea10_level1</option>
                                    <option value="438_Wang2024_Human_2024_Infancy_BrodmannArea10_level2"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="32">
                                        438_Wang2024_Human_2024_Infancy_BrodmannArea10_level2</option>
                                    <option value="438_Wang2024_Human_2024_Infancy_BrodmannArea10_level3"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="33">
                                        438_Wang2024_Human_2024_Infancy_BrodmannArea10_level3</option>
                                    <option value="439_Wang2024_Human_2024_Infancy_BrodmannArea17_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="30">
                                        439_Wang2024_Human_2024_Infancy_BrodmannArea17_level1</option>
                                    <option value="439_Wang2024_Human_2024_Infancy_BrodmannArea17_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="31">
                                        439_Wang2024_Human_2024_Infancy_BrodmannArea17_level2</option>
                                    <option value="439_Wang2024_Human_2024_Infancy_BrodmannArea17_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="32">
                                        439_Wang2024_Human_2024_Infancy_BrodmannArea17_level3</option>
                                    <option value="440_Wang2024_Human_2024_Infancy_BrodmannArea9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="31">
                                        440_Wang2024_Human_2024_Infancy_BrodmannArea9_level1</option>
                                    <option value="440_Wang2024_Human_2024_Infancy_BrodmannArea9_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="32">
                                        440_Wang2024_Human_2024_Infancy_BrodmannArea9_level2</option>
                                    <option value="440_Wang2024_Human_2024_Infancy_BrodmannArea9_level3"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="33">
                                        440_Wang2024_Human_2024_Infancy_BrodmannArea9_level3</option>
                                    <option value="441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="33">
                                        441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level1</option>
                                    <option value="441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level2"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="34">
                                        441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level2</option>
                                    <option value="441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level3"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="35">
                                        441_Wang2024_Human_2024_Adolescence_BrodmannArea17_level3</option>
                                    <option value="442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="34">
                                        442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level1</option>
                                    <option value="442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level2"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="35">
                                        442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level2</option>
                                    <option value="442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level3"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="36">
                                        442_Wang2024_Human_2024_Adolescence_BrodmannArea9_level3</option>
                                    <option value="443_Braun2023_Human_2023_FirstTrimester_Brain_CarnegieStage18_level1"
                                        data-section="Brain/Human/Brain" data-key="0">
                                        443_Braun2023_Human_2023_FirstTrimester_Brain_CarnegieStage18_level1</option>
                                    <option value="444_Braun2023_Human_2023_FirstTrimester_Cerebellum_9wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="36">
                                        444_Braun2023_Human_2023_FirstTrimester_Cerebellum_9wpc_level1</option>
                                    <option value="445_Braun2023_Human_2023_FirstTrimester_Cerebellum_CarnegieStage20_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="37">
                                        445_Braun2023_Human_2023_FirstTrimester_Cerebellum_CarnegieStage20_level1</option>
                                    <option value="446_Braun2023_Human_2023_FirstTrimester_Cerebellum_13wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="38">
                                        446_Braun2023_Human_2023_FirstTrimester_Cerebellum_13wpc_level1</option>
                                    <option value="447_Braun2023_Human_2023_FirstTrimester_Cerebellum_12wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="39">
                                        447_Braun2023_Human_2023_FirstTrimester_Cerebellum_12wpc_level1</option>
                                    <option value="448_Braun2023_Human_2023_FirstTrimester_Cerebellum_15wpc_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="40">
                                        448_Braun2023_Human_2023_FirstTrimester_Cerebellum_15wpc_level1</option>
                                    <option value="449_Braun2023_Human_2023_FirstTrimester_Diencephalon_9wpc_level1"
                                        data-section="Brain/Human/Diencephalon" data-key="0">
                                        449_Braun2023_Human_2023_FirstTrimester_Diencephalon_9wpc_level1</option>
                                    <option value="450_Braun2023_Human_2023_FirstTrimester_Diencephalon_CarnegieStage20_level1"
                                        data-section="Brain/Human/Diencephalon" data-key="1">
                                        450_Braun2023_Human_2023_FirstTrimester_Diencephalon_CarnegieStage20_level1</option>
                                    <option value="451_Braun2023_Human_2023_FirstTrimester_Diencephalon_15wpc_level1"
                                        data-section="Brain/Human/Diencephalon" data-key="2">
                                        451_Braun2023_Human_2023_FirstTrimester_Diencephalon_15wpc_level1</option>
                                    <option value="452_Braun2023_Human_2023_FirstTrimester_Forebrain_9wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="3">
                                        452_Braun2023_Human_2023_FirstTrimester_Forebrain_9wpc_level1</option>
                                    <option value="453_Braun2023_Human_2023_FirstTrimester_Forebrain_10wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="4">
                                        453_Braun2023_Human_2023_FirstTrimester_Forebrain_10wpc_level1</option>
                                    <option value="454_Braun2023_Human_2023_FirstTrimester_Forebrain_11wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="5">
                                        454_Braun2023_Human_2023_FirstTrimester_Forebrain_11wpc_level1</option>
                                    <option value="455_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage22_level1"
                                        data-section="Brain/Human/Forebrain" data-key="6">
                                        455_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage22_level1</option>
                                    <option value="456_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage20_level1"
                                        data-section="Brain/Human/Forebrain" data-key="7">
                                        456_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage20_level1</option>
                                    <option value="457_Braun2023_Human_2023_FirstTrimester_Forebrain_13wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="8">
                                        457_Braun2023_Human_2023_FirstTrimester_Forebrain_13wpc_level1</option>
                                    <option value="458_Braun2023_Human_2023_FirstTrimester_Forebrain_12wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="9">
                                        458_Braun2023_Human_2023_FirstTrimester_Forebrain_12wpc_level1</option>
                                    <option value="459_Braun2023_Human_2023_FirstTrimester_Forebrain_14wpc_level1"
                                        data-section="Brain/Human/Forebrain" data-key="10">
                                        459_Braun2023_Human_2023_FirstTrimester_Forebrain_14wpc_level1</option>
                                    <option value="460_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage16_level1"
                                        data-section="Brain/Human/Forebrain" data-key="11">
                                        460_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage16_level1</option>
                                    <option value="461_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage15_level1"
                                        data-section="Brain/Human/Forebrain" data-key="12">
                                        461_Braun2023_Human_2023_FirstTrimester_Forebrain_CarnegieStage15_level1</option>
                                    <option value="462_Braun2023_Human_2023_FirstTrimester_Head_CarnegieStage15_level1"
                                        data-section="Brain/Human/Brain" data-key="1">
                                        462_Braun2023_Human_2023_FirstTrimester_Head_CarnegieStage15_level1</option>
                                    <option value="463_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage20_level1"
                                        data-section="Brain/Human/Hindbrain" data-key="0">
                                        463_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage20_level1</option>
                                    <option value="464_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage15_level1"
                                        data-section="Brain/Human/Hindbrain" data-key="1">
                                        464_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage15_level1</option>
                                    <option value="465_Braun2023_Human_2023_FirstTrimester_Hindbrain_13wpc_level1"
                                        data-section="Brain/Human/Hindbrain" data-key="2">
                                        465_Braun2023_Human_2023_FirstTrimester_Hindbrain_13wpc_level1</option>
                                    <option value="466_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage16_level1"
                                        data-section="Brain/Human/Hindbrain" data-key="3">
                                        466_Braun2023_Human_2023_FirstTrimester_Hindbrain_CarnegieStage16_level1</option>
                                    <option value="467_Braun2023_Human_2023_FirstTrimester_Medulla_9wpc_level1"
                                        data-section="Brain/Human/Medulla" data-key="0">
                                        467_Braun2023_Human_2023_FirstTrimester_Medulla_9wpc_level1</option>
                                    <option value="468_Braun2023_Human_2023_FirstTrimester_Medulla_10wpc_level1"
                                        data-section="Brain/Human/Medulla" data-key="1">
                                        468_Braun2023_Human_2023_FirstTrimester_Medulla_10wpc_level1</option>
                                    <option value="469_Braun2023_Human_2023_FirstTrimester_Medulla_CarnegieStage20_level1"
                                        data-section="Brain/Human/Medulla" data-key="2">
                                        469_Braun2023_Human_2023_FirstTrimester_Medulla_CarnegieStage20_level1</option>
                                    <option value="470_Braun2023_Human_2023_FirstTrimester_Medulla_14wpc_level1"
                                        data-section="Brain/Human/Medulla" data-key="3">
                                        470_Braun2023_Human_2023_FirstTrimester_Medulla_14wpc_level1</option>
                                    <option value="471_Braun2023_Human_2023_FirstTrimester_Medulla_CarnegieStage18_level1"
                                        data-section="Brain/Human/Medulla" data-key="4">
                                        471_Braun2023_Human_2023_FirstTrimester_Medulla_CarnegieStage18_level1</option>
                                    <option value="472_Braun2023_Human_2023_FirstTrimester_Midbrain_9wpc_level1"
                                        data-section="Brain/Human/Midbrain" data-key="18">
                                        472_Braun2023_Human_2023_FirstTrimester_Midbrain_9wpc_level1</option>
                                    <option value="473_Braun2023_Human_2023_FirstTrimester_Midbrain_10wpc_level1"
                                        data-section="Brain/Human/Midbrain" data-key="19">
                                        473_Braun2023_Human_2023_FirstTrimester_Midbrain_10wpc_level1</option>
                                    <option value="474_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage20_level1"
                                        data-section="Brain/Human/Midbrain" data-key="20">
                                        474_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage20_level1</option>
                                    <option value="475_Braun2023_Human_2023_FirstTrimester_Midbrain_13wpc_level1"
                                        data-section="Brain/Human/Midbrain" data-key="21">
                                        475_Braun2023_Human_2023_FirstTrimester_Midbrain_13wpc_level1</option>
                                    <option value="476_Braun2023_Human_2023_FirstTrimester_Midbrain_12wpc_level1"
                                        data-section="Brain/Human/Midbrain" data-key="22">
                                        476_Braun2023_Human_2023_FirstTrimester_Midbrain_12wpc_level1</option>
                                    <option value="477_Braun2023_Human_2023_FirstTrimester_Midbrain_15wpc_level1"
                                        data-section="Brain/Human/Midbrain" data-key="23">
                                        477_Braun2023_Human_2023_FirstTrimester_Midbrain_15wpc_level1</option>
                                    <option value="478_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage16_level1"
                                        data-section="Brain/Human/Midbrain" data-key="24">
                                        478_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage16_level1</option>
                                    <option value="479_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage15_level1"
                                        data-section="Brain/Human/Midbrain" data-key="25">
                                        479_Braun2023_Human_2023_FirstTrimester_Midbrain_CarnegieStage15_level1</option>
                                    <option value="480_Braun2023_Human_2023_FirstTrimester_Pons_9wpc_level1"
                                        data-section="Brain/Human/Pons" data-key="12">
                                        480_Braun2023_Human_2023_FirstTrimester_Pons_9wpc_level1</option>
                                    <option value="481_Braun2023_Human_2023_FirstTrimester_Pons_10wpc_level1"
                                        data-section="Brain/Human/Pons" data-key="13">
                                        481_Braun2023_Human_2023_FirstTrimester_Pons_10wpc_level1</option>
                                    <option value="482_Braun2023_Human_2023_FirstTrimester_Pons_CarnegieStage20_level1"
                                        data-section="Brain/Human/Pons" data-key="14">
                                        482_Braun2023_Human_2023_FirstTrimester_Pons_CarnegieStage20_level1</option>
                                    <option value="483_Braun2023_Human_2023_FirstTrimester_Pons_12wpc_level1"
                                        data-section="Brain/Human/Pons" data-key="15">
                                        483_Braun2023_Human_2023_FirstTrimester_Pons_12wpc_level1</option>
                                    <option value="484_Braun2023_Human_2023_FirstTrimester_Pons_14wpc_level1"
                                        data-section="Brain/Human/Pons" data-key="16">
                                        484_Braun2023_Human_2023_FirstTrimester_Pons_14wpc_level1</option>
                                    <option value="485_Braun2023_Human_2023_FirstTrimester_Telencephalon_9wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="3">
                                        485_Braun2023_Human_2023_FirstTrimester_Telencephalon_9wpc_level1</option>
                                    <option value="486_Braun2023_Human_2023_FirstTrimester_Telencephalon_10wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="4">
                                        486_Braun2023_Human_2023_FirstTrimester_Telencephalon_10wpc_level1</option>
                                    <option value="487_Braun2023_Human_2023_FirstTrimester_Telencephalon_11wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="5">
                                        487_Braun2023_Human_2023_FirstTrimester_Telencephalon_11wpc_level1</option>
                                    <option value="488_Braun2023_Human_2023_FirstTrimester_Telencephalon_CarnegieStage20_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="6">
                                        488_Braun2023_Human_2023_FirstTrimester_Telencephalon_CarnegieStage20_level1</option>
                                    <option value="489_Braun2023_Human_2023_FirstTrimester_Telencephalon_13wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="7">
                                        489_Braun2023_Human_2023_FirstTrimester_Telencephalon_13wpc_level1</option>
                                    <option value="490_Braun2023_Human_2023_FirstTrimester_Telencephalon_12wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="8">
                                        490_Braun2023_Human_2023_FirstTrimester_Telencephalon_12wpc_level1</option>
                                    <option value="491_Braun2023_Human_2023_FirstTrimester_Telencephalon_14wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="9">
                                        491_Braun2023_Human_2023_FirstTrimester_Telencephalon_14wpc_level1</option>
                                    <option value="492_Braun2023_Human_2023_FirstTrimester_Telencephalon_15wpc_level1"
                                        data-section="Brain/Human/Telencephalon" data-key="10">
                                        492_Braun2023_Human_2023_FirstTrimester_Telencephalon_15wpc_level1</option>
                                    <option value="494_Rexach2024_Human_2024_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="36">
                                        494_Rexach2024_Human_2024_PrimaryVisualCortex_level1</option>
                                    <option value="495_Rexach2024_Human_2024_InsularCortex_level1"
                                        data-section="Brain/Human/Insular Neocortex" data-key="9">
                                        495_Rexach2024_Human_2024_InsularCortex_level1</option>
                                    <option value="496_Rexach2024_Human_2024_BrodmannArea4_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="28">
                                        496_Rexach2024_Human_2024_BrodmannArea4_level1</option>
                                    <option value="497_Dharshini2024_Human_2024_BrodmannArea9_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="37">
                                        497_Dharshini2024_Human_2024_BrodmannArea9_level1</option>
                                    <option value="498_Dharshini2024_Human_2024_BrodmannArea7_level1"
                                        data-section="Brain/Human/Parietal Neocortex" data-key="22">
                                        498_Dharshini2024_Human_2024_BrodmannArea7_level1</option>
                                    <option value="499_Dharshini2024_Human_2024_BrodmannArea17_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="37">
                                        499_Dharshini2024_Human_2024_BrodmannArea17_level1</option>
                                    <option value="500_NM2024_Human_2024_VagalNucleus_level1"
                                        data-section="Brain/Human/Vagal Nucleus" data-key="0">
                                        500_NM2024_Human_2024_VagalNucleus_level1</option>
                                    <option value="501_NM2024_Human_2024_PrimaryVisualCortex_level1"
                                        data-section="Brain/Human/Primary Visual Cortex" data-key="38">
                                        501_NM2024_Human_2024_PrimaryVisualCortex_level1</option>
                                    <option value="502_NM2024_Human_2024_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="34">
                                        502_NM2024_Human_2024_PrefrontalCortex_level1</option>
                                    <option value="503_NM2024_Human_2024_PrimaryMotorCortex_level1"
                                        data-section="Brain/Human/Primary Motor Cortex" data-key="29">
                                        503_NM2024_Human_2024_PrimaryMotorCortex_level1</option>
                                    <option value="504_NM2024_Human_2024_GlobusPallidus_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="34">
                                        504_NM2024_Human_2024_GlobusPallidus_level1</option>
                                    <option value="505_Clarence_Human_2025_CaudateNucleus_PostnatalEarly_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="35">
                                        505_Clarence_Human_2025_CaudateNucleus_PostnatalEarly_level1</option>
                                    <option value="506_Clarence_Human_2025_HippocampalFormation_PostnatalEarly_level1"
                                        data-section="Brain/Human/Hippocampal Gyrus Formation" data-key="0">
                                        506_Clarence_Human_2025_HippocampalFormation_PostnatalEarly_level1</option>
                                    <option value="507_Clarence_Human_2025_DorsolateralPrefrontalCortex_PostnatalEarly_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="38">
                                        507_Clarence_Human_2025_DorsolateralPrefrontalCortex_PostnatalEarly_level1</option>
                                    <option value="508_Clarence_Human_2025_AnteriorCingulateCortex_PostnatalEarly_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="25">
                                        508_Clarence_Human_2025_AnteriorCingulateCortex_PostnatalEarly_level1</option>
                                    <option value="509_Clarence_Human_2025_CaudateNucleus_PostnatalLate_level1"
                                        data-section="Brain/Human/Cerebral Nuclei" data-key="36">
                                        509_Clarence_Human_2025_CaudateNucleus_PostnatalLate_level1</option>
                                    <option value="510_Clarence_Human_2025_HippocampalFormation_PostnatalLate_level1"
                                        data-section="Brain/Human/Hippocampal Gyrus Formation" data-key="1">
                                        510_Clarence_Human_2025_HippocampalFormation_PostnatalLate_level1</option>
                                    <option value="511_Clarence_Human_2025_DorsolateralPrefrontalCortex_PostnatalLate_level1"
                                        data-section="Brain/Human/Dorsolateral Prefrontal Cortex" data-key="39">
                                        511_Clarence_Human_2025_DorsolateralPrefrontalCortex_PostnatalLate_level1</option>
                                    <option value="512_Clarence_Human_2025_AnteriorCingulateCortex_PostnatalLate_level1"
                                        data-section="Brain/Human/Cingulate Neocortex" data-key="26">
                                        512_Clarence_Human_2025_AnteriorCingulateCortex_PostnatalLate_level1</option>
                                    <option value="513_Pan_Human_2024_PrefrontalCortex_level1"
                                        data-section="Brain/Human/Prefrontal Cortex" data-key="35">
                                        513_Pan_Human_2024_PrefrontalCortex_level1</option>
                                    <option value="514_Tadross_Human_2025_Hypothalamus_Tadross_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="16">
                                        514_Tadross_Human_2025_Hypothalamus_Tadross_level1</option>
                                    <option value="515_Tadross_Human_2025_Hypothalamus_Siletti_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="17">
                                        515_Tadross_Human_2025_Hypothalamus_Siletti_level1</option>
                                    <option value="539_Travaglini_2020_Blood_level1"
                                        data-section="Blood/Human" data-key="1">
                                        539_Travaglini_2020_Blood_level1</option>
                                    <option value="540_Travaglini_2020_Lung_level1"
                                        data-section="Lung/Human" data-key="0">
                                        540_Travaglini_2020_Lung_level1</option>
                                    <option value="541_Xu_Human_2023_Blood_level1"
                                        data-section="Blood/Human" data-key="2">
                                        541_Xu_Human_2023_Blood_level1</option>
                                    <option value="542_Xu_Human_2023_Bone_marrow_level1"
                                        data-section="Bone Marrow/Human" data-key="0">
                                        542_Xu_Human_2023_Bone_marrow_level1</option>
                                    <option value="543_Xu_Human_2023_Heart_LeftCardiacAtrium_level1"
                                        data-section="Heart/Human" data-key="0">
                                        543_Xu_Human_2023_Heart_LeftCardiacAtrium_level1</option>
                                    <option value="544_Xu_Human_2023_Heart_HeartLeftVentricle_level1"
                                        data-section="Heart/Human" data-key="1">
                                        544_Xu_Human_2023_Heart_HeartLeftVentricle_level1</option>
                                    <option value="545_Xu_Human_2023_Heart_RightCardiacAtrium_level1"
                                        data-section="Heart/Human" data-key="2">
                                        545_Xu_Human_2023_Heart_RightCardiacAtrium_level1</option>
                                    <option value="546_Xu_Human_2023_Heart_HeartRightVentricle_level1"
                                        data-section="Heart/Human" data-key="3">
                                        546_Xu_Human_2023_Heart_HeartRightVentricle_level1</option>
                                    <option value="547_Xu_Human_2023_Heart_ApexOfHeart_level1"
                                        data-section="Heart/Human" data-key="4">
                                        547_Xu_Human_2023_Heart_ApexOfHeart_level1</option>
                                    <option value="548_Xu_Human_2023_Heart_InterventricularSeptum_level1"
                                        data-section="Heart/Human" data-key="5">
                                        548_Xu_Human_2023_Heart_InterventricularSeptum_level1</option>
                                    <option value="549_Xu_Human_2023_Hippocampus_level1"
                                        data-section="Brain/Human/Allocortex" data-key="27">
                                        549_Xu_Human_2023_Hippocampus_level1</option>
                                    <option value="550_Xu_Human_2023_Intestine_AscendingColon_level1"
                                        data-section="Intestine/Human" data-key="0">
                                        550_Xu_Human_2023_Intestine_AscendingColon_level1</option>
                                    <option value="551_Xu_Human_2023_Intestine_Caecum_level1"
                                        data-section="Intestine/Human" data-key="1">
                                        551_Xu_Human_2023_Intestine_Caecum_level1</option>
                                    <option value="552_Xu_Human_2023_Intestine_ColonicEpithelium_level1"
                                        data-section="Intestine/Human" data-key="2">
                                        552_Xu_Human_2023_Intestine_ColonicEpithelium_level1</option>
                                    <option value="553_Xu_Human_2023_Intestine_DescendingColon_level1"
                                        data-section="Intestine/Human" data-key="3">
                                        553_Xu_Human_2023_Intestine_DescendingColon_level1</option>
                                    <option value="554_Xu_Human_2023_Intestine_Duodenum_level1"
                                        data-section="Intestine/Human" data-key="4">
                                        554_Xu_Human_2023_Intestine_Duodenum_level1</option>
                                    <option value="555_Xu_Human_2023_Intestine_Ileum_level1"
                                        data-section="Intestine/Human" data-key="5">
                                        555_Xu_Human_2023_Intestine_Ileum_level1</option>
                                    <option value="556_Xu_Human_2023_Intestine_Jejunum_level1"
                                        data-section="Intestine/Human" data-key="6">
                                        556_Xu_Human_2023_Intestine_Jejunum_level1</option>
                                    <option value="557_Xu_Human_2023_Intestine_LargeIntestine_level1"
                                        data-section="Intestine/Human" data-key="7">
                                        557_Xu_Human_2023_Intestine_LargeIntestine_level1</option>
                                    <option value="558_Xu_Human_2023_Intestine_Rectum_level1"
                                        data-section="Intestine/Human" data-key="8">
                                        558_Xu_Human_2023_Intestine_Rectum_level1</option>
                                    <option value="559_Xu_Human_2023_Intestine_SigmoidColon_level1"
                                        data-section="Intestine/Human" data-key="9">
                                        559_Xu_Human_2023_Intestine_SigmoidColon_level1</option>
                                    <option value="560_Xu_Human_2023_Intestine_SmallIntestine_level1"
                                        data-section="Intestine/Human" data-key="10">
                                        560_Xu_Human_2023_Intestine_SmallIntestine_level1</option>
                                    <option value="561_Xu_Human_2023_Intestine_TransverseColon_level1"
                                        data-section="Intestine/Human" data-key="11">
                                        561_Xu_Human_2023_Intestine_TransverseColon_level1</option>
                                    <option value="562_Xu_Human_2023_Intestine_VermiformAppendix_level1"
                                        data-section="Intestine/Human" data-key="12">
                                        562_Xu_Human_2023_Intestine_VermiformAppendix_level1</option>
                                    <option value="563_Xu_Human_2023_Kidney_CortexOfKidney_level1"
                                        data-section="Kidney/Human" data-key="0">
                                        563_Xu_Human_2023_Kidney_CortexOfKidney_level1</option>
                                    <option value="564_Xu_Human_2023_Kidney_RenalMedulla_level1"
                                        data-section="Kidney/Human" data-key="1">
                                        564_Xu_Human_2023_Kidney_RenalMedulla_level1</option>
                                    <option value="565_Xu_Human_2023_Kidney_RenalPapilla_level1"
                                        data-section="Kidney/Human" data-key="2">
                                        565_Xu_Human_2023_Kidney_RenalPapilla_level1</option>
                                    <option value="566_Xu_Human_2023_Kidney_Kidney_level1"
                                        data-section="Kidney/Human" data-key="3">
                                        566_Xu_Human_2023_Kidney_Kidney_level1</option>
                                    <option value="567_Xu_Human_2023_Kidney_RenalPelvis_level1"
                                        data-section="Kidney/Human" data-key="4">
                                        567_Xu_Human_2023_Kidney_RenalPelvis_level1</option>
                                    <option value="568_Xu_Human_2023_Liver_CaudateLobeOfLiver_level1"
                                        data-section="Liver/Human" data-key="0">
                                        568_Xu_Human_2023_Liver_CaudateLobeOfLiver_level1</option>
                                    <option value="569_Xu_Human_2023_Liver_Liver_level1"
                                        data-section="Liver/Human" data-key="1">
                                        569_Xu_Human_2023_Liver_Liver_level1</option>
                                    <option value="570_Xu_Human_2023_Lung_Lung_level1"
                                        data-section="Lung/Human" data-key="1">
                                        570_Xu_Human_2023_Lung_Lung_level1</option>
                                    <option value="571_Xu_Human_2023_Lung_LowerLobeOfLeftLung_level1"
                                        data-section="Lung/Human" data-key="2">
                                        571_Xu_Human_2023_Lung_LowerLobeOfLeftLung_level1</option>
                                    <option value="572_Xu_Human_2023_Lung_Bronchus_level1"
                                        data-section="Lung/Human" data-key="3">
                                        572_Xu_Human_2023_Lung_Bronchus_level1</option>
                                    <option value="573_Xu_Human_2023_Lung_UpperLobeOfLeftLung_level1"
                                        data-section="Lung/Human" data-key="4">
                                        573_Xu_Human_2023_Lung_UpperLobeOfLeftLung_level1</option>
                                    <option value="574_Xu_Human_2023_Lung_LungParenchyma_level1"
                                        data-section="Lung/Human" data-key="5">
                                        574_Xu_Human_2023_Lung_LungParenchyma_level1</option>
                                    <option value="575_Xu_Human_2023_Lymph_node_BronchopulmonaryLymphNode_level1"
                                        data-section="Lymph Node/Human" data-key="0">
                                        575_Xu_Human_2023_Lymph_node_BronchopulmonaryLymphNode_level1</option>
                                    <option value="576_Xu_Human_2023_Lymph_node_ThoracicLymphNode_level1"
                                        data-section="Lymph Node/Human" data-key="1">
                                        576_Xu_Human_2023_Lymph_node_ThoracicLymphNode_level1</option>
                                    <option value="577_Xu_Human_2023_Lymph_node_MesentericLymphNode_level1"
                                        data-section="Lymph Node/Human" data-key="2">
                                        577_Xu_Human_2023_Lymph_node_MesentericLymphNode_level1</option>
                                    <option value="578_Xu_Human_2023_Lymph_node_LymphNode_level1"
                                        data-section="Lymph Node/Human" data-key="3">
                                        578_Xu_Human_2023_Lymph_node_LymphNode_level1</option>
                                    <option value="579_Xu_Human_2023_Pancreas_IsletOfLangerhans_level1"
                                        data-section="Pancreas/Human" data-key="2">
                                        579_Xu_Human_2023_Pancreas_IsletOfLangerhans_level1</option>
                                    <option value="580_Xu_Human_2023_Pancreas_Pancreas_level1"
                                        data-section="Pancreas/Human" data-key="3">
                                        580_Xu_Human_2023_Pancreas_Pancreas_level1</option>
                                    <option value="581_Xu_Human_2023_Skeletal_muscle_SkeletalMuscleOrganVertebrate_level1"
                                        data-section="Skeletal Muscle/Human" data-key="0">
                                        581_Xu_Human_2023_Skeletal_muscle_SkeletalMuscleOrganVertebrate_level1</option>
                                    <option value="582_Xu_Human_2023_Skeletal_muscle_MuscleOfPelvicDiaphragm_level1"
                                        data-section="Skeletal Muscle/Human" data-key="1">
                                        582_Xu_Human_2023_Skeletal_muscle_MuscleOfPelvicDiaphragm_level1</option>
                                    <option value="583_Xu_Human_2023_Skeletal_muscle_RectusAbdominisMuscle_level1"
                                        data-section="Skeletal Muscle/Human" data-key="2">
                                        583_Xu_Human_2023_Skeletal_muscle_RectusAbdominisMuscle_level1</option>
                                    <option value="584_Xu_Human_2023_Skeletal_muscle_MuscleOfAbdomen_level1"
                                        data-section="Skeletal Muscle/Human" data-key="3">
                                        584_Xu_Human_2023_Skeletal_muscle_MuscleOfAbdomen_level1</option>
                                    <option value="585_Xu_Human_2023_Skeletal_muscle_MuscleTissue_level1"
                                        data-section="Skeletal Muscle/Human" data-key="4">
                                        585_Xu_Human_2023_Skeletal_muscle_MuscleTissue_level1</option>
                                    <option value="586_Xu_Human_2023_Spleen_level1"
                                        data-section="Spleen/Human" data-key="0">
                                        586_Xu_Human_2023_Spleen_level1</option>
                                    <option value="Allen_Mouse_ALM2_level1" data-section="Brain/Mouse" data-key="0">
                                        Allen_Mouse_ALM2_level1</option>
                                    <option value="Allen_Mouse_ALM2_level2" data-section="Brain/Mouse" data-key="1">
                                        Allen_Mouse_ALM2_level2</option>
                                    <option value="Allen_Mouse_ALM2_level3" data-section="Brain/Mouse" data-key="2">
                                        Allen_Mouse_ALM2_level3</option>
                                    <option value="Allen_Mouse_LGd2_level1" data-section="Brain/Mouse" data-key="3">
                                        Allen_Mouse_LGd2_level1</option>
                                    <option value="Allen_Mouse_LGd2_level2" data-section="Brain/Mouse" data-key="4">
                                        Allen_Mouse_LGd2_level2</option>
                                    <option value="Allen_Mouse_LGd2_level3" data-section="Brain/Mouse" data-key="5">
                                        Allen_Mouse_LGd2_level3</option>
                                    <option value="Allen_Mouse_VISp2_level1" data-section="Brain/Mouse" data-key="6">
                                        Allen_Mouse_VISp2_level1</option>
                                    <option value="Allen_Mouse_VISp2_level2" data-section="Brain/Mouse" data-key="7">
                                        Allen_Mouse_VISp2_level2</option>
                                    <option value="Allen_Mouse_VISp2_level3" data-section="Brain/Mouse" data-key="8">
                                        Allen_Mouse_VISp2_level3</option>
                                    <option value="Allen_Mouse_ALM_level1" data-section="Brain/Mouse" data-key="9">
                                        Allen_Mouse_ALM_level1</option>
                                    <option value="Allen_Mouse_ALM_level2" data-section="Brain/Mouse" data-key="10">
                                        Allen_Mouse_ALM_level2</option>
                                    <option value="Allen_Mouse_LGd_level1" data-section="Brain/Mouse" data-key="11">
                                        Allen_Mouse_LGd_level1</option>
                                    <option value="Allen_Mouse_LGd_level2" data-section="Brain/Mouse" data-key="12">
                                        Allen_Mouse_LGd_level2</option>
                                    <option value="Allen_Mouse_VISp_level1" data-section="Brain/Mouse" data-key="13">
                                        Allen_Mouse_VISp_level1</option>
                                    <option value="Allen_Mouse_VISp_level2" data-section="Brain/Mouse" data-key="14">
                                        Allen_Mouse_VISp_level2</option>
                                    <option value="DroNc_Mouse_Hippocampus" data-section="Brain/Mouse" data-key="15">
                                        DroNc_Mouse_Hippocampus</option>
                                    <option value="DropViz_all_level1" data-section="Brain/Mouse" data-key="16">
                                        DropViz_all_level1</option>
                                    <option value="DropViz_all_level2" data-section="Brain/Mouse" data-key="17">
                                        DropViz_all_level2</option>
                                    <option value="DropViz_CB_level1" data-section="Brain/Mouse" data-key="18">
                                        DropViz_CB_level1</option>
                                    <option value="DropViz_CB_level2" data-section="Brain/Mouse" data-key="19">
                                        DropViz_CB_level2</option>
                                    <option value="DropViz_ENT_level1" data-section="Brain/Mouse" data-key="20">
                                        DropViz_ENT_level1</option>
                                    <option value="DropViz_ENT_level2" data-section="Brain/Mouse" data-key="21">
                                        DropViz_ENT_level2</option>
                                    <option value="DropViz_FC_level1" data-section="Brain/Mouse" data-key="22">
                                        DropViz_FC_level1</option>
                                    <option value="DropViz_FC_level2" data-section="Brain/Mouse" data-key="23">
                                        DropViz_FC_level2</option>
                                    <option value="DropViz_GP_level1" data-section="Brain/Mouse" data-key="24">
                                        DropViz_GP_level1</option>
                                    <option value="DropViz_GP_level2" data-section="Brain/Mouse" data-key="25">
                                        DropViz_GP_level2</option>
                                    <option value="DropViz_HC_level1" data-section="Brain/Mouse" data-key="26">
                                        DropViz_HC_level1</option>
                                    <option value="DropViz_HC_level2" data-section="Brain/Mouse" data-key="27">
                                        DropViz_HC_level2</option>
                                    <option value="DropViz_PC_level1" data-section="Brain/Mouse" data-key="28">
                                        DropViz_PC_level1</option>
                                    <option value="DropViz_PC_level2" data-section="Brain/Mouse" data-key="29">
                                        DropViz_PC_level2</option>
                                    <option value="DropViz_SN_level1" data-section="Brain/Mouse" data-key="30">
                                        DropViz_SN_level1</option>
                                    <option value="DropViz_SN_level2" data-section="Brain/Mouse" data-key="31">
                                        DropViz_SN_level2</option>
                                    <option value="DropViz_STR_level1" data-section="Brain/Mouse" data-key="32">
                                        DropViz_STR_level1</option>
                                    <option value="DropViz_STR_level2" data-section="Brain/Mouse" data-key="33">
                                        DropViz_STR_level2</option>
                                    <option value="DropViz_TH_level1" data-section="Brain/Mouse" data-key="34">
                                        DropViz_TH_level1</option>
                                    <option value="DropViz_TH_level2" data-section="Brain/Mouse" data-key="35">
                                        DropViz_TH_level2</option>
                                    <option value="GSE106678_Mouse_Cortex" data-section="Brain/Mouse" data-key="36">
                                        GSE106678_Mouse_Cortex</option>
                                    <option value="GSE82187_Mouse_Striatum" data-section="Brain/Mouse" data-key="37">
                                        GSE82187_Mouse_Striatum</option>
                                    <option value="GSE87544_Mouse_Hypothalamus" data-section="Brain/Mouse"
                                        data-key="38">GSE87544_Mouse_Hypothalamus</option>
                                    <option value="GSE89164_Mouse_Hindbrain" data-section="Brain/Mouse" data-key="39">
                                        GSE89164_Mouse_Hindbrain</option>
                                    <option value="GSE93374_Mouse_Arc_ME_level1" data-section="Brain/Mouse"
                                        data-key="40">GSE93374_Mouse_Arc_ME_level1</option>
                                    <option value="GSE93374_Mouse_Arc_ME_level2" data-section="Brain/Mouse"
                                        data-key="41">GSE93374_Mouse_Arc_ME_level2</option>
                                    <option value="GSE93374_Mouse_Arc_ME_neurons" data-section="Brain/Mouse"
                                        data-key="42">GSE93374_Mouse_Arc_ME_neurons</option>
                                    <option value="GSE98816_Mouse_Brain_Vascular" data-section="Brain/Mouse"
                                        data-key="43">GSE98816_Mouse_Brain_Vascular</option>
                                    <option value="Linnarsson_GSE101601_Mouse_Somatosensory_cortex"
                                        data-section="Brain/Mouse" data-key="44">
                                        Linnarsson_GSE101601_Mouse_Somatosensory_cortex</option>
                                    <option value="Linnarsson_GSE103840_Mouse_Dorsal_horn" data-section="Brain/Mouse"
                                        data-key="45">Linnarsson_GSE103840_Mouse_Dorsal_horn</option>
                                    <option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1"
                                        data-section="Brain/Mouse" data-key="46">
                                        Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level1</option>
                                    <option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2"
                                        data-section="Brain/Mouse" data-key="47">
                                        Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level2</option>
                                    <option value="Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3"
                                        data-section="Brain/Mouse" data-key="48">
                                        Linnarsson_GSE59739_Mouse_Dorsal_root_ganglion_level3</option>
                                    <option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1"
                                        data-section="Brain/Mouse" data-key="49">
                                        Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level1</option>
                                    <option value="Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2"
                                        data-section="Brain/Mouse" data-key="50">
                                        Linnarsson_GSE60361_Mouse_Cortex_Hippocampus_level2</option>
                                    <option value="Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2"
                                        data-section="Brain/Mouse" data-key="51">
                                        Linnarsson_GSE74672_Mouse_Hypothalamus_Neurons_level2</option>
                                    <option value="Linnarsson_GSE74672_Mouse_Hypothalamus_level1"
                                        data-section="Brain/Mouse" data-key="52">
                                        Linnarsson_GSE74672_Mouse_Hypothalamus_level1</option>
                                    <option value="Linnarsson_GSE75330_Mouse_Oligodendrocytes" data-section="Brain/Mouse"
                                        data-key="53">Linnarsson_GSE75330_Mouse_Oligodendrocytes</option>
                                    <option value="Linnarsson_GSE76381_Mouse_Midbrain" data-section="Brain/Mouse"
                                        data-key="54">Linnarsson_GSE76381_Mouse_Midbrain</option>
                                    <option value="Linnarsson_GSE78845_Mouse_Ganglia" data-section="Brain/Mouse"
                                        data-key="55">Linnarsson_GSE78845_Mouse_Ganglia</option>
                                    <option value="Linnarsson_GSE95752_Mouse_Dentate_gyrus" data-section="Brain/Mouse"
                                        data-key="56">Linnarsson_GSE95752_Mouse_Dentate_gyrus</option>
                                    <option value="Linnarsson_GSE95315_Mouse_Dentate_gyrus" data-section="Brain/Mouse"
                                        data-key="57">Linnarsson_GSE95315_Mouse_Dentate_gyrus</option>
                                    <option value="Linnarsson_GSE104323_Mouse_Dentate_gyrus" data-section="Brain/Mouse"
                                        data-key="58">Linnarsson_GSE104323_Mouse_Dentate_gyrus</option>
                                    <option value="MouseCellAtlas_Brain" data-section="Brain/Mouse" data-key="59">
                                        MouseCellAtlas_Brain</option>
                                    <option value="MouseCellAtlas_Fetal_Brain" data-section="Brain/Mouse" data-key="60">
                                        MouseCellAtlas_Fetal_Brain</option>
                                    <option value="MouseCellAtlas_Neonatal_Calvaria" data-section="Brain/Mouse"
                                        data-key="61">MouseCellAtlas_Neonatal_Calvaria</option>
                                    <option value="TabulaMuris_FACS_Brain" data-section="Brain/Mouse" data-key="62">
                                        TabulaMuris_FACS_Brain</option>
                                    <option value="TabulaMuris_FACS_Brain_Myeloid" data-section="Brain/Mouse"
                                        data-key="63">TabulaMuris_FACS_Brain_Myeloid</option>
                                    <option value="TabulaMuris_FACS_Brain_Non-Myeloid" data-section="Brain/Mouse"
                                        data-key="64">TabulaMuris_FACS_Brain_Non-Myeloid</option>
                                    <option value="GSE106707_Mouse_Striatum_Cortex" data-section="Brain/Mouse"
                                        data-key="65">GSE106707_Mouse_Striatum_Cortex</option>
                                    <option value="GSE97478_Mouse_Striatum_Cortex" data-section="Brain/Mouse"
                                        data-key="66">GSE97478_Mouse_Striatum_Cortex</option>
                                    <option value="Linnarsson_MouseBrainAtlas_level5" data-section="Brain/Mouse"
                                        data-key="67">Linnarsson_MouseBrainAtlas_level5</option>
                                    <option value="Linnarsson_MouseBrainAtlas_level6_rank1" data-section="Brain/Mouse"
                                        data-key="68">Linnarsson_MouseBrainAtlas_level6_rank1</option>
                                    <option value="Linnarsson_MouseBrainAtlas_level6_rank2" data-section="Brain/Mouse"
                                        data-key="69">Linnarsson_MouseBrainAtlas_level6_rank2</option>
                                    <option value="Linnarsson_MouseBrainAtlas_level6_rank3" data-section="Brain/Mouse"
                                        data-key="70">Linnarsson_MouseBrainAtlas_level6_rank3</option>
                                    <option value="Linnarsson_MouseBrainAtlas_level6_rank4" data-section="Brain/Mouse"
                                        data-key="71">Linnarsson_MouseBrainAtlas_level6_rank4</option>
                                    <option value="MouseCellAtlas_Mammary_Gland" data-section="Breast/Mouse"
                                        data-key="0">MouseCellAtlas_Mammary_Gland</option>
                                    <option value="TabulaMuris_FACS_Mammary_Gland" data-section="Breast/Mouse"
                                        data-key="1">TabulaMuris_FACS_Mammary_Gland</option>
                                    <option value="TabulaMuris_droplet_Mammary" data-section="Breast/Mouse"
                                        data-key="2">TabulaMuris_droplet_Mammary</option>
                                    <option value="GSE100597_Mouse_Embryo" data-section="Embryo/Mouse" data-key="0">
                                        GSE100597_Mouse_Embryo</option>
                                    <option value="MouseCellAtlas_Embryo_all" data-section="Embryo/Mouse" data-key="1">
                                        MouseCellAtlas_Embryo_all</option>
                                    <option value="GSE92332_Mouse_Epithelium_SMARTseq" data-section="Epithelial/Mouse"
                                        data-key="0">GSE92332_Mouse_Epithelium_SMARTseq</option>
                                    <option value="GSE92332_Mouse_Epithelium_droplet" data-section="Epithelial/Mouse"
                                        data-key="1">GSE92332_Mouse_Epithelium_droplet</option>
                                    <option value="TabulaMuris_FACS_Diaphragm" data-section="Diaphram/Mouse"
                                        data-key="0">TabulaMuris_FACS_Diaphragm</option>
                                    <option value="TabulaMuris_FACS_Fat" data-section="Fat/Mouse" data-key="0">
                                        TabulaMuris_FACS_Fat</option>
                                    <option value="MouseCellAtlas_Neonatal_Heart" data-section="Heart/Mouse"
                                        data-key="0">MouseCellAtlas_Neonatal_Heart</option>
                                    <option value="TabulaMuris_FACS_Heart" data-section="Heart/Mouse" data-key="1">
                                        TabulaMuris_FACS_Heart</option>
                                    <option value="TabulaMuris_droplet_Heart" data-section="Heart/Mouse" data-key="2">
                                        TabulaMuris_droplet_Heart</option>
                                    <option value="MouseCellAtlas_Kidney" data-section="Kidney/Mouse" data-key="0">
                                        MouseCellAtlas_Kidney</option>
                                    <option value="TabulaMuris_FACS_Kidney" data-section="Kidney/Mouse" data-key="1">
                                        TabulaMuris_FACS_Kidney</option>
                                    <option value="TabulaMuris_droplet_Kidney" data-section="Kidney/Mouse"
                                        data-key="2">TabulaMuris_droplet_Kidney</option>
                                    <option value="TabulaMuris_FACS_Large_Intestine" data-section="Large Intestine/Mouse"
                                        data-key="0">TabulaMuris_FACS_Large_Intestine</option>
                                    <option value="MouseCellAtlas_Fetal_Liver" data-section="Liver/Mouse" data-key="0">
                                        MouseCellAtlas_Fetal_Liver</option>
                                    <option value="MouseCellAtlas_Liver" data-section="Liver/Mouse" data-key="1">
                                        MouseCellAtlas_Liver</option>
                                    <option value="TabulaMuris_FACS_Liver" data-section="Liver/Mouse" data-key="2">
                                        TabulaMuris_FACS_Liver</option>
                                    <option value="TabulaMuris_droplet_Liver" data-section="Liver/Mouse" data-key="3">
                                        TabulaMuris_droplet_Liver</option>
                                    <option value="GSE99235_Mouse_Lung_Vascular" data-section="Lung/Mouse"
                                        data-key="0">GSE99235_Mouse_Lung_Vascular</option>
                                    <option value="MouseCellAtlas_Fetal_Lung" data-section="Lung/Mouse" data-key="1">
                                        MouseCellAtlas_Fetal_Lung</option>
                                    <option value="MouseCellAtlas_Lung" data-section="Lung/Mouse" data-key="2">
                                        MouseCellAtlas_Lung</option>
                                    <option value="TabulaMuris_FACS_Lung" data-section="Lung/Mouse" data-key="3">
                                        TabulaMuris_FACS_Lung</option>
                                    <option value="TabulaMuris_droplet_Lung" data-section="Lung/Mouse" data-key="4">
                                        TabulaMuris_droplet_Lung</option>
                                    <option value="MouseCellAtlas_Muscle" data-section="Muscle/Mouse" data-key="0">
                                        MouseCellAtlas_Muscle</option>
                                    <option value="TabulaMuris_FACS_Limb_Muscle" data-section="Muscle/Mouse"
                                        data-key="1">TabulaMuris_FACS_Limb_Muscle</option>
                                    <option value="MouseCellAtlas_Neonatal_Muscle" data-section="Muscle/Mouse"
                                        data-key="2">MouseCellAtlas_Neonatal_Muscle</option>
                                    <option value="TabulaMuris_droplet_Muscle" data-section="Muscle/Mouse"
                                        data-key="3">TabulaMuris_droplet_Muscle</option>
                                    <option value="MouseCellAtlas_Ovary" data-section="Ovary/Mouse" data-key="0">
                                        MouseCellAtlas_Ovary</option>
                                    <option value="GSE81547_Human_Pancreas" data-section="Pancreas/Human" data-key="0">
                                        GSE81547_Human_Pancreas</option>
                                    <option value="GSE84133_Human_Pancreas" data-section="Pancreas/Human" data-key="1">
                                        GSE84133_Human_Pancreas</option>
                                    <option value="GSE84133_Mouse_Pancreas" data-section="Pancreas/Mouse" data-key="0">
                                        GSE84133_Mouse_Pancreas</option>
                                    <option value="MouseCellAtlas_Pancreas" data-section="Pancreas/Mouse" data-key="1">
                                        MouseCellAtlas_Pancreas</option>
                                    <option value="TabulaMuris_FACS_Pancreas" data-section="Pancreas/Mouse"
                                        data-key="2">TabulaMuris_FACS_Pancreas</option>
                                    <option value="MouseCellAtlas_Placenta" data-section="Placenta/Mouse" data-key="0">
                                        MouseCellAtlas_Placenta</option>
                                    <option value="MouseCellAtlas_Prostate" data-section="Prostate/Mouse" data-key="0">
                                        MouseCellAtlas_Prostate</option>
                                    <option value="MouseCellAtlas_Neonatal_Rib" data-section="Ribs/Mouse" data-key="0">
                                        MouseCellAtlas_Neonatal_Rib</option>
                                    <option value="Linnarsson_GSE67602_Mouse_Skin_Epidermis" data-section="Skin/Mouse"
                                        data-key="0">Linnarsson_GSE67602_Mouse_Skin_Epidermis</option>
                                    <option value="TabulaMuris_FACS_Skin" data-section="Skin/Mouse" data-key="1">
                                        TabulaMuris_FACS_Skin</option>
                                    <option value="MouseCellAtlas_Neonatal_Skin" data-section="Skin/Mouse"
                                        data-key="2">MouseCellAtlas_Neonatal_Skin</option>
                                    <option value="MouseCellAtlas_Fetal_Intestine" data-section="Intestine/Mouse"
                                        data-key="0">MouseCellAtlas_Fetal_Intestine</option>
                                    <option value="MouseCellAtlas_Small_Intestine" data-section="Small Intestine/Mouse"
                                        data-key="0">MouseCellAtlas_Small_Intestine</option>
                                    <option value="MouseCellAtlas_Spleen" data-section="Spleen/Mouse" data-key="0">
                                        MouseCellAtlas_Spleen</option>
                                    <option value="TabulaMuris_FACS_Spleen" data-section="Spleen/Mouse" data-key="1">
                                        TabulaMuris_FACS_Spleen</option>
                                    <option value="TabulaMuris_droplet_Spleen" data-section="Spleen/Mouse"
                                        data-key="2">TabulaMuris_droplet_Spleen</option>
                                    <option value="MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured"
                                        data-section="Stem Cell/Mouse" data-key="0">
                                        MouseCellAtlas_Mesenchymal_Stem_Cell_Cultured</option>
                                    <option value="MouseCellAtlas_Trophoblast_Stem_Cell" data-section="Stem Cell/Mouse"
                                        data-key="1">MouseCellAtlas_Trophoblast_Stem_Cell</option>
                                    <option value="MouseCellAtlas_Embryonic_Mesenchyme" data-section="Stem Cell/Mouse"
                                        data-key="2">MouseCellAtlas_Embryonic_Mesenchyme</option>
                                    <option value="MouseCellAtlas_Embryonic_Stem_Cell" data-section="Stem Cell/Mouse"
                                        data-key="3">MouseCellAtlas_Embryonic_Stem_Cell</option>
                                    <option value="MouseCellAtlas_Fetal_Stomache" data-section="Stomach/Mouse"
                                        data-key="0">MouseCellAtlas_Fetal_Stomache</option>
                                    <option value="MouseCellAtlas_Stomach" data-section="Stomach/Mouse" data-key="1">
                                        MouseCellAtlas_Stomach</option>
                                    <option value="MouseCellAtlas_Testis" data-section="Testis/Mouse" data-key="0">
                                        MouseCellAtlas_Testis</option>
                                    <option value="MouseCellAtlas_Thymus" data-section="Thymus/Mouse" data-key="0">
                                        MouseCellAtlas_Thymus</option>
                                    <option value="TabulaMuris_FACS_Thymus" data-section="Thymus/Mouse" data-key="1">
                                        TabulaMuris_FACS_Thymus</option>
                                    <option value="TabulaMuris_droplet_Thymus" data-section="Thymus/Mouse"
                                        data-key="2">TabulaMuris_droplet_Thymus</option>
                                    <option value="TabulaMuris_FACS_Tongue" data-section="Tongue/Mouse" data-key="0">
                                        TabulaMuris_FACS_Tongue</option>
                                    <option value="TabulaMuris_droplet_Tongue" data-section="Tongue/Mouse"
                                        data-key="1">TabulaMuris_droplet_Tongue</option>
                                    <option value="TabulaMuris_FACS_Trachea" data-section="Trachea/Mouse" data-key="0">
                                        TabulaMuris_FACS_Trachea</option>
                                    <option value="TabulaMuris_droplet_Trachea" data-section="Trachea/Mouse"
                                        data-key="1">TabulaMuris_droplet_Trachea</option>
                                    <option value="MouseCellAtlas_Uterus" data-section="Uterus/Mouse" data-key="0">
                                        MouseCellAtlas_Uterus</option>
                                    <option value="MouseCellAtlas_all" data-section="Other/Mouse" data-key="0">
                                        MouseCellAtlas_all</option>
                                    <option value="MouseCellAtlas_Adult_all" data-section="Other/Mouse" data-key="1">
                                        MouseCellAtlas_Adult_all</option>
                                    <option value="MouseCellAtlas_Neonatal_all" data-section="Other/Mouse"
                                        data-key="2">MouseCellAtlas_Neonatal_all</option>
                                    <option value="PBMC_10x_68k" data-section="Other/Human" data-key="3">PBMC_10x_68k
                                    </option>
                                    <option value="TabulaMuris_FACS_all" data-section="Other/Mouse" data-key="4">
                                        TabulaMuris_FACS_all</option>
                                    <option value="TabulaMuris_droplet_all" data-section="Other/Mouse" data-key="5">
                                        TabulaMuris_droplet_all</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body" style="padding-bottom: 10;">
                            <h4>Other options</h4>
                            <div class="form-inline">
                                Multiple test correction method:
                                <select class="form-control" id="adjPmeth" name="adjPmeth" style="width:auto;">
                                    <option selected value="bonferroni">Bonferroni</option>
                                    <option value="BH">Benjamini-Hochberg (FDR)</option>
                                    <option value="BY">Benjamini-Yekutieli</option>
                                    <option value="holm">Holm</option>
                                    <option value="hochberg">Hochberg</option>
                                    <option value="hommel">Hommel</option>
                                </select>
                            </div>
                            <br />
                            <input type="checkbox" id="step2" name="step2"> Perform step 2 (per dataset conditional
                            analysis)
                            if there is more then one significant cell type per dataset.
                            <a class="infoPop" data-toggle="popover"
                                data-content="Step 2 in the workflow is per dataset conditional analysis.
							When there are more than one significant cell types from the same dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
							significant cell types within the dataset. Based on this, forward selection will be performed to identify independent signals.
							See tutorial for details.">
                                <i class="fa fa-question-circle-o fa-lg"></i>
                            </a>
                            <br />
                            <input type="checkbox" id="step3" name="step3"> Perform step 3 (cross-datasets
                            conditional analysis)
                            if there is significant cell types from more than one dataset.
                            <a class="infoPop" data-toggle="popover"
                                data-content="Step 3 in the workflow is cross-datasets conditional analysis.
							When there are significant cell types from more than one dataset, FUMA will perform pair-wise conditional analyses for all possible pairs of
							significant cell types across datasets. See tutorial for details.">
                                <i class="fa fa-question-circle-o fa-lg"></i>
                            </a>
                            <br />
                            <span class="info"><i class="fa fa-info"></i>
                                Step 2 and 3 options are disabled when all scRNA datasets are selected.
                            </span>
                            <br />
                            <br />
                            <div class="form-inline">
                                Title:
                                <input type="text" class="form-control" id="title" name="title" />
                                <span class="info"><i class="fa fa-info"></i> Optional</span>
                            </div>
                        </div>
                    </div>

                    <br />
                    <div id="CheckInput"></div>
                    <input type="submit" value="Submit" class="btn btn-default" id="cellSubmit"
                        name="cellSubmit" /><br /><br />
                    {{ html()->form()->close() }}
                </div>
                @include('celltype.joblist')
                <div id="DIY" class="sidePanel container" style="padding-top:50px;">
                    <h4>Do It Yourself</h4>
                </div>
                @include('celltype.result')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}
    <script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="//d3js.org/d3.v3.min.js"></script>
    <script src="//labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script type="text/javascript" src="//d3js.org/queue.v1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tree-multiselect@2.6.3/dist/jquery.tree-multiselect.min.js"></script>

    {{-- Hand written ones --}}
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var id = "{{ $id }}";
        var status = "{{ $status }}";
        var page = "{{ $page }}";
        var prefix = "{{ $prefix }}";
        var subdir = "{{ Config::get('app.subdir') }}";
        var loggedin = "{{ Auth::check() }}";
    </script>

    {{-- Imports from the project --}}
    <script type="text/javascript" src="{!! URL::asset('js/sidebar.js') !!}?131"></script>
    <script type="text/javascript" src="{!! URL::asset('js/cell_results.js') !!}?135"></script>
    <script type="text/javascript" src="{!! URL::asset('js/celltype.js') !!}?134"></script>

    <script>
        var params = {
            sortable: true
        };
        $("select#cellDataSets").treeMultiselect({
            searchable: true,
            searchParams: ['section', 'text'],
            hideSidePanel: true,
            startCollapsed: true
        });
    </script>
@endsection
