var prefix = "xqtls";
var id = ""

import { XqtlsState as pageState}  from "../pages/pageStateComponents.js";
import { deleteJobs } from './helpers.js';
export const XQTLSSetup = function(){
    const page = pageState.get("page");
    id = pageState.get("id");
    const subdir = pageState.get("subdir");
    var status  = pageState.get("status");
    updateQueryHistory();
	$('#refreshTable').on('click', function(){
		updateQueryHistory();
	});

	// Get SNP2GENE job IDs
	$.ajax({
		url: pageState.get("subdir")+"/xqtls/getQTLsAnalysisIDs",
		type: "POST",
		error: function(){
			alert("error for getQTLsAnalysisIDs");
		},
		success: function(data){
			$('#paramsID').html('<option value=0 selected>None</option>');
			data.forEach(function(d){
				$('#paramsID').append('<option value='+d.jobID+'>'+d.jobID+' ('+d.title+')</option>');
			})
		}
	});

    if(status.length==0 || status=="getJob") {
        // var id = jobID;
        lavaSummaryTable(subdir, page, prefix, id);
        colocSummaryTable(subdir, page, prefix, id);
    };

	CheckAll();

	$('.multiSelect a').on('click',function(){
		var selection = $(this).siblings("select").attr("id");
		if($(this).hasClass('all')){
			$("#"+selection+" option").each(function(){
				$(this).prop('selected', true);
			});
		}else if($(this).hasClass('clear')){
			$("#"+selection+" option").each(function(){
				$(this).prop('selected', false);
			});
		}
		CheckAll();
	});
    
    $('#deleteJob').on('click', function(){
        deleteJobs(pageState.get("subdir"), pageState.get("page"), updateQueryHistory)
    });
}

const updateQueryHistory = function(){
    const subdir = pageState.get("subdir");
    $.getJSON( subdir + "/xqtls/getQTLSHistory", function( data ){
        var items = '<tr><td colspan="5" style="text-align: center;">No Jobs Found</td></tr>';
        if(data.length){
            items = '';
            $.each( data, function( key, val ) {

                if (val.parent != null && val.parent.removed_at != null) {
                    val.parent = null;
                }

                if (val.status == "OK") {
                    var status = '<a href="'+subdir+'/xqtls/'+val.jobID+'#xqtlTables">load results</a>';
                }
                else {
                    status = val.status;
                }

                items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title+"</td><td>"
                    +val.created_at+"</td><td>"+status+"</td>"
                    +'<td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
                    +val.jobID+'"/></td></tr>';
            });
        }
        // Put list in table
        $('#queryhistory table tbody')
            .empty()
            .append(items);
    });
}

