<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware('redirect_admin_customer')->group(function () {
    Route::inertia('/', 'Menu')->name('home');
});

Route::redirect('/menu', '/');

Route::inertia('/welcome', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('welcome');

Route::middleware(['auth', 'verified', 'redirect_admin_customer'])->group(function () {
    Route::inertia('my-orders', 'Orders/Mine')->name('orders.mine');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::inertia('admin', 'Admin/Dashboard')->name('admin');
    Route::inertia('admin/ingredients', 'Admin/Ingredients')->name('admin.ingredients');
    Route::inertia('admin/pizzas', 'Admin/Pizzas')->name('admin.pizzas');
    Route::inertia('admin/orders', 'Admin/Orders')->name('admin.orders');
});

require __DIR__.'/settings.php';
