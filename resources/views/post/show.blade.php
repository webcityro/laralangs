@extends('laralangs::layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-12 text-right border-bottom border-secondary">
			<a href="{{ route('laralangs.post.index') }}" class="btn btn-primary">{{ __('Show all posts') }}</a>
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-12 bg-white p-4">
		<h1>{{ $post->title }}</h1>
		<p>{{ $post->body }}</p>
		</div>
	</div>
</div>
@endsection
