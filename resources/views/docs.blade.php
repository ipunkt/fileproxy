@extends('layouts.app')

@section('title')
	Documentation
@endsection

@section('content')

	<h1>{{ config('app.name') }}</h1>
	{{ config('app.name') }} is a file service for serving files with a bit more management:
	<ul>
		<li>count statistics</li>
		<li>serve time-based accessible files</li>
		<li>serve count-based accessible files</li>
		<li>serve remote files</li>
		<li>serve uploaded files</li>
	</ul>

@endsection