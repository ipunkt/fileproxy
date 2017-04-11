@extends('layouts.app')

@section('title')
	{{ $proxyFile->filename }}
@endsection

@section('content')

	<h2>{{ $proxyFile->filename }}</h2>

	<div class="side-by-side">
		<div>
			<p>
				<img src="http://mimeicon.herokuapp.com/{{ $proxyFile->mimetype }}?size=64" width="64" alt="Mimetype: {{ $proxyFile->mimetype }}" title="Mimetype: {{ $proxyFile->mimetype }}">
			</p>
			<p>{{ bytesToHuman($proxyFile->size) }}</p>
			@if ($proxyFile->type === 'remote')
				<p>Source: {{ $proxyFile->remoteFile->url }}</p>
			@endif
			<p>{{ $proxyFile->hits()->count() }} Hits</p>
		</div>
		<div>
			<p><strong>Change Filename</strong></p>
			<form method="post" action="{{ route('file.update', ['file' => $proxyFile->reference]) }}">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				@if ($errors->has('path'))
					{{ $errors->first('path') }}<br/>
				@endif
				<label for="filename">Filename</label>
				<input type="text" name="filename" value="{{ old('filename', $proxyFile->filename) }}" id="filename" minlength="6" maxlength="{{ MAX_STRING_LENGTH }}" required>
				<br/>
				<label></label>
				<button type="submit">Update Filename</button>
			</form>
		</div>
	</div>

	<hr>

	<h3>Aliases</h3>

	<div class="side-by-side">
		<div>
			<p><strong>Create an alias</strong></p>
			<form method="post" action="{{ route('file.aliases.store', ['file' => $proxyFile->reference]) }}">
				{{ csrf_field() }}
				@if ($errors->has('path'))
					{{ $errors->first('path') }}<br/>
				@endif
				<label for="path">Path</label>
				<input type="text" name="path" id="path" minlength="6" maxlength="255" required>
				<br/>

				@if ($errors->has('hits'))
					{{ $errors->first('hits') }}<br/>
				@endif
				<label for="hits">Hits Allowed (0 for unlimited)</label>
				<input type="number" name="hits" value="0" min="0" id="hits">
				<br/>

				@if ($errors->has('from'))
					{{ $errors->first('from') }}<br/>
				@endif
				<label for="from">Valid From (leave empty for now)</label>
				<input type="datetime-local" name="from" value="" id="from">
				<br/>

				@if ($errors->has('until'))
					{{ $errors->first('until') }}<br/>
				@endif
				<label for="until">Valid Until (leave empty for unlimited)</label>
				<input type="datetime-local" name="until" value="" id="until">
				<br/>

				<label></label>
				<button type="submit">Make File Available As Alias</button>
			</form>
		</div>
		<div>
			<table width="100%">
				<tbody>
				@foreach($aliases as $alias)
					<tr>
						<td>
							@if ($alias->hitsLeft() && $alias->isValidNow())
								<a href="{{ route('serve', ['alias' => $alias->path]) }}" target="_blank" class="btn" title="Download file">&#11147;</a>
							@endif
							{{ $alias->path }}
						</td>
						<td>
							{{ $alias->hits()->count() }} /
							@if ($alias->hitsTotal === -1)
							&infin;
							@else
								{{ $alias->hitsTotal }}
							@endif
							downloads
						</td>
						<td>
							<form method="post" action="{{ route('file.aliases.destroy', ['file' => $proxyFile->reference, 'alias' => $alias->getKey()]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit">Delete Alias</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>


@endsection