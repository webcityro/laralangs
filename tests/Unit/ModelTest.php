<?php

namespace Webcityro\Laralangs\Tests\Unit;

use Webcityro\Laralangs\Models\Post;
use Illuminate\Support\Facades\Artisan;

use Webcityro\Laralangs\Tests\TestCase;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\LaralangsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelTest extends TestCase {

	use RefreshDatabase;

	protected function setUp():void {
		parent::setUp();
		Artisan::call('migrate', ['-v' => '', '--path' => '../../../../Database/Migrations/post/2020_06_21_212357_create_post_tables.php']);
	}

	/** @test */
	public function creating_an_multi_language_db_entry_and_fetching_it_beck() {
		$newPost = $this->newPost();
		$this->assertInstanceOf(Post::class, $newPost);

		$post = Post::first();

		$this->assertEquals(1, $post->sortOrder);
		$this->assertEquals('1', $post->active);
		$this->assertEquals('Test title', $post->title);
		$this->assertEquals('Test body', $post->body);
		$this->assertEquals('Test titlu', $post->t(2)->title);
		$this->assertEquals('Test continut', $post->t('ro')->body);
	}

	/** @test */
	public function updating_an_multi_language_db_entry_and_fetching_it_beck() {
		$post = $this->newPost();

		$post->updateWithLanguages(
			$this->validData(2, true)['fields'],
			$this->validData(2, true, ' 2')['translations']
		);

		$this->assertInstanceOf(Post::class, $post);
		$this->assertEquals(2, $post->sortOrder);
		$this->assertEquals('Test title 2', $post->title);
		$this->assertEquals('Test body 2', $post->body);
		$this->assertEquals('Test titlu 2', $post->t(2)->title);
		$this->assertEquals('Test continut 2', $post->t('ro')->body);
	}

	/** @test */
	public function the_language_entries_get_deleted_when_deleting_the_parent_entry() {
		$post = $this->newPost();

		$post->delete();

		$this->assertCount(0, Post::where('id', $post->id)->get());
		$this->assertCount(0, $post->loadLanguages());
	}

	/** @test */
	public function the_get_languages_array_method_returns_the_models_languages_as_an_array_with_the_language_id_as_the_array_key() {
		$post = $this->newPost();

		$this->assertJsonStringEqualsJsonString(collect($this->validData()['translations'])->toJson(), $post->getLanguagesArray()->toJson());

	}

	/** @test */
	public function the_modal_returns_the_right_format_when_converted_to_json() {
		$post = $this->newPost();

		$this->assertJsonStringEqualsJsonString(collect(array_merge($this->validData(), [
			'fields' => array_merge($this->validData()['fields'], [
				'id' => $post->id,
				'updated_at' => now()->format('Y-m-d H:i:s'),
				'created_at' => now()->format('Y-m-d H:i:s')
			])
		]))->toJson(), $post->toJson());

	}

	protected function validData($sortOrder = 1, $active = true, $append = '') {
		return [
			'fields' => [
				'sortOrder' => $sortOrder,
				'active' => $active
			],
			'translations' => [
				1 => [
					'title' => 'Test title'.$append,
					'body' => 'Test body'.$append
				],
				2 => [
					'title' => 'Test titlu'.$append,
					'body' => 'Test continut'.$append
				]
			]
		];
	}

	protected function newPost() {
		return Post::createWithLanguages(
			$this->validData()['fields'],
			$this->validData()['translations']
		);
	}
}
