import swal from 'sweetalert';
var prefix = "jobs";
export function setupDeleteJob() {
	$('#deleteJob').on('click', async() => {
		var span = document.createElement("span");
		span.innerHTML = "Do you really want to remove selected jobs?<br><div class='alert alert-danger'>If you have selected a public job, it will be permanently deleted from the public list.</div>";
		let isConfirm = await swal({
			title: "Are you sure?",
			content: span,
			icon: "warning",
			buttons: {
				cancel: true,
				confirm: true,
			}
		});
		if (isConfirm) {
			$('.deleteJobCheck').each(function () {
				if ($(this).is(":checked")) {
					let subdir = ""; 
					$.ajax({
						url: subdir + '/' + $(this).attr('job-type') + '/deleteJob',
						type: "POST",
						data: {
							jobID: $(this).attr('job-id')
						},
						error: function (xhr, status, error) {
							var err = JSON.parse(xhr.responseText);
							alert(`Error at deleteJob: ${err.message}`);
						},
						success: function (resdata) {
							// chech if resdata is null
							if (resdata != "") {
								alert(resdata);
							}
						}
					});
				}
			});
		};
	}
)}

export default setupDeleteJob;


