<?php

namespace Webcityro\Laralangs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;
use Symfony\Component\HttpFoundation\Response;
use Webcityro\Laralangs\Http\Requests\LanguageRequest;

class LanguagesController extends Controller {

	public function store(LanguageRequest $request)	{
		$image = $request->uploadImage();
		$data = Language::create($request->validated());
		$this->manageFiles(Language::default()->locale, $data['locale']);
		return response()->json($data, Response::HTTP_CREATED);
	}

	public function update(Language $language, LanguageRequest $request)	{
		$image = $request->uploadImage();
		$language->update($data = $request->validated());

		if ($language->locale !== $data['locale']) {
			$this->manageFiles($language->locale, $data['locale']);
		}

		return response()->json($language->fresh(), Response::HTTP_OK);
	}

	public function destroy(Language $language)	{
		File::deleteDirectory(resource_path('lang/'.$language->locale));
		File::delete(resource_path('lang/'.$language->locale.'.json'));
		Storage::disk('public')->delete(Laralangs::getImagesDirectory().'/'.$language->image);
		$language->delete();

		return response()->json($language, Response::HTTP_OK);
	}

	private function manageFiles($originalLocale, $newLocale) {
		$action = is_null(request('language')) ? 'copy' : 'move';

		File::{$action.'Directory'}(
			resource_path('lang/'.$originalLocale),
			resource_path('lang/'.$newLocale)
		);

		if (File::exists(resource_path('lang/'.$originalLocale.'.json'))) {
			File::{$action.''}(
				resource_path('lang/'.$originalLocale.'.json'),
				resource_path('lang/'.$newLocale.'.json')
			);
		}
	}
}
