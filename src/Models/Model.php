<?php

namespace Webcityro\Laralangs\Models;

use Illuminate\Support\Facades\DB;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;

class Model extends \Illuminate\Database\Eloquent\Model {

	protected $languages;

	public function languagesQuery() {
		return $this->languageTable()->where(Laralangs::getPivotColumnName($this->getTable()), $this->id);
    }

	public function loadLanguages() {
		return $this->languages = $this->languagesQuery()->get();
    }

	public function getLanguages() {
		if (is_null($this->languages)) {
			$this->loadLanguages();
		}
		return $this->languages;
    }

	protected function getLanguageTableName() {
		return Laralangs::getLanguageTableName($this->getTable());
	}

	protected function languageTable() {
		return DB::table($this->getLanguageTableName());
	}

	protected function languages() {
		return $this->languageTable()->where(Laralangs::getPivotColumnName($this->getTable()), $this->id);
	}

	protected function createWithLanguages($fields, $languageFields) {
		$newModel = $this->create($fields);
		$this->languageTable()->insert($this->makeLanguageTableRowsArray($newModel, $languageFields));
		return $newModel;
	}

	public function updateWithLanguages($fields, $languageFields) {
		$this->update($fields);

		foreach ($languageFields as $languageID => $columns) {
			$this->languageTable()->updateOrInsert(
				$this->getLanguagePivotColumnsArray($this->id, $languageID),
				$columns
			);
		}

		$this->loadLanguages();
		return $this;
	}

	public function getLanguagesArray() {
		return $this->getLanguages()->mapWithKeys(function ($translation) {
			return [
				$translation->{Laralangs::getLanguagePivotColumnName()} => collect($translation)->except([
					'id',
					Laralangs::getPivotColumnName($this->getTable()),
					Laralangs::getLanguagePivotColumnName()
				])
			];
		});
	}

	protected function makeLanguageTableRowsArray($newModel, $fields) {
		$newFields = [];

		foreach ($fields as $languageID => $columns) {
			$newFields[] = array_merge(
				$columns,
				$this->getLanguagePivotColumnsArray($newModel->id, $languageID)
			);
		}

		return $newFields;
	}

	protected function getLanguagePivotColumnsArray(int $id, int $languageID) {
		return [
			Laralangs::getPivotColumnName($this->getTable()) => $id,
			Laralangs::getLanguagePivotColumnName() => $languageID
		];
	}

	public function delete() {
		parent::delete();
		$this->languagesQuery()->delete();
	}

	public function t($locale) {
		$languageID = (int) is_numeric($locale) ? $locale : Language::where('locale', $locale)->first()->id;
		return $this->getLanguages()->where(Laralangs::getLanguagePivotColumnName(), $languageID)->first();
	}

	public function toJson($options = 0) {
		return collect([
			'fields' => $this->attributesForJson(),
			'translations' => $this->getLanguagesArray()
		])->toJson();
	}

	protected function attributesForJson() {
		return $this->attributes;
	}

	public function __get($key)	{
		if (isset($this->attributes[$key])) {
			return parent::__get($key);
		}
		return $this->getLanguages()->where(Laralangs::getLanguagePivotColumnName(), Language::default()->id)->first()->{$key};
	}
}
