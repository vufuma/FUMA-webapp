<div class="accordion-item" style="padding:0px;">
    <h2 class="accordion-header" id="heading31">
        <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobPosMapPanel"
            data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobPosMapPanel">
            3-1. Gene Mapping (positional mapping)
        </button>
    </h2>
    <div class="accordion-collapse collapse" id="NewJobPosMapPanel" aria-labelledby="heading31">
        <div class="accordion-body">
            <h5>Positional mapping</h5>
            <table class="table table-bordered inputTable" id="NewJobPosMap" style="width: auto;">
                <tr>
                    <td>Perform positional mapping
                        <a class="infoPop" data-bs-toggle="popover" title="Positional maping"
                            data-bs-content="When checked, positional mapping will be carried out and includes functional consequences of SNPs on gene functions (such as exonic, intronic and splicing).">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td><input type="checkbox" class="form-check-inline" name="posMap" id="posMap" checked
                            onchange="window.CheckAll();"></td>
                    <td></td>
                </tr>
                <tr class="posMapOptions">
                    <td>Distance to genes or <br>functional consequences of SNPs on genes to map
                        <a class="infoPop" data-bs-toggle="popover" title="Positional mapping"
                            data-bs-content="
                                Positional mapping can be performed purely based on the physical distance between SNPs and genes by providing the maximum distance.
                                Optionally, functional consequences of SNPs on genes can be selected to map only specific SNPs such as SNPs locating on exonic regions.
                                Note that when functional consequences are selected, only SNPs location on the gene body (distance 0) are mapped to genes except upstream and downstream SNPs which are up to 1kb apart from TSS or TES.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Maximum distance:</span>
                            <input type="number" class="form-control" id="posMapWindow" name="posMapWindow"
                                value="10" min="0" max="1000" onkeyup="window.CheckAll();"
                                onpaste="window.CheckAll();" oninput="window.CheckAll();">
                            <span class="input-group-text">kb</span>
                        </div>
                        OR<br>
                        Functional consequences of SNPs on genes:<br>
                        <div class="multiSelect input-group">
                            <a class="clear">Clear</a>&nbsp;&nbsp;
                            <select multiple class="form-select" id="posMapAnnot" name="posMapAnnot[]"
                                onchange="window.CheckAll();">
                                <option value="exonic">exonic</option>
                                <option value="splicing">splicing</option>
                                <option value="intronic">intronic</option>
                                <option value="UTR3">3UTR</option>
                                <option value="UTR5">5UTR</option>
                                <option value="upstream">upstream</option>
                                <option value="downstream">downstream</option>
                            </select>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>

            <div id="posMapOptFilt">
                Optional SNP filtering by functional annotations for positional mapping<br>
                <span class="info"><i class="fa fa-info"></i> This filtering only applies to SNPs mapped by
                    positional mapping criterion. When eQTL mapping is also performed, this filtering can be
                    specified separately.<br>
                    All these annotations will be available for all SNPs within LD of identified lead SNPs
                    in the result tables, but this filtering affect gene prioritization.
                </span>
                <table class="table table-bordered inputTable" id="posMapOptFiltTable" style="width: auto;">
                    <tr>
                        <td rowspan="2">CADD</td>
                        <td>Perform SNPs filtering based on CADD score.
                            <a class="infoPop" data-bs-toggle="popover" title="CADD score filtering"
                                data-bs-content="Please check this option to filter SNPs based on CADD score and specify minimum score in the box below.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="checkbox" class="form-check-inline" name="posMapCADDcheck"
                                id="posMapCADDcheck" onchange="window.CheckAll();"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Minimum CADD score (&ge;)
                            <a class="infoPop" data-bs-toggle="popover" title="CADD score"
                                data-bs-content="CADD score is the score of deleteriousness of SNPs. The higher, the more deleterious. 12.37 is the suggestive threshold to be deleterious. Coding SNPs tend to have high score than non-coding SNPs.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td><input type="number" class="form-control" id="posMapCADDth" name="posMapCADDth"
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
                        <td><input type="checkbox" class="form-check-inline" name="posMapRDBcheck"
                                id="posMapRDBcheck" onchange="window.CheckAll();"></td>
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
                            <!-- <input type="text" class="form-control" id="posMapRDBth" name="posMapRDBth" value="7" style="width: 80px;"> -->
                            <select class="form-select" id="posMapRDBth" name="posMapRDBth"
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
                        <td><input type="checkbox" class="form-check-inline" name="posMapChr15check"
                                id="posMapChr15check" onchange="window.CheckAll();"></td>
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
                                <select multiple class="form-select" size="10" id="posMapChr15Ts"
                                    name="posMapChr15Ts[]" onchange="window.CheckAll();">
                                    @include('snp2gene.epi_options')
                                </select>
                            </span>
                            <br>
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
                        <td><input type="number" class="form-control" id="posMapChr15Max" name="posMapChr15Max"
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
                            <select class="form-select" id="posMapChr15Meth" name="posMapChr15Meth"
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
                                <select multiple class="form-select" size="10" id="posMapAnnoDs"
                                    name="posMapAnnoDs[]">
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
                            <select class="form-select" id="posMapAnnoMeth" name="posMapAnnoMeth">
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
        </div>
    </div>
</div>