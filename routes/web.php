<?php

use App\Http\Controllers\ElementoLoginController;
use App\Http\Controllers\ElementoLogoutController;
use App\Http\Controllers\ElementoLookupController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Models\Elemento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/welcome', '/login');
Route::redirect('/', '/login')->name('home');
Route::view('/admin/login', 'pages.auth.admin-login')->name('admin.login');
Route::post('/elementos/lookup', ElementoLookupController::class)
    ->middleware('throttle:elemento-lookup')
    ->name('elementos.lookup');
Route::post('/elementos/login', ElementoLoginController::class)
    ->middleware('throttle:login')
    ->name('elementos.login');
Route::post('/elementos/logout', ElementoLogoutController::class)
    ->middleware('actor.auth')
    ->name('elementos.logout');

Route::middleware('actor.auth')->get('/dashboard', function (Request $request) {
    $actor = $request->user();

    abort_unless($actor && in_array(get_class($actor), [Elemento::class, User::class], true), 403);

    return view('dashboard.access', [
        'actor' => $actor,
    ]);
})->name('dashboard');

Route::middleware('actor.auth')->get('/account/settings', function (Request $request) {
    return view('account.settings', ['actor' => $request->user()]);
})->name('account.settings');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('team.dashboard');
    });

require __DIR__.'/settings.php';
