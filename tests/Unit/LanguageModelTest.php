<?php

namespace Webcityro\Laralangs\Tests\Unit;

use Illuminate\Support\Facades\App;
use Webcityro\Laralangs\Tests\TestCase;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageModelTest extends TestCase {

	use RefreshDatabase;

	/** @test */
	public function languages_table_get_seeded() {
		$this->assertDatabaseHas('languages', [
			'locale' => 'en',
			'locale' => 'ro',
		]);
	}

	/** @test */
	public function languages_default_method_returns_the_default_language_row() {
		$defaultLanguage = Language::default();

		$this->assertEquals(App::getLocale(), $defaultLanguage->locale);
	}

	/** @test */
	public function languages_default_method_returns_the_default_language_row_with_locale_as_primary_key() {
		config()->set(['laralangs.primaryKey' => 'locale']);
		Laralangs::setDefaultLanguage('en');
		$defaultLanguage = Language::default();

		$this->assertEquals(App::getLocale(), $defaultLanguage->locale);
	}

	/** @test */
	public function languages_model_active_method_returns_only_active_rows() {
		$france = Language::create([
			'name' => 'France',
            'locale' => 'fr',
            'image' => 'FR_fr.gif',
            'sortOrder' => 3,
            'active' => false,
		]);

		$this->assertCount(2, Language::active()->get());
		$this->assertNull(Language::active()->where('locale', 'fr')->first());
	}
}
