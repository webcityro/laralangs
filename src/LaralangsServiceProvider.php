<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\ServiceProvider;

class LaralangsServiceProvider extends ServiceProvider {

	public function boot() {
		$this->registerResources();
	}

	public function register() {
		//
	}

	protected function registerResources() {
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
	}
}
