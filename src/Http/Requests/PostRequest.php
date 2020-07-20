<?php

namespace Webcityro\Laralangs\Http\Requests;

use Webcityro\Laralangs\Facades\Laralangs;

class PostRequest extends Request {
	public function rules() {
		return $this->translationsRules([
			'active' => 'required',
			'sortOrder' => 'required',
		], [
			'title' => 'required|unique:'.Laralangs::getLanguageTableName('posts').',title'.($this->post ? ','.$this->post->id : ''),
			'body' => 'required',
		]);
	}

	public function attributes() {
		return array_merge(parent::attributes(), [
			'fields.active' => 'status'
		]);
	}
}
