<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Webcityro\Laralangs\Facades\Laralangs;

class LaralangsServiceProvider extends ServiceProvider {

	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->registerPublishing();
		}

		$this->registerFacades();
		$this->registerResources();
		$this->registerRoutes();
	}

	public function register() {
		$this->commands([
			\Webcityro\Laralangs\Console\Commands\MakeMigrationCommand::class
		]);
	}

	protected function registerResources() {
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'laralangs');
	}

	protected function registerPublishing() {
		$this->publishes([
			__DIR__.'/../config/laralangs.php' => config_path('laralangs.php')
		], 'laralangs-config');

		$this->publishes([
			__DIR__.'/Console/Creators/stubs/LaralangsServiceProvider.stub' => app_path('Providers/LaralangsServiceProvider.php')
		], 'laralangs-provider');
	}

	protected function registerFacades() {
		$this->app->singleton('Laralangs', function ($app) {
			return new \Webcityro\Laralangs\Laralangs();
		});
	}

	protected function registerRoutes() {
		Route::group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		});
	}

	protected function routeConfiguration()
	{
		return [
			'prefix' => Laralangs::getRoutesPrefix(),
			'namespace' => 'Webcityro\Laralangs\Http\Controllers',
			'as' => 'laralangs.',
			'middleware' => 'web'
		];
	}
}
