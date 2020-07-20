<?php

namespace Webcityro\Laralangs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest {

	protected $fieldsAttributesArray = [],
			  $translationsAttributesArray = [];

	public function validate(array $fields, array $translationsRules, ...$pattern) {
		return parent::validate($this->translationsRules($fields, $translationsRules), $pattern);
	}

	public function translationsRules(array $fields, array $translationsRules) {
		return array_merge(
			$this->makeRule($fields, 'fields'),
			$this->makeRule($translationsRules, 'translations', '.*.')
		);
	}

	protected function makeRule(array $Rules, string $key, string $separator = '.') {
		$rules = [];

		foreach ($Rules as $tField => $tRule) {
			$rules[$key.$separator.$tField] = $tRule;
			$this->addAttribute($tField, $key, $separator);
		}

		return $rules;
	}

	protected function addAttribute(string $field, string $key, string $separator = '.') {
		$bum = explode('.', $field);
		$attribute = strtolower(implode(' ', preg_split('/(?=[A-Z])\-?\_?/', array_pop($bum))));
		$this->{$key.'AttributesArray'}[$key.$separator.$field] = $attribute;
	}

	public function attributes() {
		return array_merge(
			$this->fieldsAttributesArray,
			$this->translationsAttributesArray
		);
	}
}
