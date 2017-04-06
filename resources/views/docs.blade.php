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

	<h4>Serving a file or remote url</h4>

	<p><strong><kbd>POST /api/files</kbd></strong></p>

	<p>Request Body has following content for file upload</p>
<pre><code>
{
  "id": null,
  "type": "files",
  "attributes": {
    "type": "attachment",
    "source": "BASE64_ENCODED_FILE_CONTENT"
    "filename": "filename.pdf"
  }
}
</code></pre>
	<p>Request Body has following content for creating a remote file to serve.</p>
<pre><code>
{
  "id": null,
  "type": "files",
  "attributes": {
    "type": "uri",
    "source": "https://domain.tld/file.ext"
  }
}
</code></pre>

	<p>The response for success for an uploaded file is like this.</p>

<pre><code>
{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 938,
      "checksum": "588c77c934d6a3b2f1d07973569ef5eb5779dd4f",
      "mimetype": "text/html"
    }
  }
}
</code></pre>

	<p>The response for success for a remote url is like this.</p>

<pre><code>
{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 0,
      "checksum": null,
      "mimetype": null
    }
  }
}
</code></pre>

	<p>This happens when the remote files will be fetched by a queue, so you do not get a valid response in sync.</p>

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