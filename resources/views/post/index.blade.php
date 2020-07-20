@extends('laralangs::layouts.app')

@section('content')
<div class="container" id="postApp">
	<h1>Post</h1>
	<div class="row">
		<div class="col-12 text-right">
			<a href="{{ route('laralangs.post.create') }}" class="btn btn-primary">{{ __('Create a post') }}</a>
		</div>
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($posts as $post)
				<tr>
					<td>{{ $post->id }}</td>
					<td><a href="{{ route('laralangs.post.show', $post->id) }}">{{ $post->title }}</a></td>
					<td>{{ $post->active == '1' ? 'Active' : 'Inactive' }}</td>
					<td>
						<a href="{{ route('laralangs.post.edit', $post->id) }}" class="btn btn-primary">Edit</a>
						<a
							@click.prevent="deletePost"
							href="{{ route('laralangs.post.destroy', $post->id) }}"
							class="btn btn-danger"
						>Delete</a>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="4">
						<h3>No post added yet.</h3>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>

</div>
@endsection

@section('bodyEnd')
<script src="{{ asset('vendor/Webcityro/laralangs/js/post/index.js') }}" defer></script>
@endsection
