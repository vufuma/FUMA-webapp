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
                            <br>

                            <div class="alert alert-warning">
                            If you clicked the option to run the drug set analysis, please make sure to click on running MAGMA above. 
                            </div>

                            <div class="accordion-collapse collapse" id="drugsetsParams">
                                <div style="font-weight: bold;color: #007bff;">Do you want to test individual drugs?</div>
                                <div class="accordion-body">
                                    <i>1. Select drugs by clinical phase:</i><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug_mp0" onchange="window.CheckAll()" checked>
                                        drug_mp0
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug_mp1" onchange="window.CheckAll()">
                                        drug_mp1
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug_mp2" onchange="window.CheckAll()">
                                        drug_mp2
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug_mp3" onchange="window.CheckAll()">
                                        drug_mp3
                                    </label><br>

                                    <label>
                                        <input type="radio" name="drugsets" value="drug_mp4" onchange="window.CheckAll()">
                                        drug_mp4
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>2. Filter dgidb gene annotations by score</i><br>

                                    <label>
                                        <input type="radio" name="dgidb" value="all" onchange="window.CheckAll()" checked>
                                        all
                                    </label><br>

                                    <label>
                                        <input type="radio" name="dgidb" value="median" onchange="window.CheckAll()">
                                        median
                                    </label><br>

                                    <label>
                                        <input type="radio" name="dgidb" value="none" onchange="window.CheckAll()">
                                        none
                                    </label><br>

                                    <label>
                                        <input type="radio" name="dgidb" value="p75" onchange="window.CheckAll()">
                                        p75
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>3. Conditional Analysis?</i><br>
                                    <label>
                                        <input type="radio" name="conditional_drug" value="yes" onchange="window.CheckAll()" checked>
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="conditional_drug" value="no" onchange="window.CheckAll()">
                                        no
                                    </label><br>
                                </div>
                                <div class="accordion-body">
                                    <i>4. Enrich: Specify drug group type to test for enrichment</i><br>
                                    <label>
                                        <input type="radio" name="enrich" value="None" onchange="window.CheckAll()">
                                        None
                                    </label><br>
                                    <label>
                                        <input type="radio" name="enrich" value="atc3" onchange="window.CheckAll()" checked>
                                        atc3
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>5. Correct for drug gene set covariance during enrichment testing?</i><br>
                                    <label>
                                        <input type="radio" name="correct_cov" value="TRUE" onchange="window.CheckAll()" checked>
                                        TRUE
                                    </label><br>
                                    <label>
                                        <input type="radio" name="correct_cov" value="FALSE" onchange="window.CheckAll()">
                                        FALSE
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>6. Minimum gene set size</i><br>
                                    <input type="number" class="form-control" id="min_set_size_drug" name="min_set_size_drug"
                                value="2" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" />
                                </div>

                                <div class="accordion-body">
                                    <i>7. Minimum drug group sample size for enrichment testing</i><br>
                                    <input type="number" class="form-control" id="min_sample_size" name="min_sample_size"
                                value="5" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" />
                                </div>

                                <div class="accordion-body">
                                    <i>8. Multiple testing correction</i><br>
                                    <label>
                                        <input type="radio" name="multiple_testing_drug" value="bonf" onchange="window.CheckAll()">
                                        Bonferroni
                                    </label><br>
                                    <label>
                                        <input type="radio" name="multiple_testing_drug" value="fdr" onchange="window.CheckAll()" checked>
                                        FDR
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>9. Use PoPs scores?</i><br>
                                    <label>
                                        <input type="radio" name="use_pops_drug" value="yes" onchange="window.CheckAll()">
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="use_pops_drug" value="no" onchange="window.CheckAll()" checked>
                                        no
                                    </label><br>
                                </div>

                                <div style="font-weight: bold;color: #007bff;">Do you want to test mechanisms of action (MoA)?</div>
                                <div class="accordion-body">
                                    <i>1. Select gene sets:</i><br>

                                    <label>
                                        <input type="radio" name="moasets" value="none" onchange="window.CheckAll()" checked>
                                        Not testing MoA
                                    </label><br>

                                    <label>
                                        <input type="radio" name="moasets" value="moa_group" onchange="window.CheckAll()">
                                        moa_group
                                    </label><br>

                                    <label>
                                        <input type="radio" name="moasets" value="moa_nested" onchange="window.CheckAll()">
                                        moa_nested
                                    </label><br>

                                    <label>
                                        <input type="radio" name="moasets" value="moa_specific" onchange="window.CheckAll()">
                                        moa_specific
                                    </label><br>

                                    <label>
                                        <input type="radio" name="moasets" value="moa_combined" onchange="window.CheckAll()">
                                        moa_combined
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>2. Conditional Analysis?</i><br>
                                    <label>
                                        <input type="radio" name="conditional_moa" value="yes" onchange="window.CheckAll()" checked>
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="conditional_moa" value="no" onchange="window.CheckAll()">
                                        no
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>4. Minimum gene set size</i><br>
                                    <input type="number" class="form-control" id="min_set_size_moa" name="min_set_size_moa"
                                value="2" onkeyup="window.CheckAll();" onpaste="window.CheckAll();"
                                oninput="window.CheckAll();" />
                                </div>

                                <div class="accordion-body">
                                    <i>5. Multiple testing correction</i><br>
                                    <label>
                                        <input type="radio" name="multiple_testing_moa" value="bonf" onchange="window.CheckAll()">
                                        Bonferroni
                                    </label><br>
                                    <label>
                                        <input type="radio" name="multiple_testing_moa" value="fdr" onchange="window.CheckAll()" checked>
                                        FDR
                                    </label><br>
                                </div>

                                <div class="accordion-body">
                                    <i>6. Use PoPs scores?</i><br>
                                    <label>
                                        <input type="radio" name="use_pops_moa" value="yes" onchange="window.CheckAll()">
                                        yes
                                    </label><br>
                                    <label>
                                        <input type="radio" name="use_pops_moa" value="no" onchange="window.CheckAll()" checked>
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