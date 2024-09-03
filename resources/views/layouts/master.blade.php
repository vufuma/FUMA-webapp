<!DOCTYPE html>
<html lang="en">
	<head>
		@include('partials._head')
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/fuma.js'])
        @stack('vite')

	</head>
	<body>
		<div id="header" class="row" style="padding-top: 16px">
			@include('partials._nav')
		</div>

		@if ( Session::has('alert-warning') )
		<div class="container-fluid text-center">
			<div class="center-block">
				<div class="alert alert-warning alert-dismissable" style="display:inline-block;">
					<span type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></span>
					<em> {!! Session::get('alert-warning') !!}</em>
				</div>
			</div>
		</div>
		@endif
		<div id="script_alert_block" class="container-fluid text-center"></div>
		<div class="container-fluid">
			<div id="main" class="row" style="padding-bottom: 50px;">
				@yield('content')
			</div>
		</div>
		<div id="foot" class="row">
			<footer>
				@include('partials._footer')
			</footer>
		</div>
        @include('partials._javascript')
        @yield('scripts')
        @stack('page_scripts')

	</body>
</html>
