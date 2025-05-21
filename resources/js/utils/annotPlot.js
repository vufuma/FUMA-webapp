import { AnnotPlotPageState as pageState}  from "../pages/pageStateComponents.js";
import { display_dismissable_warning } from "./alerts.js";
export const setAnnotPlotPageState = function(
    id,
    page,
    subdir,
    loggedin, 
) {
	pageState.setState(
		id,
		page,
		subdir,
		loggedin, 
	);
}

export const AnnotPlotSetup = function(
	prefix,
	type,
	rowI,
	GWASplot,
	CADDplot,
	RDBplot,
	eqtlplot,
	ciplot,
	Chr15,
	Chr15cells		
) {
	$('.ImgDownSubmit').hide();
	var plotData;
	var genes;
	var chrom;
	var xMin;
	var xMax;
	var xMin_init;
	var xMax_init;
	var eqtlgenes;

	$.ajax({
		url: 'annotPlot/getData',
		type: 'POST',
		data: {
			jobID: pageState.get('id'),
			prefix: prefix,
			type: type,
			rowI: rowI,
			GWASplot: GWASplot,
			CADDplot: CADDplot,
			RDBplot: RDBplot,
			eqtlplot: eqtlplot,
			ciplot: ciplot,
			Chr15: Chr15,
			Chr15cells: Chr15cells
		},
		beforeSend: function () {
			$("#load").append('<span style="color:grey;"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i><br>Loading ...</span><br>');
		},
		success: function (data) {
			try {
				plotData = JSON.parse(data.replace(/NaN/g, "-1"));
				chrom = plotData["chrom"];
				xMin = plotData["xMin"];
				xMax = plotData["xMax"];
				xMin_init = plotData["xMin_init"];
				xMax_init = plotData["xMax_init"];
				eqtlgenes = plotData["eqtlgenes"];
			}
			catch (e) {
				if (e instanceof SyntaxError) {
					display_dismissable_warning(`Could not parse result of annotPlot/getData data<br> ${e}`, pageState.get('id'));
				} else {
					display_dismissable_warning(`Exception handling result of annotPlot/getData<br> ${e}`, pageState.get('id'));
				}
				$('#load').html("");
			}
		},
		complete: function () {
			$.ajax({
				url: 'annotPlot/getGenes',
				type: 'POST',
				data: {
					jobID: pageState.get('id'),
					prefix: prefix,
					chrom: chrom,
					eqtlplot: eqtlplot,
					ciplot: ciplot,
					xMin: xMin,
					xMax: xMax,
					eqtlgenes: eqtlgenes
				},
				success: function (data) {
					try {
						genes = JSON.parse(data);
					}
					catch (e) {
						if (e instanceof SyntaxError) {
							display_dismissable_warning(`Could not parse result of annotPlot/getGenes data<br> ${e}`, pageState.get('id'));
						} else {
							display_dismissable_warning(`Exception handling result of annotPlot/getGenes<br> ${e}`, pageState.get('id'));
						}
					}
					finally {
						$('#load').html("");
					}
				},
				complete: function () {
					Plot(plotData, genes, chrom, xMin_init, xMax_init, eqtlgenes, GWASplot, CADDplot, RDBplot, Chr15cells, Chr15, eqtlplot, ciplot);
				}
			});
		}
	});
};

