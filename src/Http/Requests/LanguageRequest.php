<?php

namespace Webcityro\Laralangs\Http\Requests;

use Illuminate\Support\Facades\Storage;
use Webcityro\Laralangs\Facades\Laralangs;
use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest {

	protected $imageName;

	public function authorize() {
		return true;
	}

	public function rules()	{
		return [
			'name' => 'required|max:30|unique:languages,name',
			'locale' => 'required|size:2|unique:languages,locale',
			'image' => 'sometimes|image',
			'sortOrder' => 'required|numeric',
			'active' => 'required|boolean'
		];
	}

	public function uploadImage() {
		if (!$this->hasFile('image')) {
			return $this->imageName = optional($this->language)->image;
		}

		$this->deleteImageIfExists();
		$this->imageName = $this->name.'_'.$this->input('locale').'.'.$this->image->getClientOriginalExtension();
		return Storage::putFileAs(
			Laralangs::getImagesDirectory(),
			$this->image,
			$this->imageName, [
				'disk' => 'public'
			]
		);
	}

	public function validated() {
		return array_merge($this->validator->validated(), ['image' => $this->imageName]);
	}

	protected function deleteImageIfExists() {
		if ($this->language === null || !$this->hasFile('image')) {
			return;
		}

		Storage::disk('public')->delete(Laralangs::getImagesDirectory().'/'.$this->language->image);
	}
}
