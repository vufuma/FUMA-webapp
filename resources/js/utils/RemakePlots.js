export const GWplot = function (
	data,
	plotContainer = "#manhattan",
	genePlotContainer = "#geneManhattan",
	genePlotControls = {}
) {
	var margin = { top: 30, right: 30, bottom: 50, left: 50 },
		width = 800,
		height = 300;
	const topGenesSelector = genePlotControls.topGenesSelector || "#topGenes";
	const descriptionSelector = genePlotControls.descriptionSelector || "#geneManhattanDesc";


	for (let [key, value] of Object.entries(data)) {
		if (key == 'manhattan.txt') {
			var svg = d3.select(plotContainer).append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", height + margin.top + margin.bottom)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			let chromSize = []
			for (let i = 0; i < 23; i++) { chromSize.push(0) }

			value.forEach(function (d) {
				d['chr'] = +d['chr']; //chr
				d['bp'] = +d['bp']; // bp
				d['p'] = +d['p']; // p
				if (chromSize[d['chr'] - 1] < d['bp']) { chromSize[d['chr'] - 1] = d['bp'] }
			});
			for (let i = 0; i < 23; i++) { chromSize[i] *= 1.1 }

			var chr = d3.set(value.map(function (d) { return d['chr']; })).values();
			var chromStart = [];
			chromStart.push(0);
			for (let i = 1; i < 23; i++) {
				if (chr.indexOf(i.toString()) >= 0) {
					chromStart.push(chromStart[i - 1] + chromSize[i - 1]);
				} else {
					chromStart.push(chromStart[i - 1])
				}
			}
			var x = d3.scaleLinear().range([0, width]);
			x.domain([0, chromSize.reduce(function (a, b) { return a + b; }, 0)]);
			var xAxis = d3.axisBottom(x);
			var y = d3.scaleLinear().range([height, 0]);
			var minP = d3.min(value, function (d) { if (d['p'] > 1e-300) { return d['p'] } })
			var lowP = d3.min(value, function (d) { return d['p'] });
			var yMax = -Math.log10(minP);
			if (lowP < 1e-300) {
				if (yMax >= 300) { yMax = 360; }
				else { yMax += yMax * 0.2; }
				yMax += 10;
			}
			y.domain([0, yMax]);

			var yAxis = d3.axisLeft().scale(y);

			svg.selectAll("dot.manhattan").data(value).enter()
				.append("circle")
				.attr("r", 2)
				.attr("cx", function (d) { return x(d['bp'] + chromStart[d['chr'] - 1]) })
				.attr("cy", function (d) { if (d['p'] < 1e-300) { return y(yMax) } else { return y(-Math.log10(d['p'])) } })
				.attr("fill", function (d) { if (d['chr'] % 2 == 0) { return "steelblue" } else { return "blue" } });


			svg.append("line")
				.attr("x1", 0).attr("x2", width)
				.attr("y1", y(-Math.log10(5e-8))).attr("y2", y(-Math.log10(5e-8)))
				.style("stroke", "red")
				.style("stroke-dasharray", ("3,3"));
			svg.append("g").attr("class", "x axis")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis).selectAll("text").remove();
			svg.append("g").attr("class", "y axis").call(yAxis)
				.selectAll('text')
				.each(function (d) {
					if (d >= -Math.log10(minP) * 1.2) { this.remove() }
				})
				.style('font-size', '11px');
			if (lowP < 1e-300) {
				svg.append("text")
					.attr("x", -32).attr("y", y(yMax) + 2)
					.text(">300")
					.style("font-size", '11px')
					.style("font-family", "sans-serif");
				svg.append("text")
					.attr("x", 0).attr("y", y(yMax) * 1.5)
					.text("\u2248")
					.attr("text-anchor", "middle")
					.style("font-size", '20px')
					.style("font-family", "sans-serif");
			}

			//Chr label
			for (let i = 0; i < chr.length; i++) {
				svg.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + x((chromStart[chr[i] - 1] * 2 + chromSize[chr[i] - 1]) / 2) + "," + (height + 20) + ")")
					.text(chr[i])
					.style("font-size", "10px");
			}
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + width / 2 + "," + (height + 35) + ")")
				.text("Chromosome");
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (-35) + "," + (height / 2) + ")rotate(-90)")
				.text("-log10 P-value");
			svg.selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style("font-family", "sans-serif");
		} else if (key == 'magma.genes.out') {

			if (value == null || value.length == 0) {
				$(genePlotContainer).html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
					+ ' MAGMA was not able to perform.</span><br></div>');
			} else {
				var svg2 = d3.select(genePlotContainer).append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
				let chromSize = [];
				for (let i = 0; i < 23; i++) { chromSize.push(0) }
				value.forEach(function (d) {
					if (d['CHR'] == 'X') { d['CHR'] = 23; }
					d['CHR'] = +d['CHR']; //chr
					d['START'] = +d['START']; //start
					d['STOP'] = +d['STOP']; //stop
					d['P'] = +d['P']; //p
					if (chromSize[d['CHR'] - 1] < d['START']) { chromSize[d['CHR'] - 1] = d['STOP'] }
				});
				for (let i = 0; i < 23; i++) { chromSize[i] *= 1.1 }
				var nSigGenes = 0;
				var sortedP = [];
				sortedP.push(0);
				value.forEach(function (d) {
					if (d['P'] <= 0.05 / value.length) { nSigGenes++; }
					sortedP.push(d['P']);
				});
				$(topGenesSelector).val(nSigGenes);

				$(descriptionSelector).html("Input SNPs were mapped to " + value.length + " protein coding genes. "
					+ "Genome wide significance (red dashed line in the plot) was defined at P = 0.05/" + value.length + " = " + (0.05 / value.length).toExponential(3) + ".");

				sortedP = sortedP.sort(function (a, b) { return a - b; });
				// var chr = d3.set(value.map(function(d){return d.CHR;})).values();
				chr = d3.set(value.map(function (d) { return d['CHR']; })).values();

				chromStart = [];
				chromStart.push(0);
				for (let i = 1; i < 23; i++) {
					if (chr.indexOf(i.toString()) >= 0) {
						chromStart.push(chromStart[i - 1] + chromSize[i - 1]);
					} else {
						chromStart.push(chromStart[i - 1])
					}
				}
				x = d3.scaleLinear().range([0, width]);
				x.domain([0, chromSize.reduce(function (a, b) { return a + b; }, 0)]);
				xAxis = d3.axisBottom(x);
				y = d3.scaleLinear().range([height, 0]);
				// y.domain([0, d3.max(value, function(d){return -Math.log10(d.P);})+1]);
				y.domain([0, d3.max(value, function (d) { return -Math.log10(d['P']); }) + 1]);
				yAxis = d3.axisLeft(y);

				svg2.selectAll("dot.geneManhattan").data(value).enter()
					.append("circle")
					.attr("r", 2)
					.attr("cx", function (d) { return x((d['START'] + d['STOP']) / 2 + chromStart[d['CHR'] - 1]) })
					.attr("cy", function (d) { return y(-Math.log10(d['P'])) })
					.attr("fill", function (d) { if (d['CHR'] % 2 == 0) { return "steelblue" } else { return "blue" } });

				svg2.selectAll('text.gene').data(value.filter(function (d) { if (d['P'] <= 0.05 / value.length) { return d; } })).enter()
					.append("text")
					.attr("class", "gene")
					.attr("x", function (d) { return x((d['START'] + d['STOP']) / 2 + chromStart[d['CHR'] - 1]) })
					.attr("y", function (d) { return y(-Math.log10(d['P'])) - 2 })
					.text(function (d) { return d['SYMBOL'] })
					.style("font-size", "10px");

				svg2.append("line")
					.attr("x1", 0).attr("x2", width)
					.attr("y1", y(-Math.log10(0.05 / value.length))).attr("y2", y(-Math.log10(0.05 / value.length)))
					.style("stroke", "red")
					.style("stroke-dasharray", ("3,3"));
				svg2.append("g").attr("class", "x axis")
					.attr("transform", "translate(0," + height + ")")
					.call(xAxis).selectAll("text").remove();
				svg2.append("g").attr("class", "y axis").call(yAxis)
					.selectAll('text').style('font-size', '11px');

				//Chr label
				for (let i = 0; i < chr.length; i++) {
					svg2.append("text").attr("text-anchor", "middle")
						.attr("transform", "translate(" + x((chromStart[chr[i] - 1] * 2 + chromSize[chr[i] - 1]) / 2) + "," + (height + 20) + ")")
						.text(chr[i])
						.style("font-size", "10px");
				}
				svg2.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + width / 2 + "," + (height + 35) + ")")
					.text("Chromosome");
				svg2.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + (-35) + "," + (height / 2) + ")rotate(-90)")
					.text("-log10 P-value");
				svg2.selectAll('path').style('fill', 'none').style('stroke', 'grey');
				svg2.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
				svg2.selectAll('text').style("font-family", "sans-serif");

				$(topGenesSelector).off("input.remakeGenePlot").on("input.remakeGenePlot", function () {
					svg2.selectAll(".gene").remove();
					var n = $(topGenesSelector).val();
					svg2.selectAll('text.gene').data(value.filter(function (d) { if (d['P'] <= sortedP[n]) { return d; } })).enter()
						.append("text")
						.attr("class", "gene")
						.attr("x", function (d) { return x((d['START'] + d['STOP']) / 2 + chromStart[d['CHR'] - 1]) })
						.attr("y", function (d) { return y(-Math.log10(d['P'])) - 2 })
						.text(function (d) { return d['SYMBOL'] })
						.style("font-size", "10px")
						.style("font-family", "sans-serif");
				})
			}
		}
	}
}

