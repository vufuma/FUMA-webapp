<!-- Nav bar -->
<nav class="navbar navbar-expand-lg fixed-top bg-dark navbar-dark align-items-center">
	<!--div class="container-fluid"-->
	<a class="navbar-brand fuma_brand" href="{{ Config::get('app.subdir') }}/" style="padding-left: 30px;">
		<img src="{!! URL::asset('image/fuma.png') !!}" height="50" alt="FUMAGwas">
	</a>
	<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#topOfPageNav"
		aria-controls="topOfPageNav" aria-expanded="false" aria-label="Toggle navigation ">
		<span class="navbar-toggler-icon"> </span>
	</button>
	<div class="collapse navbar-collapse" id="topOfPageNav">
		<ul class="navbar-nav ms-auto me-3">
			<!-- local_start -->
			<li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : ''}}" href="/">Home</a></li>
			@can('Access Admin Page')
			<li class="nav-item"><a class="nav-link {{ Request::is('admin') ? 'active' : ''}}" href="/admin">Admin
					Dashboard</a></li>
			@endcan
			<li class="nav-item"><a class="nav-link {{ Request::is('tutorial') ? 'active' : ''}}"
					href="/tutorial">Tutorial</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('browse*') ? 'active' : ''}}" href="/browse">Browse
					Public Results</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('snp2gene*') ? 'active' : ''}}"
					href="/snp2gene">SNP2GENE</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('gene2func*') ? 'active' : ''}}"
					href="/gene2func">GENE2FUNC</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('celltype*') ? 'active' : ''}}" href="/celltype">Cell
					Type</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('links') ? 'active' : ''}}" href="/links">Links</a>
			</li>
			<li class="nav-item"><a class="nav-link {{ Request::is('downloadPage') ? 'active' : ''}}"
					href="/downloadPage">Downloads</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('faq') ? 'active' : ''}}" href="/faq">FAQs</a></li>
			<li class="nav-item"><a class="nav-link {{ Request::is('updates') ? 'active' : ''}}"
					href="/updates">Updates</a></li>
			<li class="nav-item">
				<a id="appInfo" class="infoPop nav-link" data-placement="bottom" data-bs-toggle="popover"
					data-bs-html="true" title="FUMA information" data-bs-content='<div style="width:200px;">
						Current FUMA verion: <span id="FUMAver"></span><br>
						Total users: <span id="FUMAuser"></span><br>
						Total SNP2GENE jobs: <span id="FUMAs2g"></span><br>
						Total GENE2FUNC jobs: <span id="FUMAg2f"></span><br>
						Total CellType jobs: <span id="FUMAcellType"></span><br>
						Currently running jobs: <span id="FUMArun"></span><br>
						Currently queued jobs: <span id="FUMAque"></span></div>'>
					<i class="fa fa-info-circle fa-lg"></i>
				</a>
			</li>
			<!-- local_end -->
			<!-- Authentication Links -->
			@if (! Auth::check())
			<li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
			<li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
			@else
			<li class="nav-item dropdown">
				<a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" role="button"
					aria-expanded="false">
					{{ Auth::user()-> name }} <span class="caret"></span>
				</a>

				<ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="{{ Config::get('app.subdir') }}/snp2gene#joblist-panel">SNP2GENE
							My Jobs</a></li>
					<li><a class="dropdown-item" href="{{ Config::get('app.subdir') }}/gene2func#queryhistory">GENE2FUNC
							History</a>
					<li>
						@can('Administer roles & permissions')
					<li><a class="dropdown-item" href="{{ url('/admin/users') }}">
							<i class="fa fa-btn fa-unlock"></i>
							Admin
						</a></li>
					@endcan
					<li><a class="dropdown-item" href="{{ url('/logout') }}" id="fuma-logout-link"
							onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="fa fa-btn fa-sign-out"></i>
							Logout
						</a></li>

				</ul>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					{{ csrf_field() }}
				</form>
			</li>
			@endif
		</ul>
	</div>
	<!--/div-->
</nav>