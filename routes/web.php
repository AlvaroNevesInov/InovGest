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
    Route::post('orders/{order}/convert-to-supplier-orders', [App\Http\Controllers\OrderController::class, 'convertToSupplierOrders'])
        ->name('orders.convertToSupplierOrders');
    Route::get('orders/{order}/pdf', [App\Http\Controllers\OrderController::class, 'generatePdf'])
        ->name('orders.pdf');

    // Supplier Orders (Encomendas a Fornecedores)
    Route::resource('supplier-orders', App\Http\Controllers\SupplierOrderController::class);
    Route::post('supplier-orders/{supplierOrder}/send', [App\Http\Controllers\SupplierOrderController::class, 'send'])
        ->name('supplier-orders.send');
    Route::get('supplier-orders/{supplierOrder}/pdf', [App\Http\Controllers\SupplierOrderController::class, 'generatePdf'])
        ->name('supplier-orders.pdf');

    // Work Orders (Ordens de Trabalho)
    Route::resource('work-orders', App\Http\Controllers\WorkOrderController::class);

    // Bank Accounts (Contas Bancárias)
    Route::resource('bank-accounts', App\Http\Controllers\BankAccountController::class);

    // Client Accounts (Conta Corrente Clientes)
    Route::resource('client-accounts', App\Http\Controllers\ClientAccountController::class);

    // Supplier Invoices (Faturas Fornecedores)
    Route::resource('supplier-invoices', App\Http\Controllers\SupplierInvoiceController::class);
    Route::post('supplier-invoices/{supplierInvoice}/mark-as-paid', [App\Http\Controllers\SupplierInvoiceController::class, 'markAsPaid'])
        ->name('supplier-invoices.mark-as-paid');
    Route::get('supplier-invoices/{supplierInvoice}/download', [App\Http\Controllers\SupplierInvoiceController::class, 'download'])
        ->name('supplier-invoices.download');

    // Calendar (Calendário)
    Route::resource('calendar', App\Http\Controllers\CalendarController::class);

    // Users (Utilizadores)
    Route::resource('users', App\Http\Controllers\UserController::class);

    // Roles (Grupos de Permissões)
    Route::resource('roles', App\Http\Controllers\RoleController::class)
        ->except(['show', 'create', 'edit']);

    // Activity Log (Logs de Atividade)
    Route::get('activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])
        ->name('activity-log.index');

    // Documents
    Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])
        ->name('documents.index');
    Route::post('documents', [App\Http\Controllers\DocumentController::class, 'store'])
        ->name('documents.store');
    Route::get('documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])
        ->name('documents.download');
    Route::delete('documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])
        ->name('documents.destroy');

    // Company Switching
    Route::post('companies/{company}/switch', App\Http\Controllers\CompanySwitchController::class)
        ->name('companies.switch');

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
