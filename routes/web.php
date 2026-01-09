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
    Route::post('entities/validate-vies', [App\Http\Controllers\EntityController::class, 'validateVies'])
        ->name('entities.validateVies');

    // Contacts
    Route::resource('contacts', App\Http\Controllers\ContactController::class);

    // Proposals
    Route::resource('proposals', App\Http\Controllers\ProposalController::class);
    Route::post('proposals/{proposal}/close', [App\Http\Controllers\ProposalController::class, 'close'])
        ->name('proposals.close');
    Route::post('proposals/{proposal}/convert-to-order', [App\Http\Controllers\ProposalController::class, 'convertToOrder'])
        ->name('proposals.convertToOrder');
    Route::get('proposals/{proposal}/pdf', [App\Http\Controllers\ProposalController::class, 'generatePdf'])
        ->name('proposals.pdf');

    // Orders (Encomendas)
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::post('orders/{order}/close', [App\Http\Controllers\OrderController::class, 'close'])
        ->name('orders.close');
    Route::get('orders/{order}/pdf', [App\Http\Controllers\OrderController::class, 'generatePdf'])
        ->name('orders.pdf');

    // Documents
    Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])
        ->name('documents.index');
    Route::post('documents', [App\Http\Controllers\DocumentController::class, 'store'])
        ->name('documents.store');
    Route::get('documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])
        ->name('documents.download');
    Route::delete('documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])
        ->name('documents.destroy');

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
