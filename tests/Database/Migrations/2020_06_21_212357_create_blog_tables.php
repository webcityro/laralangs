<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBlogTables extends Migration {

	public function up() {
		Schema::create('blogs', function (Blueprint $table) {
			$table->id();
			$table->unsignedInteger('sortOrder');
			$table->boolean('active');
			$table->timestamps();
		});

		Schema::create('blog_languages', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('blogID');
			$table->unsignedBigInteger('languageID');
			$table->string('title');
			$table->text('body');

			$table->foreign('blogID')->references('id')->on('blogs')
                ->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down() {
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('blogs');
		Schema::dropIfExists('blog_languages');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
