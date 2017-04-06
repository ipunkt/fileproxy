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

	<h2>Documentation</h2>

	<h3>API</h3>

	<h4>Statistics Endpoint</h4>

	<p><strong><kbd>GET /api/statistics</kbd></strong></p>

	<p>You can retrieve the whole statistics for the file proxy application.</p>

	<pre><code>
{
  "data": {
    "type": "statistics",
    "id": "statistics",
    "attributes": {
      "size": 0,
      "files": 0,
      "aliases": 0,
      "hits": 0
    }
  }
}
	</code></pre>

@endsection