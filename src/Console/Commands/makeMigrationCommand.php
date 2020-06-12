<?php

namespace Webcityro\Laralangs\Console\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Webcityro\Laralangs\Console\Creators\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MakeMigrationCommand extends BaseCommand {

	protected $signature = 'laralangs:make:migration {name : The name of the migration}
		{--table= : The name of the table to be created}
		{--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}';
	protected $description = 'Creates a laralangs migration for a translatable model';
	protected $creator;
	protected $composer;

	public function __construct(MigrationCreator $creator, Composer $composer) {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

	public function handle() {
		$name = Str::snake(trim($this->input->getArgument('name')));
		$table = $this->input->getOption('table');

		if (!$name || !$table) {
			$this->error('The name and table are required!');
			return;
		}

		$this->writeMigration($name, $table);
        $this->composer->dumpAutoloads();
	}

	protected function writeMigration($name, $table) {
        $file = pathinfo($this->creator->create(
            $name, $this->getMigrationPath(), $table
        ), PATHINFO_FILENAME);

        $this->line("<info>Created Laralangs Migration:</info> {$file}");
	}

	protected function getMigrationPath() {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath().'/'.$targetPath
                            : $targetPath;
        }

        return parent::getMigrationPath();
	}

    protected function usingRealPath() {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }
}
