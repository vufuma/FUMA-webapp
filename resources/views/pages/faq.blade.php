@extends('layouts.master')

@section('content')
	<div style="padding-top: 50px; padding-right: 50px; padding-left: 50px;">
		<div class="card mb-2">
			<div class="card-header faq">
				<h4>My job returned an error. What should I do? 
					<a href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" class="active" style="float: right; padding-right:20px;">
						<i class="fa fa-chevron-down"></i>
					</a>
				</h4>
			</div>
			<div class="card-body collapse" id="faq1" >
				When you encountered an error with a FUMA job, please check the <a target="_blank" href="https://groups.google.com/g/fuma-gwas-users/c/N3HCEXBJ8Iw/m/utS6HxWoAAAJ">troubleshooting guide</a>.<br/>
			</div>
		</div>

		<div class="card mb-2">
			<div class="card-header faq">
				<h4>How many jobs can I submit at once?
					<a href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" class="active" style="float: right; padding-right:20px;">
						<i class="fa fa-chevron-down"></i>
					</a>
				</h4>
			</div>
			<div class="card-body collapse" id="faq2">
				We maintain a dedicated server for running FUMA jobs. As this is a free service we provide for the advancement of science, this also means that there is a limited amount of computational resources to go around.<br/>
				In order to prevent single users to occupy the entire server, there is a job limit of <strong>10 jobs per user</strong>.<br/>
			</div>
		</div>

		<div class="card mb-2">
			<div class="card-header faq">
				<h4>How many jobs can I save on FUMA?
					<a  href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" class="active" style="float: right; padding-right:20px;">
						<i class="fa fa-chevron-down"></i>
					</a>
				</h4>
			</div>
			<div class="card-body collapse" id="faq3">
				Each user can store at most <strong>100 SNP2GENE jobs </strong> on the FUMA server (Policy updated as of v1.6.5).<br/>
				Currently, there is no restriction on the number of GENE2FUNC and Cell Type jobs stored on the FUMA server because these jobs tend to be small in size (but subject to change).<br/>
				All faulty jobs will be deleted after 1 month.<br/>
			</div>
		</div>
	</div>
@endsection

@push('page_scripts')
	{{-- Imports from the web --}}

	{{-- Imports from the project --}}

	{{-- Hand written ones --}}
	<script type="module">
		$(function(){
			$('.card-header.faq a').on('click', function(){
				if($(this).attr('class')=="active"){
					$(this).removeClass('active');
					$(this).children('i').attr('class', 'fa fa-chevron-up');
				}else{
					$(this).addClass('active');
					$(this).children('i').attr('class', 'fa fa-chevron-down');
				}
			});
		});
	</script>
@endpush
