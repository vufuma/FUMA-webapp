import { tip as d3Tip } from "d3-v6-tip";
import { S2GPageState as pageState}  from "../pages/pageStateComponents.js";

export const GWplot = function (data) {
	var margin = { top: 30, right: 30, bottom: 50, left: 50 },
		width = 800,
		height = 300;

	var svg = d3.select("#manhattan").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var svg2 = d3.select("#geneManhattan").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	for (let [key, value] of Object.entries(data)) {
		if (key == 'manhattan.txt') {

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
				$("#geneManhattan").html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
					+ ' MAGMA was not able to perform.</span><br></div>');
			} else {
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
				$('#topGenes').val(nSigGenes);

				$('#geneManhattanDesc').html("Input SNPs were mapped to " + value.length + " protein coding genes. "
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

				$('#topGenes').on("input", function () {
					svg2.selectAll(".gene").remove();
					var n = $('#topGenes').val();
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

export function QQplot(data) {
	var margin = { top: 30, right: 30, bottom: 50, left: 50 },
		width = 300,
		height = 300;

	var qqSNP = d3.select("#QQplot").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var qqGene = d3.select("#geneQQplot").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");




	for (let [key, value] of Object.entries(data)) {
		if (key == 'QQSNPs.txt') {
			value.forEach(function (d) {
				d.obs = +d['obs'];
				d.exp = +d['exp'];
			});

			var x = d3.scaleLinear().range([0, width]);
			var y = d3.scaleLinear().range([height, 0]);
			var xMax = d3.max(value, function (d) { return d.exp; });
			var minP = d3.max(value, function (d) { if (d.obs < 300) { return d.obs } })
			var lowP = d3.max(value, function (d) { return d.obs });
			var yMax = minP;
			if (lowP > 300) {
				if (yMax >= 300) { yMaxp = 360; }
				else { yMax += yMax * 0.2; }
			}
			x.domain([0, (xMax + xMax * 0.01)]);
			y.domain([0, (yMax + yMax * 0.01)]);
			var yAxis = d3.axisLeft(y);
			var xAxis = d3.axisBottom(x);

			// var maxP = Math.min(d3.max(data, function(d){return d.exp;}), d3.max(data, function(d){return d.obs;}));
			var maxP = Math.min(xMax, yMax);

			qqSNP.selectAll("dot.QQ").data(value).enter()
				.append("circle")
				.attr("r", 2)
				.attr("cx", function (d) { return x(d.exp) })
				.attr("cy", function (d) { if (d.obs > 300) { y(yMax) } else { return y(d.obs) } })
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

				var x = d3.scaleLinear().range([0, width]);
				var y = d3.scaleLinear().range([height, 0]);
				var xMax = d3.max(all_row, function (d) { return d.exp; });
				var yMax = d3.max(all_row, function (d) { return d.obs; });
				x.domain([0, (xMax + xMax * 0.01)]);
				y.domain([0, (yMax + yMax * 0.01)]);
				var yAxis = d3.axisLeft(y);
				var xAxis = d3.axisBottom(x);

				// var maxP = Math.min(d3.max(all_row, function(d){return d.exp;}), d3.max(all_row, function(d){return d.obs;}));
				var maxP = Math.min(xMax, yMax);

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

export function MAGMA_GStable(data) {
	$('#MAGMAtable').DataTable({
		"data": data['magma.sets.top'],
		columns: [
			{ data: 'FULL_NAME' },
			{ data: 'NGENES' },
			{ data: 'BETA' },
			{ data: 'BETA_STD' },
			{ data: 'SE' },
			{ data: 'P' },
			{ data: 'Pbon' },
		],
		"order": [[6, 'asc']],
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
}

export function MAGMA_expPlot(data) {
	var data_title = {
		'gtex_v8_ts_avg_log2TPM': 'GTEx v8 53 tissue types',
		'gtex_v8_ts_general_avg_log2TPM': 'GTEx v8 30 general tissue types',
		'gtex_v7_ts_avg_log2TPM': 'GTEx v7 53 tissue types',
		'gtex_v7_ts_general_avg_log2TPM': 'GTEx v7 30 general tissue types',
		'gtex_v6_ts_avg_log2RPKM': 'GTEx v6 53 tissue types',
		'gtex_v6_ts_general_avg_log2RPKM': 'GTEx v6 30 general tissue types',
		'bs_age_avg_log2RPKM': "BrainSpan 29 different ages of brain samples",
		"bs_dev_avg_log2RPKM": "BrainSpan 11 general developmental stages of brain samples"
	}

	if (data == null || data == undefined || data.lenght == 0) {
		$('#magmaPlot').html('<div style="text-align:center; padding-top:50px; padding-bottom:50px;"><span style="color: red; font-size: 22px;"><i class="fa fa-ban"></i>'
			+ ' There was an error, MAGMA was not able to perform.</span><br></div>');
	} else {
		data.forEach(function (d) {
			d['p'] = +d['p']; //P-value
			d['ascending_P_idx'] = +d['ascending_P_idx']; //P order
			d['ascending_var_or_covar_idx'] = +d['ascending_var_or_covar_idx']; //alph order
		})

		var bars = [];
		var xLabels = [];
		var dataset = d3.set(data.map(function (d) { return d['f_name'] })).values();
		var cellwidth = 15;
		var margin = { top: 30, right: 30, bottom: 100, left: 80 },
			height = 250;
		dataset.forEach(function (ds) {
			$('#magmaPlot').append('<div id="' + ds + 'Panel"><h4>' + data_title[ds] + '</h4></div>')

			// img download buttons
			$('#' + ds + 'Panel').append('<div id="' + ds + 'Plot">Download the plot as '
				+ '<button class="btn btn-default btn-sm ImgDown" onclick=' + "'" + 'expImgDown("' + ds + '","png");' + "'" + '>PNG</button> '
				+ '<button class="btn btn-default btn-sm ImgDown" onclick=' + "'" + 'expImgDown("' + ds + '","jpeg");' + "'" + '>JPG</button> '
				+ '<button class="btn btn-default btn-sm ImgDown" onclick=' + "'" + 'expImgDown("' + ds + '","svg");' + "'" + '>SVG</button> '
				+ '<button class="btn btn-default btn-sm ImgDown" onclick=' + "'" + 'expImgDown("' + ds + '","pdf");' + "'" + '>PDF</button></div>'
			);

			// plot
			$('#' + ds + 'Panel').append('<div id="' + ds + '"></div>')
			var tdata = [];
			var maxLabel = 100;
			data.forEach(function (d) {
				if (d['f_name'] == ds) {
					tdata.push(d)
					if (d['var_name'].length * 5.5 > maxLabel) { maxLabel = d['var_name'].length * 5.5 }
				}
			});
			margin.bottom = maxLabel;
			var width = cellwidth * tdata.length;
			var svg = d3.select("#" + ds).append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", height + margin.top + margin.bottom)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			var x = d3.scaleBand().range([0, width]);
			var xAxis = d3.axisBottom(x);
			x.domain(tdata.map(function (d) { return d['var_name']; }));
			var y = d3.scaleLinear().range([height, 0]);
			var yAxis = d3.axisLeft(y);
			y.domain([0, d3.max(tdata, function (d) { return -Math.log10(d['p']); })]);

			var Pbon = 0.05 / tdata.length;

			var bar = svg.selectAll("rect.expgeneral").data(tdata).enter()
				.append("rect")
				.attr("x", function (d) { return d['ascending_P_idx'] * cellwidth; })
				.attr("y", function (d) { return y(-Math.log10(d['p'])); })
				.attr("width", cellwidth - 1)
				.attr("height", function (d) { return height - y(-Math.log10(d['p'])); })
				.style("fill", function (d) {
					if (d['p'] < Pbon) { return "#c00"; }
					else { return "#5668f4"; }
				})
				.style("stroke", "grey");
			bars.push(bar);
			var xLabel = svg.append("g").selectAll(".xLabel")
				.data(tdata).enter().append("text")
				.text(function (d) { return d['var_name']; })
				.style("text-anchor", "end")
				.style("font-size", "11px")
				.attr("transform", function (d) {
					return "translate(" + (d['ascending_P_idx'] * cellwidth + ((cellwidth - 1) / 2) + 3) + "," + (height + 8) + ")rotate(-70)";
				});
			xLabels.push(xLabel);
			svg.append("line")
				.attr("x1", 0).attr("x2", width)
				.attr("y1", y(-Math.log10(Pbon))).attr("y2", y(-Math.log10(Pbon)))
				.style("stroke", "black")
				.style("stroke-dasharray", ("3,3"));

			svg.append('g').attr("class", "y axis")
				.call(yAxis)
				.selectAll('text').style('font-size', '11px').style('font-family', 'sans-serif');
			svg.append('g').attr("class", "x axis")
				.attr("transform", "translate(0," + (height) + ")")
				.call(xAxis).selectAll('text').remove();
			svg.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (-margin.left / 2 - 15) + "," + height / 2 + ")rotate(-90)")
				.text("-log 10 P-value");
			svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
			svg.selectAll('text').style('font-family', 'sans-serif');

		});

		function sortOptions(type) {
			if (type == "alph") {
				for (let i = 0; i < bars.length; i++) {
					bars[i].transition().duration(1000)
						.attr("x", function (d) { return d['ascending_var_or_covar_idx'] * cellwidth; });
					xLabels[i].transition().duration(1000)
						.attr("transform", function (d) {
							return "translate(" + (d['ascending_var_or_covar_idx'] * cellwidth + ((cellwidth - 1) / 2) + 3) + "," + (height + 8) + ")rotate(-70)";
						});
				}
			} else if (type == "p") {
				for (let i = 0; i < bars.length; i++) {
					bars[i].transition().duration(1000)
						.attr("x", function (d) { return d['ascending_P_idx'] * cellwidth; });
					xLabels[i].transition().duration(1000)
						.attr("transform", function (d) {
							return "translate(" + (d['ascending_P_idx'] * cellwidth + ((cellwidth - 1) / 2) + 3) + "," + (height + 8) + ")rotate(-70)";
						});
				}
			}
		}

		d3.select('#magma_exp_order').on("change", function () {
			sortOptions($('#magma_exp_order').val());
		});
	}
}

export function expImgDown(gs, type) {
	let id = pageState.get('id');
	$('#expData').val($('#' + gs).html());
	$('#expType').val(type);
	$('#expJobID').val(id);
	$('#expDir').val('jobs');
	$('#expFileName').val("magma_exp_" + gs);
	$('#expSubmit').trigger('click');
}

export function ImgDown(name, type) {
	let id = pageState.get('id');
	$('#' + name + 'Data').val($('#' + name).html());
	$('#' + name + 'Type').val(type);
	$('#' + name + 'Dir').val('jobs')
	$('#' + name + 'ID').val(id);
	$('#' + name + 'FileName').val(name);
	$('#' + name + 'Submit').trigger('click');
}

export function circosDown(type) {
	let id = pageState.get('id');
	$('#circosPlotID').val(id);
	//$('#circosPlotDir').val(prefix);
	$('#circosPlotType').val(type);
	$('#circosPlotSubmit').trigger('click');
}

export function ciMapCircosPlot(data) {
	data = data[0];

	// let sortedKeys = Object.keys(data).sort();
	// let sortedJson = {};
	// for (let key of sortedKeys) {
	// 	sortedJson[key] = data[key];
	// }
	// data = sortedJson;

	var images = '<div class="row">';

	for (var key in data) {
		images += '<div class="col-md-4 col-xs-4 col-sm-4">'
			+ 'Chromosome ' + key + '<br>'
			// + '<a target="_blank" href="' + 'data:image/png;base64,' + data[key] + '">' // TODO: do something to click and open in new tab
			+ '<img width="80%" src="' + 'data:image/png;base64,' + data[key] + '"></img>'
			+ '</a><br><br>'
			+ '</div>';
	}
	images += '</div>';
	$('#ciMapCircosPlot').html(images);
}

export function showResultTables(subdir, page, prefix, id, posMap, eqtlMap, ciMap, orcol, becol, secol) {
	$('#plotClear').hide();
	$('#download').attr('disabled', false);
	if (eqtlMap == 0) {
		$('#eqtlTableTab').hide();
		$('#check_eqtl_annotPlot').hide();
		$('#annotPlot_eqtl').prop('checked', false);
		$('#eqtlfiledown').hide();
		$('#eqtlfile').prop('checked', false);
	}

	if (ciMap == 0) {
		$('#ciTableTab').hide();
		$('#check_ci_annotPlot').hide();
		$('#annotPlot_ci').prop('checked', false);
		$('#cifiledown').hide();
		$('#cifile').prop('checked', false);
	}

	$.ajax({
		url: subdir + '/' + page + '/paramTable',
		type: "POST",
		data: {
			prefix: prefix,
			jobID: id
		},
		error: function () {
			alert("param table error");
		},
		success: function (data) {
			data = JSON.parse(data);
			var table = '<table class="table table-condensed table-bordered table-sm" style="width: 90%; text-align: right;"><tbody>'
			data.forEach(function (d) {
				if (d[0] != "created_at") {
					d[1] = d[1].replace(/:/g, ', ');
				}
				table += '<tr><td>' + d[0] + '</td><td>' + d[1] + '</td></tr>'
			})
			table += '</tbody></table>'
			$('#paramTable').html(table);
		}
	});

	$.ajax({
		url: subdir + '/' + page + '/sumTable',
		type: "POST",
		data: {
			prefix: prefix,
			jobID: id
		},
		success: function (data) {
			$('#sumTable').append(data);
		},
		complete: function () {
			$.ajax({
				url: subdir + '/' + page + '/getFilesContents',
				type: 'POST',
				data: {
					jobID: id,
					fileNames: ['annov.stats.txt', 'interval_sum.txt']
				},
				error: function () {
					alert("JobQuery get file contents error");
					return;
				},
				success: function (data) {
					PlotSNPAnnot(data['annov.stats.txt']);
					PlotLocuSum(data['interval_sum.txt']);
				}
			});
		}
	});

	var file = "GenomicRiskLoci.txt";
	var lociTable = $('#lociTable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax": {
			url: "DTfile",
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: "GenomicLocus:uniqID:rsID:chr:pos:p:start:end:nSNPs:nGWASSNPs:nIndSigSNPs:IndSigSNPs:nLeadSNPs:LeadSNPs"
			}
		},
		error: function () {
			alert("GenomicRiskLoci table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "leadSNPs.txt";
	var leadTable = $('#leadSNPtable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax": {
			url: "DTfile",
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nIndSigSNPs:IndSigSNPs"
			}
		},
		error: function () {
			alert("sigSNPs table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10,
	});

	file = "IndSigSNPs.txt";
	var IndSigTable = $('#sigSNPtable').DataTable({
		"processing": true,
		serverSide: false,
		select: true,
		"ajax": {
			url: "DTfile",
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: "No:GenomicLocus:uniqID:rsID:chr:pos:p:nSNPs:nGWASSNPs"
			}
		},
		error: function () {
			alert("sigSNPs table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10,
	});

	var table = "<thead>"
		+ "<tr>"
		+ "<th>uniqID</th><th>rsID</th><th>chr</th><th>pos</th><th>non_effect_allele</th><th>effect_allele</th><th>MAF</th><th>gwasP</th>";
	var cols = "uniqID:rsID:chr:pos:non_effect_allele:effect_allele:MAF:gwasP";
	var cadd_col = 14;
	if (orcol != "NA") {
		table += "<th>OR</th>";
		cols += ":or";
		cadd_col += 1;
	}
	if (becol != "NA") {
		table += "<th>Beta</th>";
		cols += ":beta";
		cadd_col += 1;
	}
	if (secol != "NA") {
		table += "<th>SE</th>";
		cols += ":se";
		cadd_col += 1;
	}
	table += "<th>Genomic Locus</th><th>r2</th><th>IndSigSNP</th><th>Nearest gene</th><th>dist</th><th>position</th><th>CADD</th><th>RDB</th><th>minChrState(127)</th><th>commonChrState(127)</th>"
		+ "</tr>"
		+ "</thead>";
	cols += ":GenomicLocus:r2:IndSigSNP:nearestGene:dist:func:CADD:RDB:minChrState:commonChrState";
	file = "snps.txt";
	$('#SNPtable').html(table)
	var SNPtable = $('#SNPtable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax: {
			url: 'DTfile',
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: cols
			}
		},
		error: function () {
			alert("SNP table error");
		},
		"columnDefs": [
			{ type: "scientific", targets: 7 },
			{ type: "num", targets: cadd_col }
		],
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "annov.txt";
	var annovTable = $('#annovTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax: {
			url: 'DTfile',
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: "uniqID:chr:pos:gene:symbol:dist:annot:exonic_func:exon"
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	file = "genes.txt";
	table = "<thead><tr><th>Gene</th><th>Symbol</th><th>HUGO</th><th>entrezID</th><th>chr</th><th>start</th><th>end</th>";
	table += "<th>strand</th><th>type</th><th>pLI</th><th>ncRVIS</th>";
	var col = "ensg:symbol:HUGO:entrezID:chr:start:end:strand:type:pLI:ncRVIS";
	if (posMap == 1) {
		table += "<th>posMapSNPs</th><th>posMapMaxCADD</th>";
		col += ":posMapSNPs:posMapMaxCADD";
	}
	if (eqtlMap == 1) {
		table += "<th>eqtlMapSNPs</th><th>eqtlMapminP</th><th>eqtlMapminQ</th><th>eqtlMapts</th><th>eqtlDirection</th>";
		col += ":eqtlMapSNPs:eqtlMapminP:eqtlMapminQ:eqtlMapts:eqtlDirection";
	}
	if (ciMap == 1) {
		table += "<th>ciMap</th><th>ciMapts</th>";
		col += ":ciMap:ciMapts";
	}
	table += "<th>minGwasP</th><th>Genomic Locus</th><th>IndSigSNPs</th></tr></thead>";
	col += ":minGwasP:GenomicLocus:IndSigSNPs"
	$('#geneTable').append(table);
	$('#geneTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax: {
			url: 'DTfile',
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: col
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	if (eqtlMap == 1) {
		file = "eqtl.txt";
		var eqtlTable = $('#eqtlTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax: {
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					jobID: id,
					prefix: prefix,
					infile: file,
					header: "uniqID:chr:pos:testedAllele:db:tissue:gene:symbol:p:FDR:signed_stats:RiskIncAllele:alignedDirection"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});
	}

	if (ciMap == 1) {
		file = "ci.txt";
		var ciTable = $('#ciTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax: {
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					jobID: id,
					prefix: prefix,
					infile: file,
					header: "GenomicLocus:region1:region2:FDR:type:DB:tissue/cell:inter/intra:SNPs:genes"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});

		file = "ciSNPs.txt";
		var ciSNPsTable = $('#ciSNPsTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax: {
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					jobID: id,
					prefix: prefix,
					infile: file,
					header: "uniqID:rsID:chr:pos:reg_region:type:tissue/cell"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});

		file = "ciProm.txt";
		var ciGenesTable = $('#ciGenesTable').DataTable({
			processing: true,
			serverSide: true,
			searchDelay: 3000,
			select: false,
			ajax: {
				url: 'DTfileServerSide',
				type: "POST",
				data: {
					jobID: id,
					prefix: prefix,
					infile: file,
					header: "region2:reg_region:type:tissue/cell:genes"
				}
			},
			"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"iDisplayLength": 10
		});
	}

	file = "gwascatalog.txt";
	var gwascatTable = $('#gwascatTable').DataTable({
		processing: true,
		serverSide: false,
		select: false,
		ajax: {
			url: 'DTfile',
			type: "POST",
			data: {
				jobID: id,
				prefix: prefix,
				infile: file,
				header: "GenomicLocus:IndSigSNP:chr:bp:snp:PMID:Trait:FirstAuth:Date:P"
			}
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});

	$('#sigSNPtable tbody').on('click', 'tr', function () {
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('IndSigSNP');
		var rowI = IndSigTable.row(this).index();
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = IndSigTable.row(rowI).data();
		var chr = rowData[4];

		$.ajax({
			url: subdir + '/' + page + '/locusPlot',
			type: "POST",
			data: {
				type: "IndSigSNP",
				jobID: id,
				prefix: prefix,
				rowI: rowI
			},
			success: function (data) {
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "IndSigSNP", chr, orcol, becol, secol);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected Ind. Sig. SNP</h5><table class='table table-striped table-sm'><tr><td>Ind. Sig. SNP</td><td>" + rowData[3]
			+ "</td></tr><tr><td>Chrom</td><td>" + rowData[4] + "</td></tr><tr><td>BP</td><td>"
			+ rowData[5] + "</td></tr><tr><td>P-value</td><td>" + rowData[6] + "</td></tr><tr><td>SNPs within LD</td><td>"
			+ rowData[7] + "</td></tr><tr><td>GWAS SNPs within LD</td><td>" + rowData[8] + "</td></tr>";
		$('#selectedLeadSNP').html(out);
	});

	$('#leadSNPtable tbody').on('click', 'tr', function () {
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('leadSNP');
		var rowI = leadTable.row(this).index();
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = leadTable.row(rowI).data();
		var chr = rowData[4];

		$.ajax({
			url: subdir + '/' + page + '/locusPlot',
			type: "POST",
			data: {
				type: "leadSNP",
				jobID: id,
				prefix: prefix,
				rowI: rowI
			},
			success: function (data) {
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "leadSNP", chr, orcol, becol, secol);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected lead SNP</h5><table class='table table-striped table-sm'><tr><td>Lead SNP</td><td>" + rowData[3]
			+ "</td></tr><tr><td>Chrom</td><td>" + rowData[4] + "</td></tr><tr><td>BP</td><td>"
			+ rowData[5] + "</td></tr><tr><td>P-value</td><td>" + rowData[6] + "</td></tr>"
			+ "<tr><td>#Ind. Sig. SNPs</td><td>" + rowData[7] + "</td></tr>";
		$('#selectedLeadSNP').html(out);
	});

	$('#lociTable tbody').on('click', 'tr', function () {
		$('#plotClear').show();
		$('#annotPlotPanel').show();
		$('#annotPlotSelect').val('GenomicLocus');
		var rowI = lociTable.row(this).index();
		$('#annotPlotRow').val(rowI);
		Chr15Select();
		d3.select('#locusPlot').select("svg").remove();
		var rowData = lociTable.row(rowI).data();
		var chr = rowData[3];

		$.ajax({
			url: subdir + '/' + page + '/locusPlot',
			type: "POST",
			data: {
				type: "loci",
				jobID: id,
				prefix: prefix,
				rowI: rowI
			},
			success: function (data) {
				var plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				locusPlot(plotData, "loci", chr, orcol, becol, secol);
			}
		});

		$('#selectedLeadSNP').html("");
		var out = "<h5>Selected Locus</h5><table class='table table-striped table-sm'><tr><td>top lead SNP</td><td>" + rowData[2]
			+ "</td></tr><tr><td>Chrom</td><td>" + rowData[3] + "</td></tr><tr><td>BP</td><td>"
			+ rowData[4] + "</td></tr><tr><td>P-value</td><td>" + rowData[5] + "</td></tr>"
			+ "<tr><td>#Ind. Sig. SNPs</td><td>" + rowData[10] + "</td></tr><tr><td>#lead SNPs</td><td>" + rowData[12]
			+ "</td></tr><tr><td>SNPs within LD</td><td>"
			+ rowData[8] + "</td></tr><tr><td>GWAS SNPs within LD</td><td>" + rowData[9] + "</td></tr>";

		$('#selectedLeadSNP').html(out);
	});
}

export function locusPlot(data, type, chr, orcol, becol, secol) {
	// create plot space
	var colorScale = d3.scaleLinear().domain([0.0, 0.5, 1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
	var margin = { top: 50, right: 50, bottom: 60, left: 50 },
		width = 700 - margin.right - margin.left,
		height = 300 - margin.top - margin.bottom;
	// set range
	var x = d3.scaleLinear().domain([0,10]).range([0, width]);
	var y = d3.scaleLinear().domain([0,10]).range([height, 0]);

	// Variable svg actually points to first (g) element: <svg><g>
	var svg = d3.select("#locusPlot").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var legData = [];
	for (let i = 10; i > 0; i--) {
		legData.push(i * 0.1);
	}
	// legend
	var legendGwas = svg.selectAll(".legendGWAS")
		.data(legData)
		.enter()
		.append("g").attr("class", "legend")
	legendGwas.append("rect")
		.attr("x", width + 10)
		.attr("y", function (d) { return 10 + (10 - d * 10) * 10 })
		.attr("width", 20)
		.attr("height", 10)
		.style("fill", function (d) { return colorScale(d) });
	legendGwas.append("text")
		.attr("text-anchor", "start")
		.attr("x", width + 32)
		.attr("y", function (d) { return 20 + (10 - d * 10) * 10 })
		.text(function (d) { return Math.round(d * 100) / 100 })
		.style("font-size", "10px");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (width + 20) + ",5)")
		.text("r2").style("font-size", "10px");

	svg.append("circle")
		.attr("cx", 145).attr("cy", height + 45).attr("r", 4.5)
		.style("fill", "#4d0099").style("stroke", "black").style("strole-width", "2");
	svg.append("text").attr("text-anchor", "top")
		.attr("x", 150).attr("y", height + 50)
		.text("Top lead SNP");
	svg.append("circle")
		.attr("cx", 250).attr("cy", height + 45).attr("r", 4)
		.style("fill", "#9933ff").style("stroke", "black").style("strole-width", "2");
	svg.append("text").attr("text-anchor", "top")
		.attr("x", 255).attr("y", height + 50)
		.text("Lead SNPs");
	svg.append("circle")
		.attr("cx", 340).attr("cy", height + 45).attr("r", 3.5)
		.style("fill", "red").style("stroke", "black").style("strole-width", "2");
	svg.append("text").attr("text-anchor", "top")
		.attr("x", 345).attr("y", height + 50)
		.text("Independent significant SNPs");

	data.snps.forEach(function (d) {
		d.pos = +d.pos;
		d.gwasP = +d.gwasP;
		d.r2 = +d.r2;
		d.ld = +d.ld;
	});

	data.allsnps.forEach(function (d) {
		d[0] = +d[0]; //pos
		d[1] = +d[1]; //P
	});

	var side = (d3.max(data.allsnps, function (d) { return d[0] }) - d3.min(data.allsnps, function (d) { return d[0] })) * 0.05;
	x.domain([d3.min(data.allsnps, function (d) { return d[0] }) - side, d3.max(data.allsnps, function (d) { return d[0] }) + side]);
	y.domain([0, Math.max(d3.max(data.snps, function (d) { return -Math.log10(d.gwasP) }), d3.max(data.allsnps, function (d) { return -Math.log10(d[1]) }))]);
	var xAxis = d3.axisBottom(x).ticks(5);
	var yAxis = d3.axisLeft(y);

	// Define the tool tip formatting for the SNPs
	var tip = d3Tip().attr("class", "d3-tip")
		.offset([-10, 0])
		.html(function (d) {
			var out = "<table><tr><td>rsID: </td><td>" + d.rsID + "</td></tr>"
			    + "<tr><td>BP: </td><td>" + d.pos + "</td></tr>"
			    + "<tr><td>P: </td><td>" + d.gwasP + "</td></tr>"
				+ "<tr><td>MAF: </td><td>" + d.MAF + "</td></tr>"
				+ "<tr><td>r2: </td><td>" + d.r2 + "</td></tr>"
				+ "<tr><td>Ind. Sig. SNP:&nbsp;</td><td>" + d.IndSigSNP + "</td></tr>";
			if (orcol != "NA") { out += "<tr><td>OR: </td><td>" + d.or + "</td></tr>"; }
			if (becol != "NA") { out += "<tr><td>Beta: </td><td>" + d.beta + "</td></tr>"; }
			if (secol != "NA") { out += "<tr><td>SE: </td><td>" + d.se + "</td></tr>"; }
			out += "</table>"
			return out;
		});
	// activate the tool tips
	svg.call(tip);

	// Create zoom behavior and restrict zoom extent to 10x
	// T.B.D. restrict translation 
	var zoom = d3.zoom()
		.scaleExtent([1,10])
		.on("zoom", zoomed);

	svg.call(zoom);

	// add rect
	svg.append("rect").attr("width", width).attr("height", height)
		.style("fill", "transparent")
		.style("shape-rendering", "crispEdges");

	// dot plot for gwas tagged SNPs
	// SNPs not in LD
	svg.selectAll("dot").data(data.allsnps).enter()
		.append("circle")
		.attr("class", "nonLD")
		.attr("r", 3).attr("cx", function (d) { return x(d[0]); })
		.attr("cy", function (d) { return y(-Math.log10(d[1])); })
		.style('fill', "grey");
	// SNPs in LD
	svg.selectAll("dot").data(data.snps.filter(function (d) { if (d.gwasP != -1 && d.ld == 1) { return d; } })).enter()
		.append("circle")
		.attr("class", "dot")
		.attr("r", 3).attr("cx", function (d) { return x(d.pos); })
		.attr("cy", function (d) { return y(-Math.log10(d.gwasP)); })
		.style('fill', function (d) { return colorScale(d.r2); })
		.on("mouseover", tip.show)
		.on("mouseout", tip.hide);
	// add rect for 1KG SNPs
	svg.selectAll("rect.KGSNPs").data(data.snps.filter(function (d) { if (d.gwasP == -1) { return d; } })).enter()
		.append("rect")
		.attr("class", "KGSNPs")
		.attr("x", function (d) { return x(d.pos) })
		.attr("y", -20)
		.attr("height", "10")
		.attr("width", "3")
		.style('fill', function (d) { if (d.ld == 0) { return "grey"; } else { return colorScale(d.r2); } })
		.on("mouseover", tip.show)
		.on("mouseout", tip.hide);

	svg.selectAll("dot.leadSNPs").data(data.snps.filter(function (d) { if (d.ld > 1) { return d; } })).enter()
		.append("circle")
		.attr("class", "leadSNPs")
		.attr("cx", function (d) { return x(d.pos) })
		.attr("cy", function (d) { return y(-Math.log10(d.gwasP)); })
		.attr("r", function (d) {
			if (d.ld == 2) { return 3.5; }
			else if (d.ld == 3) { return 4; }
			else if (d.ld == 4) { return 4.5; }
		})
		.style("fill", function (d) {
			if (d.ld == 2) { return colorScale(d.r2); }
			else if (d.ld == 3) { return "#9933ff" }
			else if (d.ld == 4) { return "#4d0099" }
		})
		.style("stroke", "black").style("stroke-width", "2")
		.on("mouseover", tip.show)
		.on("mouseout", tip.hide);

	svg.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);
	svg.append("g").attr("class", "y axis").call(yAxis);
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (-margin.left / 2 - 5) + "," + (height / 2) + ")rotate(-90)")
		.text("-log10 P-value");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (width / 2) + "," + (height + 32) + ")")
		.text("Chromosome " + chr);
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (-margin.left / 2) + ", -15)")
		.style("font-size", "8px")
		.text("1000G SNPs");

	// x-direction only zoom handler
	function zoomed() {
		// get the current transform and apply to x axis
		var new_x_scale = d3.event.transform.rescaleX(x);
		svg.select(".x.axis").transition()
        .duration(0)
        .call(xAxis.scale(new_x_scale));

		// For the SNPs do the x-position scaling (cy) based on the transformed scale and 
		// set appropriate coloring/hidden(transparent) for all the SNP classes
		svg.selectAll(".nonLD").attr("cx", function (d) { return new_x_scale(d[0]); })
			.attr("cy", function (d) { return y(-Math.log10(d[1])); })
			.style("fill", function (d) { if (new_x_scale(d[0]) < 0 || new_x_scale(d[0]) > width) { return "transparent"; } else { return "grey"; } });
		svg.selectAll(".dot").attr("cx", function (d) { return new_x_scale(d.pos); })
			.attr("cy", function (d) { return y(-Math.log10(d.gwasP)); })
			.style("fill", function (d) { if (new_x_scale(d.pos) < 0 || new_x_scale(d.pos) > width) { return "transparent"; } else if (d.ld == 0) { return "grey"; } else { return colorScale(d.r2); } });
		svg.selectAll(".KGSNPs")
			.attr("x", function (d) { return new_x_scale(d.pos) })
			.attr("y", -20)
			.style('fill', function (d) { if (new_x_scale(d.pos) < 0 || new_x_scale(d.pos) > width) { return "transparent"; } else if (d.ld == 0) { return "grey"; } else { return colorScale(d.r2); } });
		svg.selectAll(".leadSNPs")
			.attr("cx", function (d) { return new_x_scale(d.pos); })
			.attr("cy", function (d) { return y(-Math.log10(d.gwasP)); })
			.style("fill", function (d) {
				if (new_x_scale(d.pos) < 0 || new_x_scale(d.pos) > width) { return "transparent"; }
				else if (d.ld == 2) { return colorScale(d.r2); }
				else if (d.ld == 3) { return "#9933ff" }
				else if (d.ld == 4) { return "#4d0099" }
			})
			.style("stroke", function (d) { if (new_x_scale(d.pos) < 0 || new_x_scale(d.pos) > width) { return "transparent"; } else { return "black"; } });
	}

	d3.select('#plotClear').on('click', reset);
	function reset() {
		// simply restore the identity transform to the zoom setting 
		// for the top level enclosing g element
		svg.transition().duration(750).call(zoom.transform, d3.zoomIdentity);
	}
}

export function PlotSNPAnnot(data) {
	var margin = { top: 20, right: 80, bottom: 90, left: 40 },
		width = 500 - margin.right - margin.left,
		height = 250 - margin.top - margin.bottom;

	data.forEach(function (d) {
		d.prop = + d.prop;
		d.enrichment = + d.enrichment;
		d['fisher.P'] = + d['fisher.P']; // fisher.P
	});
	data.sort(function (a, b) {
		return b.prop - a.prop;
	})
	var max_e = d3.max(data, function (d) { if (d.enrichment > 0) { return Math.log2(d.enrichment) } });
	max_e = Math.ceil(max_e * 100) / 100
	var min_e = d3.min(data, function (d) { if (d.enrichment > 0) { return Math.log2(d.enrichment) } });
	min_e = Math.ceil(min_e * 100) / 100
	var colorScale = d3.scaleLinear().domain([min_e, 0, max_e]).range(["#0000ff", "#ffffe6", "#ff0000"]);
	var x_element = data.map(function (d) { return d.annot; });
	//var x = d3.scaleOrdinal().domain(x_element).rangeRoundBands([0, width], 0.1);
	var x = d3.scaleBand().domain(x_element).range([0,width]).round(true).padding(0.1);
	var y = d3.scaleLinear().range([height, 0]);
	var xAxis = d3.axisBottom(x);
	var yAxis = d3.axisLeft(y).ticks(5);
	var svg = d3.select('#snpAnnotPlot').append('svg')
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append('g').attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	var tip = d3Tip()
		.attr('class', 'd3-tip')
		.offset([-5, 0])
		.html(function (d) {
			return 'count: ' + d.count
				+ '<br>proportion: ' + Number(d.prop).toPrecision(3)
				+ '<br>enrichment: ' + Number(d.enrichment).toPrecision(3) + '<br>P: ' + Number(Number(d['fisher.P']).toPrecision(3)).toExponential(2);
		})
	svg.call(tip);
	y.domain([0, d3.max(data, function (d) { return d.prop }) * 1.05]);
	// legend
	var t = [];
	for (var i = 0; i < 15; i++) { t.push(i); }
	svg.append('text')
		.attr("x", width + 10)
		.attr("y", 10)
		.text("-log2(E)")
		.style("font-size", "10px");
	svg.selectAll(".legend").data(t).enter().append("g")
		.append("rect")
		.attr("class", 'legendRect')
		.attr("x", width + 10)
		.attr("y", function (d) { return (d - 1) * 5 + 18 })
		.attr("width", 20)
		.attr("height", 5)
		.attr("fill", function (d) { return colorScale(max_e - d * ((max_e - min_e) / (t.length - 1))) });
	svg.selectAll("text.legend").data([0, 14]).enter().append("g")
		.append("text")
		.attr("text-anchor", "start")
		.attr("class", "legenedText")
		.attr("x", width + 32)
		.attr("y", function (d) { return (d - 1) * 5 + 25 })
		.text(function (d) { return Math.round((max_e - d * ((max_e - min_e) / (t.length - 1))) * 100) / 100 })
		.style("font-size", "10px");
	svg.append("text")
		.attr("x", width + 10)
		.attr("y", 105)
		.text("* p<0.05")
		.style("font-size", "10px")
	svg.append("text")
		.attr("x", width + 10)
		.attr("y", 120)
		.text("** p<0.05/" + x_element.length)
		.style("font-size", "10px")

	// background bar for small proportion
	svg.selectAll('.backbar').data(data).enter().append('rect').attr("class", "backbar")
		.attr("x", function (d) { return x(d.annot); })
		.attr("width", x.bandwidth())
		.attr("y", y(d3.max(data, function (d) { return d.prop }) / 2))
		.attr("height", height - y(d3.max(data, function (d) { return d.prop }) / 2))
		.attr("fill", "transparent")
		.attr("opacity", 0)
		.on("mouseover", tip.show)
		.on("mouseout", tip.hide);

	// plot main bar
	svg.selectAll('.bar').data(data).enter().append('rect').attr("class", "bar")
		.attr("x", function (d) { return x(d.annot); })
		.attr("width", x.bandwidth())
		.attr("y", function (d) { return y(d.prop); })
		.attr("height", function (d) { return height - y(d.prop); })
		.attr("fill", function (d) { return colorScale(Math.log2(d.enrichment)) })
		.on("mouseover", tip.show)
		.on("mouseout", tip.hide);

	// significance
	svg.selectAll('text.p').data(data.filter(function (d) { if (d['fisher.P'] < 0.05) { return d } })).enter()
		.append('text')
		.attr("x", function (d) { return x(d.annot) + (width * 0.5 / x_element.length) * 0.7; })
		.attr("y", function (d) { return y(d.prop) * 1.02; })
		.attr("text-anchor", "start")
		.text(function (d) {
			if (d['fisher.p'] < 0.05 / x_element.length) { return "**" }
			else { return "*" }
		});

	// axis
	svg.append('g').attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis).selectAll('text')
		.attr("transform", function () { return "rotate(-65)"; })
		.attr("dy", "-.45em")
		.attr("dx", "-.65em")
		.style("text-anchor", "end");
	svg.append('g').attr("class", "y axis")
		.call(yAxis);
	// No text in this text element crashes TCPDF - leave this as a warning
	//	.append("text")
	//	.attr("transform", "rotate(-90)")
	//	.attr("dy", ".71em")
	//	.style("text-anchor", "end");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(-28," + (height / 2) + ")rotate(-90)")
		.text("Proportion");
	svg.selectAll('path').style('fill', 'none').style('stroke', 'grey');
	svg.selectAll('text').style('font-family', 'sans-serif');
	svg.selectAll('.axis').selectAll('text').style('font-size', '11px');
}

export function PlotLocuSum(data) {
	data.forEach(function (d) {
		d.nSNPs = +d.nSNPs;
		d.size = +(d.size / 1000);
		d.nGenes = +d.nGenes;
		d.nWithinGene = +d.nWithinGene;
	});
	var y_element = data.map(function (d) { return d.label; });
	var margin = { top: 60, right: 30, bottom: 70, left: 180 },
		width = 600,
		height = 15 * y_element.length;
	var y = d3.scaleBand().domain(y_element).range([0, height]).padding(0.1);
	var yAxis = d3.axisLeft(y);
	var svg = d3.select('#lociPlot').append('svg')
		.attr("class", 'plotSVG')
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append('g').attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	var tip_size = d3Tip()
		.attr('class', 'd3-tip')
		.offset([0, 0])
		.html(function (d) { return d.size + " kb"; });
	svg.call(tip_size);
	var tip_nSNPs = d3Tip()
		.attr('class', 'd3-tip')
		.offset([0, 0])
		.html(function (d) { return d.nSNPs; });
	svg.call(tip_nSNPs);
	var tip_nGenes = d3Tip()
		.attr('class', 'd3-tip')
		.offset([0, 0])
		.html(function (d) { return d.nGenes; });
	svg.call(tip_nGenes);
	var tip_nWithinGene = d3Tip()
		.attr('class', 'd3-tip')
		.offset([0, 0])
		.html(function (d) { return d.nWithinGene; });
	svg.call(tip_nWithinGene);
	var currentWidth = 0;
	var eachWidth = 140;
	// plot nSNPs
	var x = d3.scaleLinear().range([currentWidth, currentWidth + eachWidth]);
	var xAxis = d3.axisBottom(x);
	x.domain([0, d3.max(data, function (d) { return d.size })]);
	svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
		.attr("x", x(0))
		.attr("width", function (d) { return x(d.size) })
		.attr("y", function (d) { return y(d.label) })
		.attr("height", y.bandwidth())
		.attr("fill", "lightgreen")
		.on("mouseover", tip_size.show)
		.on("mouseout", tip_size.hide);
	svg.append('g').attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis).selectAll("text")
		.style("text-anchor", "end")
		.attr("transform", function () { return "translate(-12,3)rotate(-65)"; });
	svg.append('g').attr("class", "y axis")
		.call(yAxis)
	// No text in this text element crashes TCPDF - leave this as a warning
	//	.append("text").attr("transform", "rotate(-90)")
	//	.attr("dy", ".71em")
	//	.style("text-anchor", "end");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(-50,-5)")
		.text("Genomic loci");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (-5) + ")")
		.style("text-anchor", "middle")
		.text("Size (kb)");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (height + margin.bottom - 20) + ")")
		.style("text-anchor", "middle")
		.text("Size (kb)");
	currentWidth += eachWidth + 10;
	// plot size
	x = d3.scaleLinear().range([currentWidth, currentWidth + eachWidth]);
	xAxis = d3.axisBottom(x);
	x.domain([0, d3.max(data, function (d) { return d.nSNPs; })]);
	svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
		.attr("x", x(0))
		.attr("width", function (d) { return x(d.nSNPs) - currentWidth })
		.attr("y", function (d) { return y(d.label) })
		.attr("height", y.bandwidth())
		.attr("fill", "skyblue")
		.on("mouseover", tip_nSNPs.show)
		.on("mouseout", tip_nSNPs.hide);
	svg.append('g').attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis).selectAll("text")
		.style("text-anchor", "end")
		.attr("transform", function () { return "translate(-12,3)rotate(-65)"; });
	// .attr("dx","-.65em").attr("dy", "-.2em");
	svg.append('g').attr("class", "y axis")
		.attr("transform", "translate(" + currentWidth + ",0)")
		.call(yAxis).selectAll("text").remove();
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (-5) + ")")
		.style("text-anchor", "middle")
		.text("#SNPs");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (height + margin.bottom - 20) + ")")
		.style("text-anchor", "middle")
		.text("#SNPs");
	currentWidth += eachWidth + 10;

	// plot nGene
	x = d3.scaleLinear().range([currentWidth, currentWidth + eachWidth]);
	xAxis = d3.axisBottom(x);
	x.domain([0, d3.max(data, function (d) { return d.nGenes; })]);
	svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
		.attr("x", x(0))
		.attr("width", function (d) { return x(d.nGenes) - currentWidth })
		.attr("y", function (d) { return y(d.label) })
		.attr("height", y.bandwidth())
		.attr("fill", "orange")
		.on("mouseover", tip_nGenes.show)
		.on("mouseout", tip_nGenes.hide);
	svg.append('g').attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis).selectAll("text")
		.style("text-anchor", "end")
		.attr("transform", function () { return "translate(-12,3)rotate(-65)"; });
	svg.append('g').attr("class", "y axis")
		.attr("transform", "translate(" + currentWidth + ",0)")
		.call(yAxis).selectAll("text").remove();
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (-5) + ")")
		.style("text-anchor", "middle")
		.text("#mapped genes");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (height + margin.bottom - 20) + ")")
		.style("text-anchor", "middle")
		.text("#mapped genes");
	currentWidth += eachWidth + 10;

	// plot nWithinGene
	x = d3.scaleLinear().range([currentWidth, currentWidth + eachWidth]);
	xAxis = d3.axisBottom(x);
	x.domain([0, d3.max(data, function (d) { return d.nWithinGene; })]);
	svg.selectAll('rect.size').data(data).enter().append("rect").attr("class", "bar")
		.attr("x", x(0))
		.attr("width", function (d) { return x(d.nWithinGene) - currentWidth })
		.attr("y", function (d) { return y(d.label) })
		.attr("height", y.bandwidth())
		.attr("fill", "pink")
		.on("mouseover", tip_nWithinGene.show)
		.on("mouseout", tip_nWithinGene.hide);
	svg.append('g').attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis).selectAll("text")
		.style("text-anchor", "end")
		.attr("transform", function () { return "translate(-12,3)rotate(-65)"; });
	svg.append('g').attr("class", "y axis")
		.attr("transform", "translate(" + currentWidth + ",0)")
		.call(yAxis).selectAll("text").remove();
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (-20) + ")")
		.style("text-anchor", "middle")
		.text("#genes physically");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (-5) + ")")
		.style("text-anchor", "middle")
		.text("located in loci");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (height + margin.bottom - 20) + ")")
		.style("text-anchor", "middle")
		.text("#genes physically");
	svg.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + (currentWidth + eachWidth / 2) + "," + (height + margin.bottom - 5) + ")")
		.style("text-anchor", "middle")
		.text("located in loci");
	svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
	svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
	svg.selectAll('text').style('font-family', 'sans-serif');
	svg.selectAll('.axis').selectAll('text').style('font-size', '11px');
}

