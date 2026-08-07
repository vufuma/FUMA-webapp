import { loadResults, DownloadFiles } from './cell_results.js';
import { CellTypeState as pageState}  from "../pages/pageStateComponents.js";
import { deleteJobs } from './helpers.js';


export const CellTypeSetup = function(){
	// hide submit buttons for imgDown
	$('.ImgDownSubmit').hide();
	$('#cellSubmit').attr("disabled", true);
	$('#resultSide').hide();

    // hash activate
	var hashid = window.location.hash;
	if(hashid=="" && pageState.get("status").length==0){
		$('a[href="#newJob"]').trigger('click');
	}else if(hashid==""){
		$('a[href="#result"]').trigger('click');
	}else{
		$('a[href="'+hashid+'"]').trigger('click');
	}

	// download file selection
	$('.allfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			if(!$(this).is(':disabled')){
				$(this).prop("checked", true);
			}
		});
		DownloadFiles();
	});
	$('.clearfiles').on('click', function(){
		$('#downFileCheck input').each(function(){
			$(this).prop("checked", false);
		});
		DownloadFiles();
	});

	getJobList();
	$('#refreshTable').on('click', function(){
		getJobList();
	});

	// Get SNP2GENE job IDs
	$.ajax({
		url: pageState.get("subdir")+"/celltype/getS2GIDs",
		type: "POST",
		error: function(){
			alert("error for getS2GIDs");
		},
		success: function(data){
			$('#s2gID').html('<option value=0 selected>None</option>');
			data.forEach(function(d){
				$('#s2gID').append('<option value='+d.jobID+'>'+d.jobID+' ('+d.title+')</option>');
			})
		},
		complete: function(){
			CheckInput();
		}
	})


	$('#deleteJob').on('click', function(){
		deleteJobs(pageState.get("subdir"), pageState.get("page"), getJobList)
	}
	)
	

	if(pageState.get("status").length>0){
		var jobStatus;
		$.get({
			url: pageState.get("subdir")+'/'+pageState.get("page")+'/checkJobStatus/'+pageState.get("id"),
			error: function(){
				alert("ERROR: checkJobStatus")
			},
			success: function(data){
				jobStatus = data;
			},
			complete: function(){
				if(jobStatus=="OK"){
					$('#resultSide').show();
					loadResults(pageState.get("id"));
					geneRankingTable();
				}
			}
		});
	}
};

