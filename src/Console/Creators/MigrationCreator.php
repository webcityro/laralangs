<?php

namespace Webcityro\Laralangs\Console\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Closure;

class MigrationCreator {

	protected $postCreate = [];
	protected $files;

	public function __construct(FileSystem $files) {
		$this->files = $files;
	}

	public function create($name, $path, $table) {
		$this->ensureMigrationDoesntAlreadyExist($name);

		$stub = $this->getStub();

		$this->files->put(
			$path = $this->getPath($name, $path),
			$this->populateStub($name, $stub, $table)
		);
		$this->firePostCreateHooks($table);

		return $path;
	}

	protected function populateStub($name, $stub, $table) {
		$stub = str_replace('DummyClass', $this->getClassName($name), $stub);
		$stub = str_replace('DummyTable', $table, $stub);
		$stub = str_replace('DummyLanguageTable', $this->getLanguageTableName($table), $stub);
		$stub = str_replace('DummyPivotColumn', $this->getPivotColumnName($table), $stub);

		return $stub;
	}

	protected function getClassName($name) {
		return Str::studly($name);
	}

	protected function getLanguageTableName($table) {
		return $table.config('laralangs.languageTableSuffix', '_language');
	}

	protected function getPivotColumnName($table) {
		return Str::camel(Str::singular($table)).config('laralangs.idSuffix', 'ID');
	}

	protected function getStub() {
		return $this->files->get(__DIR__.'/stubs/migration.stub');
	}

	protected function getPath($name, $path) {
		return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
	}

	protected function getDatePrefix() {
		return date('Y_m_d_His');
	}

	protected function ensureMigrationDoesntAlreadyExist($name) {
		if (class_exists($className = $this->getClassName($name))) {
			throw new InvalidArgumentException("A {$className} class already exists.");
		}
	}

	public function afterCreate(Closure $callback) {
		$this->postCreate[] = $callback;
	}

	protected function firePostCreateHooks($table) {
		foreach ($this->postCreate as $callback) {
			call_user_func($callback, $table);
		}
	}
}