const lavaSummaryTable = function(){
    const file = "lava_bivar_results_all_datasets_significant.txt";
    id = pageState.get("id");

	$('#lavaTable').DataTable({
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
				header: "locus:chr:phen1:rho:rho.lower:rho.upper:r2:r2.lower:r2.upper:p:dataset:p.adjust:symbol"
			}
		},
		error: function () {
			alert("LAVA table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
}

const colocSummaryTable = function(){
    const file = "coloc_results_filtered.txt";
    id = pageState.get("id");
	$('#colocTable').DataTable({
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
				header: "tissue:gene:nsnps:PP.H0.abf:PP.H1.abf:PP.H2.abf:PP.H3.abf:PP.H4.abf:symbol"
			}
		},
		error: function () {
			alert("GenomicRiskLoci table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
}

export const CheckAll = function() {
	var submit = true;
	var table;
	table = $('#xqtlsAnalysis')[0];

	if (
		$('#locusSumstat').val().length === 0 ||
		!($('#grch37').is(':checked') || $('#grch38').is(':checked'))
		) {
		submit = false;
		$(table.rows[0].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please upload a summary statistics file for the locus of interest AND indicate if your input file is in GRCh37 or GRCh38 coordinates.</div>');
	} else {
		$(table.rows[0].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
	}

	if($('#chrom').val().length==0 || $('#locusStart').val().length==0 || $('#locusEnd').val().length==0){
		submit = false;
		$(table.rows[1].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide chromosome, start and end position for the locus of interest. The start and end positions need to be in the same genomic build as specified above. </div>');
	} else {
		$(table.rows[1].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
	}

	if($("select[name='eqtlGtexv10Ds[]'] option:selected").length==0 && $("select[name='eqtlMetabrainDs[]'] option:selected").length==0 && $("select[name='sceqtlbryois2022BrainDs[]'] option:selected").length==0 && $("select[name='sceqtljerber2021DopaminergicDs[]'] option:selected").length==0 && $("select[name='sqtlGtexv10Ds[]'] option:selected").length==0){
		submit = false;
		$(table.rows[5].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please select at least one dataset.</div>');
	} else {
		$(table.rows[5].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
	}

	if($('#coloc').is(':checked')){
		if($('#pp4').val().length==0 ){
			submit = false;
			$(table.rows[2].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide the threshold for PP4 cutoff. </div>');
		} else {
			$(table.rows[2].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. Colocalization is selected.</div>');
		}
	} else {
		$(table.rows[2].cells[2]).html('<div class="alert alert-info" style="padding-bottom: 10; padding-top: 10;">Colocalization is not selected. Click the checkbox to select colocalization. </div>');
	}

	if($('#lava').is(':checked')){
		if($('#phenotype').val().length==0){
			submit = false;
			$(table.rows[3].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide phenotype for LAVA analysis.</div>');
		} else {
			$(table.rows[3].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. LAVA is selected. </div>');
		}
			
	} else {
		$(table.rows[3].cells[2]).html('<div class="alert alert-info" style="padding-bottom: 10; padding-top: 10;">LAVA is not selected. Click the checkbox to select LAVA.</div>');
	}

	if ($('#cases').val().length==0 || $('#totalN').val().length==0){
		submit = false;
		$(table.rows[4].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide number of cases and the total number of sample size. If the trait is quantiative, please enter NA for Cases. The total number of sample size has to be an integer..</div>');
	} else {
		$(table.rows[4].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
	}



	if(submit){$('#xqtlsSubmit').attr("disabled", false);}
	else{$('#xqtlsSubmit').attr("disabled", true);}
};

export function loadParams(){
	var paramsID = $('#paramsID').val();
	if(paramsID > 0){
		$.ajax({
			url: pageState.get('subdir')+"/xqtls/loadParams",
			type: "POST",
			data: {
				jobID: paramsID
			},
			error: function(){
				alert("error for loadParams");
			},
			success: function(data){
				data = JSON.parse(data);
				setParams(data);
			}
		})
	}
}

function setParams(data){

	// build
	if(data.build=="GRCh37"){$('#grch37').prop('checked', true);}
	else{$('#grch37').prop('checked', false);}

	if(data.build=="GRCh38"){$('#grch38').prop('checked', true);}
	else{$('#grch38').prop('checked', false);}

	// chromosome, start, end
	$('#chrom').val(data.chrom);
	$('#locusStart').val(data.start);
	$('#locusEnd').val(data.end);

	// coloc parameterizations
	if(data.coloc=="1"){$('#coloc').prop('checked', true);}
	else{$('#coloc').prop('checked', false);}

	if(data.pp4!="NA"){$('#pp4').val(data.pp4)}
	else{$('#pp4').val('')}

	if(data.colocGene!="NA"){$('#colocGene').val(data.colocGene)}
	else{$('#colocGene').val('')}

	// lava parameterization
	if(data.lava=="1"){$('#lava').prop('checked', true);}
	else{$('#lava').prop('checked', false);}

	if(data.phenotype!="NA"){$('#phenotype').val(data.phenotype)}
	else{$('#phenotype').val('')}

	if(data.lavaGene!="NA"){$('#lavaGene').val(data.lavaGene)}
	else{$('#lavaGene').val('')}

	// other parameters
	$('#cases').val(data.cases);
	$('#totalN').val(data.totalN);

	// datasets
	if(data.datasets != "NA"){
		let dsList = data.datasets.split(":");

		// clear previous selections
		$('select[id$="Ds"] option').prop('selected', false);

		dsList.forEach(function (ds) {

			let qtlType = ds.split("-")[0].toLowerCase();

			let database = ds.split("-")[1];
			if (database == "gtex_v10") {
				database = "Gtexv10";
			}

			let baseTissue = ds.split("-")[2]

			let selectId = "#" + qtlType + database + "Ds";

			console.log(selectId);

			$(selectId + ' option[value="' + ds + '"]')
				.prop('selected', true);

		});
		CheckAll();
		// if(data.eqtlMapSig=="1"){$('#sigeqtlCheck').prop("checked", true);}
		// else{$('#sigeqtlCheck').prop("checked", false);$('#eqtlP').val(data.eqtlMapP);}
	}

	CheckAll();
}

export default XQTLSSetup;