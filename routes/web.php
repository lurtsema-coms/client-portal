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
            return redirect()->route('request');
        }
    }
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::middleware('role:admin')->group(function () {
        Route::view('clients', 'clients')->name('clients');
        Volt::route('clients/{client}/{clientRequest}', 'requests.view-request')->name('requests.view-request');
        Volt::route('clients/{client}', 'clients.view-client')->name('clients.view-client');
        Route::view('users', 'users')->name('users');
        Volt::route('add-users', 'users.add-users')->name('add-users');
        Volt::route('edit-users/{id}', 'users.edit-users')->name('edit-users');
    });

    Route::middleware('role:client')->group(function () {
        Route::view('request', 'request')->name('request');
        Volt::route('add-request', 'requests.add-request')->name('add-request');
        Volt::route('edit-request/{id}', 'requests.view-request')->name('edit-request');
    });
});


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
