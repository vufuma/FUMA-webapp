<style> 
.accordion-button.accordion-highlight {
background-color: #efeff8ff;
border-color: rgba(0,0,0,0.1);
}
</style>
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
                <tr>
                    <td>
                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                            MAGMA Drug Sets analysis

                            <a class="infoPop"
                            data-bs-toggle="popover"
                            title="MAGMA Drug Sets analysis"
                            data-bs-content="When this option is checked, FUMA performs genetically informed drug repositioning using drug gene set analysis using MAGMA.">
                                <i class="fa-regular fa-circle-question fa-lg"></i>
                            </a>
                        </div>
                        <br>
                    </td>
                    <td>
                        <div class="accordion-item">
                            <div class="accordion-header d-flex justify-content-start align-items-center">
                                <input 
                                    type="checkbox"
                                    class="form-check-input m-0"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#drugsetsParams"
                                    name="drugsets"
                                    id="drugsets"
                                    onchange="window.CheckAll()">
                            </div>

                            <div class="accordion-collapse collapse" id="drugsetsParams">
                                <div class="accordion-body">
                                    <i>Select Drug Sets:</i><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug" onchange="window.CheckAll()" checked>
                                        drug
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="moa_clue" onchange="window.CheckAll()">
                                        moa_clue
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="moa_targ_clue" onchange="window.CheckAll()">
                                        moa_targ_clue
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="moa_chembl" onchange="window.CheckAll()">
                                        moa_chembl
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="moa_targ_chembl" onchange="window.CheckAll()">
                                        moa_targ_chembl
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="all" onchange="window.CheckAll()">
                                        all
                                    </label><br>
                                </div>
                                <div class="accordion-body">
                                    <i>Conditional Analysis?</i><br>
                                    <label>
                                        <input type="radio" name="conditional" value="yes" onchange="window.CheckAll()">
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="conditional" value="no" onchange="window.CheckAll()">
                                        no
                                    </label><br>
                                </div>
                                <div class="accordion-body">
                                    <i>Enrich: Specify drug group type to test for enrichment</i><br>
                                    <label>
                                        <input type="radio" name="enrich" value="None" onchange="window.CheckAll()">
                                        None
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="atc1" onchange="window.CheckAll()">
                                        atc1
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="atc2" onchange="window.CheckAll()">
                                        atc2
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="atc3" onchange="window.CheckAll()">
                                        atc3
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="atc4moa_clue" onchange="window.CheckAll()">
                                        atc4moa_clue
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="moa_targ_clue" onchange="window.CheckAll()">
                                        moa_targ_clue
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="moa_chembl" onchange="window.CheckAll()">
                                        moa_chembl
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="moa_targ_chembl" onchange="window.CheckAll()">
                                        moa_targ_chembl
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>Correct for drug gene set covariance during enrichment testing?</i><br>
                                    <label>
                                        <input type="radio" name="correct_cov" value="correct_cov_yes" onchange="window.CheckAll()">
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="correct_cov" value="correct_cov_no" onchange="window.CheckAll()">
                                        no
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i> Minimum gene set size</i><br>
                                    <input type="number" class="form-control" id="min_set_size" name="min_set_size"
                                value="5" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" />
                                </div>

                                <div class="accordion-body">
                                    <i> Minimum drug group sample size for enrichment testing</i><br>
                                    <input type="number" class="form-control" id="min_sample_size" name="min_sample_size"
                                value="5" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" />
                                </div>

                                <div class="accordion-body">
                                    <i>Multiple testing correction</i><br>
                                    <label>
                                        <input type="radio" name="multiple_testing" value="multiple_testing_bonf" onchange="window.CheckAll()">
                                        Bonferroni
                                    </label><br>
                                    <label>
                                        <input type="radio" name="multiple_testing" value="multiple_testing_fdr" onchange="window.CheckAll()">
                                        FDR
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>Use PoPs scores?</i><br>
                                    <label>
                                        <input type="radio" name="use_pops" value="use_pops_yes" onchange="window.CheckAll()">
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="use_pops" value="use_pops_no" onchange="window.CheckAll()">
                                        no
                                    </label><br>
                                </div>

                            </div>
                        </div>


                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>