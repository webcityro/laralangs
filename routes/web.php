<?php

use Illuminate\Support\Facades\Route;

Route::resource('/post', 'PostsController');
Route::resource('/language', 'LanguagesController');
