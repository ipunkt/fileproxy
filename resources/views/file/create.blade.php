@extends('layouts.centered')

@section('title')
	Serve A File
@endsection

@section('content')

	<h2>Serve a new file</h2>

	<form method="post" action="{{ route('file.store') }}" enctype="multipart/form-data">
		{{ csrf_field() }}
		@if ($errors->has('file'))
			{{ $errors->first('file') }}
			<br/><br/>
		@endif

		<input type="file" name="file" class="form-control" required>
		<button type="submit">Serve File</button>
	</form>

@endsection