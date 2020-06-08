<?php

namespace Webcityro\Laralangs\Tests;

use Webcityro\Laralangs\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageModelTest extends TestCase {

	use RefreshDatabase;

	/** @test */
	public function languages_table_get_seeded() {
		$this->assertCount(2, Language::all());
	}

	/** @test */
	public function languages_model_active_method_returns_only_active_rows() {
		$france = Language::create([
			'name' => 'France',
            'code' => 'fr',
            'image' => 'FR_fr.gif',
            'sortOrder' => 3,
            'active' => false,
		]);

		$this->assertCount(2, Language::active()->get());
		$this->assertNull(Language::active()->where('code', 'fr')->first());
	}

	/** @test */
	public function languages_table_has_english_and_romanian_rows() {
		$this->assertEquals('English', Language::where('code', 'en')->first()->name);
		$this->assertEquals('Romana', Language::where('code', 'ro')->first()->name);
	}
}
