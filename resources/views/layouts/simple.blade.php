<!DOCTYPE html>
<html lang="en">
	<!--'resources/css/app.css', -->
	<head>
		@include('partials._head')
        @vite(['resources/js/app.js', 'resources/js/fuma.js'])
        @stack('vite')
	</head>
	<body>
		<div id="script_alert_block" class="container-fluid text-center"></div>
		<div class="container-fluid">
			<div id="main" class="row">
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
