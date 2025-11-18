<style> 
.accordion-button.accordion-highlight {
background-color: #efeff8ff;
border-color: rgba(0,0,0,0.1);
}
</style>
<div class="accordion-item" style="padding:0px;">
    <h2 class="accordion-header" id="heading32">
        <button class="accordion-button fs-5 collapsed " type="button" data-bs-target="#NewJobEqtlMapPanel"
            data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobEqtlMapPanel">
            3-2. Gene Mapping (eQTL mapping)
        </button>
    </h2>
    <div class="accordion-collapse collapse" id="NewJobEqtlMapPanel" aria-labelledby="heading32">
        <div class="accordion-body">
            <h5>eQTL mapping</h5>
            <table class="table table-bordered inputTable" id="NewJobEqtlMap" style="width: auto; border: 1px solid black;">
                <tr>
                    <th>Perform eQTL mapping
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
                    <th class="table-active align-middle">Tissue types
                        <a class="infoPop" data-bs-toggle="popover" title="Tissue types of eQTLs"
                            data-bs-content="This is mandatory parameter for eQTL mapping. Currently 44 tissue types from GTEx and two large scale eQTL study of blood cell are available.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </th>
                    <td class="table-active"> Available datasets
                        <!-- <span class="multiSelect">
                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                            <select multiple class="form-select" id="eqtlMapTs" name="eqtlMapTs[]"
                                size="10" onchange="window.CheckAll();">
                                @include('snp2gene.eqtl_options')
                            </select>
                        </span> -->
                        <div class="accordion-item" style="padding:0px;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlGtexv8">
                                    eQTL GTEx v8
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlGtexv8">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlGtexv8Ts" name="eqtlGtexv8Ts[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.eqtl_gtexv8_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="padding:0px; accordion-bg:gray;">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed accordion-highlight" type="button" data-bs-toggle="collapse" data-bs-target="#eqtlCatalog">
                                    eQTL Catalog
                                </button>
                            </h5>
                            <div class="accordion-collapse collapse" id="eqtlCatalog">
                                <div class="accordion-body">
                                    <span class="multiSelect">
                                        <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                        <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                        <select multiple class="form-select" id="eqtlCatalogTs" name="eqtlCatalogTs[]"
                                            size="10" onchange="window.CheckAll();">
                                            @include('snp2gene.eqtl_eqtlcatalog_options')
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <span class="info"><i class="fa fa-info"></i>
                            From FUMA v1.3.0 GTEx v7, and from FUMA v1.3.5c GTEx v8 have been added.<br>
                            When the "all" option is selected, both GTEx v6, v7 and v8 will be used.<br>
                            To avoid this, please manually select the specific version to use.
                        </span>
                    </td>
                    <td class="table-active"></td>
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

            <div id="eqtlMapOptFilt">
                Optional SNP filtering by functional annotation for eQTL mapping<br>
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
</div>