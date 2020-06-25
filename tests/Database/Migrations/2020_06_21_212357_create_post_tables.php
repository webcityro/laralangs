<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePostTables extends Migration {

	public function up() {
		Schema::create('posts', function (Blueprint $table) {
			$table->id();
			$table->unsignedInteger('sortOrder');
			$table->boolean('active');
			$table->timestamps();
		});

		Schema::create('post_languages', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('postID');
			$table->unsignedBigInteger('languageID');
			$table->string('title');
			$table->text('body');

			$table->foreign('postID')->references('id')->on('posts')
                ->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down() {
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('posts');
		Schema::dropIfExists('post_languages');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