export const ManhattanplotUpload = function (event) {
	const file = event.currentTarget.files[0];
	const message = $("#ManhattanMessage");
	message.removeClass("alert alert-danger alert-success").empty();

	if (!file) {
		return;
	}

	const reader = new FileReader();
	reader.onerror = function () {
		message.addClass("alert alert-danger").text("Unable to read the selected file.");
	};
	reader.onload = function () {
		if (typeof reader.result !== "string") {
			message.addClass("alert alert-danger").text("Unable to read the selected file as text.");
			return;
		}

		const rows = d3.tsvParse(reader.result);
		const requiredColumns = ["chr", "bp", "p"];
		if (rows.length === 0 || !requiredColumns.every(column => rows.columns.includes(column))) {
			message.addClass("alert alert-danger")
				.text("The file must be a tab-delimited Manhattan data file with chr, bp, and p columns.");
			return;
		}

		const selectedData = { "manhattan.txt": rows };
		d3.select("#remakeManhattan").selectAll("*").remove();
		GWplot(selectedData, "#remakeManhattan");
		message.addClass("alert alert-success").text("Manhattan plot created.");
	};
	reader.readAsText(file);
};

export const GeneManhattanplotUpload = function (event) {
	const file = event.currentTarget.files[0];
	const message = $("#GeneManhattanMessage");
	message.removeClass("alert alert-danger alert-success").empty();

	if (!file) {
		return;
	}

	const reader = new FileReader();
	reader.onerror = function () {
		message.addClass("alert alert-danger").text("Unable to read the selected file.");
	};
	reader.onload = function () {
		if (typeof reader.result !== "string") {
			message.addClass("alert alert-danger").text("Unable to read the selected file as text.");
			return;
		}

		const rows = d3.tsvParse(reader.result);
		const requiredColumns = ["CHR", "START", "STOP", "P", "SYMBOL"];
		if (rows.length === 0 || !requiredColumns.every(column => rows.columns.includes(column))) {
			message.addClass("alert alert-danger")
				.text("The file must be a MAGMA gene results file with CHR, START, STOP, P, and SYMBOL columns.");
			return;
		}

		const selectedData = { "magma.genes.out": rows };
		d3.select("#remakeGeneManhattan").selectAll("*").remove();
		GWplot(selectedData, "#remakeManhattan", "#remakeGeneManhattan", {
			topGenesSelector: "#remakeTopGenes",
			descriptionSelector: "#remakeGeneManhattanDesc"
		});
		message.addClass("alert alert-success").text("Gene-based Manhattan plot created.");
	};
	reader.readAsText(file);
};

