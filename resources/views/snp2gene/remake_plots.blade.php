<div id="s2gRemakePlots" class="sidePanel container" style="min-height:80vh;padding-top:50px;">
	<h4 style="color: #00004d">Manhattan Plot (GWAS summary statistics)</h4>
	<input type="file" class="form-control-file" name="manhattan" id="manhattanFile" accept=".txt,.tsv" onchange="window.ManhattanplotUpload(event)" /><br>
	<div id="ManhattanMessage" role="alert"></div>
	<div id="remakeManhattan"></div>

	<h4 style="color: #00004d">Manhattan Plot (gene-based test)</h4>
	<input type="file" class="form-control-file" name="geneManhattan" id="geneManhattanFile" accept=".out" onchange="window.GeneManhattanplotUpload(event)" /><br>
	<div id="GeneManhattanMessage" role="alert"></div>
	<span id="remakeGeneManhattanDesc"></span><br><br>
	<label for="remakeTopGenes">Label top</label>
	<input class="form-control" type="number" id="remakeTopGenes" style="width: 80px;">
	<span>genes.</span><br><br>
	<div id="remakeGeneManhattan"></div>

	<h4 style="color: #00004d">QQ plot (GWAS summary statistics)</h4>
	<input type="file" class="form-control-file" name="qq" id="qqFile" accept=".txt" onchange="window.QQplotUpload(event)" /><br>
	<span class="info">Upload the tab-delimited QQSNPs.txt file with <code>obs</code> and <code>exp</code> columns.</span><br><br>
	<div id="QQMessage" role="alert"></div>
	<div id="remakeQQ"></div>
</div>

<script type="module">
	import { ManhattanplotUpload } from "{{ Vite::appjs('utils/RemakePlots.js') }}";
	window.ManhattanplotUpload = ManhattanplotUpload;
	import { GeneManhattanplotUpload } from "{{ Vite::appjs('utils/RemakePlots.js') }}";
	window.GeneManhattanplotUpload = GeneManhattanplotUpload;
	import { QQplotUpload } from "{{ Vite::appjs('utils/RemakePlots.js') }}";
	window.QQplotUpload = QQplotUpload;
</script>