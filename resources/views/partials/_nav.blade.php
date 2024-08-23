<!-- Nav bar -->
<nav class="navbar navbar-expand-lg fixed-top bg-light navbar-light">
	<!--div class="container-fluid"-->
        <a class="navbar-brand" href="{{ Config::get('app.subdir') }}/" style="padding-top: 5px; padding-left: 30px;">
            <img src="{!! URL::asset('image/fuma.png') !!}" height="50px;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topOfPageNav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

		<div class="collapse navbar-collapse justify-content-end" id="topOfPageNav">
			<ul class="navbar-nav">
				<!-- local_start -->
				<li class="nav-item {{ Request::is('/') ? 'active' : ''}}"><a href="/">Home</a></li>
				@can('Access Admin Page')
				<li class="nav-item {{ Request::is('admin') ? 'active' : ''}}"><a href="/admin">Admin Dashboard</a></li>
				@endcan
				<li class="nav-item {{ Request::is('tutorial') ? 'active' : ''}}"><a href="/tutorial">Tutorial</a></li>
				<li class="nav-item {{ Request::is('browse*') ? 'active' : ''}}"><a href="/browse">Browse Public Results</a></li>
				<li class="nav-item {{ Request::is('snp2gene*') ? 'active' : ''}}"><a href="/snp2gene">SNP2GENE</a></li>
				<li class="nav-item {{ Request::is('gene2func*') ? 'active' : ''}}"><a href="/gene2func">GENE2FUNC</a></li>
				<li class="nav-item {{ Request::is('celltype*') ? 'active' : ''}}"><a href="/celltype">Cell Type</a></li>
				<li class="nav-item {{ Request::is('links') ? 'active' : ''}}"><a href="/links">Links</a></li>
				<li class="nav-item {{ Request::is('downloadPage') ? 'active' : ''}}"><a href="/downloadPage">Downloads</a></li>
				<li class="nav-item {{ Request::is('faq') ? 'active' : ''}}"><a href="/faq">FAQs</a></li>
				<li class="nav-item {{ Request::is('updates') ? 'active' : ''}}"><a href="/updates">Updates</a></li>
				<li class="nav-item">
					<a id="appInfo" class="infoPop" data-placement="bottom" data-toggle="popover" data-html="true"
						title="FUMA information"
						data-content='<div style="width:200px;">
						Current FUMA verions: <span id="FUMAver"></span><br/>
						Total users: <span id="FUMAuser"></span><br/>
						Total SNP2GENE jobs: <span id="FUMAs2g"></span><br/>
						Total GENE2FUNC jobs: <span id="FUMAg2f"></span><br/>
						Total CellType jobs: <span id="FUMAcellType"></span><br/>
						Currently running jobs: <span id="FUMArun"></span><br/>
						Currently queued jobs: <span id="FUMAque"></span></div>'>
						<i class="fa fa-info-circle fa-lg"></i>
					</a>
				</li>
				<!-- local_end -->
				<!-- Authentication Links -->
				@if (! Auth::check())
					<li class="nav-item"><a href="{{ url('/login') }}">Login</a></li>
					<li class="nav-item"><a href="{{ url('/register') }}">Register</a></li>
				@else
					<li class="nav-item dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							{{ Auth::user()-> name }} <span class="caret"></span>
						</a>

						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ Config::get('app.subdir') }}/snp2gene#joblist-panel">SNP2GENE My Jobs</a></li>
							<li><a href="{{ Config::get('app.subdir') }}/gene2func#queryhistory">GENE2FUNC History</a></li>
							@can('Administer roles & permissions')
								<li><a href="{{ url('/admin/users') }}">
									<i class="fa fa-btn fa-unlock"></i>
									Admin
								</a></li>
							@endcan
							<li>
								<a href="{{ url('/logout') }}" id="fuma-logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
									<i class="fa fa-btn fa-sign-out"></i>
									Logout
								</a>
							</li>
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
