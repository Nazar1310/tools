<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/about', [IndexController::class, 'about'])->name('about');
Route::get('/terms', [IndexController::class, 'terms'])->name('terms');
Route::get('/policy', [IndexController::class, 'policy'])->name('policy');
Route::get('/search', [IndexController::class, 'search'])->name('search');
Route::get('/categories', [IndexController::class, 'categories'])->name('categories');
Route::get('/tools', [IndexController::class, 'tools'])->name('tools');
Route::get('/{category_slug}', [IndexController::class, 'category'])->name('category');
Route::get('/{category_slug}/{tool_slug}', [IndexController::class, 'tool'])->name('tool');
