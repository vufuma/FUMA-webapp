<html>
	<head><h3>FUMA job has been completed!!</h3></head>
	<body>
		<p>
			Your {{ $job_type }} job (job ID: {{ $jobID }}, job title: {{ $jobtitle }}) has been completed.<br>
			Please follow the link to go to the results page.<br>
			<a href="{{ config('app.url') }}{{ $job_type }}/{{ $jobID }}{{ $page }}">Job query</a><br>
			<br>
		</p>

		<?php
		if($status==2){
			echo '<span style="color:blue">There was an error during MAGMA process (ERROR message: '.$msg.').
			This error might be because rsID in the input file did not match with MAGMA reference panel or the number of input SNPs were too small.
			Other results are available from the link above but MAGMA results will not be displayed.</span>';
		}
		?>

		<p>
			You can post questions, suggestions and bug reports on Google Forum:
			<a href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a><br><br>
			Tanya Phung, Ph.D.<br>
			VU University Amsterdam<br>
			Dept. Complex Trait Genetics<br>
		</p>
	</body>
</html>
