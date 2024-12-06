<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('clients');
        } else if (Auth::user()->role === 'client') {
            return redirect()->route('dashboard');
        }
    }
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::middleware('role:admin')->group(function () {
        // clients
        Route::view('clients', 'clients')->name('clients');
        Volt::route('clients/{client}/requests/{clientRequest}', 'requests.admin.view-request')->name('requests.view-request');
        Volt::route('clients/{client}/deliverable/add', 'requests.admin.add-deliverable')->name('requests.add-deliverable');
        Volt::route('clients/{client}/deliverables/{clientRequest}', 'requests.admin.view-deliverable')->name('requests.view-deliverable');
        Volt::route('clients/{client}', 'clients.view-client')->name('clients.view-client');

        // users
        Route::view('users', 'users')->name('users');
        Volt::route('users/add-users', 'users.add-users')->name('add-users');
        Volt::route('users/edit-users/{id}', 'users.edit-users')->name('edit-users');
        Volt::route('users/view-users/{id}', 'users.view-users')->name('view-users');

        // more info
        Route::view('more-info', 'more-info')->name('more-info');
        Volt::route('more-info/edit/{clientType}', 'more-info.edit-more-info')->name('more-info.edit');

        // invoices
        Route::view('invoices', 'invoices')->name('invoices');
        Volt::route('invoices/{client}/show', 'invoices.show-invoice')->name('invoices.show-invoice');
        Volt::route('invoices/{client}/add', 'invoices.add-invoice')->name('invoices.add-invoice');
        Volt::route('invoices/{client}/edit/{invoice}', 'invoices.edit-invoice')->name('invoices.edit-invoice');
    });

    Route::middleware('role:client')->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::view('deliverables', 'deliverables')->name('deliverables');
        Volt::route('add-request', 'requests.client.add-request')->name('add-request');
        Volt::route('edit-request/{id}', 'requests.client.view-request')->name('edit-request');
        Volt::route('view-request-deliverables/{id}', 'view-deliverable-details')->name('view-deliverables');
        Volt::route('view-request-completed/{id}', 'view-deliverable-details')->name('view-completed');
    });
});


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
