import { XqtlsState as pageState}  from "../pages/pageStateComponents.js";
export const XQTLSSetup = function(){
    updateQueryHistory();
	$('#refreshTable').on('click', function(){
		updateQueryHistory();
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
                    var status = '<a href="'+subdir+'/xqtls/'+val.jobID+'">load results</a>';
                }
                else {
                    status = val.status;
                }

                items = items + "<tr><td>"+val.jobID+"</td><td>"+val.title+"</td><td>"
                    +(val.parent != null ? val.parent.jobID : '-')+"</td><td>"+(val.parent != null ? val.parent.title : '-')+"</td><td>"
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

export default XQTLSSetup;