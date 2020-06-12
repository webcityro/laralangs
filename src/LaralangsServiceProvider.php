<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\ServiceProvider;

class LaralangsServiceProvider extends ServiceProvider {

	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->registerPublishing();
		}

		$this->registerResources();
	}

	public function register() {
		$this->commands([
			\Webcityro\Laralangs\Console\Commands\MakeMigrationCommand::class
		]);
	}

	protected function registerResources() {
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
	}

	protected function registerPublishing() {
		$this->publishes([
			__DIR__.'/../config/laralangs.php' => config_path('laralangs.php')
		], 'laralangs-config');
	}
}
