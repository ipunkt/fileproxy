@extends('layouts.centered')

@section('title')
	Statistics
@endsection

@section('content')

	<div class="title m-b-md">
		Statistics
	</div>

	<div class="flex-center">
		<div class="card">
			<div class="number">{{ $hits }}</div>
			Downloads
		</div>
		<div class="card">
			<div class="number">{{ $aliases }}</div>
			Aliases
		</div>
		<div class="card">
			<div class="number">{{ $files }}</div>
			Files
		</div>
		<div class="card">
			<div class="number">{{ bytesToHuman($size) }}</div>
			Filesize
		</div>
	</div>

@endsection