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
	// var locusfile = $('#locusSumstat').val().length;
	table = $('#xqtlsAnalysis')[0];
	if (
		$('#locusSumstat').val().length === 0 ||
		(!$('#grch37').is(':checked') && !$('#grch38').is(':checked'))
		) {

		submit = false;
		$(table.rows[0].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please upload a summary statistics file for the locus of interest AND select if your input file is in GRCh37 or GRCh38 coordinates.</div>');
		$('#xqtlsSubmit').attr("disabled", true);
	} else {
		$(table.rows[0].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
		submit = true;
	}

	if($('#chrom').val().length==0 || $('#locusStart').val().length==0 || $('#locusEnd').val().length==0){
		submit = false;
		$(table.rows[1].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide chromosome, start and end position for the locus of interest.</div>');
		$('#xqtlsSubmit').attr("disabled", true);
	} else {
		$(table.rows[1].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
		submit = true;
	}

	if($("select[name='eqtlGtexv10Ds[]'] option:selected").length==0 && $("select[name='sqtlGtexv10Ds[]'] option:selected").length==0 && $("select[name='pqtl9Sun2023Ds[]'] option:selected").length==0){
		submit = false;
		$(table.rows[5].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please select at least one dataset.</div>');
		$('#xqtlsSubmit').attr("disabled", true);
	} else {
		$(table.rows[5].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
		submit = true;
	}

	if($('#coloc').is(':checked')){
		if($('#pp4').val().length==0 ){
			submit = false;
			$(table.rows[2].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide the threshold for PP4 cutoff. </div>');
			$('#xqtlsSubmit').attr("disabled", true); 
		} else {
			$(table.rows[2].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
		}
	} else {
		$(table.rows[2].cells[2]).html('<div class="alert alert-info" style="padding-bottom: 10; padding-top: 10;">Colocalization is not selected.</div>');
	}

	if($('#lava').is(':checked')){
		if($('#phenotype').val().length==0){
			submit = false;
			$(table.rows[3].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide phenotype for LAVA analysis.</div>');
			$('#xqtlsSubmit').attr("disabled", true); 
		} else {
			$(table.rows[3].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
		}
			
	} else {
		$(table.rows[3].cells[2]).html('<div class="alert alert-info" style="padding-bottom: 10; padding-top: 10;">LAVA is not selected.</div>');
	}

	if ($('#cases').val().length==0 || $('#totalN').val().length==0){
		submit = false;
		$(table.rows[4].cells[2]).html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please provide number of cases and number of totalN.</div>');
		$('#xqtlsSubmit').attr("disabled", true); 
	} else {
		$(table.rows[4].cells[2]).html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK.</div>');
	}



	if(submit){$('#xqtlsSubmit').attr("disabled", false);}
	else{$('#xqtlsSubmit').attr("disabled", true);}
};

export default XQTLSSetup;