function drawQQplot(data) {
	var margin = { top: 30, right: 30, bottom: 50, left: 50 },
		width = 300,
		height = 300;

	var qqSNP = d3.select("#remakeQQ").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var qqGene = d3.select("#remakeGeneQQplot").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");




	for (let [key, value] of Object.entries(data)) {
		if (key == 'QQSNPs.txt') {
			value.forEach(function (d) {
				d.obs = +d['obs'];
				d.exp = +d['exp'];
			});

			const x = d3.scaleLinear().range([0, width]);
			const y = d3.scaleLinear().range([height, 0]);
			const xMax = d3.max(value, function (d) { return d.exp; });
			const minP = d3.max(value, function (d) { if (d.obs < 300) { return d.obs } });
			const lowP = d3.max(value, function (d) { return d.obs; });
			let yMax = Number.isFinite(minP) ? minP : 300;
			if (lowP > 300) {
				if (yMax >= 300) { yMax = 360; }
				else { yMax = yMax * 1.2 + 10; }
			}
			x.domain([0, (xMax + xMax * 0.01)]);
			y.domain([0, (yMax + yMax * 0.01)]);
			const yAxis = d3.axisLeft(y);
			const xAxis = d3.axisBottom(x);

			// var maxP = Math.min(d3.max(data, function(d){return d.exp;}), d3.max(data, function(d){return d.obs;}));
			const maxP = Math.min(xMax, yMax);

			qqSNP.selectAll("dot.QQ").data(value).enter()
				.append("circle")
				.attr("r", 2)
				.attr("cx", function (d) { return x(d.exp) })
				.attr("cy", function (d) { return d.obs > 300 ? y(yMax) : y(d.obs); })
				.attr("fill", "grey");
			qqSNP.append("g").attr("class", "x axis")
				.attr("transform", "translate(0," + height + ")").call(xAxis)
				.selectAll('text').style('font-size', '11px');
			qqSNP.append("g").attr("class", "y axis").call(yAxis)
				.selectAll('text')
				.each(function (d) {
					if (d >= minP * 1.2) { this.remove() }
				})
				.style('font-size', '11px');
			if (lowP > 300) {
				qqSNP.append("text")
					.attr("x", -32).attr("y", y(yMax) + 2)
					.text(">300")
					.style("font-size", '11px')
					.style("font-family", "sans-serif");
				qqSNP.append("text")
					.attr("x", 0).attr("y", y(yMax) * 5)
					.text("\u2248")
					.attr("text-anchor", "middle")
					.style("font-size", '20px')
					.style("font-family", "sans-serif");
			}
			qqSNP.append("line")
				.attr("x1", 0).attr("x2", x(maxP))
				.attr("y1", y(0)).attr("y2", y(maxP))
				.style("stroke", "red")
				.style("stroke-dasharray", ("3,3"));
			qqSNP.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (-35) + "," + height / 2 + ")rotate(-90)")
				.text("Observed -log10 P-value");
			qqSNP.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (width / 2) + "," + (height + 35) + ")")
				.text("Expected -log10 P-value");
			qqSNP.selectAll('path').style('fill', 'none').style('stroke', 'grey');
			qqSNP.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			qqSNP.selectAll('text').style("font-family", "sans-serif");
		} else if (key == 'magma.genes.out') {
			if (value == null || value.length == 0) {
				$("#geneQQplot").html('<div style="text-align:center; padding-top:24px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
					+ ' MAGMA was not able to perform.</span><br></div>');
			} else {

				let obs = [];
				let c = 0;
				for (let i = 0; i < value.length; i++) {
					c++;
					obs.push(-Math.log10(value[i]["P"]));
				}
				obs.sort(function (a, b) { return a - b; });
				let step = (1 - 1 / c) / c;
				var all_row = [];
				for (let i = 0; i < c; i++) {
					all_row.push({
						obs: obs[i],
						exp: -Math.log10(1 - i * step),
						n: i + 1
					});
				}
				all_row.forEach(function (d) {
					d.obs = +d.obs;
					d.exp = +d.exp;
					d.n = +d.n;
				});

				const x = d3.scaleLinear().range([0, width]);
				const y = d3.scaleLinear().range([height, 0]);
				const xMax = d3.max(all_row, function (d) { return d.exp; });
				const yMax = d3.max(all_row, function (d) { return d.obs; });
				x.domain([0, (xMax + xMax * 0.01)]);
				y.domain([0, (yMax + yMax * 0.01)]);
				const yAxis = d3.axisLeft(y);
				const xAxis = d3.axisBottom(x);

				// var maxP = Math.min(d3.max(all_row, function(d){return d.exp;}), d3.max(all_row, function(d){return d.obs;}));
				const maxP = Math.min(xMax, yMax);

				qqGene.selectAll("dot.geneQQ").data(all_row).enter()
					.append("circle")
					.attr("r", 2)
					.attr("cx", function (d) { return x(d.exp) })
					.attr("cy", function (d) { return y(d.obs) })
					.attr("fill", "grey");
				qqGene.append("g").attr("class", "x axis")
					.attr("transform", "translate(0," + height + ")").call(xAxis)
					.selectAll('text').style('font-size', '11px');
				qqGene.append("g").attr("class", "y axis").call(yAxis)
					.selectAll('text').style('font-size', '11px');
				qqGene.append("line")
					.attr("x1", 0).attr("x2", x(maxP))
					.attr("y1", y(0)).attr("y2", y(maxP))
					.style("stroke", "red")
					.style("stroke-dasharray", ("3,3"));
				qqGene.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + (-35) + "," + height / 2 + ")rotate(-90)")
					.text("Observed -log10 P-value");
				qqGene.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + (width / 2) + "," + (height + 35) + ")")
					.text("Expected -log10 P-value");
				qqGene.selectAll('path').style('fill', 'none').style('stroke', 'grey');
				qqGene.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
				qqGene.selectAll("text").style("font-family", "sans-serif");
			}
		}
	}
}

