<?php

namespace Webcityro\Laralangs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest {

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
		}

		return $rules;
	}
}
