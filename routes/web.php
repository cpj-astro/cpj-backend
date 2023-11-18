<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/terms', function () {
//     return view('welcome');
// });

Route::get('terms', 'Controller@terms')->name('terms');
Route::get('privacy', 'Controller@privacy')->name('privacy');
Route::get('disclaimer', 'Controller@disclaimer')->name('disclaimer');

Route::get('mango-777-terms', 'Controller@terms_mango')->name('mango-777-terms');
Route::get('mango-777-privacy', 'Controller@privacy_mango')->name('mango-777-privacy');
Route::get('mango-777-disclaimer', 'Controller@disclaimer_mango')->name('mango-777-disclaimer');
