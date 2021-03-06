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

	<div class="side-by-side">
		<div>
			<p>The file proxy app serves files via aliases. So you have to create a file (via upload or remote url).
				After the
				file exists you can create as many aliases as you like. Each can have different accessibility flags.</p>
			<p>The main identification thing in file proxy is the returned file id (UUID4). This identifier you have to
				remember for identification, creating aliases and fetching download hits.</p>
			<p>There is no listing of serving files within the app. You have to remember in your source system or you
				have to implement it in your local version. But we didn't recommend that.</p>
		</div>
	</div>

	<h3 id="api">API</h3>

	<h4>Serving an uploaded file</h4>

	<div class="side-by-side">
		<div>
			<p>Request Body has following content for file upload.</p>
			<p>The returned file id you have to remember.</p>
		</div>
		<div>
			<p><strong><kbd>POST /api/files</kbd></strong></p>

			<pre><code>{
  "id": null,
  "type": "files",
  "attributes": {
    "type": "attachment",
    "source": "BASE64_ENCODED_FILE_CONTENT"
    "filename": "filename.pdf"
  }
}</code></pre>

			<p>Response</p>
			<pre><code>{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 938,
      "checksum": "588c77c934d6a3b2f1d07973569ef5eb5779dd4f",
      "mimetype": "text/html",
      "hits": 0
    }
  }
}</code></pre>
		</div>
	</div>

	<h4>Serving a remote url</h4>

	<div class="side-by-side">
		<div>
			<p>Request Body has following content for creating a remote file to serve.</p>
		</div>
		<div>
			<p><strong><kbd>POST /api/files</kbd></strong></p>
			<pre><code>{
  "id": null,
  "type": "files",
  "attributes": {
    "type": "uri",
    "source": "https://domain.tld/file.ext"
  }
}</code></pre>

		</div>
	</div>

	<div class="side-by-side">
		<div>
			<p>This happens when the remote files will be fetched by a queue, so you do not get a valid response in
				sync.</p>
		</div>
		<div>
			<p>Response</p>

			<pre><code>{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 0,
      "checksum": null,
      "mimetype": null,
      "hits": 0
    }
  }
}</code></pre>

		</div>
	</div>

	<h4>Fetching a files resource</h4>

	<div class="side-by-side">
		<div>
			<p>You can retrieve file data like this.</p>
		</div>
		<div>
			<p><strong><kbd>GET /api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8</kbd></strong></p>
			<p>Response</p>
			<pre><code>{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 938,
      "checksum": "588c77c934d6a3b2f1d07973569ef5eb5779dd4f",
      "mimetype": "text/html",
      "hits": 12
    }
  }
}</code></pre>
		</div>
	</div>

	<h4>Fetching a files resource including aliases</h4>

	<div class="side-by-side">
		<div>
			<p>You can retrieve file data with all existing aliases inclusively.</p>
		</div>
		<div>
			<p><strong><kbd>GET /api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8?include=aliases</kbd></strong></p>
			<p>Response</p>
			<pre><code>{
  "data": {
    "type": "files",
    "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8",
    "attributes": {
      "filename": "file.ext",
      "size": 938,
      "checksum": "588c77c934d6a3b2f1d07973569ef5eb5779dd4f",
      "mimetype": "text/html",
      "hits": 12
    },
    "links": {
      "self": "http://localhost/api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8"
    },
    "relationships": {
      "aliases": {
        "links": {
          "self": "http://localhost/api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8/relationships/aliases"
          "related": "http://localhost/api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8/aliases"
        },
        "data": [
          {
            "type": "aliases",
            "id": "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8.1",
          },
        ],
      },
    },
  },
  "included": [
    {
      "type": "aliases",
      "id":  => "d7a3913e-44a9-4aa6-ac8e-b9441cba07f8.1",
      "attributes": {
        "path": "test.pdf",
        "valid_from": "2017-04-10T09:53:34+00:00",
        "valid_until": null,
        "hits": 12,
        "hits_left' => null,
        "hits_total' => null
      }
      "links": {
        "download": "http://localhost/test.pdf"
        "self": "http://localhost/api/aliases/1f29a886-b079-3356-a5f3-c28a2a405436.1"
      }
    }
  ]
}</code></pre>
		</div>
	</div>

	<h4>Creating a file alias</h4>

	<div class="side-by-side">
		<div>
			<p>A file alias is a downloadable resource. With this request a new alias can be created.</p>
		</div>
		<div>
			<strong><kbd>POST /api/files/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8/aliases</kbd></strong>
			<pre><code>{
  "data": {
    "id": null,
    "type": "aliases",
    "attributes": {
      "path": "test-2017-03.pdf",
      "hits": 0,
      "valid_from": "2017-04-10 12:24:35",
      "valid_until": null
    }
  }
}</code></pre>
			<p>Response</p>
			<p>Empty, Status Code 204.</p>
		</div>
	</div>

	<h4>Retrieving a file alias</h4>

	<div class="side-by-side">
		<div>
			<p>You can retrieve the file alias details like this.</p>
		</div>
		<div>
			<p><strong><kbd>GET /api/aliases/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8.1</kbd></strong></p>
			<p>Response</p>
			<pre><code>{
  "data": {
    "type": "aliases"
    "id": "d8d73c35-ba4a-34e8-b95c-122d3ad2a9e9.1"
    "attributes": {
      "path": "ullam-et-nihil-ex-aut-ut.xdp"
      "valid_from": "1994-09-01T14:30:44+00:00"
      "valid_until": null
      "hits": 0
      "hits_left": null
      "hits_total": null
    }
    "links": {
      "download": "http://localhost/ullam-et-nihil-ex-aut-ut.xdp"
      "self": "http://localhost/api/aliases/d8d73c35-ba4a-34e8-b95c-122d3ad2a9e9.1"
    }
  }
}</code></pre>
		</div>
	</div>

	<h4>Deleting a file alias</h4>

	<div class="side-by-side">
		<div>
			<p>You can delete the file alias with this request.</p>
		</div>
		<div>
			<p><strong><kbd>DELETE /api/aliases/d7a3913e-44a9-4aa6-ac8e-b9441cba07f8.1</kbd></strong></p>
			<p>Response</p>
			<p>Empty, Status Code 204.</p>
		</div>
	</div>

	<h4>Statistics Endpoint</h4>

	<div class="side-by-side">
		<div>
			<p>You can retrieve the whole statistics for the file proxy application.</p>
		</div>
		<div>
			<p><strong><kbd>GET /api/statistics</kbd></strong></p>

			<p>Response</p>
			<pre><code>{
  "data": {
    "type": "statistics",
    "id": "",
    "attributes": {
      "size": 0,
      "files": 0,
      "aliases": 0,
      "hits": 0
    }
  }
}</code></pre>
		</div>
	</div>





@endsection