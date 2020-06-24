<?php

use Illuminate\Database\Seeder;
use Webcityro\Laralangs\Models\Language;

class LanguagesSeeder extends Seeder {
   public function run() {
      Language::insert([
         [
            'name' => 'English',
            'locale' => 'en',
            'image' => 'EN_us.gif',
            'sortOrder' => 1,
            'active' => true,
         ], [
            'name' => 'Romana',
            'locale' => 'ro',
            'image' => 'RO_ro.gif',
            'sortOrder' => 2,
            'active' => true,
         ]
      ]);
   }
}
