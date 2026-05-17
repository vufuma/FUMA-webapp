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