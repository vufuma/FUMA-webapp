var prefix = "xqtls";
var id = ""

import { XqtlsState as pageState}  from "../pages/pageStateComponents.js";
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
        summaryTable(subdir, page, prefix, id);
    };
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

const summaryTable = function(){
    const file = "xqtls_results.csv";
    id = pageState.get("id");
	$('#xqtlTable').DataTable({
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
				header: "phenotype:region:type:tissue:locus:rho:rhoLower:rhoUpper:p:pAdj:bonSig:GENE"
			}
		},
		error: function () {
			alert("GenomicRiskLoci table error");
		},
		"lengthMenue": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": 10
	});
}

export default XQTLSSetup;