<div id="newJob" class="sidePanel container" style="padding-top:50px;">
    <!-- This blade is also used to display an example job in the browse context where it will not be submittable -->
    @if (Request::is('snp2gene'))
        <!-- Enabled new job -->
        {!! html()->form('POST', '/snp2gene/newJob')->acceptsFiles()->novalidate()->open() !!}
        <h5 style="color: #00004d">Upload your GWAS summary statistics and set parameters to obtain functional
            annotations
            of the genomic loci associated with your trait.
        </h5>
    @else
        <!-- Disabled new job -->
        <h5 style="color: #00004d">This is an example page of SNP2GENE job submission.
            All input options are disabled in this page.
            Please register to submit your own job.
        </h5>
    @endif

    <!-- load previous settings -->
    <div class="row">
        <div class="col-6">
			<div class="input-group">
				<span class="form-inline input-group-text" style="font-size:18px;">
					Load settings from previous job&nbsp;
					<a class="infoPop" data-bs-toggle="popover"
						title="Previous jobID"
						data-bs-content="By selecting jobID of your existing SNP2GENE jobs,
					you can load parameter settings that you used before (only if there is any existing job in your account).
					Note that this does not load input files and title. Please specify input files for each submission.">
						<i class="fa-regular fa-circle-question"></i>
					</a>
				</span>
				<select class="form-select" id="paramsID" name="paramsID" onchange="loadParams();">
					<option value=0>None</option>
				</select>
			</div>
        </div>
    </div>
    <br><br>

    <!-- Input files upload -->
    <div class="accordion" style="padding-top: 0px;">
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading1">
                <button class="accordion-button fs-5" type="button" data-bs-target="#NewJobFilesPanel"
                    data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobFilesPanel">
                    1. Upload input files
                </button>
            </h2>
            <div class="accordion-collapse collapse show" id="NewJobFilesPanel" aria-labelledby="heading1">
                <div class="accordion-body">
                    <div id="fileFormatError"></div>
                    <table class="table table-bordered inputTable" id="NewJobFiles" style="width: auto;">
                        <tr>
                            <td>GWAS summary statistics
                                <a class="infoPop" data-bs-toggle="popover" title="GWAS summary statistics input file"
                                    data-bs-content="Every row should have information on one SNP.
									The minimum required columns are ‘chromosome, position and P-value’ or ‘rsID and P-value’.
									If you provide position, please make sure the position is on hg19.
									The file could be complete results of GWAS or a subset of SNPs can be used as an input.
									The input file should be plain text, zip or gzip files.
									If you would like to test FUMA, please check 'Use example input', this will load an example file automatically.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="file" class="form-control-file" name="GWASsummary"
                                    id="GWASsummary" /><br>
                                Or <div class="form-check"> <input type="checkbox" class="form-check-input"
                                        name="egGWAS" id="egGWAS" onchange="window.CheckAll()" /><label
                                        class="form-check-label">: Use example input (Crohn's disease, Franke et
                                        al. 2010).</label>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>GWAS summary statistics file columns
                                <a class="infoPop" data-bs-toggle="popover"
                                    title="GWAS summary statistics input file columns"
                                    data-bs-content="This is optional parameter to define column names.
								Unless defined, FUMA will automatically detect columns from the list of acceptable column names (see tutorial for detail).
								However, to avoid error, please provide column names.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td>
                                <span class="info"><i class="fa fa-info"></i> case insensitive</span>
                                <span class="inputSpan">Chromosome: <input type="text" class="form-control"
                                        id="chrcol" name="chrcol"></span>
                                <span class="inputSpan">Position: <input type="text" class="form-control"
                                        id="poscol" name="poscol"></span>
                                <span class="inputSpan">rsID: <input type="text" class="form-control" id="rsIDcol"
                                        name="rsIDcol"></span>
                                <span class="inputSpan">P-value: <input type="text" class="form-control"
                                        id="pcol" name="pcol"></span>
                                <span class="inputSpan">Effect allele*: <input type="text" class="form-control"
                                        id="eacol" name="eacol"></span>
                                <span style="color:red; font-size: 11px;">* "A1" is effect allele by default</span>
                                <span class="inputSpan">Non effect allele: <input type="text" class="form-control"
                                        id="neacol" name="neacol"></span>
                                <span class="inputSpan">OR: <input type="text" class="form-control" id="orcol"
                                        name="orcol"></span>
                                <span class="inputSpan">Beta: <input type="text" class="form-control" id="becol"
                                        name="becol"></span>
                                <span class="inputSpan">SE: <input type="text" class="form-control"
                                        id="secol" name="secol"></span>
                            </td>
                            <td>
                                <div class="alert alert-info"
                                    style="display: table-cell; padding-top:0; padding-bottom:0;">
                                    <i class="fa fa-exclamation-circle"></i> Optional. Please fill as much as you
                                    can. It is not necessary to fill all column names.
                                </div>
                            </td>
                        </tr>
                        <tr class="d-none"> <!-- Hide this for now as it is still a work in progress -->
                            <td>Input is build GRCh38
                                <a class="infoPop" data-bs-toggle="popover" title="GRCh38 Input"
                                    data-bs-content="The input file has chromosome and position columns on build GRCh38. The column names for the chromosome, position, effect allele, and non-effect allele must be specified above.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="checkbox" class="form-check-inline" name="GRCh38" id="GRCh38"
                                    value="1" unchecked disabled></td>
                            {{-- <td><input type="checkbox" class="form-check-inline" name="GRCh38" id="GRCh38"
									value="1" unchecked onchange="window.CheckAll()"></td> --}}
                            <td></td>
                        </tr>
                        <tr>
                            <td>Pre-defined lead SNPs
                                <a class="infoPop" data-bs-toggle="popover" title="Pre-defined lead SNPs"
                                    data-bs-content="This option can be used when you already have determined lead SNPs and do not want FUMA to do this for you. This option can be also used when you want to include specific SNPs as lead SNPs which do no reach significant P-value threshold. The input file should have 3 columns, rsID, chromosome and position with header (header could be anything but the order of columns have to match).">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="file" class="form-control-file" name="leadSNPs" id="leadSNPs"
                                    onchange="window.CheckAll()" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Identify additional independent lead SNPs
                                <a class="infoPop" data-bs-toggle="popover"
                                    title="Additional identification of lead SNPs"
                                    data-bs-content="This option is only valid when pre-defined lead SNPs are provided. Please uncheck this to NOT IDENTIFY additional lead SNPs than the provided ones. When this option is checked, FUMA will identify all independent lead SNPs after taking all SNPs in LD of pre-defined lead SNPs if there is any.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="checkbox" class="form-check-inline" name="addleadSNPs" id="addleadSNPs"
                                    value="1" checked onchange="window.CheckAll()"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Predefined genomic region
                                <a class="infoPop" data-bs-toggle="popover" title="Pre-defined genomic regions"
                                    data-bs-content="This option can be used when you already have defined specific genomic regions of interest and only require annotations of significant SNPs and their proxy SNPs in these regions. The input file should have 3 columns, chromosome, start and end position (on hg19) with header (header could be anything but the order of columns have to match).">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="file" class="form-control-file" name="regions" id="regions"
                                    onchange="window.CheckAll()" /></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


        <!-- Parameters for lead SNPs and candidate SNPs -->
        <div class="accordion-item" style="padding:0px;">
            <h2 class="accordion-header" id="heading2">
                <button class="accordion-button fs-5" type="button" data-bs-target="#NewJobParamsPanel"
                    data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobParamsPanel">
                    2. Parameters for lead SNPs and candidate SNPs identification
                </button>
            </h2>
            <div class="accordion-collapse collapse show" id="NewJobParamsPanel" aria-labelledby="heading2">
                <div class="accordion-body">
                    <table class="table table-bordered inputTable" id="NewJobParams" style="width: auto;">
                        <tr>
                            <td>Sample size (N)
                                <a class="infoPop" data-bs-toggle="popover" data-bs-html="true" title="Sample size"
                                    data-bs-content="The total number of individuals (cases + controls, or total N) used in GWAS.
									<br>
									This is only used for MAGMA. When total sample size is defined, the same number will be used for all SNPs.
									<br>
									If you have <b>column 'N'</b> in your input GWAS summary statistics file, specified column will be used for N per SNP.
									<br>
									It does not affect functional annotations and prioritizations.
									<br>
									If you don't know the sample size, the random number should be fine (> 50), yet that does not render the gene-based tests from MAGMA invalid.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td>
								<div class="form-floating">
                                	<input type="number" class="form-control"
										id="N" name="N" onkeyup="window.CheckAll();"
										onpaste="window.CheckAll();" oninput="window.CheckAll();">
									<label for="N">Total sample size (integer): </label>
								</div>
                                OR<br>
								<div class="form-floating">
									<input type="text" class="form-control"
										id="Ncol" name="Ncol" onkeyup="window.CheckAll();"
										onpaste="window.CheckAll();" oninput="window.CheckAll();">
									<label for="Ncol">Column name for N per SNP (text): </label>
								</div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Maximum P-value of lead SNPs (&lt;)</td>
                            <td><input type="number" class="form-control" id="leadP" name="leadP"
                                    value="5e-8" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Maximum P-value cutoff (&lt;)
                                <a class="infoPop" data-bs-toggle="popover" title="GWAS P-value cutoff"
                                    data-bs-content="This threshold defines the maximum P-values of SNPs to be included in the annotation. Setting it at 1 means that all SNPs that are in LD with the lead SNP will be included in the annotation and prioritization even though they may not show a significant association with the phenotype. We advise to set this threshold at least at 0.05.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="number" class="form-control" id="gwasP" name="gwasP"
                                    value="0.05" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>r<sup>2</sup> threshold to define independent significant SNPs (&ge;)</td>
                            <td><input type="number" class="form-control" id="r2" name="r2"
                                    value="0.6" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2nd r<sup>2</sup> threshold to define lead SNPs (&ge;)
                                <a class="infoPop" data-bs-toggle="popover" title="2nd r2 threshold"
                                    data-bs-content="This is a r2 threshold for second clumping to define lead SNPs from independent significant SNPs.
								When this value is same as 1st r2 threshold, lead SNPs are identical to independent significant SNPs.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="number" class="form-control" id="r2_2" name="r2_2"
                                    value="0.1" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Reference panel population</td>
                            <td>
                                <select class="form-select" id="refpanel" name="refpanel">
                                    <option value="1KG/Phase3/ALL">1000G Phase3 ALL</option>
                                    <option value="1KG/Phase3/AFR">1000G Phase3 AFR</option>
                                    <option value="1KG/Phase3/AMR">1000G Phase3 AMR</option>
                                    <option value="1KG/Phase3/EAS">1000G Phase3 EAS</option>
                                    <option selected value="1KG/Phase3/EUR">1000G Phase3 EUR</option>
                                    <option value="1KG/Phase3/SAS">1000G Phase3 SAS</option>
                                    <option value="UKB/release2b/WBrits_10k">UKB release2b 10k White British
                                    </option>
                                    <option value="UKB/release2b/EUR_10k">UKB release2b 10k European</option>
                                </select>
                            </td>
                            <td>
                                <div class="alert alert-success"
                                    style="display: table-cell; padding-top:0; padding-bottom:0;">
                                    <i class="fa fa-check"></i> OK.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Include variants in reference panel (non-GWAS tagged SNPs in LD)
                                <a class="infoPop" data-bs-toggle="popover" title="Variants in reference"
                                    data-bs-content="Select ‘yes’
								if you want to include SNPs that are not available in the GWAS output but are available in the selected reference panel.
								Including these SNPs may provide information on functional variants in LD with the lead SNP.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td>
                                <select class="form-select" id="refSNPs" name="refSNPs">
                                    <option selected value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </td>
                            <td>
                                <div class="alert alert-success"
                                    style="display: table-cell; padding-top:0; padding-bottom:0;">
                                    <i class="fa fa-check"></i> OK.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Minimum Minor Allele Frequency (&ge;)
                                <a class="infoPop" data-bs-toggle="popover" title="Minimum Minor Allele Frequency"
                                    data-bs-content="This threshold defines the minimum MAF of the SNPs to be included in the annotation. MAFs are based on the selected reference panel.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="number" class="form-control" id="maf" name="maf"
                                    value="0" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                    oninput="window.CheckAll();" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Maximum distance between LD blocks to merge into a locus (&lt; kb)
                                <a class="infoPop" data-bs-toggle="popover"
                                    title="Maximum distance between LD blocks to merge"
                                    data-bs-content="LD blocks closer than the distance will be merged into a genomic locus. If this is set at 0, only physically overlapped LD blocks will be merged. This is only for representation of GWAS risk loci which does not affect any annotation and prioritization results.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="mergeDist" name="mergeDist"
                                        value="250" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                        oninput="window.CheckAll();" />
                                    <span class="input-group-text">kb</span>
                            </td>
                </div>
                <td></td>
                </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Parameters for gene mapping -->
    <!-- positional mapping -->
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

    <!-- eqtl mapping -->
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
                <table class="table table-bordered inputTable" id="NewJobEqtlMap" style="width: auto;">
                    <tr>
                        <td>Perform eQTL mapping
                            <a class="infoPop" data-bs-toggle="popover" title="eQTL mapping"
                                data-bs-content="eQTL mapping maps SNPs to genes based on eQTL information. This maps SNPs to genes up to 1 Mb part (cis-eQTL). Please check this option to perform eQTL mapping.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
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
                                    @include('snp2gene.eqtl_options')
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
                        <td>eQTL P-value threshold
                            <a class="infoPop" data-bs-toggle="popover" title="eQTL P-value threshold"
                                data-bs-content="By default, only significant eQTLs are used (FDR &lt; 0.05). Please UNCHECK 'Use only significant snp-gene pair' to filter eQTLs based on raw P-value.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
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
                    <table class="table table-bordered inputTable" id="eqtlMapOptFiltTable">
                        <tr>
                            <td rowspan="2">CADD</td>
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
                            <td rowspan="2">RegulomeDB</td>
                            <td>Perform SNPs filtering based on RegulomeDB score
                                <a class="infoPop" data-bs-toggle="popover" title="RegulomeDB Score filtering"
                                    data-bs-content="Please check this option to filter SNPs based on RegulomeDB score and specify the maximum score in the box below.">
                                    <i class="fa-regular fa-circle-question fa-lg"></i>
                                </a>
                            </td>
                            <td><input type="checkbox" class="form-check-inline" name="eqtlMapRDBcheck"
                                    id="eqtlMapRDBcheck" onchange="window.CheckAll();"></td>
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
                            <td rowspan="4">15-core chromatin state</td>
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
                                    <select multiple class="form-select" size="10" id="eqtlMapAnnoDs"
                                        name="eqtlMapAnnoDs[]">
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
                                <select class="form-select" id="eqtlMapAnnoMeth" name="eqtlMapAnnoMeth">
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

    <!-- chromatin interaction mapping -->
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

    <!-- Gene type multiple selection -->
    <div class="accordion-item" style="padding:0px;">
        <h2 class="accordion-header" id="headingGT">
            <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobGenePanel"
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobGenePanel">
                4. Gene types
            </button>
        </h2>
        <div class="accordion-collapse collapse" id="NewJobGenePanel" aria-labelledby="headingGT">
            <div class="accordion-body">
                <table class="table table-bordered inputTable" id="NewJobGene" style="width: auto;">
                    <tr>
                        <td>Ensembl version</td>
                        <td>
                            <select class="form-select" id="ensembl" name="ensembl">
                                <option selected value="v110">v110</option>
                                <option selected value="v102">v102</option>
                                <!-- REMOVED: no longer supported by biomart option value="v92">v92</option-->
                                <!-- REMOVED: no longer supported by biomart option value="v85">v85</option-->
                            </select>
                        </td>
                        <td>
                            <div class="alert alert-success"
                                style="display: table-cell; padding-top:0; padding-bottom:0;">
                                <i class="fa fa-check"></i> OK.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Gene type
                            <a class="infoPop" data-bs-toggle="popover" title="Gene Type"
                                data-bs-content="Setting gene type defines what kind of genes should be included in the gene prioritization. Gene type is based on gene biotype obtained from BioMart (Ensembl). By default, only protein-coding genes are used for mapping.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a><br>
                            <span class="info"><i class="fa fa-info"></i> Multiple gene type can be
                                selected.</span>
                        </td>
                        <td>
                            <select multiple class="form-select" name="genetype[]" id="genetype"
                                onchange="window.CheckAll();">
                                <option value="all">All</option>
                                <option selected value="protein_coding">Protein coding</option>
                                <option
                                    value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">
                                    lncRNA</option>
                                <option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA
                                </option>
                                <option
                                    value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:3prime_overlapping_ncrna:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">
                                    Processed transcripts</option>
                                <option
                                    value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">
                                    Pseudogene</option>
                                <option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes
                                </option>
                                <option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes
                                </option>
                            </select>
                        </td>
                        <td>
                            <div class="alert alert-success"
                                style="display: table-cell; padding-top:0; padding-bottom:0;">
                                <i class="fa fa-check"></i> OK.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- MHC regions -->
    <div class="accordion-item" style="padding:0px;">
        <h2 class="accordion-header" id="headingMHC">
            <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobMHCPanel"
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobMHCPanel">
                5. MHC region
            </button>
        </h2>
        <div class="accordion-collapse collapse" id="NewJobMHCPanel" aria-labelledby="headingMHC">
            <div class="accordion-body">
                <table class="table table-bordered inputTable" id="NewJobMHC" style="width: auto;">
                    <tr>
                        <td>Exclude MHC region
                            <a class="infoPop" data-bs-toggle="popover" title="Exclude MHC region"
                                data-bs-content="Please check to EXCLUDE MHC region; default MHC region is the genomic region between MOG and COL11A2 genes.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="checkbox" class="form-check-inline" name="MHCregion" id="MHCregion"
                                    value="exMHC" checked onchange="window.CheckAll();">
                                <select class="form-select" id="MHCopt" name="MHCopt"
                                    onchange="window.CheckAll();">
                                    <option value="all">from all (annotations and MAGMA)</option>
                                    <option selected value="annot">from only annotations</option>
                                    <option value="magma">from only MAGMA</option>
                                </select>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Extended MHC region
                            <a class="infoPop" data-bs-toggle="popover" title="Extended MHC region"
                                data-bs-content="User defined MHC region. When this option is not given, the default MHC region will be used.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a><br>
                            <span class="info"><i class="fa fa-info"></i>e.g. 25000000-33000000<br>
                        </td>
                        <td><input type="text" class="form-control" name="extMHCregion" id="extMHCregion"
                                onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" /></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- MAGMA -->
    <div class="accordion-item" style="padding:0px;">
        <h2 class="accordion-header" id="headingMAG">
            <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobMAGMAPanel"
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobMAGMAPanel">
                6. MAGMA analysis
        </h2>
        <div class="accordion-collapse collapse" id="NewJobMAGMAPanel" aria-labelledby="headingMAG">
            <div class="accordion-body">
                <table class="table table-bordered inputTable" id="NewJobMAGMA" style="width: auto;">
                    <tr>
                        <td>Perform MAGMA
                            <a class="infoPop" data-bs-toggle="popover" title="MAGMA"
                                data-bs-content="When checked, MAGMA gene and gene-set analyses will be performed.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <span class="form-inline">
                                <input type="checkbox" class="form-check" name="magma" id="magma"
                                    onchange="window.CheckAll();">
                            </span>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Gene windows
                            <a class="infoPop" data-bs-toggle="popover" title="MAGMA gene window"
                                data-bs-content="The window size of genes to assign SNPs.
								To set same window size for both up- and downstream, provide one value.
								To set different window sizes for up- and downstream, provide two values separated by comma.
								e.g. 2,1 will set 2kb upstream and 1kb downstream.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </td>
                        <td>
                            <div class="row">
                                <div class=col-3>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="magma_window"
                                            name="magma_window" value="0" onkeyup="window.CheckAll();"
                                            onpaste="window.CheckAll();" oninput="window.CheckAll();">
                                        <span class="input-group-text">kb</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="row">
                                        <span class="info"><i class="fa fa-info"></i>
                                            One value will set same window size both sides, two values separated
                                            by comma will set different window sizes for up- and downstream.
                                            e.g. 2,1 will set window sizes 2kb upstream and 1kb downstream of
                                            the genes.
                                        </span>
                                    </div>
                                    <span class="info"><i class="fa fa-info"></i>
                                        Maximum window size is limited to 50.
                                    </span>
                                </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>MAGMA gene expression analysis
                            <a class="infoPop" data-bs-toggle="popover" title="MAGMA gene expression analysis"
                                data-bs-content="When magma is performed, at least one data set needs to be selected.
								Multiple data sets can be also selected.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a><br>
                        </td>
                        <td>
                            <select multiple class="form-select" name="magma_exp[]" id="magma_exp">
                                <option selected value="GTEx/v8/gtex_v8_ts_avg_log2TPM">GTEx v8: 54
                                    tissue types</option>
                                <option selected value="GTEx/v8/gtex_v8_ts_general_avg_log2TPM">GTEx v8:
                                    30 general tissue types</option>
                                <option value="GTEx/v7/gtex_v7_ts_avg_log2TPM">GTEx v7: 53 tissue types
                                </option>
                                <option value="GTEx/v7/gtex_v7_ts_general_avg_log2TPM">GTEx v7: 30
                                    general tissue types</option>
                                <option value="GTEx/v6/gtex_v6_ts_avg_log2RPKM">GTEx v6: 53 tissue types
                                </option>
                                <option value="GTEx/v6/gtex_v6_ts_general_avg_log2RPKM">GTEx v6: 30
                                    general tissue types</option>
                                <option value="BrainSpan/bs_age_avg_log2RPKM">BrainSpan: 29 different
                                    ages of brain samples</option>
                                <option value="BrainSpan/bs_dev_avg_log2RPKM">BrainSpan: 11 general
                                    developmental stages of brain samples</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-5">
        <div class="input-group">
            <span class="input-group-text" style="font-size:16px;">Title of job submission: </span>
            <input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle" />
        </div>
    </div>
</div>
<div>
    <span class="info"><i class="fa fa-info"></i>
        This is not mandatory, but job title might help you to track your jobs.
    </span>
    <br><br>
    <div class="row">
        <div class="col-2">
            <input class="btn btn-default" type="submit" value="Submit Job" name="SubmitNewJob"
                id="SubmitNewJob" />
        </div>
        <div class="col-10" style="color: red; font-size:18px;">
            <i class="fa fa-exclamation-triangle"></i> After submitting, please wait until the file is uploaded,
            and do not move away from the submission page.
        </div>
    </div>
    @if (Request::is('snp2gene'))
        {!! html()->form()->close() !!}
    @endif
</div>
</div>
