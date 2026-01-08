<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Entities (Clientes/Fornecedores)
    Route::resource('entities', App\Http\Controllers\EntityController::class);

    // Contacts
    Route::resource('contacts', App\Http\Controllers\ContactController::class);

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('countries', App\Http\Controllers\CountryController::class)
            ->except(['show', 'create', 'edit']);
        Route::resource('contact-functions', App\Http\Controllers\ContactFunctionController::class)
            ->except(['show', 'create', 'edit']);
        Route::resource('calendar-types', App\Http\Controllers\CalendarTypeController::class)
            ->except(['show', 'create', 'edit']);
        Route::resource('calendar-actions', App\Http\Controllers\CalendarActionController::class)
            ->except(['show', 'create', 'edit']);
        Route::resource('vat-rates', App\Http\Controllers\VatRateController::class)
            ->except(['show', 'create', 'edit']);
        Route::resource('articles', App\Http\Controllers\ArticleController::class);
        Route::resource('companies', App\Http\Controllers\CompanyController::class);
    });
});

require __DIR__.'/auth.php';
