<?php

namespace Webcityro\Laralangs\Tests\Feature;

use Illuminate\Support\Facades\App;
use Webcityro\Laralangs\Models\Post;
use Illuminate\Support\Facades\Artisan;
use Webcityro\Laralangs\Tests\TestCase;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webcityro\Laralangs\Tests\Traits\ResponseTrait;

class PostTest extends TestCase {

	use RefreshDatabase, ResponseTrait;

	protected function setUp():void {
		parent::setUp();
		Artisan::call('migrate', ['-v' => '', '--path' => '../../../../Database/Migrations/post/2020_06_21_212357_create_post_tables.php']);
	}

	/** @test */
	public function a_post_can_be_created() {
		$this->withoutExceptionHandling();

		$response = $this->post(route('laralangs.post.store'), $this->validData());

		$response->assertCreated();
		$this->assertCount(1, Post::all());

		$post = Post::first();
		$this->assertCount(2, $post->getLanguages());
	}

	/** @test */
	public function the_sortOrder_is_required() {
		$response = $this->postJson(route('laralangs.post.store'), $this->validData(''));

		$this->assertResponseUnprocessableWithJson($response, [
			'fields.sortOrder' => [__('validation.required', [
				'attribute' => 'sort order'
			])]
		]);
	}

	/** @test */
	public function the_active_is_required() {
		$response = $this->postJson(route('laralangs.post.store'), $this->validData(1, ''));

		$this->assertResponseUnprocessableWithJson($response, [
			'fields.active' => [__('validation.required', [
				'attribute' => 'status'
			])]
		]);
	}

	/** @test */
	public function the_title_is_required() {
		$postData = $this->validData();
		$postData['translations'][1]['title'] = '';

		$response = $this->postJson(route('laralangs.post.store'), $postData);

		$this->assertResponseUnprocessableWithJson($response, [
			'translations.1.title' => [__('validation.required', [
				'attribute' => 'title'
			])]
		]);
	}

	/** @test */
	public function the_body_is_required() {
		$postData = $this->validData();
		$postData['translations'][2]['body'] = '';

		$response = $this->postJson(route('laralangs.post.store'), $postData);

		$this->assertResponseUnprocessableWithJson($response, [
			'translations.2.body' => [__('validation.required', [
				'attribute' => 'body'
			])]
		]);
	}

	/** @test */
	public function a_post_can_be_updated() {
		$this->withoutExceptionHandling();

		$this->postJson(route('laralangs.post.store'), $this->validData());

		$post = Post::first();

		$response = $this->patchJson(route('laralangs.post.update', ['post' => $post->id]), $this->validData(2, false, ' 2'));

		$response->assertStatus(202);

		$this->assertEquals(2, $post->fresh()->sortOrder);
		$this->assertEquals(0, $post->fresh()->active);
		$this->assertEquals('Test title 2', $post->fresh()->title);
		$this->assertEquals('Test titlu 2', $post->fresh()->t(2)->title);
		$this->assertEquals('Test body 2', $post->fresh()->body);
		$this->assertEquals('Test continut 2', $post->fresh()->t(2)->body);
	}

	/** @test */
	public function a_post_can_be_Deleted() {
		$this->postJson(route('laralangs.post.store'), $this->validData());

		$post = Post::first();

		$response = $this->deleteJson(route('laralangs.post.destroy', ['post' => $post->id]));

		$response->assertStatus(202);

		$this->assertCount(0, Post::all());
		$this->assertCount(0, $post->loadLanguages());
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
}
