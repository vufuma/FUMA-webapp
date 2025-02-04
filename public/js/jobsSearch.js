var prefix = "jobs";
$(document).ready(function () {
	$('#deleteJob').on('click', function () {
		swal({
			title: "Are you sure?",
			html: true,
			text: "Do you really want to remove selected jobs?",
			type: "warning",
			showCancelButton: true,
			closeOnConfirm: true,
		}, function (isConfirm) {
			if (isConfirm) {
				$('.deleteJobCheck').each(function () {
					if ($(this).is(":checked")) {
						$.ajax({
							url: subdir + '/' + $(this).attr('job-type') + '/deleteJob',
							type: "POST",
							data: {
								jobID: $(this).attr('job-id')
							},
							error: function () {
								alert("error at deleteJob");
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
            }
        }
    )
}
    )
}
)