export function CheckInput(){
	var check = true;
	var s2gID = $('#s2gID').val();
	var fileName = $('#genes_raw').val();
	var ds = $("#cellDataSets :selected").length;
	var magmaTable;
	magmaTable = $('#NewJobFiles')[0];
	var dataTable;
	dataTable = $('#SingleCellData')[0];

	// If all datasets are selected, not allow step 2 and step 3
	var all = $("#cellDataSets :not(:selected)").length;
	if(all==0){
		$('#step2').prop('checked', false).prop('disabled', true);
		$('#step3').prop('checked', false).prop('disabled', true);
	}else{
		$('#step2').prop('disabled', false);
		$('#step3').prop('disabled', false);
	}

	if(s2gID==0 && fileName.length==0){
		check = false;
		$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-ban"></i> Please either select SNP2GENE jobID or upload a file.</div></td>');
	}else{
		if(s2gID>0){
			var filecheck = false;
			$.ajax({
				url: pageState.get("subdir")+"/celltype/checkMagmaFile",
				type: 'POST',
				data: { jobID: s2gID },
				error: function(){alert("error from checkMagmaFile")},
				success: function(data){
					if(data==1){filecheck=true}
				},
				complete: function(){
					if(!filecheck){
						check = false;
						$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-ban"></i> The seleted SNP2GENE job does not have valid MAGMA output.</div></td>');
					}else if(fileName.length>0){
						$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-ban"></i> Both SNP2GENE job ID and upload file are provided. Selected SNP2GENE job will be used.</div></td>');
					}else{
						$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-check"></i> OK. The MAGMA gene analysis results will be obtained from the selected SNP2GENE job.</div></td>');
					}
				}
			});
		}else{
			if(fileName.endsWith(".genes.raw")){
				$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-check"></i> OK. The selected file will be uploaded.</div></td>');
			}else{
				check = false;
				$(magmaTable.rows[0].cells[1]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-ban"></i> The seleted file does not have extension "genes.raw".</div></td>');
			}
		}
	}
	if(ds==0){
		check = false;
		$(dataTable.rows[0].cells[1]).html('<td><div class="alert alert-danger" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-ban"></i> Please select at least one single-cell expression data set.</div></td>');
	} else{
		$(dataTable.rows[0].cells[1]).html('<td><div class="alert alert-success" style="display: table-cell; padding-top:0; padding-bottom:0;">'+'<i class="fa fa-check"></i> OK. '+ds+' single-cell expression data sets are selected.</div></td>');
	}

	if(check){$('#cellSubmit').attr("disabled", false);}
	else{$('#cellSubmit').attr("disabled", true);}
}

function getJobList(){
	$('#joblist table tbody')
		.empty()
		.append('<tr><td colspan="7" style="text-align:center;">Retrieving data</td></tr>');
	$.getJSON( pageState.get("subdir")+'/'+ pageState.get("page") +'/getJobList', function( data ){
		$('#jobCount').text(data.length);
		var items = '<tr><td colspan="7" style="text-align: center;">No Jobs Found</td></tr>';
		if(data.length){
			items = '';
			$.each( data, function( key, val ) {

				if (val.parent != null && val.parent.removed_at != null) {
					val.parent = null;
				}

				if(val.status == 'OK'){
					val.status = '<a href="'+pageState.get("subdir")+'/'+ pageState.get("page") +'/'+val.jobID+'">Go to results</a>';
				}
				items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title
					+"</td><td>"+(val.parent != null ? val.parent.jobID : '-')+"</td><td>"+(val.parent != null ? val.parent.title : '-')
					+"</td><td>"+val.created_at+"</td><td>"+val.status
					+'</td><td style="text-align: center;"><input type="checkbox" class="deleteJobCheck" value="'
					+val.jobID+'"/></td></tr>';
			});
		}

		// Put list in table
		$('#joblist table tbody')
			.empty()
			.append(items);
	})
    .fail(function() {
        console.log("Celltype getJobList error");
    });
}

function countJobs() {
	$.getJSON(pageState.get('subdir') + '/' + pageState.get('page') + '/getJobList', function (data) {
		$('#jobCount').text(data.length);
	});
}

// const geneRankingTable = function(){
// 	const file = "celltype_step1_allGeneRankingMetrics.txt";
// 	var id = pageState.get("id");
// 	$('#geneRankingTable').DataTable({
// 		"processing": true,
// 		serverSide: false,
// 		select: true,
// 		"ajax": {
// 			url: "DTfile",
// 			type: "POST",
// 			data: {
// 				jobID: id,
// 				prefix: pageState.get("prefix"),
// 				infile: file,
// 				header: "Dataset:Cell_type:fumaCelltype:ewce:cellex:cepo"
// 			}
// 		},
// 		error: function () {
// 			alert("Table error");
// 		},
// 		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
// 		"iDisplayLength": 10
// 	});
// }

const geneRankingTable = function(){
    const file = "celltype_step1_allGeneRankingMetrics.txt";
    var id = pageState.get("id");

    $('#geneRankingTable').DataTable({
        processing: true,
        serverSide: false,
        select: true,
        ajax: {
            url: "DTfile",
            type: "POST",
            data: {
                jobID: id,
                prefix: pageState.get("prefix"),
                infile: file,
                header: "Dataset:Cell_type:fumaCelltype:ewce:cellex:cepo"
            }
        },
        columnDefs: [{
            targets: [2, 3, 4, 5],   // fumaCelltype, ewce, cellex, cepo
            createdCell: function(td, cellData) {
                const p = parseFloat(cellData);

                if (isNaN(p)) return;

                $(td).css({
                    "background-color": p < 0.05 ? "#d4edda" : "#e9ecef",
                    "color": "#000"
                });
            }
        }],
        error: function () {
            alert("Table error");
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        iDisplayLength: 10
    });
}

export default CellTypeSetup;
