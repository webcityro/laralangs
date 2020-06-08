<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration {

   public function up() {
      Schema::create('languages', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->string('code');
         $table->string('image');
         $table->integer('sortOrder')->unsigned();
         $table->boolean('active')->default(1);
      });
   }

   public function down() {
      Schema::dropIfExists('languages');
   }
}
