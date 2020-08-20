<?php

namespace Webcityro\Laralangs\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webcityro\Laralangs\Tests\TestCase;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webcityro\Laralangs\Tests\Traits\ResponseTrait;

class LanguageTest extends TestCase {

	use RefreshDatabase, ResponseTrait;

	protected function setUp():void {
		parent::setUp();

		config([
			'filesystems' => [
				'default' => 'public'
			],
			'laralangs' => [
				'images' => [
					'max_size' => Laralangs::getImageMaxSize(),
					'directory' => Laralangs::getImagesDirectory()
				]
			]
		]);
	}

	/** @test */
	public function a_language_can_be_created() {
		$this->withoutExceptionHandling();

		$this->assertCount(2, Language::all());
		$this->assertDirectoryExists(resource_path('lang/'.Language::default()->locale));

		$response = $this->postJson(route('laralangs.language.store'), $data = $this->validData());

		$this->assertCount(3, Language::all());

		$lang = Language::orderBy('id', 'desc')->first();

		$this->assertLanguageFilesExists($lang);

		$this->assertResponseCreatedWithJson(
			$response,
			array_merge($data, [
				'id' => $lang->id,
				'image' => $lang->image
			])
		);
	}

	/** @test */
	public function a_language_can_be_updated() {
		$this->withoutExceptionHandling();
		$data = [
			'name' => 'Spanish',
			'locale' => 'es',
			'image' => UploadedFile::fake()->create('EP_es.png', Laralangs::getImageMaxSize(), 'image/png'),
			'sortOrder' => 4,
			'active' => true,
		];

		$this->updateLanguage($data);
	}

	/** @test */
	public function a_language_can_be_updated_without_an_image() {
		$this->withoutExceptionHandling();
		$data = [
			'name' => 'Spanish',
			'locale' => 'es',
			'sortOrder' => 4,
			'active' => true,
		];

		$this->updateLanguage($data);
	}

	/** @test */
	public function the_name_is_required() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'name' => ''
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'name' => [__('validation.required', [
				'attribute' => 'name'
			])]
		]);
	}

	/** @test */
	public function the_name_cant_be_more_then_30_characters() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'name' => 'abcdefghijklmnopqrstuvwxyz01234'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'name' => [__('validation.max.string', [
				'attribute' => 'name',
				'max' => 30
			])]
		]);
	}

	/** @test */
	public function the_name_must_be_unique() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'name' => 'English'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'name' => [__('validation.unique', [
				'attribute' => 'name'
			])]
		]);
	}

	/** @test */
	public function the_locale_is_required() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'locale' => ''
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'locale' => [__('validation.required', [
				'attribute' => 'locale'
			])]
		]);
	}

	/** @test */
	public function the_locale_must_be_2_characters() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'locale' => 'abc'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'locale' => [__('validation.size.string', [
				'attribute' => 'locale',
				'size' => '2'
			])]
		]);
	}

	/** @test */
	public function the_locale_must_be_unique() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'locale' => 'en'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'locale' => [__('validation.unique', [
				'attribute' => 'locale'
			])]
		]);
	}

	/** @test */
	public function the_sortOrder_is_required() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'sortOrder' => ''
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'sortOrder' => [__('validation.required', [
				'attribute' => 'sort order'
			])]
		]);
	}

	/** @test */
	public function the_sortOrder_must_be_an_number() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'sortOrder' => 'abc'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'sortOrder' => [__('validation.numeric', [
				'attribute' => 'sort order'
			])]
		]);
	}

	/** @test */
	public function the_active_is_required() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'active' => ''
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'active' => [__('validation.required', [
				'attribute' => 'active'
			])]
		]);
	}

	/** @test */
	public function the_active_must_be_boolean() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'active' => 'abc'
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'active' => [__('validation.boolean', [
				'attribute' => 'active'
			])]
		]);
	}

	/** @test */
	public function the_image_is_required_at_creation() {
		// $this->withoutExceptionHandling();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'image' => ''
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'image' => [__('validation.image', [
				'attribute' => 'image'
			])]
		]);
	}

	/** @test */
	public function the_image_must_be_an_image() {
		// $this->withoutExceptionHandling();
		Storage::fake();

		$response = $this->postJson(route('laralangs.language.store'), array_merge(
			$this->validData(),
			[
				'image' => UploadedFile::fake()->create('doc.pdf', Laralangs::getImageMaxSize(), 'application/pdf'),
			]
		));

		$this->assertResponseUnprocessableWithJson($response, [
			'image' => [__('validation.image', [
				'attribute' => 'image'
			])]
		]);
	}

	/** @test */
	public function a_language_can_be_Deleted() {
		$this->withoutExceptionHandling();

		$this->assertCount(2, Language::all());
		$response = $this->postJson(route('laralangs.language.store'), $data = $this->validData());
		$response->assertCreated();
		$this->assertCount(3, Language::all());

		$lang = Language::orderBy('id', 'desc')->first();
		$this->assertLanguageFilesExists($lang);

		$response = $this->deleteJson(route('laralangs.language.destroy', $lang->id));
		$response->assertOk();
		$this->assertCount(2, Language::all());

		Storage::assertMissing(Laralangs::getImagesDirectory() . '/' . $lang->image);
		Storage::assertMissing(resource_path('lang/' . $lang->locale));

		if (File::exists(resource_path('lang/' . Language::default()->locale . '.json'))) {
			Storage::assertMissing(resource_path('lang/' . $lang->locale . '.json'));
		}

		$this->assertResponseOkWithJson(
			$response,
			array_merge($data, [
				'id' => $lang->id,
				'image' => $lang->image,
				'sortOrder' => (string)$data['sortOrder'],
				'active' => $data['active'] ? '1' : '0',
			])
		);
	}

	protected function assertLanguageFilesExists($lang)	{
		$this->assertCount(1, Storage::allFiles(Laralangs::getImagesDirectory()));
		Storage::assertExists(Laralangs::getImagesDirectory() . '/' . $lang->image);

		$this->assertDirectoryExists(resource_path('lang/' . $lang->locale));

		if (File::exists(resource_path('lang/' . Language::default()->locale . '.json'))) {
			$this->assertFileExists(resource_path('lang/' . $lang->locale . '.json'));
		}

		$defaultLanguageFiles = File::allFiles(resource_path('lang/' . Language::
		default()->locale));

		foreach ($defaultLanguageFiles as $file) {
			$filePath = resource_path('lang/' . $lang->locale . '/' . $file->getFilename());

			$this->assertFileExists($filePath);
		}
	}

	private function updateLanguage($data) {
		$this->assertCount(2, Language::all());
		$this->assertDirectoryExists(resource_path('lang/' . Language::default()->locale));

		$response = $this->postJson(route('laralangs.language.store'), $this->validData());

		$this->assertCount(3, Language::all());

		$response = $this->patchJson(route('laralangs.language.update', $response->json()['id']), $data);

		$lang = Language::orderBy('id', 'desc')->first();

		$this->assertLanguageFilesExists($lang);

		$this->assertResponseOkWithJson(
			$response,
			array_merge($data, [
				'id' => $lang->id,
				'image' => $lang->image,
				'sortOrder' => (string)$data['sortOrder'],
				'active' => $data['active'] ? '1' : '0',
			])
		);
	}

	protected function validData() {
		Storage::fake();
		return [
			'name' => 'French',
            'locale' => 'fr',
            'image' => UploadedFile::fake()->create('FR_fr.png', Laralangs::getImageMaxSize(), 'image/png'),
            'sortOrder' => 3,
            'active' => false,
		];
	}
}
