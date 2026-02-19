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

export default FLAMESSetup;