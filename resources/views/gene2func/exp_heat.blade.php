<!-- Expression heatmap -->
<div id="expPanel" class="sidePanel container" style="padding-top:50px;">
	<!-- <div id="expHeat" style='overflow:auto; width:1010px; height:450px;'></div> -->
	<h4>Gene expression heatmap</h4>
	<div class="container">
		<div class="row mb-1">
			<div class="col-sm-2">Data set:</div>
			<div class="col-sm-1">
				<select id="gene_exp_data" class="form-select" style="width: auto;">
				</select>
			</div>
		</div>
		<br>
		<div class="row mb-1">
			<div class="col-sm-2">Expression Value:
				<a class="infoPop" data-bs-toggle="popover" title="Expression value" data-bs-html="true" data-bs-content="
					<b>Average expression per label</b>:
					This is an average of log2 transformed expression value (e.g. RPKM and TPM) per label (e.g. tissue type or developmental stage).
					RPKM and TPM were wisolized at 50.
					Darker red means higher expression of that gene, compared to a darker blue color.<br>
					<b>Average of normalized expression per label</b>:
					Average value of the <u>relative</u> expression value (zero mean normalization of log2 transformed expression).
					Darker red means higher relative expression of that gene in label X, compared to a darker blue color in the same label.<br>
					">
					<i class="fa-regular fa-circle-question fa-lg"></i>
				</a>
			</div>
			<div class="col-sm-1">
				<select id="expval" class="form-select" style="width: auto;">
					<option value="log2" selected>Average expression per label (log2 transformed)</option>
					<option value="norm">Average of normalized expression per label (zero mean across samples)</option>
				</select>

			</div>
		</div>
		<br>
	    <div class="row mb-1">
			<div class="col-sm-2">Order genes by:</div>
			<div class="col-sm-1">
				<select id="geneSort" class="form-select" style="width: auto;">
					<option value="clst">Cluster</option>
					<option value="alph" selected>Alphabetical order</option>
				</select>
			</div>
		</div>

		<div class="row mb-1">
			<div class="col-sm-2">Order tissues by:</div>
			<div class="col-sm-1">
				<select id="tsSort" class="form-select" style="width: auto;">
					<option value="clst">Cluster</option>
					<option value="alph" selected>Alphabetical order</option>
				</select>
			</div>
		</div>
	</div><br><br>
	Download the plot as
	<button class="btn btn-default btn-sm ImgDown" onclick='ImgDown("expHeat","png");'>PNG</button>
	<button class="btn btn-default btn-sm ImgDown" onclick='ImgDown("expHeat","jpeg");'>JPG</button>
	<button class="btn btn-default btn-sm ImgDown" onclick='ImgDown("expHeat","svg");'>SVG</button>
	<button class="btn btn-default btn-sm ImgDown" onclick='ImgDown("expHeat","pdf");'>PDF</button>

	<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="dir" id="expHeatDir" val=""/>
		<input type="hidden" name="jobID" id="expHeatJobID" val=""/>
		<input type="hidden" name="data" id="expHeatData" val=""/>
		<input type="hidden" name="type" id="expHeatType" val=""/>
		<input type="hidden" name="fileName" id="expHeatFileName" val=""/>
		<input type="submit" id="expHeatSubmit" class="ImgDownSubmit"/>
	</form>
	<div id="expHeat"></div>
	<div id="expBox"></div>
	<br>
</div>
