<?php

namespace Webcityro\Laralangs\Http\Requests;

class PostRequest extends Request {

	public function rules() {
		return $this->translationsRules([
			'active' => 'required',
			'sortOrder' => 'required',
		], [
			'title' => 'required',
			'body' => 'required',
		]);
	}
}
