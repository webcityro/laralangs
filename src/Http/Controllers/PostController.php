<?php

namespace Webcityro\Laralangs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webcityro\Laralangs\Models\Post;
use Symfony\Component\HttpFoundation\Response;
use Webcityro\Laralangs\Http\Requests\PostRequest;

class PostController extends Controller {

	public function index() {
		return view('laralangs::post');
	}

	public function create() {
		//
	}

	public function store(PostRequest $request) {
		$post = Post::createWithLanguages($request->fields, $request->translations);

		return response()->json($post, Response::HTTP_CREATED);
	}

	public function show($id) {
		//
	}

	public function edit($id) {
		//
	}

	public function update(Post $post, PostRequest $request) {
		$request->validate([
			'active' => 'required',
			'sortOrder' => 'required',
		], [
			'title' => 'required',
			'body' => 'required',
		]);
		$post->updateWithLanguages($request->fields, $request->translations);

		return response()->json($post, Response::HTTP_ACCEPTED);
	}

	public function destroy(Post $post) {
		$post->delete();
		return response()->json($post, Response::HTTP_ACCEPTED);
	}
}
