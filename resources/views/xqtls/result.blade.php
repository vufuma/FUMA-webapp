<div class="sidePanel container" style="padding-top:50px;"  id="xqtlTables">
	<div class="card"><div class="card-body">
		<h4 style="color: #00004d">Result tables</h4>
		
		<!-- Define navigation tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active " href="#colocResultsTable" id="colocResults-tab" data-bs-toggle="tab">Colocalization</a>
			</li>

			<li class="nav-item" role="presentation">
				<a class="nav-link" href="#lavaResultsTable" id="lavaResults-tab" data-bs-toggle="tab">LAVA</a>
			</li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade show active" id="colocResultsTable" aria-labelledby="lociTablePane-tab">
				<table id="colocTable" class="table table-striped table-sm display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
					<thead>
						<tr>
							<th>Tissue</th><th>Gene</th><th>Nsnps</th><th>PP.H0.abf</th><th>PP.H1.abf</th><th>PP.H2.abf</th><th>PP.H3.abf</th><th>PP.H4.abf</th><th>Symbol</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane fade" id="lavaResultsTable" aria-labelledby="lociTablePane-tab">
				<table id="lavaTable" class="table table-striped table-sm display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
					<thead>
						<tr>
							<th>Gene</th><th>Chromosome</th><th>Phenotype</th><th>Rho</th><th>Rho lower</th><th>Rho upper</th><th>r2</th><th>r2.lower</th><th>r2.upper</th><th>p</th><th>Dataset</th><th>p.adjust</th><th>Symbol</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
        </div>
	</div>

	<div>
		<h4 style="color: #00004d">Download Results: </h4>
		<div class="clickable" onclick='tutorialDownloadVariant("colocResultsFull")'> Colocalization Full Results
			<img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
		</div>
		<div class="clickable" onclick='tutorialDownloadVariant("colocResultsFiltered")'> Colocalization Signigicant Results
			<img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
		</div>
		<div class="clickable" onclick='tutorialDownloadVariant("lavaResultsFull")'> LAVA Full Results
			<img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
		</div>
		<div class="clickable" onclick='tutorialDownloadVariant("lavaResultsFiltered")'> LAVA Significant Results
			<img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
		</div>
	</div>

</div>