export function Chr15Select() {
	if ($('#annotPlot_Chrom15').is(":checked") == true) {
		$('#annotPlotChr15Opt').show();
		var ts = [];
		var tmp = document.getElementById('annotPlotChr15Ts');
		for (var i = 0; i < tmp.options.length; i++) {
			if (tmp.options[i].selected === true) {
				ts.push(tmp.options[i].value);
			}
		}
		if (ts.length === 0) {
			$('#CheckAnnotPlotOpt').html('<span class="alert alert-danger">You have selected to plot 15-core chromatin state. Please select at least one tissue/cell type.</span>');
			$('#annotPlotSubmit').attr("disabled", true);
		} else if (ts.length > 0) {
			$('#CheckAnnotPlotOpt').html("<span class='alert alert-success'>OK. Selected tissue/cell types will appear in the plot.</span>");
			$('#annotPlotSubmit').attr("disabled", false);
		}
	} else {
		$('#annotPlotChr15Opt').hide();
		$('#annotPlotSubmit').attr("disabled", false);
		$('#CheckAnnotPlotOpt').html('<span class="alert alert-success">OK. Good to go. Click "Plot" to create regional plot with selected annotations.</span>');
	}
}

export function DownloadFiles() {
	var check = false;
	$('#downFileCheck input').each(function () {
		if ($(this).is(":checked") == true) { check = true; }
	})
	if (check) { $('#download').prop('disabled', false) }
	else { $('#download').prop('disabled', true) }
}

export default GWplot;
