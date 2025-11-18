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