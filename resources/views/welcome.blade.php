@extends('layouts.centered')

@section('content')

	<div class="title m-b-md">
		{{ config('app.name') }}
	</div>

	<div class="links">
		@if (config('fileproxy.web.accept_file_upload'))
		<a href="{{ route('file.create') }}">Serve a file</a>
		@endif
		@if (config('fileproxy.web.accept_remote_creation'))
		<a href="{{ route('url.create') }}">Serve a remote file</a>
		@endif
		<a href="/docs">Documentation</a>
	</div>

@endsection