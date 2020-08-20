<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\Str;

class Laralangs {

	protected $acceptedPrimaryKeys = ['id', 'locale'];

	protected $languagePrimaryKey;

    public function getLanguageTableName($table) {
		return Str::singular($table).config('laralangs.languageTableSuffix', '_languages');
	}

    public function getLanguagePivotColumnName() {
		return 'language'.config('laralangs.idSuffix', 'ID');
	}

    public function getPivotColumnName($table) {
		return Str::camel(Str::singular($table)).config('laralangs.idSuffix', 'ID');
	}

	public function getDefaultLanguagePrimaryKey() {
		if (!is_null(config('laralangs.primaryKey')) &&
			!in_array(config('laralangs.primaryKey'), $this->acceptedPrimaryKeys)) {
			dd('Webcityro\Laralangs: The key "'.config('laralangs.primaryKey').'" is invalid. The accepted keys are: "'.implode('", "', $this->acceptedPrimaryKeys).'" use one of them.');
		}
		return config('laralangs.primaryKey', 'id');
	}

	public function getDefaultLanguageValue() {
		return $this->languagePrimaryKey;
	}

	public function setDefaultLanguage($key) {
		$this->languagePrimaryKey = $key;
	}

	public function getRoutesPrefix() {
		return config('laralangs.routePrefix', 'laralangs');
	}

	public function getImagesDirectory() {
		return config('laralangs.images.directory', 'images/language_flags');
	}

	public function getImageMaxSize() {
		return config('laralangs.images.max_size', 1000);
	}
}