function Plot(plotData, genes, chrom, xMin_init, xMax_init, eqtlgenes, GWASplot, CADDplot, RDBplot, Chr15cells, Chr15, eqtlplot, ciplot) {
	/*---------------------------------------------
	| Set parameters
	---------------------------------------------*/
	var margin = { top: 50, right: 280, left: 60, bottom: 100 },
		width = 600;
	// 5% of the genomic region is added to both side
	var side = (xMax_init * 1 - xMin_init * 1) * 0.05;
	if (side == 0) { side = 500; }

	// set x axis
	var x = d3.scaleLinear().range([0, width]);
	// The default is no tick values for the axis
	// They will be enabled on the last subplot visible.
	var xAxis = d3.axisBottom(x).ticks(5).tickFormat("");
	var xLastAxis = d3.axisBottom(x).ticks(5);
	x.domain([(xMin_init * 1 - side), (xMax_init * 1 + side)]);

	// define colors
	var colorScale = d3.scaleLinear().domain([0.0, 0.5, 1.0]).range(["#2c7bb6", "#ffffbf", "#d7191c"]).interpolate(d3.interpolateHcl);
	var Chr15colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];
	var Chr15eid = ["E017", "E002", "E008", "E001", "E015", "E014", "E016", "E003", "E024", "E020", "E019", "E018", "E021",
		"E022", "E007", "E009", "E010", "E013", "E012", "E011", "E004", "E005", "E006", "E062", "E034", "E045",
		"E033", "E044", "E043", "E039", "E041", "E042", "E040", "E037", "E048", "E038", "E047", "E029", "E031",
		"E035", "E051", "E050", "E036", "E032", "E046", "E030", "E026", "E049", "E025", "E023", "E052", "E055",
		"E056", "E059", "E061", "E057", "E058", "E028", "E027", "E054", "E053", "E112", "E093", "E071", "E074",
		"E068", "E069", "E072", "E067", "E073", "E070", "E082", "E081", "E063", "E100", "E108", "E107", "E089",
		"E090", "E083", "E104", "E095", "E105", "E065", "E078", "E076", "E103", "E111", "E092", "E085", "E084",
		"E109", "E106", "E075", "E101", "E102", "E110", "E077", "E079", "E094", "E099", "E086", "E088", "E097",
		"E087", "E080", "E091", "E066", "E098", "E096", "E113", "E114", "E115", "E116", "E117", "E118", "E119",
		"E120", "E121", "E122", "E123", "E124", "E125", "E126", "E127", "E128", "E129"];
	var Chr15group = ["IMR90", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "ESC", "iPSC", "iPSC", "iPSC", "iPSC",
		"iPSC", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv", "ES-deriv",
		"ES-deriv", "ES-deriv", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
		"Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
		"Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell", "Blood & T-cell",
		"HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "HSC & B-cell",
		"HSC & B-cell", "HSC & B-cell", "HSC & B-cell", "Mesench", "Mesench", "Mesench", "Mesench", "Myosat",
		"Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial", "Epithelial",
		"Epithelial", "Neurosph", "Neurosph", "Thymus", "Thymus", "Brain", "Brain", "Brain", "Brain", "Brain",
		"Brain", "Brain", "Brain", "Brain", "Brain", "Adipose", "Muscle", "Muscle", "Muscle", "Muscle", "Muscle",
		"Heart", "Heart", "Heart", "Heart", "Heart", "Sm. Muscle", "Sm. Muscle", "Sm. Muscle", "Sm. Muscle",
		"Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive", "Digestive",
		"Digestive", "Digestive", "Digestive", "Digestive", "Other", "Other", "Other", "Other", "Other", "Other",
		"Other", "Other", "Other", "Other", "Other", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012",
		"ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012",
		"ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012", "ENCODE2012"];
	var Chr15GroupCols = ["#E41A1C", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965", "#924965",
		"#69608A", "#69608A", "#69608A", "#69608A", "#69608A", "#4178AE", "#4178AE", "#4178AE", "#4178AE",
		"#4178AE", "#4178AE", "#4178AE", "#4178AE", "#4178AE", "#55A354", "#55A354", "#55A354", "#55A354",
		"#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354", "#55A354",
		"#55A354", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69", "#678C69",
		"#678C69", "#B65C73", "#B65C73", "#B65C73", "#B65C73", "#E67326", "#FF9D0C", "#FF9D0C", "#FF9D0C",
		"#FF9D0C", "#FF9D0C", "#FF9D0C", "#FF9D0C", "#FF9D0C", "#FFD924", "#FFD924", "#DAB92E", "#DAB92E",
		"#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B", "#C5912B",
		"#C5912B", "#AF5B39", "#C2655D", "#C2655D", "#C2655D", "#C2655D", "#C2655D", "#D56F80", "#D56F80",
		"#D56F80", "#D56F80", "#D56F80", "#F182BC", "#F182BC", "#F182BC", "#F182BC", "#C58DAA", "#C58DAA",
		"#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA", "#C58DAA",
		"#C58DAA", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999", "#999999",
		"#999999", "#999999", "#999999", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000",
		"#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000",
		"#000000"];

	var eqtlcols = ['rgb(0,10,255)', 'rgb(0,38,255)', 'rgb(0,67,255)', 'rgb(0,96,255)', 'rgb(0,125,255)', 'rgb(0,154,255)', 'rgb(0,183,255)',
		'rgb(0,212,255)', 'rgb(0,241,255)', 'rgb(0,255,10)', 'rgb(0,255,38)', 'rgb(0,255,67)', 'rgb(0,255,96)', 'rgb(0,255,125)',
		'rgb(0,255,154)', 'rgb(0,255,183)', 'rgb(0,255,212)', 'rgb(0,255,241)', 'rgb(19,0,255)', 'rgb(19,255,0)', 'rgb(48,0,255)',
		'rgb(48,255,0)', 'rgb(77,0,255)', 'rgb(77,255,0)', 'rgb(106,0,255)', 'rgb(106,255,0)', 'rgb(135,0,255)', 'rgb(135,255,0)',
		'rgb(164,0,255)', 'rgb(164,255,0)', 'rgb(192,0,255)', 'rgb(192,255,0)', 'rgb(221,0,255)', 'rgb(221,255,0)', 'rgb(250,0,255)',
		'rgb(250,255,0)', 'rgb(255,0,0)', 'rgb(255,0,29)', 'rgb(255,0,58)', 'rgb(255,0,87)', 'rgb(255,0,115)', 'rgb(255,0,144)',
		'rgb(255,0,173)', 'rgb(255,0,202)', 'rgb(255,0,231)', 'rgb(255,29,0)', 'rgb(255,58,0)', 'rgb(255,87,0)', 'rgb(255,115,0)',
		'rgb(255,144,0)', 'rgb(255,173,0)', 'rgb(255,202,0)', 'rgb(255,231,0)'
	];
	var eqtlts = ["Adipose_Subcutaneous", "Adipose_Visceral_Omentum", "Adrenal_Gland", "Bladder",
		"Cells_EBV-transformed_lymphocytes", "Whole_Blood", "Artery_Aorta", "Artery_Coronary",
		"Artery_Tibial", "Brain_Amygdala", "Brain_Anterior_cingulate_cortex_BA24", "Brain_Caudate_basal_ganglia",
		"Brain_Cerebellar_Hemisphere", "Brain_Cerebellum", "Brain_Cortex", "Brain_Frontal_Cortex_BA9",
		"Brain_Hippocampus", "Brain_Hypothalamus", "Brain_Nucleus_accumbens_basal_ganglia", "Brain_Putamen_basal_ganglia",
		"Brain_Spinal_cord_cervical_c-1", "Brain_Substantia_nigra", "Breast_Mammary_Tissue", "Cervix_Ectocervix",
		"Cervix_Endocervix", "Colon_Sigmoid", "Colon_Transverse", "Esophagus_Gastroesophageal_Junction",
		"Esophagus_Mucosa", "Esophagus_Muscularis", "Fallopian_Tube", "Heart_Atrial_Appendage",
		"Heart_Left_Ventricle", "Kidney_Cortex", "Liver", "Lung", "Muscle_Skeletal", "Nerve_Tibial",
		"Ovary", "Pancreas", "Pituitary", "Prostate", " Minor_Salivary_Gland", "Cells_Transformed_fibroblasts",
		"Skin_Not_Sun_Exposed_Suprapubic", "Skin_Sun_Exposed_Lower_leg", "Small_Intestine_Terminal_Ileum", "Spleen",
		"Stomach", "Testis", "Thyroid", "Uterus", "Vagina", "BloodeQTL", "BIOS_eQTL_geneLevel"];
	var eQTLcolors = {};
	var eid = [];

	// Variable stores whch plot panel is the bottom
	var xAxisLabel = "gene";
	var lastXAxisGroup = null; // holds the lowermost xAxis group 

	// height variables
	var height; // Total height of the plot
	var genesHeight; // Depends on the overlap of genes
	var gwasHeight = 200;
	var caddHeight = 150;
	var rdbHeight = 150;
	var chrHeight; // Depends on the number of selected epigenomes
	var eqtlHeight; // Depends on the number the number of genes which have eQTLs
	var ciHeight; // Depends on the number of data sets and interactions
	var ciregHeight; // Depends on t he number of selected epigenomes

	// minimum Y variables
	var gwasTop = 0;
	var genesTop = (gwasHeight + gwasTop + 10) * GWASplot;
	var caddTop;
	var rdbTop;
	var chrTop;
	var eqtlTop;
	var ciTop;
	var ciregTop;

	var ci_cellsize = 3;

	// gene data
	genes.genes.forEach(function (d) {
		d[2] = +d[2]; //start position
		d[3] = +d[3]; //end position
		d[6] = 1; //y
	});
	genes.genes = geneOver(genes.genes, x, width); //avoid overlap of genes

	//  snps data
	plotData.snps.forEach(function (d) {
		d[2] = +d[2]; //pos
		d[4] = +d[4]; //gwasP
		if (d[4] == -1) { d[4] = NaN }
		d[5] = +d[5]; //ld
		d[6] = +d[6]; //r2
		d[9] = +d[9]; //CADD
		d[13] = +d[13]; //MapFilt
	});

	// define height
	genesHeight = 20 * (d3.max(genes.genes, function (d) { return d[6]; }) + 1);
	caddTop = (genesTop + genesHeight + 10);
	rdbTop = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot;
	chrTop = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot + (rdbHeight + 10) * RDBplot;
	var cells = Chr15cells.split(":");
	if (cells.length > 30 || cells[0] == "all") { chrHeight = 300; }
	else { chrHeight = 10 * cells.length; }
	var eqtlNgenes = parseInt(plotData["eqtlNgenes"])
	if (plotData["eqtl"].length == 0) {
		eqtlNgenes = 0;
	}
	eqtlTop = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot + (rdbHeight + 10) * RDBplot + (chrHeight + 10) * Chr15;
	eqtlHeight = eqtlplot * (eqtlNgenes * 55);

	ciHeight = 0;
	plotData.ciheight.forEach(function (d) {
		if (d * ci_cellsize + 10 < 30) {
			ciHeight += 30;
		} else {
			ciHeight += d * ci_cellsize + 10;
		}
		ciHeight += 5;
	});
	ciTop = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot + (rdbHeight + 10) * RDBplot + (chrHeight + 10) * Chr15 + eqtlHeight;
	ciregHeight = plotData["cieid"].length * 10;
	if (ciregHeight > 250) { ciregHeight = 250 }
	ciregTop = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot + (rdbHeight + 10) * RDBplot + (chrHeight + 10) * Chr15 + eqtlHeight + ciHeight;
	height = (gwasHeight + 10) * GWASplot + genesHeight + 10 + (caddHeight + 10) * CADDplot + (rdbHeight + 10) * RDBplot + (chrHeight + 10) * Chr15 + eqtlHeight + ciHeight + ciregHeight;
	if (plotData["eqtl"].length > 0) {
		ciTop += 10;
		ciregTop += 10;
		height += 10;
	}
	if (plotData["ci"].length > 0 && plotData["cireg"].length > 0) {
		ciregTop += 10;
		height += 10;
	}

	// Prepare svg
	// The variable svg points to the top level container (g) element: <svg><g>
	var root = d3.select('#annotPlot').append('svg')
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom);
	// Append a white background fill at the root.
	// This makes it simpler to render this to png and jpg
	root.append("rect")
		.attr("id", "annotPlot_backgrond")
		.attr("width", "100%")
		.attr("height", "100%")
		.attr("fill", "#fff");

	var base = root
		.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	// Add a clipPath: everything out of this area won't be drawn.
	base.append("defs").append("base:clipPath")
		.attr("id", "clip")
		.append("base:rect")
		.attr("width", width )
		.attr("height", height + 30 )
		.attr("x", 0)
		.attr("y", -30);

  	// Create the svg variable: where both the circles and the brush take place
  	var svg = base.append('g')
    	.attr("clip-path", "url(#clip)")

	// Create zoom behavior and restrict zoom extent to 1000x
	var zoom = d3.zoom()
		.scaleExtent([0, 1000])
		.extent([[0, 0], [width, height]])
		.on("zoom", zoomed);


	// Vertical bar shows mouse x position
	var vertical = svg.append("rect")
		.attr("class", "vertical")
		.attr("z-index", "500")
		.attr("width", 5)
		.attr("height", height + 25)
		.attr("x", 0)
		.attr("y", -25)
		.attr("fill", "none");

	// Transparent rect for mouse over
	// assign events to display and move the vertical track bar
	svg.append("rect")
		.attr("width", width)
		.attr("height", height)
		.attr("fill", "none")
		.style("fill", "none")
		.style("shape-rendering", "crispEdges")
		.style("pointer-events", "all")
		.on("mousemove", function () {
			var mousex = d3.mouse(this)[0];
			vertical.attr("x", mousex);
		})
		.on("mouseover", function () {
			var mousex = d3.mouse(this)[0];
			vertical.attr("x", mousex).style("stroke", "grey");
		})
		.on("mouseout", function () {
			vertical.style("stroke", "transparent")
		});

	svg.call(zoom);
	/*---------------------------------------------
	| Plot genes
	---------------------------------------------*/
	var y = d3.scaleLinear().range([genesTop + genesHeight, genesTop]);
	y.domain([d3.max(genes.genes, function (d) { return d[6]; }) + 1, 0]);

	// genes legend
	base.append("rect").attr("x", width + 20).attr("y", genesTop + 10)
		.attr("width", 20).attr("height", 5).attr("fill", "red");
	base.append("text").attr("x", width + 45).attr("y", genesTop + 15)
		.text("Mapped genes").style("font-size", "10px");
	base.append("rect").attr("x", width + 20).attr("y", genesTop + 25)
		.attr("width", 20).attr("height", 5).attr("fill", "blue");
	base.append("text").attr("x", width + 45).attr("y", genesTop + 30)
		.text("Non-mapped protein coding genes").style("font-size", "10px");
	base.append("rect").attr("x", width + 20).attr("y", genesTop + 40)
		.attr("width", 20).attr("height", 5).attr("fill", "#383838");
	base.append("text").attr("x", width + 45).attr("y", genesTop + 45)
		.text("Non-mapped non-coding genes").style("font-size", "10px");

	// genes
	svg.selectAll('rect.gene').data(genes.genes).enter().append("g")
		.insert('rect').attr("class", "cell").attr("class", "genesrect")
		.attr("x", function (d) {
			return x(d[2]); 
		})
		// .attr("y", function(d){return y(d.strand)})
		.attr("y", function (d) { return y(d[6]) })
		.attr("width", function (d) {
			return x(d[3]) - x(d[2]);
		})
		.attr("height", 1)
		.attr("fill", function (d) {
			if (genes["mappedGenes"].indexOf(d[1]) >= 0) { return "red"; }
			else if (d[5] == "protein_coding") { return "blue"; }
			else { return "#383838" }
		});

	// gene names
	svg.selectAll("text.genes").data(genes.genes).enter()
		.append("text").attr("class", "geneName").attr("text-anchor", "middle")
		.attr("x", function (d) {
			return x(((d[3] - d[2]) / 2) + d[2]);
		})
		.attr("y", function (d) { return y(d[6]); })
		.attr("dy", "-.7em")
		.text(function (d) {
			if (d[4] == 1) {
				return d[1] + "\u2192";
			} else {
				return "\u2190" + d[1];
			}
		})
		.style("font-size", "9px")
		.style("font-family", "sans-serif")
		.style("fill", "black");


	lastXAxisGroup = base.append("g").attr("class", "x axis genes")
		.attr("transform", "translate(0," + (genesTop + genesHeight) + ")")
		.call(xLastAxis)

	xAxisLabel = "genes";
	lastXAxisGroup.selectAll('text').style('font-size', '11px');

	//exon plot
	genes.exons.forEach(function (d) {
		d[6] = +d[6]; // exon start
		d[7] = +d[7]; //exon end
		d[4] = +d[4]; //strand
		d[8] = genes.genes.filter(function (d2) { if (d2[0] == d[0]) { return d2; } })[0][6];
	});
	svg.selectAll('rect.exon').data(genes.exons).enter().append("g")
		.insert('rect').attr("class", "cell").attr("class", "exons")
		.attr("x", function (d) {
			return x(d[6]);
		})
		// .attr("y", function(d){return y(d.strand)-4.5})
		.attr("y", function (d) { return y(d[8]) - 4.5 })
		.attr("width", function (d) {
			return x(d[7]) - x(d[6]); 
		})
		.attr("height", 9)
		.attr("fill", function (d) {
			if (genes["mappedGenes"].indexOf(d[1]) >= 0) { return "red"; }
			else if (d[5] == "protein_coding") { return "blue"; }
			else { return "#383838" }
		});

	/*---------------------------------------------
		| Plot GWAS P-value
		-----------------------
		----------------------*/
	if (GWASplot == 1) {
		plotData.osnps.forEach(function (d) {
			d[1] = +d[1]; //pos
			d[2] = +d[2]; //gwasP
		});
		y = d3.scaleLinear().range([gwasTop + gwasHeight, gwasTop]);
		var yAxis = d3.axisLeft(y);

		// legend
		var legData = [];
		for (i = 10; i > 0; i--) {
			legData.push(i * 0.1);
		}
		var legendGwas = base.selectAll(".legendGWAS")
			.data(legData)
			.enter()
			.append("g").attr("class", "legend")
		legendGwas.append("rect")
			.attr("x", width + 20)
			.attr("y", function (d) { return 10 + (10 - d * 10) * 10 })
			.attr("width", 20)
			.attr("height", 10)
			.style("fill", function (d) { return colorScale(d) });
		legendGwas.append("text")
			.attr("text-anchor", "start")
			.attr("x", width + 42)
			.attr("y", function (d) { return 20 + (10 - d * 10) * 10 })
			.text(function (d) { return Math.round(d * 100) / 100 })
			.style("font-size", "10px");
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (width + 30) + ",5)")
			.text("r2").style("font-size", "10px");

		base.append("circle")
			.attr("cx", width + 20).attr("cy", 130).attr("r", 4.5)
			.style("fill", "#4d0099").style("stroke", "black").style("stroke-width", "2");
		base.append("text").attr("text-anchor", "bottom")
			.attr("x", width + 30).attr("y", 133)
			.text("Top lead SNP").style("font-size", "10px");
		base.append("circle")
			.attr("cx", width + 20).attr("cy", 145).attr("r", 4)
			.style("fill", "#9933ff").style("stroke", "black").style("stroke-width", "2");
		base.append("text").attr("text-anchor", "top")
			.attr("x", width + 30).attr("y", 148)
			.text("Lead SNPs").style("font-size", "10px");
		base.append("circle")
			.attr("cx", width + 20).attr("cy", 160).attr("r", 3.5)
			.style("fill", "red").style("stroke", "black").style("stroke-width", "2");
		base.append("text").attr("text-anchor", "top")
			.attr("x", width + 30).attr("y", 163)
			.text("Independent significant SNPs").style("font-size", "10px");

		// plot SNPs which are not in LD (filled in grey)
		var maxY = Math.max(d3.max(plotData.snps, function (d) { return -Math.log10(d[4]) }), d3.max(plotData.osnps, function (d) { return -Math.log10(d[2]) }))
		y.domain([0, maxY + 1]);
		svg.selectAll("dot").data(plotData.osnps).enter()
			.append("circle")
			.attr("class", "GWASnonLD")
			.attr("r", 3.5)
			.attr("cx", function (d) { return x(d[1]); })
			.attr("cy", function (d) { return y(-Math.log10(d[2])); })
			.style("fill", function () { return "grey"; });

		// plot SNPs which exist in the input GWAS file
		svg.selectAll("dot").data(plotData.snps.filter(function (d) { if (!isNaN(d[4]) && d[5] == 1) { return d; } })).enter()
			.append("circle")
			.attr("class", "GWASdot")
			.attr("r", 3.5)
			.attr("cx", function (d) { return x(d[2]); })
			.attr("cy", function (d) { return y(-Math.log10(d[4])); })
			.style("fill", function (d) { return colorScale(d[6]); })
			.on("click", function (d) {
				let table = '<table class="table table-sm" style="font-size: 11px;" cellpadding="1">'
					+ '<tr><td>Selected SNP</td><td>' + d[3]
					+ '</td></tr><tr><td>bp</td><td>' + d[2] + '</td></tr><tr><td>r<sup>2</sup></td><td>' + d[6]
					+ '</td></tr><tr><td>Ind. Sig. SNPs</td><td>' + d[7]
					+ '</td></tr><tr><td>GWAS P-value</td><td>' + d[4]
					+ '</td></tr><tr><td>Annotation</td><td>' + d[12]
					+ '</td></tr><tr><td>Nearest Gene</td><td>' + d[11]
					+ '</td></tr><tr><td>CADD</td><td>' + d[9];
				if (d[10] == "NA") {
					table += '</td></tr><tr><td>RDB</td><td>' + d[10] + '</td>';
				} else {
					table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr' + chrom + '/' + (d[2] - 1) + '">' + d[10]
						+ ' (external link)*</a></td></tr>';
				}
				if (Chr15 == 1) {
					cells = Chr15cells.split(":");
					if (cells[0] == "all") { cells = Chr15eid; }
					for (let i = 0; i < cells.length; i++) {
						table += '<tr><td>' + cells[i] + '</td><td>' + d[14 + i] + '</td></tr>';
					}
				}
				if (eqtlplot == 1 & plotData["eqtl"].length > 0) {
					table += '<tr><td>eQTLs</td><td>' + d[plotData.snps[0].length - 1] + '</td></tr>';
				}
				table += '</table>'
				$('#annotTable').html(table);
			});

		// plot SNPs which do not exist in input GWAS (rect)
		svg.selectAll('rect.KGSNPs').data(plotData.snps.filter(function (d) { if (isNaN(d[4])) { return d; } })).enter()
			.append("rect")
			.attr("class", "KGSNPs")
			.attr("x", function (d) { return x(d[2]) })
			.attr("y", -20)
			.attr("width", "3")
			.attr("height", "10")
			.style("fill", function (d) { if (d[5] == 0) { return "grey" } else { return colorScale(d[6]) } })
			.on("click", function (d) {
				let table = '<table class="table table-sm" style="font-size: 11px;" cellpadding="1">'
					+ '<tr><td>Selected SNP</td><td>' + d[3]
					+ '</td></tr><tr><td>bp</td><td>' + d[2] + '</td></tr><tr><td>r<sup>2</sup></td><td>' + d[6]
					+ '</td></tr><tr><td>Ind. Sig. SNPs</td><td>' + d[7]
					+ '</td></tr><tr><td>GWAS P-value</td><td>' + d[4]
					+ '</td></tr><tr><td>Annotation</td><td>' + d[12]
					+ '</td></tr><tr><td>Nearest Gene</td><td>' + d[11]
					+ '</td></tr><tr><td>CADD</td><td>' + d[9];
				if (d[10] == "NA") {
					table += '</td></tr><tr><td>RDB</td><td>' + d[10] + '</td>';
				} else {
					table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr' + chrom + '/' + (d[2] - 1) + '">' + d[10]
						+ ' (external link)*</a></td></tr>';
				}
				if (Chr15 == 1) {
					cells = Chr15cells.split(":");
					if (cells[0] == "all") { cells = Chr15eid; }
					for (let i = 0; i < cells.length; i++) {
						table += '<tr><td>' + cells[i] + '</td><td>' + d[14 + i] + '</td></tr>';
					}
				}
				if (eqtlplot == 1 & plotData["eqtl"].length > 0) {
					table += '<tr><td>eQTLs</td><td>' + d[plotData.snps[0].length - 1] + '</td></tr>';
				}
				table += '</table>'
				$('#annotTable').html(table);
			});

		// lead SNPs
		svg.selectAll("dot.leadSNPs").data(plotData.snps.filter(function (d) { if (d[5] >= 2) { return d; } })).enter()
			.append("circle")
			.attr("class", "leadSNPs")
			.attr("cx", function (d) { return x(d[2]) })
			.attr("cy", function (d) { return y(-Math.log10(d[4])); })
			.attr("r", function (d) {
				if (d[5] == 2) { return 3.5; }
				else if (d[5] == 3) { return 4; }
				else if (d[5] == 4) { return 4.5; }
			})
			.style("fill", function (d) {
				if (d[5] == 2) { return colorScale(d[6]); }
				else if (d[5] == 3) { return "#9933ff" }
				else if (d[5] == 4) { return "#4d0099" }
			})
			.style("stroke", "black")
			.on("click", function (d) {
				let table = '<table class="table table-sm" style="font-size: 11px;" cellpadding="1">'
					+ '<tr><td>Selected SNP</td><td>' + d[3]
					+ '</td></tr><tr><td>bp</td><td>' + d[2] + '</td></tr><tr><td>r<sup>2</sup></td><td>' + d[6]
					+ '</td></tr><tr><td>Ind. Sig. SNPs</td><td>' + d[7]
					+ '</td></tr><tr><td>GWAS P-value</td><td>' + d[4]
					+ '</td></tr><tr><td>Annotation</td><td>' + d[12]
					+ '</td></tr><tr><td>Nearest Gene</td><td>' + d[11]
					+ '</td></tr><tr><td>CADD</td><td>' + d[9];
				if (d[10] == "NA") {
					table += '</td></tr><tr><td>RDB</td><td>' + d[10] + '</td>';
				} else {
					table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr' + chrom + '/' + (d[2] - 1) + '">' + d[10]
						+ ' (external link)*</a></td></tr>';
				}
				if (Chr15 == 1) {
					cells = Chr15cells.split(":");
					if (cells[0] == "all") { cells = Chr15eid; }
					for (let i = 0; i < cells.length; i++) {
						table += '<tr><td>' + cells[i] + '</td><td>' + d[14 + i] + '</td></tr>';
					}
				}
				if (eqtlplot == 1 & plotData["eqtl"].length > 0) {
					table += '<tr><td>eQTLs</td><td>' + d[plotData.snps[0].length - 1] + '</td></tr>';
				}
				table += '</table>'
				$('#annotTable').html(table);
			});

		// labels
		
		xAxisLabel = "GWAS"
		lastXAxisGroup.call(xAxis); // clear ticks on prev axis
		lastXAxisGroup = base.append("g").attr("class", "x axis GWAS")
			.attr("transform", "translate(0," + (gwasTop + gwasHeight) + ")")
			.call(xLastAxis);
		base.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (-10 - margin.left / 2) + "," + (gwasTop + gwasHeight / 2) + ")rotate(-90)")
			.text("-log10 P-value");
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (-margin.left / 2) + ", -15)")
			.style("font-size", "8px")
			.text("ref SNPs");
	}

	/*---------------------------------------------
		| Plot CADD
		---------------------------------------------*/
	if (CADDplot == 1) {
		y = d3.scaleLinear().range([caddTop + caddHeight, caddTop]);
		yAxis = d3.axisLeft(y);

		// legend
		y.domain([0, d3.max(plotData.snps, function (d) { return d[9] }) + 1]);
		base.append("circle").attr("cx", width + 20).attr("cy", caddTop + 50)
			.attr("r", 3.5).attr("fill", "blue");
		base.append("text").attr("x", width + 30).attr("y", caddTop + 53)
			.text("exonic SNPs").style("font-size", "10px");
		base.append("circle").attr("cx", width + 20).attr("cy", caddTop + 70)
			.attr("r", 3.5).attr("fill", "skyblue");
		base.append("text").attr("x", width + 30).attr("y", caddTop + 73)
			.text("other SNPs").style("font-size", "10px");

		// plot SNPs
		svg.selectAll("dot").data(plotData.snps.filter(function (d) { if (d[5] != 0) { return d; } })).enter()
			.append("circle")
			.attr("class", "CADDdot")
			.attr("r", 3.5)
			.attr("cx", function (d) { return x(d[2]); })
			.attr("cy", function (d) { return y(d[9]); })
			// .style("fill", function(d){if(d.ld==0){return "grey";}else if(d.func=="exonic" || d.func=="splicing"){return "blue"}else{return "skyblue";}})
			.style("fill", function (d) {
				if (d[13] == 0) {
					return "grey";
				} else {
					if (d[12] == "exonic") { return "blue"; }
					else { return "skyblue"; }
				}
			})
			.on("click", function (d) {
				let table = '<table class="table table-sm" style="font-size: 11px;" cellpadding="1">'
					+ '<tr><td>Selected SNP</td><td>' + d[3]
					+ '</td></tr><tr><td>bp</td><td>' + d[2] + '</td></tr><tr><td>r<sup>2</sup></td><td>' + d[6]
					+ '</td></tr><tr><td>Ind. Sig. SNPs</td><td>' + d[7]
					+ '</td></tr><tr><td>GWAS P-value</td><td>' + d[4]
					+ '</td></tr><tr><td>Annotation</td><td>' + d[12]
					+ '</td></tr><tr><td>Nearest Gene</td><td>' + d[11]
					+ '</td></tr><tr><td>CADD</td><td>' + d[9];
				if (d[10] == "NA") {
					table += '</td></tr><tr><td>RDB</td><td>' + d[10] + '</td>';
				} else {
					table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr' + chrom + '/' + (d[2] - 1) + '">' + d[10]
						+ ' (external link)*</a></td></tr>';
				}
				if (Chr15 == 1) {
					cells = Chr15cells.split(":");
					if (cells[0] == "all") { cells = Chr15eid; }
					for (let i = 0; i < cells.length; i++) {
						table += '<tr><td>' + cells[i] + '</td><td>' + d[14 + i] + '</td></tr>';
					}
				}
				if (eqtlplot == 1 & plotData["eqtl"].length > 0) {
					table += '<tr><td>eQTLs</td><td>' + d[plotData.snps[0].length - 1] + '</td></tr>';
				}
				table += '</table>'
				$('#annotTable').html(table);
			});

		// labels
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (-10 - margin.left / 2) + "," + (caddTop + caddHeight / 2) + ")rotate(-90)")
			.text("CADD score");
		lastXAxisGroup.call(xAxis); // clear ticks on prev axis
		lastXAxisGroup = base.append("g").attr("class", "x axis CADD")
			.attr("transform", "translate(0," + (caddTop + caddHeight) + ")")
			.call(xLastAxis)

		xAxisLabel = "CADD";
		lastXAxisGroup.selectAll('text').style('font-size', '11px');

		base.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');
	}

	/*---------------------------------------------
		| Plot RegulomeDB
		---------------------------------------------*/
	if (RDBplot == 1) {
		var y_element = ["1a", "1b", "1c", "1d", "1e", "1f", "2a", "2b", "2c", "3a", "3b", "4", "5", "6", "7"];
		y = d3.scalePoint().domain(y_element).range([rdbTop, rdbTop + rdbHeight]);
		yAxis = d3.axisLeft(y).tickFormat(function (d) { return d; });

		// plot SNPs
		svg.selectAll("dot").data(plotData.snps.filter(function (d) { if (d[10] != "NA" && d[10] != "" && d[5] != 0) { return d; } })).enter()
			.append("circle")
			.attr("class", "RDBdot")
			.attr("r", 3.5)
			.attr("cx", function (d) { return x(d[2]); })
			.attr("cy", function (d) { return y(d[10]); })
			// .style("fill", function(d){if(d.ld==0){return "grey"}else{return "MediumAquaMarine"}})
			.style("fill", function (d) {
				if (d[13] == 0) { return "grey"; }
				else { return "MediumAquaMarine"; }
			})
			.on("click", function (d) {
				let table = '<table class="table table-sm" style="font-size: 11px;" cellpadding="1">'
					+ '<tr><td>Selected SNP</td><td>' + d[3]
					+ '</td></tr><tr><td>bp</td><td>' + d[2] + '</td></tr><tr><td>r<sup>2</sup></td><td>' + d[6]
					+ '</td></tr><tr><td>Ind. Sig. SNPs</td><td>' + d[7]
					+ '</td></tr><tr><td>GWAS P-value</td><td>' + d[4]
					+ '</td></tr><tr><td>Annotation</td><td>' + d[12]
					+ '</td></tr><tr><td>Nearest Gene</td><td>' + d[11]
					+ '</td></tr><tr><td>CADD</td><td>' + d[9];
				if (d[10] == "NA") {
					table += '</td></tr><tr><td>RDB</td><td>' + d[10] + '</td>';
				} else {
					table += '</td></tr><tr><td>RDB</td><td><a target="_blank" href="http://regulomedb.org/snp/chr' + chrom + '/' + (d[2] - 1) + '">' + d[10]
						+ ' (external link)*</a></td></tr>';
				}
				if (Chr15 == 1) {
					cells = Chr15cells.split(":");
					if (cells[0] == "all") { cells = Chr15eid; }
					for (let i = 0; i < cells.length; i++) {
						table += '<tr><td>' + cells[i] + '</td><td>' + d[14 + i] + '</td></tr>';
					}
				}
				if (eqtlplot == 1 & plotData["eqtl"].length > 0) {
					table += '<tr><td>eQTLs</td><td>' + d[plotData.snps[0].length - 1] + '</td></tr>';
				}
				table += '</table>'
				$('#annotTable').html(table);
			});

		// labels
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (-10 - margin.left / 2) + "," + (rdbTop + rdbHeight / 2) + ")rotate(-90)")
			.text("RegulomeDB score");
		lastXAxisGroup.call(xAxis); // clear ticks on prev axis
		lastXAxisGroup = base.append("g").attr("class", "x axis RDB")
			.attr("transform", "translate(0," + (rdbTop + rdbHeight) + ")")
			.call(xLastAxis);

		xAxisLabel = "RDB";
		lastXAxisGroup.selectAll('text').style('font-size', '11px');

		base.append("g").attr("class", "y axis").call(yAxis)
			.selectAll('text').style('font-size', '11px');
		RDBlegend();
	}

	/*---------------------------------------------
		| Plot 15 core Chromatin state
		---------------------------------------------*/
	if (Chr15 == 1) {
		plotData.Chr15.forEach(function (d) {
			d[1] = +d[1]; //start
			d[2] = +d[2]; //end
			d[3] = +d[3]; //state
		});
		// var colors = ["#FF0000", "#FF4500", "#32CD32", "#008000", "#006400", "#C2E105", "#FFFF00", "#66CDAA", "#8A91D0", "#CD5C5C", "#E9967A", "#BDB76B", "#808080", "#C0C0C0", "white"];

		cells = d3.set(plotData.Chr15.map(function (d) { return d[0]; })).values();
		// EIDlegend(cells);
		var chr15gcol = [];
		y_element = [];
		for (let i = 0; i < Chr15eid.length; i++) {
			if (cells.indexOf(Chr15eid[i]) >= 0) {
				y_element.push(Chr15eid[i]);
				chr15gcol.push(Chr15GroupCols[i]);
			}
		}
		var tileHeight = 10;
		if (y_element.length > 20) {
			tileHeight = chrHeight / y_element.length;
		}
		var yChr15 = d3.scaleBand().domain(y_element).range([chrTop, chrTop + chrHeight]);
		var yAxisChr15 = d3.axisLeft(yChr15).tickFormat(function (d) { return d; });

		// legend
		var states = ["TssA", "TssAFlnk", "TxFlnk", "Tx", "Tx/Wk", "EnhG", "Enh", "ZNF/Rpts", "Het", "TssBiv", "BivFlnk", "EnhBiv", "ReprPC", "ReprPCWk", "Quies"];
		legData = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
		var legendChr15 = base.selectAll(".legendChr15")
			.data(legData)
			.enter()
			.append("g").attr("class", "legend");
		// The color box legend is either a single vertical column
		if (y_element.length > 10) {
			var legHead = chrTop + y_element.length * tileHeight / 2 - 8 * 7.5;
			legendChr15.append("rect")
				.attr("x", width + 10)
				.attr("y", function (d) { return legHead + d * 8; })
				.attr("width", 20)
				.attr("height", 8)
				.style("fill", function (d) { return Chr15colors[d] })
				.style("stroke", "grey")
				.style("stroke-width", "0.5");
			legendChr15.append("text")
				.attr("text-anchor", "start")
				.attr("x", width + 10 + 22)
				.attr("y", function (d) { return legHead + d * 8 + 8; })
				.text(function (d) { return states[d] })
				.style("font-size", "9px");
		} else {
		    legHead = chrTop + y_element.length * tileHeight / 2 - 8 * 4;
			legendChr15.append("rect")
				.attr("x", function (d) { if (d < 7) { return width + 10; } else { return width + 70; } })
				.attr("y", function (d) { if (d < 7) { return legHead + d * 8; } else { return legHead + (d - 7) * 8 } })
				.attr("width", 20)
				.attr("height", 8)
				.style("fill", function (d) { return Chr15colors[d] })
				.style("stroke", "grey")
				.style("stroke-width", "0.5");
			legendChr15.append("text")
				.attr("text-anchor", "start")
				.attr("x", function (d) { if (d < 7) { return width + 10 + 22; } else { return width + 70 + 22; } })
				.attr("y", function (d) { if (d < 7) { return legHead + d * 8 + 8; } else { return legHead + (d - 7) * 8 + 8 } })
				.text(function (d) { return states[d] })
				.style("font-size", "9px");
		}

		// plot rect
		svg.selectAll("rect.chr").data(plotData.Chr15).enter().append("g")
			.insert('rect').attr('class', 'cell').attr("class", "Chr15rect")
			.attr("x", function (d) {
				return x(d[1]);
			})
			.attr("width", function (d) {
				return x(d[2]) - x(d[1]);
			})
			.attr("height", tileHeight)
			.attr('y', function (d) { return yChr15(d[0]) })
			.attr("fill", function (d) {
				return Chr15colors[d[3] - 1];
			})
			.on("mousemove", function () {
				var mousex = d3.mouse(this)[0];
				vertical.attr("x", mousex);
			})
			.on("mouseover", function () {
				var mousex = d3.mouse(this)[0];
				vertical.attr("x", mousex).style("stroke", "grey");
			})
			.on("mouseout", function () {
				vertical.style("stroke", "transparent")
			});

		// label - the y 
		base.append("text").attr("text-anchor", "middle")
			.attr("transform", "translate(" + (-margin.left / 2 - 15) + "," + (chrTop + (y_element.length * tileHeight) / 2) + ")rotate(-90)")
			.text("Chromatin state");
		lastXAxisGroup.call(xAxis); // clear ticks on prev axis
		lastXAxisGroup = base.append("g").attr("class", "x axis Chr15")
			.attr("transform", "translate(0," + (chrTop + y_element.length * tileHeight) + ")")
			.call(xLastAxis);

		xAxisLabel = "chr15";
		lastXAxisGroup.selectAll('text').style('font-size', '11px');

		if (y_element.length > 30) {
			base.append("g").attr("class", "y axis").call(yAxisChr15).selectAll("text").remove();
		} else {
			base.append("g").attr("class", "y axis").call(yAxisChr15)
				.selectAll("text").attr("transform", "translate(-5,0)").style("font-size", "10px");
		}
		for (let i = 0; i < y_element.length; i++) {
			base.append("rect").attr("x", -10).attr("y", yChr15(y_element[i]))
				.attr("width", 10).attr("height", tileHeight)
				.attr("fill", chr15gcol[i]);
		}
		EIDlegend(y_element);
	}

	/*---------------------------------------------
		| Plot eQTLs
		---------------------------------------------*/
	if (eqtlplot == 1) {
		if (plotData["eqtl"].length == 0) {
			base.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (width / 2) + "," + (height + margin.bottom - 30) + ")")
				.text("No eQTL of selected tissues exists in this region.")
				.style('font-family', 'sans-serif');
		} else {
			xAxisLabel = "eqtl";
			plotData.eqtl.forEach(function (d) {
				d[11] = +d[11]; //pos
				d[5] = +d[5]; //p
				d[7] = +d[7]; //FDR
				d[13] = +d[13]; //eqtlMapFilt
			});
			eqtlgenes = d3.set(plotData.eqtl.map(function (d) { return d[12]; })).values();
			var tissue = d3.set(plotData.eqtl.map(function (d) { return d[2]; })).values();

			// eqtl color and DB
			var db = {};
			for (i = 0; i < tissue.length; i++) {
				eQTLcolors[tissue[i]] = eqtlcols[Math.round(i * eqtlcols.length / tissue.length)];
				var temp;
				plotData.eqtl.forEach(function (d) { if (d[2] == tissue[i]) { temp = d[1] } });
				db[tissue[i]] = temp;
			}

			// legend
			legData = [];
			for (i = 0; i < tissue.length; i++) {
				legData.push(i);
			}
			var legendEqtl = base.selectAll(".legendEqtl")
				.data(legData)
				.enter()
				.append("g").attr("class", "legend")
			legendEqtl.append("circle")
				.attr("r", 3.5)
				.attr("cx", width + 10)
				.attr("cy", function (d) { return eqtlTop + 10 + d * 10 })
				.style("fill", function (d) { return eQTLcolors[tissue[d]] });
			legendEqtl.append("text")
				.attr("text-anchor", "start")
				.attr("x", width + 15)
				.attr("y", function (d) { return eqtlTop + 13 + d * 10; })
				.text(function (d) { return db[tissue[d]] + " " + tissue[d] })
				.style("font-size", "10px");

			// plot eQTLs per gene
			for (i = 0; i < eqtlgenes.length; i++) {
				y = d3.scaleLinear().range([eqtlTop + 55 * i + 50, eqtlTop + 55 * i]);
				yAxis = d3.axisLeft(y).ticks(4);
				var yMax = d3.max(plotData.eqtl, function (d) { return -Math.log10(d[5]) })
				if (yMax == undefined) { yMax = d3.max(plotData.eqtl, function (d) { return -Math.log10(d[7]) }) }
				y.domain([0, yMax + 0.5]);
				svg.selectAll("dot").data(plotData.eqtl.filter(function (d) { if (d[12] === eqtlgenes[i]) { return d } })).enter()
					.append("circle").attr("class", "eqtldot")
					.attr("r", 3.5)
					.attr("cx", function (d) { return x(d[11]); })
					.attr("cy", function (d) { if (d[5] >= 0) { return y(-Math.log10(d[5])); } else { return y(-Math.log10(d[7])); } })
					.style("fill", function (d) {
						if (d[13] == 0) {
							return "grey";
						} else {
							return eQTLcolors[d[2]]
						}
					})
					.on("click", function () {

					});
				var gene_font_size = '9px'
				if (eqtlgenes[i].length > 6) { gene_font_size = '7px' }
				base.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + (-margin.left / 2) + "," + (eqtlTop + i * 55 + 25) + ")rotate(-90)")
					.text(eqtlgenes[i])
					.style("font-size", gene_font_size);
				if (i == eqtlgenes.length - 1) {
					lastXAxisGroup.call(xAxis); // clear ticks on prev axis
					lastXAxisGroup = base.append("g").attr("class", "x axis eqtlend")
						.attr("transform", "translate(0," + (eqtlTop + 55 * i + 50) + ")")
						.call(xLastAxis);
					lastXAxisGroup.selectAll('text').style('font-size', '11px');
				} else {
					base.append("rect")
						.attr("x", 0).attr("y", y(0))
						.attr("width", width).attr("height", 0.3)
						.style("fill", "grey");
				}
				base.append("g").attr("class", "y axis").call(yAxis)
					.selectAll('text')
					.style('font-size', '11px');
			}

			// labels
			base.append("text").attr("text-anchor", "middle")
				.attr("transform", "translate(" + (-margin.left / 2 - 15) + "," + (eqtlTop + eqtlHeight / 2) + ")rotate(-90)")
				.text("eQTL -log10 P-value")
				.style("font-size", "10px");
		}
	}

	/*---------------------------------------------
		| Plot chromatin interactions
		---------------------------------------------*/
	if (ciplot == 1) {
		if (plotData["citypes"].length > 0) {
			xAxisLabel = "ci";
			plotData.ci.forEach(function (d) {
				d[0] = +d[0]; //start1
				d[1] = +d[1]; //end1
				d[2] = +d[2]; //start2
				d[3] = +d[3]; //end2
				d[4] = +d[4]; //FDR
				d[8] = +d[8]; //y
			});
			var minFDR = d3.min(plotData.ci, function (d) { if (d[4] > 0) { return d[4]; } });
			plotData.ci.forEach(function (d) {
				if (d[4] == 0) {
					d[4] = minFDR;
				}
			});
			var cicolor = d3.scaleLinear().domain([0, d3.max(plotData.ci, function (d) { return -Math.log10(d[4]) })]).range(["pink", "red"]);
			var cur_height = 0;

			// plot chromatin interaction per data set
			for (let i = 0; i < plotData["citypes"].length; i++) {
				var types = plotData.citypes[i];
				types = types.split(":");
				var max_y = plotData.ciheight[i];
				var tmp_height = 0;
				if (max_y * ci_cellsize + 10 < 30) {
					tmp_height = 30;
				} else {
					tmp_height = max_y * ci_cellsize + 10;
				}

				y = d3.scaleLinear().range([ciTop + 5 * i + cur_height + tmp_height, ciTop + 5 * i + cur_height]);
				yAxis = d3.axisLeft(y).ticks(0);
				y.domain([max_y + 1, 0]);

				svg.selectAll("rect.ci1").data(plotData.ci.filter(function (d) { if (d[5] == types[0] && d[6] == types[1] && d[7] == types[2]) { return d; } })).enter()
					.insert("rect").attr("class", "cirect1")
					.attr("x", function (d) {
						if (x(d[0]) > width) { return width }
						else { return x(d[0]) }
					})
					.attr("y", function (d) { return y(d[8]) })
					.attr("width", function (d) {
						return x(d[1]) - x(d[0]);
					})
					.attr("height", ci_cellsize)
					.attr("fill", function (d) {
						return cicolor(-Math.log10(d[4]))
					})
					.attr("stroke", function () {
						return "grey";
					})
					.attr("stroke-width", 0.1);
				svg.selectAll("rect.ci2").data(plotData.ci.filter(function (d) { if (d[5] == types[0] && d[6] == types[1] && d[7] == types[2]) { return d; } })).enter()
					.insert("rect").attr("class", "cirect2")
					.attr("x", function (d) {
						if (x(d[2]) > width) { return width }
						else { return x(d[2]) }
					})
					.attr("y", function (d) { return y(d[8]) })
					.attr("width", function (d) {
						return x(d[3]) - x(d[2]);
					})
					.attr("height", ci_cellsize)
					.attr("fill", function (d) {
						return cicolor(-Math.log10(d[4]))
					})
					.attr("stroke", function () {
						return "grey"
					})
					.attr("stroke-width", 0.1);

				svg.selectAll("rect.ci").data(plotData.ci.filter(function (d) { if (d[5] == types[0] && d[6] == types[1] && d[7] == types[2] && Math.abs(d[2] - d[1]) > 1) { return d; } })).enter()
					.insert("rect").attr("class", "cirect")
					.attr("x", function (d) {
						return x(d[1]);
					})
					.attr("y", function (d) { return y(d[8]) + ci_cellsize * 0.5 })
					.attr("width", function (d) {
						return x(d[2]) - x(d[1]);
					})
					.attr("height", 0.8)
					.attr("fill", "grey");

				svg.append("text").attr("text-anchor", "start")
					.attr("transform", "translate(10," + (ciTop + cur_height + 5 * i + 2) + ")")
					.text(types.join(" "))
					.style("font-size", "8.5px").style("font-family", "sans-serif");
				svg.append("g").attr("class", "y axis").call(yAxis)
					.selectAll('text').attr("transform", "translate(-5,0)").style('font-size', '11px');
				if (i == plotData.citypes.length - 1) {
					lastXAxisGroup.call(xAxis); // clear ticks on prev axis
					lastXAxisGroup = base.append("g").attr("class", "x axis ci")
						.attr("transform", "translate(0," + (ciTop + cur_height + 5 * i + tmp_height) + ")")
						.call(xLastAxis);
					lastXAxisGroup.selectAll('text').style('font-size', '11px');
				} else {
					svg.append("rect")
						.attr("x", 0).attr("y", y(max_y + 1))
						.attr("width", width).attr("height", 0.3)
						.style("fill", "grey");
				}
				cur_height += tmp_height;
			}

			// plot enhancer and promoter if annoated
			if (plotData["cieid"].length > 0) {
				xAxisLabel = "cireg";
				plotData.cireg.forEach(function (d) {
					d[0] = +d[0]; //start
					d[1] = +d[1]; //end
				});
				var cieid = plotData["cieid"];
				cieid.forEach(function (d) {
					if (eid.indexOf(d) < 0) { eid.push(d) }
				});
				chr15gcol = [];
				for (var i = 0; i < Chr15eid.length; i++) {
					if (cieid.indexOf(Chr15eid[i]) >= 0) {
						chr15gcol.push(Chr15GroupCols[i]);
					}
				}
				tileHeight = ciregHeight / cieid.length;

				// legend
				base.append("rect").attr("x", width + 20).attr("y", ciregTop + 5)
					.attr("width", 20).attr("height", 5).attr("fill", "orange");
				base.append("text").attr("x", width + 45).attr("y", ciregTop + 10)
					.text("Enhancers").style("font-size", "10px");
				base.append("rect").attr("x", width + 20).attr("y", ciregTop + 15)
					.attr("width", 20).attr("height", 5).attr("fill", "green");
				base.append("text").attr("x", width + 45).attr("y", ciregTop + 20)
					.text("Promoters").style("font-size", "10px");
				base.append("rect").attr("x", width + 20).attr("y", ciregTop + 25)
					.attr("width", 20).attr("height", 5).attr("fill", "blue");
				base.append("text").attr("x", width + 45).attr("y", ciregTop + 30)
					.text("Dyadic").style("font-size", "10px");

				var yCireg = d3.scaleBand().domain(cieid).range([ciregTop, ciregTop + ciregHeight]);
				var yAxisCireg = d3.axisLeft(yCireg).tickFormat(function (d) { return d; });
				svg.selectAll("rect.cireg").data(plotData.cireg).enter().append("g")
					.insert('rect').attr("class", "ciregrect")
					.attr('x', function (d) {
						return x(d[0]);
					})
					.attr('y', function (d) { return yCireg(d[3]) })
					.attr("width", function (d) {
						return x(d[1]) - x(d[0]);
					})
					.attr("height", tileHeight)
					.attr("fill", function (d) {
						if (d[2] == "enh") { return "orange" }
						else if (d[2] == "prom") { return "green" }
						else { return "blue" }
					});
				if (cieid.length > 30) {
					base.append("g").attr("class", "y axis").call(yAxisCireg)
						.selectAll('text').remove();
				} else {
					base.append("g").attr("class", "y axis").call(yAxisCireg)
						.selectAll('text').attr("transform", "translate(-5,0)").style('font-size', '11px');
				}
				for (let i = 0; i < cieid.length; i++) {
					svg.append("rect").attr("x", -10).attr("y", yCireg(cieid[i]))
						.attr("width", 10).attr("height", tileHeight)
						.attr("fill", chr15gcol[i]);
				}
				lastXAxisGroup.call(xAxis); // clear ticks on prev axis
				lastXAxisGroup = base.append("g").attr("class", "x axis cireg")
					.attr("transform", "translate(0," + (ciregTop + ciregHeight) + ")")
					.call(xLastAxis);
				lastXAxisGroup.selectAll('text').style('font-size', '11px');
				base.append("text").attr("text-anchor", "middle")
					.attr("transform", "translate(" + (-margin.left / 2 - 15) + "," + (ciregTop + ciregHeight / 2) + ")rotate(-90)")
					.text("Regulatory elements");

			}
		}
	}
	base.append("text").attr("text-anchor", "middle")
		.attr("transform", "translate(" + width / 2 + "," + (height + 35) + ")")
		.text("Chromosome " + chrom);

	// add style to text
	base.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
	base.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
	base.selectAll('text').style('font-family', 'sans-serif');

	// x-direction only zoom handler
	function zoomed() {
		// get the current transform and apply to x axis
		var new_x_scale = d3.event.transform.rescaleX(x);  

		if (xAxisLabel != "genes") {
			base.select(".x.axis.genes").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "GWAS") {
			base.select(".x.axis.GWAS").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "CADD") {
			base.select(".x.axis.CADD").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "GWAS") {
			base.select(".x.axis.RDB").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "Chr15") {
			base.select(".x.axis.Chr15").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "eqtl") {
			base.select(".x.axis.eqtlend").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "ci") {
			base.select(".x.axis.ci").call(xAxis.scale(new_x_scale));
		}
		if (xAxisLabel != "cireg") {
			base.select(".x.axis.cireg").call(xAxis.scale(new_x_scale));
		}
		lastXAxisGroup.call(xLastAxis.scale(new_x_scale)); // enable ticks on the very last axis

		// Rescale x positions and rectangle widths
		svg.selectAll(".GWASdot").attr("cx", function (d) { return new_x_scale(d[2]); });
		svg.selectAll(".GWASnonLD").attr("cx", function (d) { return new_x_scale(d[1]); });
		svg.selectAll(".KGSNPs").attr("x", function (d) { return new_x_scale(d[2]); });
		svg.selectAll(".leadSNPs").attr("cx", function (d) { return new_x_scale(d[2]); });
		svg.selectAll(".CADDdot").attr("cx", function (d) { return new_x_scale(d[2]); });
		svg.selectAll(".RDBdot").attr("cx", function (d) { return new_x_scale(d[2]); });
		svg.selectAll(".genesrect").attr("x", function (d) {
			return new_x_scale(d[2]);
		})
			.attr("width", function (d) {
				return new_x_scale(d[3]) - new_x_scale(d[2]);
			});
		svg.selectAll(".geneName")
			.attr("x", function (d) {
				return new_x_scale(((d[3] - d[2]) / 2) + d[2]);
			});
		svg.selectAll(".exons").attr("x", function (d) {
			return new_x_scale(d[6]);
		})
			.attr("width", function (d) {
				return new_x_scale(d[7]) - new_x_scale(d[6]);
			});
		svg.selectAll(".Chr15rect")
			.attr("x", function (d) {
				return new_x_scale(d[1]);
			})
			.attr("width", function (d) {
				return new_x_scale(d[2]) - new_x_scale(d[1]);
			});
		svg.selectAll(".eqtldot").attr("cx", function (d) { return new_x_scale(d[11]) });
		svg.selectAll(".cirect1")
			.attr("x", function (d) {
				return new_x_scale(d[0]);
			})
			.attr("width", function (d) {
				return new_x_scale(d[1]) - new_x_scale(d[0]);
			});
		svg.selectAll(".cirect2")
			.attr("x", function (d) {
				return new_x_scale(d[2]);
			})
			.attr("width", function (d) {
				return new_x_scale(d[3]) - new_x_scale(d[2]);
			});
		svg.selectAll(".cirect")
			.attr("x", function (d) {
				return new_x_scale(d[1]);
			})
			.attr("width", function (d) {
				return new_x_scale(d[2]) - new_x_scale(d[1]);
			});

		svg.selectAll(".ciregrect")
			.attr("x", function (d) {
				return new_x_scale(d[0]); 
			})
			.attr("width", function (d) {
				return new_x_scale(d[1]) - new_x_scale(d[0]);
			})
		svg.selectAll('.axis').selectAll('path').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('.axis').selectAll('line').style('fill', 'none').style('stroke', 'grey');
		svg.selectAll('text').style('font-family', 'sans-serif');
	}

	// Plot Clear button
	d3.select('#plotclear').on('click', function () {
		// simply restore the identity transform to the zoom setting 
		// for the top level enclosing g element
		svg.transition().duration(750).call(zoom.transform, d3.zoomIdentity);
	});
};

