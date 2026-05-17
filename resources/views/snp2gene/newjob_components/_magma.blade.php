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