<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>@yield('title', 'Your file proxy') &middot; {{ config('app.name') }}</title>
	<link href="{{ mix('/css/app.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<div class="flex-center position-ref full-height">
	<div class="top-left links">
		<a href="{{ url('/') }}">File Proxy</a>
	</div>
	<div class="top-right links">
		<a href="{{ url('/docs') }}">Documentation</a>
		<a href="{{ url('/stats') }}">Statistics</a>
	</div>

	<div class="centered-content">
		@yield('content')
	</div>
</div>
</body>
<script src="{{ mix('/js/app.js') }}"></script>
</html>