function geneOver(genes, x, width) {
	var tg = genes;

	for (let i = 1; i < tg.length; i++) {
		var temp = tg.filter(function (d2) {
			if ((d2[2] <= tg[i][2] && d2[3] >= tg[i][2] && d2[3] <= tg[i][3])
				|| (d2[2] <= tg[i][2] && d2[3] >= tg[i][3])
			) { return d2; }
			else if (x((d2[3] + d2[2] + d2[1].length * 12) / 2) >= x((tg[i][3] + tg[i][2]) / 2) - ((tg[i][1].length * 12) / 2)
				&& x((d2[3] + d2[2] + d2[1].length * 12) / 2) <= x((tg[i][3] + tg[i][2]) / 2) + ((tg[i][1].length * 12) / 2)
			) { return d2 }
		})
		if (temp.length > 1) {
			var yall = [];
			for (var j = 0; j < temp.length; j++) {
				if (temp[j][1] != tg[i][1]) {
					yall.push(temp[j][6]);
				}
			}
			tg[i][6] = getMinY(yall);
		} else {
			tg[i][6] = 1;
		}
	}

	return tg;
}

function getMinY(y) {
	y.sort(function (a, b) { return a - b });
	var miny = 1;
	if (Math.min.apply(null, y) > 1) { return 1; }
	if (y.length == 1) { return y[0] + 1; }
	for (var l = 1; l < y.length; l++) {
		if (y[l] - y[l - 1] > 1) {
			miny = y[l - 1] + 1;
			break;
		} else {
			miny = y[l] + 1;
		}
	}
	return miny;
}

