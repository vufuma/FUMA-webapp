var prefix = "flames";
var id = ""

import { FlamesState as pageState}  from "../pages/pageStateComponents.js";
import { deleteJobs } from './helpers.js';
export const FLAMESSetup = function(){
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
        url: pageState.get("subdir")+"/flames/getS2GIDs",
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
    });

    if(status.length==0 || status=="getJob") {
        // var id = jobID;
        flamesResultTable(subdir, page, prefix, id);
    };

    $('#deleteJob').on('click', function(){
            deleteJobs(pageState.get("subdir"), pageState.get("page"), updateQueryHistory)
    });

}

const updateQueryHistory = function(){
    const subdir = pageState.get("subdir");
    $.getJSON( subdir + "/flames/getFLAMESHistory", function( data ){
        var items = '<tr><td colspan="5" style="text-align: center;">No Jobs Found</td></tr>';
        if(data.length){
            items = '';
            $.each( data, function( key, val ) {

                if (val.parent != null && val.parent.removed_at != null) {
                    val.parent = null;
                }

                if (val.status == "OK") {
                    var status = '<a href="'+subdir+'/flames/'+val.jobID+'#flamesResults">load results</a>';
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

const flamesResultTable = function(){
    const file = "FLAMES_scores_fmt.pred";
    id = pageState.get("id");
    $('#predTable').DataTable({
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
                header: "locus:symbol:ensg:FLAMES_scaled:FLAMES_raw:estimated_cumulative_precision"
            }
        },
        error: function () {
            alert("FLAMES pred table error");
        },
        "lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "iDisplayLength": 10
    });
}

export const CheckInput = function(){
    var submit = true;
    var s2gID = $('#s2gID').val();

    if(s2gID==0){
        submit = false;
        $('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please select a SNP2GENE jobID.</div>')
    }else{
        if(s2gID>0){
            var filecheck = false;
            $.ajax({
                url: pageState.get("subdir")+"/flames/checkSNP2GENEFiles",
                type: 'POST',
                data: { jobID: s2gID },
                error: function(){alert("error from checkSNP2GENEFiles")},
                success: function(data){
                    if(data==1){filecheck=true}
                },
                complete: function(){
                    if(!filecheck){
                        submit = false;
                        $('#CheckInput').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">The seleted SNP2GENE job does not have valid outputs necessary for FLAMES.</div>')
                    }else{
                        $('#CheckInput').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. The files needed for FLAMES will be obtained from the selected SNP2GENE job.</div>')
                    }
                }
            });
        }
    }

    if ($('#gwasSumstat').val().length === 0) {
        submit = false;
        $('#gwasInputCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please upload GWAS summary statistics.</div>')
    } else{
        $('#gwasInputCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. ');
    }

    if ($('#totalN').val().length === 0) {
        submit = false;
        $('#otherParamsCheck').html('<div class="alert alert-danger" style="padding-bottom: 10; padding-top: 10;">Please input sample size.</div>')
    } else{
        $('#otherParamsCheck').html('<div class="alert alert-success" style="padding-bottom: 10; padding-top: 10;">OK. ');
    }

    if(submit){$('#flamesSubmit').attr("disabled", false);}
	else{$('#flamesSubmit').attr("disabled", true);}
}

export default FLAMESSetup;