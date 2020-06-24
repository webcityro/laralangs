<?php

namespace Webcityro\Laralangs\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

use Webcityro\Laralangs\LaralangsServiceProvider;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Tests\Models\Blog;

class ModelTest extends TestCase {

	use RefreshDatabase;

	protected function setUp():void {
		parent::setUp();
		Artisan::call('migrate', ['-v' => '', '--path' => '../../../../tests/Database/Migrations/2020_06_21_212357_create_blog_tables.php']);
	}

	/** @test */
	public function creating_an_multi_language_db_entry_and_fetching_it_beck() {
		$blog = Blog::createWithLanguages([
			'sortOrder' => 1,
			'active' => true
		], [
			1 => [
				'title' => 'Test title',
				'body' => 'Test body'
			],
			2 => [
				'title' => 'Test titlu',
				'body' => 'Test continut'
			]
		]);

		$this->assertInstanceOf(Blog::class, $blog);
		$this->assertEquals(1, $blog->sortOrder);
		$this->assertTrue($blog->active);
		$this->assertEquals('Test title', $blog->title);
		$this->assertEquals('Test body', $blog->body);
		$this->assertEquals('Test titlu', $blog->t(2)->title);
		$this->assertEquals('Test continut', $blog->t('ro')->body);
	}

	/** @test */
	public function updating_an_multi_language_db_entry_and_fetching_it_beck() {
		$blog = Blog::createWithLanguages([
			'sortOrder' => 1,
			'active' => true
		], [
			1 => [
				'title' => 'Test title',
				'body' => 'Test body'
			],
			2 => [
				'title' => 'Test titlu',
				'body' => 'Test continut'
			]
		]);

		$blog->updateWithLanguages([
			'sortOrder' => 2,
			'active' => true
		], [
			1 => [
				'title' => 'Test title 2',
				'body' => 'Test body 2'
			],
			2 => [
				'title' => 'Test titlu 2',
				'body' => 'Test continut 2'
			]
		]);

		$this->assertInstanceOf(Blog::class, $blog);
		$this->assertEquals(2, $blog->sortOrder);
		$this->assertEquals('Test title 2', $blog->title);
		$this->assertEquals('Test body 2', $blog->body);
		$this->assertEquals('Test titlu 2', $blog->t(2)->title);
		$this->assertEquals('Test continut 2', $blog->t('ro')->body);
	}

	/** @test */
	public function the_language_entries_get_deleted_when_deleting_the_parent_entry() {
		$blog = Blog::createWithLanguages([
			'sortOrder' => 1,
			'active' => true
		], [
			1 => [
				'title' => 'Test title',
				'body' => 'Test body'
			],
			2 => [
				'title' => 'Test titlu',
				'body' => 'Test continut'
			]
		]);

		$blog->delete();

		$this->assertCount(0, Blog::where('id', $blog->id)->get());
		$this->assertCount(0, $blog->loadLanguages());
	}

	/** @test */
	public function the_get_languages_array_method_returns_the_models_languages_as_an_array_with_the_language_id_as_the_array_key() {
		$blog = Blog::createWithLanguages([
			'sortOrder' => 1,
			'active' => true
		], [
			1 => [
				'title' => 'Test title',
				'body' => 'Test body'
			],
			2 => [
				'title' => 'Test titlu',
				'body' => 'Test continut'
			]
		]);

		$this->assertJsonStringEqualsJsonString(collect([
			1 => [
				'title' => 'Test title',
				'body' => 'Test body'
			],
			2 => [
				'title' => 'Test titlu',
				'body' => 'Test continut'
			]
		])->toJson(), $blog->getLanguagesArray()->toJson());

	}
}
