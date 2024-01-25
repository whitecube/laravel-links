<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/blog', function (Category $category) {
    return view('welcome');
})->name('posts.index');

Route::get('/blog/{category}', function (Category $category) {
    return view('welcome');
})->name('posts.category');

Route::get('/blog/{category}/{post}', function (Category $category, Post $post) {
    return view('welcome');
})->name('posts.item');
