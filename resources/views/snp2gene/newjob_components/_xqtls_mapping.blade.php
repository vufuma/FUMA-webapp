<style> 
.accordion-button.accordion-highlight {
background-color: #efeff8ff;
border-color: rgba(0,0,0,0.1);
}
</style>
<div class="accordion-item" style="padding:0px;">

    <h2 class="accordion-header" id="heading32">
        <button class="accordion-button fs-5 collapsed " type="button" data-bs-target="#NewJobXqtlMapPanel"
            data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobXqtlMapPanel">
            3-2. Gene Mapping (xQTLs mapping)
        </button>
    </h2>

    <div class="accordion-collapse collapse" id="NewJobXqtlMapPanel" aria-labelledby="heading32">
        <div class="accordion-body">
            <table class="table table-bordered inputTable" id="NewJobEqtlMap" style="width: auto; ">
                <tr>
                    <div class="alert alert-info">
			            Starting from FUMA v.2.0.0, this functionality of eQTL Mapping is kept for backward compatibility. To make adding new QTL datasets easier to FUMA, a new functionality below (<i>Perform xQTLs Mapping</i>) is added. You can still use this functionality to carry out analysis as before but new QTL datasets will only be added to the xQTLs Mapping functionality.
                    </div>
                    <th class="h5">Perform eQTL Mapping
                        <a class="infoPop" data-bs-toggle="popover" title="eQTL mapping"
                                data-bs-content="eQTL mapping maps SNPs to genes based on eQTL information. This maps SNPs to genes up to 1 Mb part (cis-eQTL). Please check this option to perform eQTL mapping.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </th>
                    <td><input type="checkbox" class="form-check-inline" name="eqtlMap" id="eqtlMap"
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <tr class="eqtlMapOptions">
                        <td>Tissue types
                            <a class="infoPop" data-bs-toggle="popover" title="Tissue types of eQTLs"
                                data-bs-content="This is mandatory parameter for eQTL mapping. Currently 44 tissue types from GTEx and two large scale eQTL study of blood cell are available.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <span class="multiSelect">
                                <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                <select multiple class="form-select" id="eqtlMapTs" name="eqtlMapTs[]"
                                    size="10" onchange="window.CheckAll();">
                                    @include('snp2gene.xqtls_options.eqtls.fumav1_eqtl_options')
                                </select>
                            </span>
                            <span class="info"><i class="fa fa-info"></i>
                                From FUMA v1.3.0 GTEx v7, and from FUMA v1.3.5c GTEx v8 have been added.<br>
                                When the "all" option is selected, both GTEx v6, v7 and v8 will be used.<br>
                                To avoid this, please manually select the specific version to use.
                            </span>
                        </td>
                        <td></td>
                </tr>
                <tr class="eqtlMapOptions">
                    <th class="align-middle">eQTL P-value threshold
                        <a class="infoPop" data-bs-toggle="popover" title="eQTL P-value threshold"
                            data-bs-content="By default, only significant eQTLs are used (FDR &lt; 0.05). Please UNCHECK 'Use only significant snp-gene pair' to filter eQTLs based on raw P-value.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </th>
                    <td>
                        <div class="input-group mb-1">
                            <span class="input-group-text">Use only significant snp-gene pairs: </span>
                            <input type="checkbox" class="form-control" name="sigeqtlCheck" id="sigeqtlCheck"
                                checked onchange="window.CheckAll();">
                            <span class="input-group-text">(FDR&lt;0.05)</span>
                        </div>
                        OR
                        <div class="input-group mt-1">
                            <span class="input-group-text">(nominal) P-value cutoff (&lt;): </span>
                            <input type="number" class="form-control" name="eqtlP" id="eqtlP"
                                value="1e-3" onchange="window.CheckAll();">
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>

                <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#qtlsFilterOptions" aria-expanded="true" aria-controls="qtlsFilterOptions">
                Optional SNP filtering by functional annotation for eQTL mapping
                </button>
            </h2>
            <div id="qtlsFilterOptions" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by
                        eQTL mapping criterion.<br>
                        All these annotations will be available for all SNPs within LD of identified lead SNPs
                        in the result tables, but this filtering affect gene prioritization.
                    </span>
                    <table class="table table-bordered inputTable" id="eqtlMapOptFiltTable" style="width: auto; border: 1px solid black;">
                        <tr>
                            <th scope=row; rowspan="2"; class="align-middle">CADD</th>
                            <td>Perform SNPs filtering based on CADD score.
                                <a class="infoPop" data-bs-toggle="popover" title="CADD score filtering"
                                    data-bs-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="checkbox" class="form-check-inline" name="eqtlMapCADDcheck"
                                    id="eqtlMapCADDcheck" onchange="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Minimum CADD score (&ge;)
                                <a class="infoPop" data-bs-toggle="popover" title="CADD score"
                                    data-bs-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="number" class="form-control" id="eqtlMapCADDth" name="eqtlMapCADDth"
                                    value="12.37" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th rowspan="2" class="table-active align-middle">RegulomeDB</th>
                            <td class="table-active">Perform SNPs filtering based on RegulomeDB score
                                <a class="infoPop" data-bs-toggle="popover" title="RegulomeDB Score filtering"
                                    data-bs-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td class="table-active"><input type="checkbox" class="form-check-inline" name="eqtlMapRDBcheck"
                                    id="eqtlMapRDBcheck" onchange="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="table-active">Maximum RegulomeDB score (categorical)
                                <a class="infoPop" data-bs-toggle="popover" title="RegulomeDB score"
                                    data-bs-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td class="table-active">
                                <!-- <input type="text" class="form-control" id="eqtlMapRDBth" name="eqtlMapRDBth" value="7"> -->
                                <select class="form-select" id="eqtlMapRDBth" name="eqtlMapRDBth"
                                    onchange="window.CheckAll();">
                                    <option>1a</option>
                                    <option>1b</option>
                                    <option>1c</option>
                                    <option>1d</option>
                                    <option>1e</option>
                                    <option>1f</option>
                                    <option>2a</option>
                                    <option>2b</option>
                                    <option>2c</option>
                                    <option>3a</option>
                                    <option>3b</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option selected>7</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th rowspan="4" class="align-middle">15-core chromatin state</th>
                            <td>Perform SNPs filtering based on chromatin state
                                <a class="infoPop" data-bs-toggle="popover" title="15-core chromatin state filtering"
                                    data-bs-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="checkbox" class="form-check-inline" name="eqtlMapChr15check"
                                    id="eqtlMapChr15check" onchange="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Tissue/cell types for 15-core chromatin state<br>
                                <span class="info"><i class="fa fa-info"></i> Multiple tissue/cell types can be
                                    selected.</span>
                            </td>
                            <td>
                                <span class="multiSelect">
                                    <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                    <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                    <select multiple class="form-select" size="10" id="eqtlMapChr15Ts"
                                        name="eqtlMapChr15Ts[]" onchange="window.CheckAll();">
                                        @include('snp2gene.epi_options')
                                    </select>
                                </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>15-core chromatin state maximum state
                                <a class="infoPop" data-bs-toggle="popover" title="The maximum chromatin state"
                                    data-bs-content="The chromatin state represents accessibility of genomic regions (every 200bp) with 15 categorical states. Generally, states &le; 7 are open in given tissue/cell types.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="number" class="form-control" id="eqtlMapChr15Max"
                                    name="eqtlMapChr15Max" value="7" onkeyup="window.CheckAll();"
                                    onpaste="window.CheckAll();" oninput="window.CheckAll();" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>15-core chromatin state filtering method
                                <a class="infoPop" data-bs-toggle="popover"
                                    title="Filtering method for chromatin state"
                                    data-bs-content="When multiple tissue/cell types are selected, SNPs will be kept if they have chromatin state lower than the threshold in any of, majority of or all of selected tissue/cell types.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td>
                                <select class="form-select" id="eqtlMapChr15Meth" name="eqtlMapChr15Meth"
                                    onchange="window.CheckAll();">
                                    <option selected value="any">any</option>
                                    <option value="majority">majority</option>
                                    <option value="all">all</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th rowspan="2" class="table-active align-middle">Additional annotations</th>
                            <td class="table-active">Annotation datasets<br>
                                <span class="info"><i class="fa fa-info"></i> Multiple datasets can be
                                    selected.</span><br>
                                <span class="info"><i class="fa fa-info"></i> Filtering is performed when at
                                    least one annotation is selected.</span><br>
                            </td>
                            <td class="table-active">
                                <span class="multiSelect">
                                    <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                    <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                    <select multiple class="form-select" size="10" id="eqtlMapAnnoDs"
                                        name="eqtlMapAnnoDs[]">
                                        @include('snp2gene.bed_annot')
                                    </select>
                                </span>
                                <br>
                            </td>
                            <td class="table-active">
                                <div class="alert alert-info"
                                    style="display: table-cell; padding-top:0; padding-bottom:0;">
                                    <i class="fa fa-exclamation-circle"></i> Optional.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="table-active">Annotation filtering method
                                <a class="infoPop" data-bs-toggle="popover" title="Filtering method for annotations"
                                    data-bs-content="When multiple datasets are selected, SNPs will be kept if they are overlapped with any of, majority of or all of selected annotations
                                    unless an option 'No filtering' is selected.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td class="table-active">
                                <select class="form-select" id="eqtlMapAnnoMeth" name="eqtlMapAnnoMeth">
                                    <option selected value="NA">No filtering (only annotate SNPs)</option>
                                    <option value="any">any</option>
                                    <option value="majority">majority</option>
                                    <option value="all">all</option>
                                </select>
                            </td>
                            <td class="table-active">
                                <div class="alert alert-info"
                                    style="display: table-cell; padding-top:0; padding-bottom:0;">
                                    <i class="fa fa-exclamation-circle"></i> Optional.
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="accordion-body">
            <table class="table table-bordered inputTable" id="NewJobXqtlsMap" style="width: auto; ">
                <tr>
                    <div class="alert alert-info">
			            Starting from FUMA v.2.0.0, xQTLs mapping maps SNPs to genes based on xQTLs information. Only significant associations are used. In some certain datasets where it was not possible to obtain the significant associations, a threshold based on p value is used. The default is 1e-3 but you can modify this. Please check this option to perform xQTLs mapping.
                    </div>
                    <th class="h5">Perform xQTLs Mapping
                    </th>
                    <td><input type="checkbox" class="form-check-inline" name="xqtlsMap" id="xqtlsMap"
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <tr class="xqtlsMapOptions">
                    <th class="align-middle">eQTLs Datasets
                    </th>
                    <td> 
                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlAdipose">
                                    Adipose
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlAdipose">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlAdiposeDs" name="eqtlAdiposeDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_adipose_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlAdrenalGland">
                                    Adrenal Gland
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlAdrenalGland">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlAdrenalGlandDs" name="eqtlAdrenalGlandDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_adrenalgland_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlArtery">
                                    Artery
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlArtery">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlArteryDs" name="eqtlArteryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_artery_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlBladder">
                                    Bladder
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlBladder">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlBladderDs" name="eqtlBladderDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_bladder_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlBlood">
                                    Blood
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlBlood">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlBloodDs" name="eqtlBloodDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_blood_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlBrain">
                                    Brain
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlBrain">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlBrainDs" name="eqtlBrainDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_brain_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlBreast">
                                    Breast
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlBreast">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlBreastDs" name="eqtlBreastDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_breast_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlColon">
                                    Colon
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlColon">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlColonDs" name="eqtlColonDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_colon_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlEsophagus">
                                    Esophagus
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlEsophagus">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlEsophagusDs" name="eqtlEsophagusDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_esophagus_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlHeart">
                                    Heart
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlHeart">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlHeartDs" name="eqtlHeartDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_heart_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlKidney">
                                    Kidney
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlKidney">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlKidneyDs" name="eqtlKidneyDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_kidney_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlLiver">
                                    Liver
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlLiver">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlLiverDs" name="eqtlLiverDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_liver_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlLung">
                                    Lung
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlLung">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlLungDs" name="eqtlLungDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_lung_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlMuscle">
                                    Muscle
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlMuscle">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlMuscleDs" name="eqtlMuscleDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_muscle_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlNerve">
                                    Nerve
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlNerve">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlNerveDs" name="eqtlNerveDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_nerve_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlOvary">
                                    Ovary
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlOvary">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlOvaryDs" name="eqtlOvaryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_ovary_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlPancreas">
                                    Pancreas
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlPancreas">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlPancreasDs" name="eqtlPancreasDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_pancreas_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlPituitary">
                                    Pituitary
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlPituitary">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlPituitaryDs" name="eqtlPituitaryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_pituitary_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlProstate">
                                    Prostate
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlProstate">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlProstateDs" name="eqtlProstateDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_prostate_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlSalivaryGland">
                                    Salivary Gland
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlSalivaryGland">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlSalivaryGlandDs" name="eqtlSalivaryGlandDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_salivarygland_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlSkin">
                                    Skin
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlSkin">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlSkinDs" name="eqtlSkinDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_skin_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlSmallIntestine">
                                    Small Intestine
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlSmallIntestine">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlSmallIntestineDs" name="eqtlSmallIntestineDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_smallintestine_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlSpleen">
                                    Spleen
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlSpleen">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlSpleenDs" name="eqtlSpleenDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_spleen_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlStomach">
                                    Stomach
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlStomach">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlStomachDs" name="eqtlStomachDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_stomach_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlTestis">
                                    Testis
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlTestis">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlTestisDs" name="eqtlTestisDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_testis_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlThyroid">
                                    Thyroid
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlThyroid">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlThyroidDs" name="eqtlThyroidDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_thyroid_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlUterus">
                                    Uterus
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlUterus">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlUterusDs" name="eqtlUterusDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_uterus_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlVagina">
                                    Vagina
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlVagina">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlVaginaDs" name="eqtlVaginaDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.eqtls._eqtl_vagina_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>


                    </td>


                    <td rowspan="3"></td>
                </tr>

                <tr class="xqtlsMapOptions">
                    <th class="align-middle">sqtls Datasets
                    </th>
                    <td> 
                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlAdipose">
                                    Adipose
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlAdipose">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlAdiposeDs" name="sqtlAdiposeDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_adipose_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlAdrenalGland">
                                    Adrenal Gland
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlAdrenalGland">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlAdrenalGlandDs" name="sqtlAdrenalGlandDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_adrenalgland_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlArtery">
                                    Artery
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlArtery">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlArteryDs" name="sqtlArteryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_artery_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlBladder">
                                    Bladder
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlBladder">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlBladderDs" name="sqtlBladderDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_bladder_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlBlood">
                                    Blood
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlBlood">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlBloodDs" name="sqtlBloodDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_blood_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlBrain">
                                    Brain
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlBrain">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlBrainDs" name="sqtlBrainDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_brain_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlBreast">
                                    Breast
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlBreast">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlBreastDs" name="sqtlBreastDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_breast_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlColon">
                                    Colon
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlColon">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlColonDs" name="sqtlColonDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_colon_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlEsophagus">
                                    Esophagus
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlEsophagus">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlEsophagusDs" name="sqtlEsophagusDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_esophagus_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlHeart">
                                    Heart
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlHeart">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlHeartDs" name="sqtlHeartDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_heart_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlKidney">
                                    Kidney
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlKidney">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlKidneyDs" name="sqtlKidneyDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_kidney_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlLiver">
                                    Liver
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlLiver">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlLiverDs" name="sqtlLiverDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_liver_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlLung">
                                    Lung
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlLung">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlLungDs" name="sqtlLungDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_lung_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlMuscle">
                                    Muscle
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlMuscle">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlMuscleDs" name="sqtlMuscleDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_muscle_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlNerve">
                                    Nerve
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlNerve">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlNerveDs" name="sqtlNerveDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_nerve_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlOvary">
                                    Ovary
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlOvary">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlOvaryDs" name="sqtlOvaryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_ovary_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlPancreas">
                                    Pancreas
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlPancreas">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlPancreasDs" name="sqtlPancreasDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_pancreas_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlPituitary">
                                    Pituitary
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlPituitary">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlPituitaryDs" name="sqtlPituitaryDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_pituitary_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlProstate">
                                    Prostate
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlProstate">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlProstateDs" name="sqtlProstateDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_prostate_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlSalivaryGland">
                                    Salivary Gland
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlSalivaryGland">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlSalivaryGlandDs" name="sqtlSalivaryGlandDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_salivarygland_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlSkin">
                                    Skin
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlSkin">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlSkinDs" name="sqtlSkinDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_skin_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlSmallIntestine">
                                    Small Intestine
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlSmallIntestine">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlSmallIntestineDs" name="sqtlSmallIntestineDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_smallintestine_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlSpleen">
                                    Spleen
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlSpleen">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlSpleenDs" name="sqtlSpleenDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_spleen_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlStomach">
                                    Stomach
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlStomach">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlStomachDs" name="sqtlStomachDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_stomach_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlTestis">
                                    Testis
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlTestis">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlTestisDs" name="sqtlTestisDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_testis_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlThyroid">
                                    Thyroid
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlThyroid">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlThyroidDs" name="sqtlThyroidDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_thyroid_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlUterus">
                                    Uterus
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlUterus">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlUterusDs" name="sqtlUterusDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_uterus_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sqtlVagina">
                                    Vagina
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sqtlVagina">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sqtlVaginaDs" name="sqtlVaginaDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sqtls._sqtl_vagina_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>


                    </td>
					<td rowspan="3"></td>
                </tr>

                <tr class="xqtlsMapOptions">
                    <th class="align-middle">pQTLs Datasets
                    </th>
                    <td> 
                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#pqtlPlasma">
                                    Plasma
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="pqtlPlasma">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="pqtlPlasmaDs" name="pqtlPlasmaDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.pqtls._pqtl_plasma_options')
                                        </select>
                                    </span>
                                </div>
                            </div>

                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#pqtlBrain">
                                    Brain
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="pqtlBrain">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="pqtlBrainDs" name="pqtlBrainDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.pqtls._pqtl_brain_options')
                                        </select>
                                    </span>
                                </div>
                            </div>

                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#pqtlCsf">
                                    Cerebrospinal fluid (CSF)
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="pqtlCsf">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="pqtlCsfDs" name="pqtlCsfDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.pqtls._pqtl_csf_options')
                                        </select>
                                    </span>
                                </div>
                            </div>

                        </div>

                    </td>
                    <td></td>
                </tr>

                <tr class="xqtlsMapOptions">
                    <th class="align-middle">single-cell eQTLs Datasets
                    </th>
                    <td> 
                        <div class="accordion-item" style="padding:0px;">

                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#sceqtlBrain">
                                    Brain
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="sceqtlBrain">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="sceqtlBrainDs" name="sceqtlBrainDs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.xqtls_options.sceqtls._sceqtl_brain_options')
                                        </select>
                                    </span>
                                </div>
                            </div>

                        </div>

                    </td>
                    <td></td>
                </tr>

                <tr class="xqtlsMapOptions">
                    <th class="align-middle">P-value threshold
                    </th>
                    <td> 
                        <span class="form-inline">(nominal) P-value cutoff (&lt;): <input type="number"
										class="form-control" name="xqtlP" id="xqtlP" value="1e-3"
										onchange="window.CheckAll();"></span>

                    </td>
                    <td>
                        <span class="info"><i class="fa fa-info fa-sm"></i>
                        For certain datasets, you need to specify the p value threshold cutoff. If not, the default value of 1e-3 will be used. 
                        </span>
                    </td>
                </tr>

            </table>
        </div>


    



    </div>
</div>