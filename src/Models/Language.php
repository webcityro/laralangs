<?php

namespace Webcityro\Laralangs\Models;

use Illuminate\Database\Eloquent\Model;
use Webcityro\Laralangs\Facades\Laralangs;

class Language extends Model {
	protected $fillable = ['name', 'locale', 'image', 'sortOrder', 'active'];

	public    $timestamps = false;

	public function scopeActive($query) {
		return $query->where('active', true);
	}

	public function scopeDefault($query) {
		return $this->active()->where(Laralangs::getDefaultLanguagePrimaryKey(), Laralangs::getDefaultLanguageValue())->first();
	}
}
