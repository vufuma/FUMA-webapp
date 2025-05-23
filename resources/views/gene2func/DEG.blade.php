<!-- Tissue specificity bar chart -->
<div id="tsEnrichBarPanel"  class="sidePanel container" style="padding-top:50px;">
	<h4>Differentially expressed genes
		<a class="infoPop" data-bs-toggle="popover" title="Enrichment in DEG sets"
			data-bs-content="Pre-calculated differentially expressed genes (DEG) sets were created for each of expression data set.
			DEG sets are defined by a two-sided t-tests per label versus all remaining (tissue types or developmental stages).
			Genes with a Bonferroni corrected p-value < 0.05 and absolute log fold change ≥ 0.58 are selected as DEG.
			For the signed DEG, the direction of expression was taken into account
			(i.e. a up-regulated DEG set contains all genes that are significantly overexpressed in sample with that label compared to other samples).
			The -log10(P values) in the graph refer to the probability of the hypergeometric test.">
			<i class="fa-regular fa-circle-question fa-lg"></i>
		</a>
	</h4>
	<span class="info"><i class="fa fa-info"></i>
		Significantly enriched DEG sets (P<sub>bon</sub> &lt; 0.05) are highlighted in red.
	</span><br><br>
	<div id="DEGPlot">
		<form method="post" target="_blank" action="{{ Config::get('app.subdir') }}/{{$page}}/imgdown">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dir" id="DEGDir" val=""/>
			<input type="hidden" name="jobID" id="DEGJobID" val=""/>
			<input type="hidden" name="data" id="DEGData" val=""/>
			<input type="hidden" name="type" id="DEGType" val=""/>
			<input type="hidden" name="fileName" id="DEGFileName" val=""/>
			<input type="submit" id="DEGSubmit" class="ImgDownSubmit"/>
		</form>
		<br>
		<span class="form-inline">
			Order tissue by :
			<select id="DEGorder" class="form-select" style="width: auto;">
				<option value="alph">Alphabetical</option>
				<option value="up">up-regulated DEG P-value</option>
				<option value="down">down-regulated DEG P-value</option>
				<option value="two" selected>two-side DEG P-value</option>
			</select>
		</span>
	</div>
</div>
