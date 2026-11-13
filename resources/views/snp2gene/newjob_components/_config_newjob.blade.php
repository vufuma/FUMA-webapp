<div class="accordion" style="padding-top: 0px;">
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading1">
            <button class="accordion-button fs-5" type="button" data-bs-target="#NewJobConfig"
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobConfig">
                0. Job configuration
            </button>
        </h2>
        <div class="accordion-collapse collapse show" id="NewJobConfig" aria-labelledby="heading1">
            <div class="accordion-body">
                <table class="table table-bordered inputTable" style="width: auto;">
                    <tr>    <!-- load previous settings -->
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
                    </tr>

                    <tr>
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text" style="font-size:18px;">Title of job submission: </span>
                                    <input type="text" class="form-control" name="NewJobTitle" id="NewJobTitle" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="info"><i class="fa fa-info"></i>
                                This is not mandatory, but job title might help you to track your jobs.
                            </span>
                        </div>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>