<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::inertia('menu', 'Menu')->name('menu');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('admin/ingredients', 'Admin/Ingredients')->name('admin.ingredients');
    Route::inertia('admin/pizzas', 'Admin/Pizzas')->name('admin.pizzas');
    Route::inertia('admin/orders', 'Admin/Orders')->name('admin.orders');
});

require __DIR__.'/settings.php';
