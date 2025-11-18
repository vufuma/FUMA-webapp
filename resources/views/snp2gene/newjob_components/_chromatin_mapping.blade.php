<div class="accordion-item" style="padding: 0px;">
    <h2 class="accordion-header" id="heading33">
        <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobCiMapPanel"
            data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobCiMapPanel">
            3-3. Gene Mapping (3D Chromatin Interaction mapping)
        </button>
    </h2>
    <div class="accordion-collapse collapse" id="NewJobCiMapPanel" aria-labelledby="heading33">
        <div class="accordion-body">
            <h5>chromatin interaction mapping</h5>
            <table class="table table-bordered inputTable" id="NewJobCiMap" style="width: auto;">
                <tr>
                    <td>Perform chromatin interaction mapping
                        <a class="infoPop" data-bs-toggle="popover" title="3D chromatin interaction mapping"
                            data-bs-content="3D chromatin interaction mapping maps SNPs to genes based on chromatin interactions such as Hi-C and ChIA-PET. Please check to perform this mapping.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td><input type="checkbox" class="form-check-inline" name="ciMap" id="ciMap"
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Builtin chromatin interaction data
                        <a class="infoPop" data-bs-toggle="popover" title="Build-in Hi-C data"
                            data-bs-content="Hi-C datasets of 21 tissue and cell types from GSE87112 are selectabe as build-in data. Multiple tissue and cell types can be selected.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td>
                        <span class="multiSelect">
                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                            <select multiple class="form-select" id="ciMapBuiltin" name="ciMapBuiltin[]"
                                size="10" onchange="window.CheckAll();">
                                @include('snp2gene.ci_options')
                            </select>
                        </span>
                    </td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Custom chromatin interactions
                        <a class="infoPop" data-bs-toggle="popover" title="Custom chromatin interaction matrices"
                            data-bs-content="Please upload files of custom chromatin interaction matrices (significant loops). The input files have to follow the specific format. Please refer the tutorial for details. The file name should be '(Name_of_the_data).txt.gz' in which (Name_of_the_data) will be used in the results table.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td>
                        <span id="ciFiles"></span><br>
                        <button type="button" class="btn btn-default btn-xs" id="ciFileAdd">add
                            file</button>
                        <input type="hidden" value="0" id="ciFileN" name="ciFileN">
                    </td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>FDR threshold
                        <a class="infoPop" data-bs-toggle="popover"
                            title="FDR threshold for significant interaction"
                            data-bs-content="Significance of interaction for build-in Hi-C datasets are computed by Fit-Hi-C (see tutorial for details). The default threshold is FDR &le; 1e-6 as suggested by Schmit et al. (2016).">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">FDR cutoff (&lt;): </span>
                            <input type="number" class="form-control" name="ciMapFDR" id="ciMapFDR"
                                value="1e-6" onchange="window.CheckAll();">
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Promoter region window
                        <a class="infoPop" data-bs-toggle="popover" title="Promoter region window"
                            data-bs-content="The window of promoter regions are used to overlap TSS of genes with significantly interacted regions with risk loci.
                                By default, promoter region is defined as 250bp upstream and 500bp downsteram of TSS. Genes whose promoter regions are overlapped with the interacted region are used for gene mapping.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td><input type="text" class="form-control" name="ciMapPromWindow" id="ciMapPromWindow"
                            value="250-500" onchange="window.CheckAll();">
                        <span class="info"><i class="fa fa-info"></i>
                            Please specify both upstream and downstream from TSS. For example, "250-500"
                            means 250bp upstream and 500bp downstream from TSS.
                        </span>
                    </td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Annotate enhancer/promoter regions (Roadmap 111 epigenomes)
                        <a class="infoPop" data-bs-toggle="popover" title="Enhancer/promoter regions"
                            data-bs-content="Enhancers are annotated to overlapped candidate SNPs which are also overlapped with significant chromatin interactions (region 1).
                                Promoters are annotated to regions which are significantly interacted with risk loci (region 2). Dyadic enhancer/promoter regions are annotated for both. Please refer the tutorial for details.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td>
                        <span class="multiSelect">
                            <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                            <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                            <select multiple class="form-select" id="ciMapRoadmap" name="ciMapRoadmap[]"
                                size="10" onchange="window.CheckAll();">
                                @include('snp2gene.PE_options')
                            </select>
                        </span>
                    </td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Filter SNPs by enhancers
                        <a class="infoPop" data-bs-toggle="popover" title="Filter SNPs by enhancers"
                            data-bs-content="Only map SNPs which are overlapped with enhancers of selected epigenomes. Please select at least one epigenome to enable this option.
                                If this option is not checked, all SNPs overlapped with chromatin interaction are used for mapping.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td><input type="checkbox" class="form-check" name="ciMapEnhFilt" id="ciMapEnhFilt"
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <tr class="ciMapOptions">
                    <td>Filter genes by promoters
                        <a class="infoPop" data-bs-toggle="popover" title="Filter genes by promoters"
                            data-bs-content="Only map to genes whose promoter regions are overlap with promoters of selected epigenomes. Please select at least one epigenome to enable this option.
                                If this option is not checked, all genes whose promoter regions are overlapped with the interacted regions are mapped.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td><input type="checkbox" class="form-check" name="ciMapPromFilt" id="ciMapPromFilt"
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <!-- </div> -->
            </table>

            <div id="ciMapOptFilt">
                Optional SNP filtering by functional annotation for chromatin interaction mapping<br>
                <span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by
                    chromatin interaction mapping criterion.<br>
                    All these annotations will be available for all SNPs within LD of identified lead SNPs
                    in the result tables, but this filtering affect gene prioritization.
                </span>
                <table class="table table-bordered inputTable" id="ciMapOptFiltTable">
                    <tr>
                        <td rowspan="2">CADD</td>
                        <td>Perform SNPs filtering based on CADD score.
                            <a class="infoPop" data-bs-toggle="popover" title="CADD score filtering"
                                data-bs-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="checkbox" class="form-check" name="ciMapCADDcheck" id="ciMapCADDcheck"
                                onchange="window.CheckAll();"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Minimum CADD score (&ge;)
                            <a class="infoPop" data-bs-toggle="popover" title="CADD score"
                                data-bs-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="number" class="form-control" id="ciMapCADDth" name="ciMapCADDth"
                                value="12.37" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="2">RegulomeDB</td>
                        <td>Perform SNPs filtering based on RegulomeDB score
                            <a class="infoPop" data-bs-toggle="popover" title="RegulomeDB Score filtering"
                                data-bs-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="checkbox" class="form-check" name="ciMapRDBcheck" id="ciMapRDBcheck"
                                onchange="window.CheckAll();"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Maximum RegulomeDB score (categorical)
                            <a class="infoPop" data-bs-toggle="popover" title="RegulomeDB score"
                                data-bs-content="RegulomeDB score is a categorical score to represent regulatory function of SNPs based on eQTLs and epigenome information. '1a' is the most likely functional and 7 is the least liekly. Some SNPs have 'NA' which are not assigned any score.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <select class="form-select" id="ciMapRDBth" name="ciMapRDBth"
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
                        <td rowspan="4">15-core chromatin state</td>
                        <td>Perform SNPs filtering based on chromatin state
                            <a class="infoPop" data-bs-toggle="popover" title="15-core chromatin state filtering"
                                data-bs-content="Please check this option to filter SNPs based on chromatin state and specify the following options.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="checkbox" class="form-check" name="ciMapChr15check"
                                id="ciMapChr15check" onchange="window.CheckAll();"></td>
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
                                <select multiple class="form-select" size="10" id="ciMapChr15Ts"
                                    name="ciMapChr15Ts[]" onchange="window.CheckAll();">
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
                        <td><input type="number" class="form-control" id="ciMapChr15Max" name="ciMapChr15Max"
                                value="7" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" /></td>
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
                            <select class="form-select" id="ciMapChr15Meth" name="ciMapChr15Meth"
                                onchange="window.CheckAll();">
                                <option selected value="any">any</option>
                                <option value="majority">majority</option>
                                <option value="all">all</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="2">Additional annotations</td>
                        <td>Annotation datasets<br>
                            <span class="info"><i class="fa fa-info"></i> Multiple datasets can be
                                selected.</span><br>
                            <span class="info"><i class="fa fa-info"></i> Filtering is performed when at
                                least one annotation is selected.</span><br>
                        </td>
                        <td>
                            <span class="multiSelect">
                                <a class="clear" style="float:right; padding-right:20px;">Clear</a>
                                <a class="all" style="float:right; padding-right:20px;">Select all</a><br>
                                <select multiple class="form-select" size="10" id="ciMapAnnoDs"
                                    name="ciMapAnnoDs[]">
                                    @include('snp2gene.bed_annot')
                                </select>
                            </span>
                            <br>
                        </td>
                        <td>
                            <div class="alert alert-info"
                                style="display: table-cell; padding-top:0; padding-bottom:0;">
                                <i class="fa fa-exclamation-circle"></i> Optional.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Annotation filtering method
                            <a class="infoPop" data-bs-toggle="popover" title="Filtering method for annotations"
                                data-bs-content="When multiple datasets are selected, SNPs will be kept if they are overlapped with any of, majority of or all of selected annotations
                                unless an option 'No filtering' is selected.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <select class="form-select" id="ciMapAnnoMeth" name="ciMapAnnoMeth">
                                <option selected value="NA">No filtering (only annotate SNPs)</option>
                                <option value="any">any</option>
                                <option value="majority">majority</option>
                                <option value="all">all</option>
                            </select>
                        </td>
                        <td>
                            <div class="alert alert-info"
                                style="display: table-cell; padding-top:0; padding-bottom:0;">
                                <i class="fa fa-exclamation-circle"></i> Optional.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div>
            </div>
        </div>
    </div>
</div>