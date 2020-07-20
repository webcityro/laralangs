<?php

namespace Webcityro\Laralangs;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webcityro\Laralangs\Models\Language;
use Webcityro\Laralangs\Facades\Laralangs;

class LaralangsServiceProvider extends ServiceProvider {

	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->registerPublishing();
		}

		$this->registerFacades();
		$this->registerResources();
		$this->registerRoutes();
		$this->registerDirectives();
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
			__DIR__.'/../database/seeds' => database_path('seeds')
		], 'laralangs-seeders');

		$this->publishes([
			__DIR__.'/Console/Creators/stubs/LaralangsServiceProvider.stub' => app_path('Providers/LaralangsServiceProvider.php')
		], 'laralangs-provider');

		$this->publishes([
			__DIR__.'/../resources/js/' => resource_path('vendor/Webcityro/Laralangs/js'),
			__DIR__.'/../resources/images/' => public_path('vendor/Webcityro/Laralangs/images')
		], 'laralangs-js');

		$this->publishes([
			__DIR__.'/../resources/views/' => resource_path('vendor/Webcityro/Laralangs/views')
		], 'laralangs-views');
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

	protected function registerDirectives() {
		Blade::directive('laralangsHead', function () {
			return "<script>
				window.Laralangs = {
					languages:".Language::all().",
					defaultLanguage: ".Language::default()
				."};
			</script>";
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
