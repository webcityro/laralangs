@extends('laralangs::layouts.app')

@section('content')
<div class="container" id="postApp">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<laralangs-post-form
				endpoint="{{ route('laralangs.post.update', $post->id) }}"
				:post="{{ $post->toJson() }}"
			></laralangs-post-form>
		</div>
	</div>
</div>
@endsection

@section('bodyEnd')
	<script src="{{ asset('vendor/Webcityro/laralangs/js/post/createAndEdit.js') }}" defer></script>
@endsection