function RDBlegend() {
	var margin = { top: 20, right: 20, bottom: 20, left: 20 },
		width = 600,
		height = 350;
	var svg = d3.select('#RDBlegend').append('svg')
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append('g').attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	let FileName = "RDB.txt";
	$.ajax({
		url: pageState.get('subdir') + '/' + pageState.get('page') + '/legendText',
		type: 'POST',
		data: {
			fileNames: [FileName]
		},
		error: function () {
			alert("JobQuery get file contents error");
			return;
		},
		success: function (data) {
			data = data[FileName]
			//console.log(data);
			svg.append("text")
				.attr("x", 0)
				.attr("y", 0)
				.text("RegulomeDB Categorical Scores")
				.style("font-size", "14px");
			var curHeight = 20;
			svg.append("rect")
				.attr("x", 0)
				.attr("y", 6)
				.attr("height", 1)
				.attr("width", 550);
			svg.append("rect")
				.attr("x", 0)
				.attr("y", curHeight + 5)
				.attr("height", 1)
				.attr("width", 550);

			svg.append("text")
				.attr("x", 5)
				.attr("y", curHeight)
				.text("Category")
				.style("font-size", "13px");
			svg.append("text")
				.attr("x", (500 + 60) / 2)
				.attr("y", curHeight)
				.text("Description")
				.style("font-size", "13px");

			data.forEach(function (d) {
				if (d.Category == "") {
					curHeight += 5;
				}
				svg.append("text")
					.attr("x", 5)
					.attr("y", curHeight + 15)
					.text(d.Category)
					.style("font-size", "13px");
				svg.append("text")
					.attr("x", 60)
					.attr("y", curHeight + 15)
					.text(d.Description)
					.style("font-size", "13px");
				curHeight += 15;

			});
			svg.append("rect")
				.attr("x", 0)
				.attr("y", curHeight + 5)
				.attr("height", 1)
				.attr("width", 550);
			svg.selectAll('text').style("font-family", "sans-serif");
		}
	});
}

