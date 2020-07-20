<?php

namespace Webcityro\Laralangs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webcityro\Laralangs\Models\Post;
use Symfony\Component\HttpFoundation\Response;
use Webcityro\Laralangs\Http\Requests\PostRequest;

class PostsController extends Controller {

	public function index() {
		return view('laralangs::post.index')->with([
			'posts' => Post::paginate(15)
		]);
	}

	public function create() {
		return view('laralangs::post.create');
	}

	public function store(PostRequest $request) {
		$post = Post::createWithLanguages($request->fields, $request->translations);

		return response()->json($post, Response::HTTP_CREATED);
	}

	public function show(Post $post) {
		return view('laralangs::post.show')->with(compact('post'));
	}

	public function edit(Post $post) {
		return view('laralangs::post.edit')->with(compact('post'));
	}

	public function update(Post $post, PostRequest $request) {
		$post->updateWithLanguages($request->fields, $request->translations);

		return response()->json($post, Response::HTTP_ACCEPTED);
	}

	public function destroy(Post $post) {
		$post->delete();
		return response()->json($post, Response::HTTP_ACCEPTED);
	}
}
