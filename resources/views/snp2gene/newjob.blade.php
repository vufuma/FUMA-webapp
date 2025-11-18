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
    @include('snp2gene.newjob_components._upload_input')

    <!-- Parameters for gene mapping -->
    <!-- positional mapping -->
    @include('snp2gene.newjob_components._positional_mapping')

    <!-- eqtl mapping -->
     @include('snp2gene.newjob_components._eqtls_mapping')
                            
    <!-- chromatin interaction mapping -->
    @include('snp2gene.newjob_components._chromatin_mapping')

    <!-- Gene type multiple selection -->
    @include('snp2gene.newjob_components._gene_type')

    <!-- MHC regions -->
    @include('snp2gene.newjob_components._mhc_region')

    <!-- MAGMA -->
    @include('snp2gene.newjob_components._magma')

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