function EIDlegend(cells) {
	var margin = { top: 20, right: 20, bottom: 20, left: 20 },
		width = 800,
		height = 30 + 15 * cells.length;
	var svg = d3.select('#EIDlegend').append('svg')
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append('g').attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	let FileName = "EID.txt";
	$.ajax({
		url: pageState.get('subdir') + '/' + pageState.get('page') + '/legendText',
		type: 'POST',
		data: {
			fileNames: [FileName]
		},
		error: function () {
			alert("JobQuery get file contents error");
			return;
		},
		success: function (data) {
			data = data[FileName]

			svg.append("text")
				.attr("x", 0)
				.attr("y", 0)
				.text("Epigenome ID")
				.style("font-size", "14px");
			var curHeight = 20;
			svg.append("rect")
				.attr("x", 0)
				.attr("y", 6)
				.attr("height", 1)
				.attr("width", 750);
			svg.append("rect")
				.attr("x", 0)
				.attr("y", curHeight + 3)
				.attr("height", 1)
				.attr("width", 750);

			svg.append("text")
				.attr("x", 5)
				.attr("y", curHeight)
				.text("EID")
				.style("font-size", "13px");
			svg.append("text")
				.attr("x", 50)
				.attr("y", curHeight)
				.text("Color")
				.style("font-size", "13px");
			svg.append("text")
				.attr("x", 120)
				.attr("y", curHeight)
				.text("Group")
				.style("font-size", "13px");
			svg.append("text")
				.attr("x", 210)
				.attr("y", curHeight)
				.text("Anatomy")
				.style("font-size", "13px");
			svg.append("text")
				.attr("x", 370)
				.attr("y", curHeight)
				.text("Standerdized epigenome name")
				.style("font-size", "13px");

			data.forEach(function (d) {
				if (cells.indexOf(d.EID) >= 0) {
					svg.append("text")
						.attr("x", 5)
						.attr("y", curHeight + 15)
						.text(d.EID)
						.style("font-size", "13px");
					svg.append("rect")
						.attr("x", 50)
						.attr("y", curHeight + 4)
						.attr("width", 60)
						.attr("height", 15)
						.attr("fill", d.Color);
					svg.append("text")
						.attr("x", 50)
						.attr("y", curHeight + 15)
						.text(d.Color)
						.style("fill", function () { if (d.Color == "#000000") { return "white" } else { return "black" } })
						.style("font-size", "13px");
					svg.append("text")
						.attr("x", 120)
						.attr("y", curHeight + 15)
						.text(d.Group)
						.style("font-size", "13px");
					svg.append("text")
						.attr("x", 210)
						.attr("y", curHeight + 15)
						.text(d.Anatomy)
						.style("font-size", "13px");
					svg.append("text")
						.attr("x", 370)
						.attr("y", curHeight + 15)
						.text(d.Name)
						.style("font-size", "13px");
					curHeight += 15;

				}
			});
			svg.append("rect")
				.attr("x", 0)
				.attr("y", curHeight + 5)
				.attr("height", 1)
				.attr("width", 750);
			svg.selectAll('text').style("font-family", "sans-serif");
		}
	});
}

export function ImgDown(name, type) {
	$('#' + name + 'Data').val($('#' + name).html());
	$('#' + name + 'Type').val(type);
	$('#' + name + 'ID').val(pageState.get('id'));
	$('#' + name + 'FileName').val(name);
	$('#' + name + 'Dir').val('jobs');
	$('#' + name + 'Submit').trigger('click');
}
