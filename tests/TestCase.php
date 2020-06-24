<?php

namespace Webcityro\Laralangs\Tests;

use LanguagesSeeder;
use Illuminate\Support\Facades\Artisan;
use Webcityro\Laralangs\LaralangsServiceProvider;
use Webcityro\Laralangs\Facades\Laralangs;

class TestCase extends \Orchestra\Testbench\TestCase {

	protected function setUp():void {
		parent::setUp();
		Artisan::call('migrate');
		Artisan::call('db:seed', ['--class' => 'LanguagesSeeder']);
		Laralangs::setDefaultLanguage(1);
	}

	protected function getPackageProviders($app):array {
		return [
			LaralangsServiceProvider::class
		];
	}

	protected function getEnvironmentSetUp($app):void {
		$app['config']->set('database.default', 'testdb');
		$app['config']->set('database.connections.testdb', [
			'driver' => 'sqlite',
			'database' => ':memory:'
		]);
	}

	public function tearDown():void {
		Artisan::call('migrate:reset');
		parent::tearDown();
	}
}
