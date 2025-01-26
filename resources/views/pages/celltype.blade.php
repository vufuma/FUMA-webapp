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
                                    <option value="7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Primary motor cortex" data-key="0">
                                        7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level1</option>
                                    <option value="7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Primary motor cortex" data-key="1">
                                        7_Siletti_CerebralCortex.PrCG.M1C_Human_2022_level2</option>
                                    <option value="33_Siletti_CerebralCortex.MFG.A46_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Dorsolateral prefrontal cortex" data-key="2">
                                        33_Siletti_CerebralCortex.MFG.A46_Human_2022_level1</option>
                                    <option value="33_Siletti_CerebralCortex.MFG.A46_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Dorsolateral prefrontal cortex" data-key="3">
                                        33_Siletti_CerebralCortex.MFG.A46_Human_2022_level2</option>
                                    <option value="16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Ventrolateral prefrontal cortex" data-key="4">
                                        16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level1</option>
                                    <option value="16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Ventrolateral prefrontal cortex" data-key="5">
                                        16_Siletti_CerebralCortex.IFG.A44-A45_Human_2022_level2</option>
                                    <option value="24_Siletti_CerebralCortex.POrG.A13_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Orbital frontal cortex" data-key="6">
                                        24_Siletti_CerebralCortex.POrG.A13_Human_2022_level1</option>
                                    <option value="24_Siletti_CerebralCortex.POrG.A13_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Orbital frontal cortex" data-key="7">
                                        24_Siletti_CerebralCortex.POrG.A13_Human_2022_level2</option>
                                    <option value="40_Siletti_CerebralCortex.ReG.A14_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Orbital frontal cortex" data-key="8">
                                        40_Siletti_CerebralCortex.ReG.A14_Human_2022_level1</option>
                                    <option value="40_Siletti_CerebralCortex.ReG.A14_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Orbital frontal cortex" data-key="9">
                                        40_Siletti_CerebralCortex.ReG.A14_Human_2022_level2</option>
                                    <option value="17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Primary somatosensory cortex" data-key="10">
                                        17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level1</option>
                                    <option value="17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Primary somatosensory cortex" data-key="11">
                                        17_Siletti_CerebralCortex.PoCG.S1C_Human_2022_level2</option>
                                    <option value="10_Siletti_CerebralCortex.PaO.A43_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="12">
                                        10_Siletti_CerebralCortex.PaO.A43_Human_2022_level1</option>
                                    <option value="10_Siletti_CerebralCortex.PaO.A43_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="13">
                                        10_Siletti_CerebralCortex.PaO.A43_Human_2022_level2</option>
                                    <option value="14_Siletti_CerebralCortex.SMG_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="14">
                                        14_Siletti_CerebralCortex.SMG_Human_2022_level1</option>
                                    <option value="14_Siletti_CerebralCortex.SMG_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="15">
                                        14_Siletti_CerebralCortex.SMG_Human_2022_level2</option>
                                    <option value="22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="16">
                                        22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level1</option>
                                    <option value="22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Parietal cortex" data-key="17">
                                        22_Siletti_CerebralCortex.SPL.A5-A7_Human_2022_level2</option>
                                    <option value="27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Primary auditory cortex" data-key="18">
                                        27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level1</option>
                                    <option value="27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Primary auditory cortex" data-key="19">
                                        27_Siletti_CerebralCortex.TTG.A1C_Human_2022_level2</option>
                                    <option value="19_Siletti_CerebralCortex.TP.A38_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Temporal cortex" data-key="20">
                                        19_Siletti_CerebralCortex.TP.A38_Human_2022_level1</option>
                                    <option value="19_Siletti_CerebralCortex.TP.A38_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Temporal cortex" data-key="21">
                                        19_Siletti_CerebralCortex.TP.A38_Human_2022_level2</option>
                                    <option value="26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Primary visual cortex" data-key="22">
                                        26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level1</option>
                                    <option value="26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Primary visual cortex" data-key="23">
                                        26_Siletti_CerebralCortex.LiG.V1C_Human_2022_level2</option>
                                    <option value="29_Siletti_CerebralCortex.V2_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Occipital cortex" data-key="24">
                                        29_Siletti_CerebralCortex.V2_Human_2022_level1</option>
                                    <option value="29_Siletti_CerebralCortex.V2_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Occipital cortex" data-key="25">
                                        29_Siletti_CerebralCortex.V2_Human_2022_level2</option>
                                    <option value="36_Siletti_CerebralCortex.Pro_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Occipital cortex" data-key="26">
                                        36_Siletti_CerebralCortex.Pro_Human_2022_level1</option>
                                    <option value="36_Siletti_CerebralCortex.Pro_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Occipital cortex" data-key="27">
                                        36_Siletti_CerebralCortex.Pro_Human_2022_level2</option>
                                    <option value="18_Siletti_CerebralCortex.SCG.A25_Human_2022 _level1"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="28">
                                        18_Siletti_CerebralCortex.SCG.A25_Human_2022 _level1</option>
                                    <option value="18_Siletti_CerebralCortex.SCG.A25_Human_2022 _level2"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="29">
                                        18_Siletti_CerebralCortex.SCG.A25_Human_2022 _level2</option>
                                    <option value="21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="30">
                                        21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level1</option>
                                    <option value="21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="31">
                                        21_Siletti_CerebralCortex.CgGC.A23_Human_2022_level2</option>
                                    <option value="30_Siletti_CerebralCortex.ACC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="32">
                                        30_Siletti_CerebralCortex.ACC_Human_2022_level1</option>
                                    <option value="30_Siletti_CerebralCortex.ACC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="33">
                                        30_Siletti_CerebralCortex.ACC_Human_2022_level2</option>
                                    <option value="37_Siletti_CerebralCortex.RoG.A32_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="34">
                                        37_Siletti_CerebralCortex.RoG.A32_Human_2022_level1</option>
                                    <option value="37_Siletti_CerebralCortex.RoG.A32_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Cingulate cortex" data-key="35">
                                        37_Siletti_CerebralCortex.RoG.A32_Human_2022_level2</option>
                                    <option value="13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Insular cortex" data-key="36">
                                        13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level1</option>
                                    <option value="13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Insular cortex" data-key="37">
                                        13_Siletti_CerebralCortex.LIG.Idg_Human_2022_level2</option>
                                    <option value="15_Siletti_CerebralCortex.Ig_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Insular cortex" data-key="38">
                                        15_Siletti_CerebralCortex.Ig_Human_2022_level1</option>
                                    <option value="15_Siletti_CerebralCortex.Ig_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Insular cortex" data-key="39">
                                        15_Siletti_CerebralCortex.Ig_Human_2022_level2</option>
                                    <option value="20_Siletti_CerebralCortex.Pir_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="40">
                                        20_Siletti_CerebralCortex.Pir_Human_2022_level1</option>
                                    <option value="20_Siletti_CerebralCortex.Pir_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="41">
                                        20_Siletti_CerebralCortex.Pir_Human_2022_level2</option>
                                    <option value="32_Siletti_CerebralCortex.AON_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="42">
                                        32_Siletti_CerebralCortex.AON_Human_2022_level1</option>
                                    <option value="32_Siletti_CerebralCortex.AON_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="43">
                                        32_Siletti_CerebralCortex.AON_Human_2022_level2</option>
                                    <option value="59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="44">
                                        59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level1</option>
                                    <option value="59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="45">
                                        59_Siletti_Hippocampus.HiT.CA4-DGC_Human_2022_level2</option>
                                    <option value="60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="46">
                                        60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level1</option>
                                    <option value="60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="47">
                                        60_Siletti_Hippocampus.HiH.HiT.Sub_Human_2022_level2</option>
                                    <option value="61_Siletti_Hippocampus.HiH.CA1_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="48">
                                        61_Siletti_Hippocampus.HiH.CA1_Human_2022_level1</option>
                                    <option value="61_Siletti_Hippocampus.HiH.CA1_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="49">
                                        61_Siletti_Hippocampus.HiH.CA1_Human_2022_level2</option>
                                    <option value="62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="50">
                                        62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level1</option>
                                    <option value="62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="51">
                                        62_Siletti_Hippocampus.HiH.CA1-3_Human_2022_level2</option>
                                    <option value="63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="52">
                                        63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level1</option>
                                    <option value="63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="53">
                                        63_Siletti_Hippocampus.HiH.CA1-CA3_Human_2022_level2</option>
                                    <option value="64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="54">
                                        64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level1</option>
                                    <option value="64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="55">
                                        64_Siletti_Hippocampus.HiH.DG-CA4_Human_2022_level2</option>
                                    <option value="65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="56">
                                        65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level1</option>
                                    <option value="65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="57">
                                        65_Siletti_Hippocampus.HiB-RostralCA1-CA3_Human_2022_level2</option>
                                    <option value="66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="58">
                                        66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level1</option>
                                    <option value="66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="59">
                                        66_Siletti_Hippocampus.HiH.CA2-3_Human_2022_level2</option>
                                    <option value="67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="60">
                                        67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level1</option>
                                    <option value="67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="61">
                                        67_Siletti_Hippocampus.HiB.RostralCA1-2_Human_2022_level2</option>
                                    <option value="68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="62">
                                        68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level1</option>
                                    <option value="68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="63">
                                        68_Siletti_Hippocampus.HiB.RostralDG-CA4_Human_2022_level2</option>
                                    <option value="69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="64">
                                        69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level1</option>
                                    <option value="69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Allocortex" data-key="65">
                                        69_Siletti_Hippocampus.HiB.RostralCA3_Human_2022_level2</option>
                                    <option value="9_Siletti_CerebralCortex.APH.MEC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="66">
                                        9_Siletti_CerebralCortex.APH.MEC_Human_2022_level1</option>
                                    <option value="9_Siletti_CerebralCortex.APH.MEC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="67">
                                        9_Siletti_CerebralCortex.APH.MEC_Human_2022_level2</option>
                                    <option value="23_Siletti_CerebralCortex.FI_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="68">
                                        23_Siletti_CerebralCortex.FI_Human_2022_level1</option>
                                    <option value="23_Siletti_CerebralCortex.FI_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="69">
                                        23_Siletti_CerebralCortex.FI_Human_2022_level2</option>
                                    <option value="25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="70">
                                        25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level1</option>
                                    <option value="25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="71">
                                        25_Siletti_CerebralCortex.CgGrs.A29-A30_Human_2022_level2</option>
                                    <option value="28_Siletti_CerebralCortex.AG.LEC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="72">
                                        28_Siletti_CerebralCortex.AG.LEC_Human_2022_level1</option>
                                    <option value="28_Siletti_CerebralCortex.AG.LEC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="73">
                                        28_Siletti_CerebralCortex.AG.LEC_Human_2022_level2</option>
                                    <option value="38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="74">
                                        38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level1</option>
                                    <option value="38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="75">
                                        38_Siletti_CerebralCortex.PRG.A35-A36_Human_2022_level2</option>
                                    <option value="39_Siletti_CerebralCortex.A35.A35r_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="76">
                                        39_Siletti_CerebralCortex.A35.A35r_Human_2022_level1</option>
                                    <option value="39_Siletti_CerebralCortex.A35.A35r_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral cortex - Periallocortex" data-key="77">
                                        39_Siletti_CerebralCortex.A35.A35r_Human_2022_level2</option>
                                    <option value="44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="78">
                                        44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level1</option>
                                    <option value="44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="79">
                                        44_Siletti_CerebralNuclei.GP.Gpe_Human_2022_level2</option>
                                    <option value="45_Siletti_CerebralNuclei.CEN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="80">
                                        45_Siletti_CerebralNuclei.CEN_Human_2022_level1</option>
                                    <option value="45_Siletti_CerebralNuclei.CEN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="81">
                                        45_Siletti_CerebralNuclei.CEN_Human_2022_level2</option>
                                    <option value="46_Siletti_CerebralNuclei.SEP_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="82">
                                        46_Siletti_CerebralNuclei.SEP_Human_2022_level1</option>
                                    <option value="46_Siletti_CerebralNuclei.SEP_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="83">
                                        46_Siletti_CerebralNuclei.SEP_Human_2022_level2</option>
                                    <option value="47_Siletti_CerebralNuclei.Cla_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="84">
                                        47_Siletti_CerebralNuclei.Cla_Human_2022_level1</option>
                                    <option value="47_Siletti_CerebralNuclei.Cla_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="85">
                                        47_Siletti_CerebralNuclei.Cla_Human_2022_level2</option>
                                    <option value="48_Siletti_CerebralNuclei.CMN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="86">
                                        48_Siletti_CerebralNuclei.CMN_Human_2022_level1</option>
                                    <option value="48_Siletti_CerebralNuclei.CMN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="87">
                                        48_Siletti_CerebralNuclei.CMN_Human_2022_level2</option>
                                    <option value="49_Siletti_CerebralNuclei.SI_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="88">
                                        49_Siletti_CerebralNuclei.SI_Human_2022_level1</option>
                                    <option value="49_Siletti_CerebralNuclei.SI_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="89">
                                        49_Siletti_CerebralNuclei.SI_Human_2022_level2</option>
                                    <option value="50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="90">
                                        50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level1</option>
                                    <option value="50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="91">
                                        50_Siletti_CerebralNuclei.BLN.BL_Human_2022_level2</option>
                                    <option value="51_Siletti_CerebralNuclei.BNST_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="92">
                                        51_Siletti_CerebralNuclei.BNST_Human_2022_level1</option>
                                    <option value="51_Siletti_CerebralNuclei.BNST_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="93">
                                        51_Siletti_CerebralNuclei.BNST_Human_2022_level2</option>
                                    <option value="52_Siletti_CerebralNuclei.Pu_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="94">
                                        52_Siletti_CerebralNuclei.Pu_Human_2022_level1</option>
                                    <option value="52_Siletti_CerebralNuclei.Pu_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="95">
                                        52_Siletti_CerebralNuclei.Pu_Human_2022_level2</option>
                                    <option value="53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="96">
                                        53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level1</option>
                                    <option value="53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="97">
                                        53_Siletti_CerebralNuclei.GP.Gpi_Human_2022_level2</option>
                                    <option value="54_Siletti_CerebralNuclei.CaB_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="98">
                                        54_Siletti_CerebralNuclei.CaB_Human_2022_level1</option>
                                    <option value="54_Siletti_CerebralNuclei.CaB_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="99">
                                        54_Siletti_CerebralNuclei.CaB_Human_2022_level2</option>
                                    <option value="55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="100">
                                        55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level1</option>
                                    <option value="55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="101">
                                        55_Siletti_CerebralNuclei.BLN.BM_Human_2022_level2</option>
                                    <option value="56_Siletti_CerebralNuclei.NAC_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="102">
                                        56_Siletti_CerebralNuclei.NAC_Human_2022_level1</option>
                                    <option value="56_Siletti_CerebralNuclei.NAC_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="103">
                                        56_Siletti_CerebralNuclei.NAC_Human_2022_level2</option>
                                    <option value="57_Siletti_CerebralNuclei.BLN.La_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="104">
                                        57_Siletti_CerebralNuclei.BLN.La_Human_2022_level1</option>
                                    <option value="57_Siletti_CerebralNuclei.BLN.La_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="105">
                                        57_Siletti_CerebralNuclei.BLN.La_Human_2022_level2</option>
                                    <option value="58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level1"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="106">
                                        58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level1</option>
                                    <option value="58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level2"
                                        data-section="Brain/Human/Cerebral nuclei" data-key="107">
                                        58_Siletti_CerebralNuclei.GP.CMN.CoA_Human_2022_level2</option>
                                    <option value="70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="108">
                                        70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level1</option>
                                    <option value="70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="109">
                                        70_Siletti_Hypothalamus.HTHma.MN_Human_2022_level2</option>
                                    <option value="71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="110">
                                        71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level1</option>
                                    <option value="71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="111">
                                        71_Siletti_Hypothalamus.HTHpo.HTHso_Human_2022_level2</option>
                                    <option value="72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="112">
                                        72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level1</option>
                                    <option value="72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="113">
                                        72_Siletti_Hypothalamus.HTHma.HTHtub_Human_2022_level2</option>
                                    <option value="73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="114">
                                        73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level1</option>
                                    <option value="73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="115">
                                        73_Siletti_Hypothalamus.HTHso.HTHtub_Human_2022_level2</option>
                                    <option value="74_Siletti_Hypothalamus.HTHpo_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="116">
                                        74_Siletti_Hypothalamus.HTHpo_Human_2022_level1</option>
                                    <option value="74_Siletti_Hypothalamus.HTHpo_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="117">
                                        74_Siletti_Hypothalamus.HTHpo_Human_2022_level2</option>
                                    <option value="75_Siletti_Hypothalamus.HTHtub_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="118">
                                        75_Siletti_Hypothalamus.HTHtub_Human_2022_level1</option>
                                    <option value="75_Siletti_Hypothalamus.HTHtub_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="119">
                                        75_Siletti_Hypothalamus.HTHtub_Human_2022_level2</option>
                                    <option value="76_Siletti_Hypothalamus.HTHso_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="120">
                                        76_Siletti_Hypothalamus.HTHso_Human_2022_level1</option>
                                    <option value="76_Siletti_Hypothalamus.HTHso_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="121">
                                        76_Siletti_Hypothalamus.HTHso_Human_2022_level2</option>
                                    <option value="77_Siletti_Hypothalamus.HTHma_Human_2022_level1"
                                        data-section="Brain/Human/Hypothalamus" data-key="122">
                                        77_Siletti_Hypothalamus.HTHma_Human_2022_level1</option>
                                    <option value="77_Siletti_Hypothalamus.HTHma_Human_2022_level2"
                                        data-section="Brain/Human/Hypothalamus" data-key="123">
                                        77_Siletti_Hypothalamus.HTHma_Human_2022_level2</option>
                                    <option value="97_Siletti_Thalamus.PoN.LG_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="124">
                                        97_Siletti_Thalamus.PoN.LG_Human_2022_level1</option>
                                    <option value="97_Siletti_Thalamus.PoN.LG_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="125">
                                        97_Siletti_Thalamus.PoN.LG_Human_2022_level2</option>
                                    <option value="98_Siletti_Thalamus.LNC.Pul_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="126">
                                        98_Siletti_Thalamus.LNC.Pul_Human_2022_level1</option>
                                    <option value="98_Siletti_Thalamus.LNC.Pul_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="127">
                                        98_Siletti_Thalamus.LNC.Pul_Human_2022_level2</option>
                                    <option value="99_Siletti_Thalamus.ANC_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="128">
                                        99_Siletti_Thalamus.ANC_Human_2022_level1</option>
                                    <option value="99_Siletti_Thalamus.ANC_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="129">
                                        99_Siletti_Thalamus.ANC_Human_2022_level2</option>
                                    <option value="100_Siletti_Thalamus.LNC.VLN_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="130">
                                        100_Siletti_Thalamus.LNC.VLN_Human_2022_level1</option>
                                    <option value="100_Siletti_Thalamus.LNC.VLN_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="131">
                                        100_Siletti_Thalamus.LNC.VLN_Human_2022_level2</option>
                                    <option value="101_Siletti_Thalamus.LNC.LP_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="132">
                                        101_Siletti_Thalamus.LNC.LP_Human_2022_level1</option>
                                    <option value="101_Siletti_Thalamus.LNC.LP_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="133">
                                        101_Siletti_Thalamus.LNC.LP_Human_2022_level2</option>
                                    <option value="102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="134">
                                        102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level1</option>
                                    <option value="102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="135">
                                        102_Siletti_Thalamus.ILN.PILN.CM.Pf_Human_2022_level2</option>
                                    <option value="103_Siletti_Thalamus.ETH_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="136">
                                        103_Siletti_Thalamus.ETH_Human_2022_level1</option>
                                    <option value="103_Siletti_Thalamus.ETH_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="137">
                                        103_Siletti_Thalamus.ETH_Human_2022_level2</option>
                                    <option value="104_Siletti_Thalamus.MNC.MD_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="138">
                                        104_Siletti_Thalamus.MNC.MD_Human_2022_level1</option>
                                    <option value="104_Siletti_Thalamus.MNC.MD_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="139">
                                        104_Siletti_Thalamus.MNC.MD_Human_2022_level2</option>
                                    <option value="105_Siletti_Thalamus.STH_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="140">
                                        105_Siletti_Thalamus.STH_Human_2022_level1</option>
                                    <option value="105_Siletti_Thalamus.STH_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="141">
                                        105_Siletti_Thalamus.STH_Human_2022_level2</option>
                                    <option value="106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="142">
                                        106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level1</option>
                                    <option value="106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="143">
                                        106_Siletti_Thalamus.LNC.LP.VPL_Human_2022_level2</option>
                                    <option value="107_Siletti_Thalamus.PoN.MG_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="144">
                                        107_Siletti_Thalamus.PoN.MG_Human_2022_level1</option>
                                    <option value="107_Siletti_Thalamus.PoN.MG_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="145">
                                        107_Siletti_Thalamus.PoN.MG_Human_2022_level2</option>
                                    <option value="108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="146">
                                        108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level1</option>
                                    <option value="108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="147">
                                        108_Siletti_Thalamus.MNC.MD.Re_Human_2022_level2</option>
                                    <option value="109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="148">
                                        109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level1</option>
                                    <option value="109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="149">
                                        109_Siletti_Thalamus.ILN.PILN.CM_Human_2022_level2</option>
                                    <option value="110_Siletti_Thalamus.LNC.VA_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="150">
                                        110_Siletti_Thalamus.LNC.VA_Human_2022_level1</option>
                                    <option value="110_Siletti_Thalamus.LNC.VA_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="151">
                                        110_Siletti_Thalamus.LNC.VA_Human_2022_level2</option>
                                    <option value="111_Siletti_Thalamus.LNC.VPL_Human_2022_level1"
                                        data-section="Brain/Human/Thalamus" data-key="152">
                                        111_Siletti_Thalamus.LNC.VPL_Human_2022_level1</option>
                                    <option value="111_Siletti_Thalamus.LNC.VPL_Human_2022_level2"
                                        data-section="Brain/Human/Thalamus" data-key="153">
                                        111_Siletti_Thalamus.LNC.VPL_Human_2022_level2</option>
                                    <option value="8_Siletti_CerebralCortex.MTG_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="154">
                                        8_Siletti_CerebralCortex.MTG_Human_2022_level1</option>
                                    <option value="8_Siletti_CerebralCortex.MTG_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="155">
                                        8_Siletti_CerebralCortex.MTG_Human_2022_level2</option>
                                    <option value="11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="156">
                                        11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level1</option>
                                    <option value="11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="157">
                                        11_Siletti_CerebralCortex.PPH.TH-TL_Human_2022_level2</option>
                                    <option value="12_Siletti_CerebralCortex.STG_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="158">
                                        12_Siletti_CerebralCortex.STG_Human_2022_level1</option>
                                    <option value="12_Siletti_CerebralCortex.STG_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="159">
                                        12_Siletti_CerebralCortex.STG_Human_2022_level2</option>
                                    <option value="31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="160">
                                        31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level1</option>
                                    <option value="31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="161">
                                        31_Siletti_CerebralCortex.FuGt.TF_Human_2022_level2</option>
                                    <option value="34_Siletti_CerebralCortex.SOG.A19_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="162">
                                        34_Siletti_CerebralCortex.SOG.A19_Human_2022_level1</option>
                                    <option value="34_Siletti_CerebralCortex.SOG.A19_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="163">
                                        34_Siletti_CerebralCortex.SOG.A19_Human_2022_level2</option>
                                    <option value="35_Siletti_CerebralCortex.ITG_Human_2022_level1"
                                        data-section="Brain/Human/Lobes" data-key="164">
                                        35_Siletti_CerebralCortex.ITG_Human_2022_level1</option>
                                    <option value="35_Siletti_CerebralCortex.ITG_Human_2022_level2"
                                        data-section="Brain/Human/Lobes" data-key="165">
                                        35_Siletti_CerebralCortex.ITG_Human_2022_level2</option>
                                    <option value="78_Siletti_Midbrain.SN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="166">
                                        78_Siletti_Midbrain.SN_Human_2022_level1</option>
                                    <option value="78_Siletti_Midbrain.SN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="167">
                                        78_Siletti_Midbrain.SN_Human_2022_level2</option>
                                    <option value="79_Siletti_Midbrain.SN-RN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="168">
                                        79_Siletti_Midbrain.SN-RN_Human_2022_level1</option>
                                    <option value="79_Siletti_Midbrain.SN-RN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="169">
                                        79_Siletti_Midbrain.SN-RN_Human_2022_level2</option>
                                    <option value="80_Siletti_Midbrain.PAG_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="170">
                                        80_Siletti_Midbrain.PAG_Human_2022_level1</option>
                                    <option value="80_Siletti_Midbrain.PAG_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="171">
                                        80_Siletti_Midbrain.PAG_Human_2022_level2</option>
                                    <option value="81_Siletti_Midbrain.IC_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="172">
                                        81_Siletti_Midbrain.IC_Human_2022_level1</option>
                                    <option value="81_Siletti_Midbrain.IC_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="173">
                                        81_Siletti_Midbrain.IC_Human_2022_level2</option>
                                    <option value="82_Siletti_Midbrain.SC_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="174">
                                        82_Siletti_Midbrain.SC_Human_2022_level1</option>
                                    <option value="82_Siletti_Midbrain.SC_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="175">
                                        82_Siletti_Midbrain.SC_Human_2022_level2</option>
                                    <option value="83_Siletti_Midbrain.PTR_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="176">
                                        83_Siletti_Midbrain.PTR_Human_2022_level1</option>
                                    <option value="83_Siletti_Midbrain.PTR_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="177">
                                        83_Siletti_Midbrain.PTR_Human_2022_level2</option>
                                    <option value="84_Siletti_Midbrain.PAG-DR_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="178">
                                        84_Siletti_Midbrain.PAG-DR_Human_2022_level1</option>
                                    <option value="84_Siletti_Midbrain.PAG-DR_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="179">
                                        84_Siletti_Midbrain.PAG-DR_Human_2022_level2</option>
                                    <option value="85_Siletti_Midbrain.RN_Human_2022_level1"
                                        data-section="Brain/Human/Midbrain" data-key="180">
                                        85_Siletti_Midbrain.RN_Human_2022_level1</option>
                                    <option value="85_Siletti_Midbrain.RN_Human_2022_level2"
                                        data-section="Brain/Human/Midbrain" data-key="181">
                                        85_Siletti_Midbrain.RN_Human_2022_level2</option>
                                    <option value="41_Siletti_Cerebellum.CBV_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="182">
                                        41_Siletti_Cerebellum.CBV_Human_2022_level1</option>
                                    <option value="41_Siletti_Cerebellum.CBV_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="183">
                                        41_Siletti_Cerebellum.CBV_Human_2022_level2</option>
                                    <option value="42_Siletti_Cerebellum.CbDN_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="184">
                                        42_Siletti_Cerebellum.CbDN_Human_2022_level1</option>
                                    <option value="42_Siletti_Cerebellum.CbDN_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="185">
                                        42_Siletti_Cerebellum.CbDN_Human_2022_level2</option>
                                    <option value="43_Siletti_Cerebellum.CBL_Human_2022_level1"
                                        data-section="Brain/Human/Cerebellum" data-key="186">
                                        43_Siletti_Cerebellum.CBL_Human_2022_level1</option>
                                    <option value="43_Siletti_Cerebellum.CBL_Human_2022_level2"
                                        data-section="Brain/Human/Cerebellum" data-key="187">
                                        43_Siletti_Cerebellum.CBL_Human_2022_level2</option>
                                    <option value="90_Siletti_Pons.PnRF_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="188">
                                        90_Siletti_Pons.PnRF_Human_2022_level1</option>
                                    <option value="90_Siletti_Pons.PnRF_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="189">
                                        90_Siletti_Pons.PnRF_Human_2022_level2</option>
                                    <option value="91_Siletti_Pons.PnEN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="190">
                                        91_Siletti_Pons.PnEN_Human_2022_level1</option>
                                    <option value="91_Siletti_Pons.PnEN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="191">
                                        91_Siletti_Pons.PnEN_Human_2022_level2</option>
                                    <option value="92_Siletti_Pons.PN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="192">
                                        92_Siletti_Pons.PN_Human_2022_level1</option>
                                    <option value="92_Siletti_Pons.PN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="193">
                                        92_Siletti_Pons.PN_Human_2022_level2</option>
                                    <option value="93_Siletti_Pons.XPnTg.DTg_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="194">
                                        93_Siletti_Pons.XPnTg.DTg_Human_2022_level1</option>
                                    <option value="93_Siletti_Pons.XPnTg.DTg_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="195">
                                        93_Siletti_Pons.XPnTg.DTg_Human_2022_level2</option>
                                    <option value="94_Siletti_Pons.PnRF.PB_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="196">
                                        94_Siletti_Pons.PnRF.PB_Human_2022_level1</option>
                                    <option value="94_Siletti_Pons.PnRF.PB_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="197">
                                        94_Siletti_Pons.PnRF.PB_Human_2022_level2</option>
                                    <option value="95_Siletti_Pons.PnAN_Human_2022_level1"
                                        data-section="Brain/Human/Pons" data-key="198">
                                        95_Siletti_Pons.PnAN_Human_2022_level1</option>
                                    <option value="95_Siletti_Pons.PnAN_Human_2022_level2"
                                        data-section="Brain/Human/Pons" data-key="199">
                                        95_Siletti_Pons.PnAN_Human_2022_level2</option>
                                    <option value="86_Siletti_Myelencephalon.MoAN_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="200">
                                        86_Siletti_Myelencephalon.MoAN_Human_2022_level1</option>
                                    <option value="86_Siletti_Myelencephalon.MoAN_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="201">
                                        86_Siletti_Myelencephalon.MoAN_Human_2022_level2</option>
                                    <option value="87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="202">
                                        87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level1</option>
                                    <option value="87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="203">
                                        87_Siletti_Myelencephalon.MoRF-MoEN_Human_2022_level2</option>
                                    <option value="88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="204">
                                        88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level1</option>
                                    <option value="88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="205">
                                        88_Siletti_Myelencephalon.PrCbN.IO_Human_2022_level2</option>
                                    <option value="89_Siletti_Myelencephalon.MoSR_Human_2022_level1"
                                        data-section="Brain/Human/Myelencephalon" data-key="206">
                                        89_Siletti_Myelencephalon.MoSR_Human_2022_level1</option>
                                    <option value="89_Siletti_Myelencephalon.MoSR_Human_2022_level2"
                                        data-section="Brain/Human/Myelencephalon" data-key="207">
                                        89_Siletti_Myelencephalon.MoSR_Human_2022_level2</option>
                                    <option value="96_Siletti_Spinalcord.Spc_Human_2022_level1"
                                        data-section="Brain/Human/Spinal cord" data-key="208">
                                        96_Siletti_Spinalcord.Spc_Human_2022_level1</option>
                                    <option value="96_Siletti_Spinalcord.Spc_Human_2022_level2"
                                        data-section="Brain/Human/Spinal cord" data-key="209">
                                        96_Siletti_Spinalcord.Spc_Human_2022_level2</option>
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
