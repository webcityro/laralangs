<?php

namespace Webcityro\Laralangs\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

use Webcityro\Laralangs\LaralangsServiceProvider;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Tests\Models\Post;

class ModelTest extends TestCase {

	use RefreshDatabase;

	protected function setUp():void {
		parent::setUp();
		Artisan::call('migrate', ['-v' => '', '--path' => '../../../../tests/Database/Migrations/2020_06_21_212357_create_post_tables.php']);
	}

	/** @test */
	public function creating_an_multi_language_db_entry_and_fetching_it_beck() {
		$post = Post::createWithLanguages([
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

		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals(1, $post->sortOrder);
		$this->assertTrue($post->active);
		$this->assertEquals('Test title', $post->title);
		$this->assertEquals('Test body', $post->body);
		$this->assertEquals('Test titlu', $post->t(2)->title);
		$this->assertEquals('Test continut', $post->t('ro')->body);
	}

	/** @test */
	public function updating_an_multi_language_db_entry_and_fetching_it_beck() {
		$post = Post::createWithLanguages([
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

		$post->updateWithLanguages([
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

		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals(2, $post->sortOrder);
		$this->assertEquals('Test title 2', $post->title);
		$this->assertEquals('Test body 2', $post->body);
		$this->assertEquals('Test titlu 2', $post->t(2)->title);
		$this->assertEquals('Test continut 2', $post->t('ro')->body);
	}

	/** @test */
	public function the_language_entries_get_deleted_when_deleting_the_parent_entry() {
		$post = Post::createWithLanguages([
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

		$post->delete();

		$this->assertCount(0, Post::where('id', $post->id)->get());
		$this->assertCount(0, $post->loadLanguages());
	}

	/** @test */
	public function the_get_languages_array_method_returns_the_models_languages_as_an_array_with_the_language_id_as_the_array_key() {
		$post = Post::createWithLanguages([
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
		])->toJson(), $post->getLanguagesArray()->toJson());

	}
}
