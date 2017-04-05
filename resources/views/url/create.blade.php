@extends('layouts.centered')

@section('title')
	Serve A Remote File
@endsection

@section('content')

	<h2>Serve a new remote file</h2>

	<form method="post" action="{{ route('url.store') }}">
		{{ csrf_field() }}
		@if ($errors->has('url'))
			{{ $errors->first('url') }}
			<br/><br/>
		@endif

		<input type="url" id="url" name="url" class="form-control" placeholder="https://domain.com/file.pdf" size="50" required>
		<button type="submit">Serve Remote File</button>
	</form>

@endsection