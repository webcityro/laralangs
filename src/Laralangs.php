<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\Str;

class Laralangs {

    public function getLanguageTableName($table) {
		return $table.config('laralangs.languageTableSuffix', '_language');
	}

    public function getPivotColumnName($table) {
		return Str::camel(Str::singular($table)).config('laralangs.idSuffix', 'ID');
	}
}
