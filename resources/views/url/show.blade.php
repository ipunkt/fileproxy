@extends('layouts.centered')

@section('title')
	Remote File will be downloaded
@endsection

@section('content')

	<h2>Your File will be downloaded soon...</h2>

	Your file will be downloaded in the queue.<br/>
	<br/>
	Your file reference is <code>{{ $reference }}</code>.<br/>
	You can create aliases after download was successful.<br />
	<br />

	<form method="get" action="{{ route('url.show', ['url' => $reference]) }}">
		<button type="submit">Re-Check if your file already downloaded.</button>
	</form>

@endsection