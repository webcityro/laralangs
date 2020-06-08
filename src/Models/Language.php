<?php

namespace Webcityro\Laralangs\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model {
	protected $fillable = ['name', 'code', 'image', 'sortOrder', 'active'];

	public    $timestamps = false;

	public function scopeActive($query) {
		return $query->where('active', true);
	}
}