export function QQplotUpload(event) {
	const file = event.currentTarget.files[0];
	const message = $("#QQMessage");
	message.removeClass("alert alert-danger alert-success").empty();

	if (!file) {
		return;
	}

	const reader = new FileReader();
	reader.onerror = function () {
		message.addClass("alert alert-danger").text("Unable to read the selected file.");
	};
	reader.onload = function () {
		if (typeof reader.result !== "string") {
			message.addClass("alert alert-danger").text("Unable to read the selected file as text.");
			return;
		}

		const rows = d3.tsvParse(reader.result);
		const requiredColumns = ["obs", "exp"];
		if (rows.length === 0 || !requiredColumns.every(column => rows.columns.includes(column))) {
			message.addClass("alert alert-danger")
				.text("The file must be a tab-delimited QQSNPs file with obs and exp columns.");
			return;
		}

		const validRows = rows.filter(row =>
			Number.isFinite(Number(row.obs)) && Number.isFinite(Number(row.exp))
		);
		if (validRows.length === 0) {
			message.addClass("alert alert-danger").text("The file contains no valid QQ plot data.");
			return;
		}

		d3.select("#remakeQQ").selectAll("*").remove();
		drawQQplot({ "QQSNPs.txt": validRows });
		message.addClass("alert alert-success").text("QQ plot created.");
	};
	reader.readAsText(file);
}


export default GWplot;