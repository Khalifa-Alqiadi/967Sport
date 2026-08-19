<?php

use App\Http\Controllers\Custom\CustomController;
use Illuminate\Support\Facades\Route;

// private route example ( require login )
/*
Route::Group(['prefix' => config('smartend.backend_path'), 'middleware' => ['auth', 'LanguageSwitcher']], function () {
    Route::get('/custom-page', [CustomController::class, 'custom_page']);
});
*/

// public route example
/*
Route::get('/custom-page', [CustomController::class, 'custom_page']);
*/
