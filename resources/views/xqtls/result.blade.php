<div class="sidePanel container" style="padding-top:50px; display: none" id="xqtlTables">
	<div class="card"><div class="card-body">
		<h4 style="color: #00004d">Result tables</h4>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link" href="#lavaResultsTable" id="lavaResults-tab" data-bs-toggle="tab">LAVA</a>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade show active" id="lavaResultsTable" aria-labelledby="lociTablePane-tab">
				<table id="lavaTable" class="table table-striped table-sm display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
					<thead>
						<tr>
							<th>Phenotype</th><th>Genomic Region</th><th>QTL Type</th><th>Tissue</th><th>Locus</th><th>Rho</th><th>Rho lower</th><th>Rho upper</th><th>P</th><th>P Adj</th><th>Bonferroni Significance</th><th>GENE</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
        </div>

		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link" href="#colocResultsTable" id="colocResults-tab" data-bs-toggle="tab">Colocalization</a>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade show active" id="colocResultsTable" aria-labelledby="lociTablePane-tab">
				<table id="colocTable" class="table table-striped table-sm display compact dt-body-center" width="100%" cellspacing="0" style="display: block; overflow-x: auto;">
					<thead>
						<tr>
							<th>Tissue</th><th>Gene</th><th>Nsnps</th><th>PP.H0.abf</th><th>PP.H1.abf</th><th>PP.H2.abf</th><th>PP.H3.abf</th><th>PP.H4.abf</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
        </div>

	</div>
</div>