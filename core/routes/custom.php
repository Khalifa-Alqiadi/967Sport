<?php

use App\Http\Controllers\Custom\CustomController;
use App\Http\Controllers\Custom\FootballController;
use Illuminate\Support\Facades\Route;

Route::get('/competitions', [FootballController::class, 'competitions'])->name('sport.competitions');
Route::get('/league/{league}', [FootballController::class, 'league'])->whereNumber('league')->name('sport.league');
Route::get('/matches', [FootballController::class, 'matches'])->name('sport.matches');
Route::get('/match/{fixture}', [FootballController::class, 'match'])->whereNumber('fixture')->name('sport.match');

Route::prefix('{lang}')
    ->where(['lang' => 'en'])
    ->group(function (): void {
        Route::get('/competitions', [FootballController::class, 'localizedCompetitions'])->name('sport.localized.competitions');
        Route::get('/league/{league}', [FootballController::class, 'localizedLeague'])->whereNumber('league')->name('sport.localized.league');
        Route::get('/matches', [FootballController::class, 'localizedMatches'])->name('sport.localized.matches');
        Route::get('/match/{fixture}', [FootballController::class, 'localizedMatch'])->whereNumber('fixture')->name('sport.localized.match');
    });

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
