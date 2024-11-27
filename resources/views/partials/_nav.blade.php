<!-- Nav bar -->
<nav class="navbar navbar-expand-lg fixed-top bg-dark navbar-dark align-items-center">
	<!--div class="container-fluid"-->
        <a class="navbar-brand fuma_brand" href="{{ Config::get('app.subdir') }}/" style="padding-left: 30px;">
            <img src="{!! URL::asset('image/fuma.png') !!}" height="50" alt="FUMAGwas">
        </a>
        <button type = "button" class = "navbar-toggler" data-bs-toggle = "collapse" data-target = "#topOfPageNav" aria-controls = "topOfPageNav" aria-expanded = "false" aria-label = "Toggle navigation ">
            <span class = "navbar-toggler-icon"> </span>
        </button>
		<div class="collapse navbar-collapse" id="topOfPageNav">
			<ul class="navbar-nav ml-auto">
				<!-- local_start -->
				<li class="nav-item {{ Request::is('/') ? 'active' : ''}}"><a class="nav-link" href="/">Home</a></li>
				@can('Access Admin Page')
				<li class="nav-item {{ Request::is('admin') ? 'active' : ''}}"><a class="nav-link" href="/admin">Admin Dashboard</a></li>
				@endcan
				<li class="nav-item {{ Request::is('tutorial') ? 'active' : ''}}"><a class="nav-link" href="/tutorial">Tutorial</a></li>
				<li class="nav-item {{ Request::is('browse*') ? 'active' : ''}}"><a class="nav-link" href="/browse">Browse Public Results</a></li>
				<li class="nav-item {{ Request::is('snp2gene*') ? 'active' : ''}}"><a class="nav-link" href="/snp2gene">SNP2GENE</a></li>
				<li class="nav-item {{ Request::is('gene2func*') ? 'active' : ''}}"><a class="nav-link" href="/gene2func">GENE2FUNC</a></li>
				<li class="nav-item {{ Request::is('celltype*') ? 'active' : ''}}"><a class="nav-link" href="/celltype">Cell Type</a></li>
				<li class="nav-item {{ Request::is('links') ? 'active' : ''}}"><a class="nav-link" href="/links">Links</a></li>
				<li class="nav-item {{ Request::is('downloadPage') ? 'active' : ''}}"><a class="nav-link" href="/downloadPage">Downloads</a></li>
				<li class="nav-item {{ Request::is('faq') ? 'active' : ''}}"><a class="nav-link" href="/faq">FAQs</a></li>
				<li class="nav-item {{ Request::is('updates') ? 'active' : ''}}"><a class="nav-link" href="/updates">Updates</a></li>
				<li class="nav-item">
					<a id="appInfo" class="infoPop nav-link" data-placement="bottom" data-bs-toggle="popover" data-html="true"
						title="FUMA information"
						data-bs-content='<div style="width:200px;">
						Current FUMA verions: <span id="FUMAver"></span><br>
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
						<a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" role="button" aria-expanded="false">
							{{ Auth::user()-> name }} <span class="caret"></span>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a class="dropdown-item" href="{{ Config::get('app.subdir') }}/snp2gene#joblist-panel">SNP2GENE My Jobs</a></li>
							<li><a class="dropdown-item" href="{{ Config::get('app.subdir') }}/gene2func#queryhistory">GENE2FUNC History</a><li>
							@can('Administer roles & permissions')
								<li><a class="dropdown-item" href="{{ url('/admin/users') }}">
									<i class="fa fa-btn fa-unlock"></i>
									Admin
								</a></li>
							@endcan
                            <li><a class="dropdown-item" href="{{ url('/logout') }}" id="fuma-logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
