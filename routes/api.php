<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SectionController; 
use App\Http\Controllers\Api\ContentItemController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SiteProfileController;
use Illuminate\Support\Facades\Route;

// Public, read-only content endpoints. Content is authored via the
// Filament admin panel at /admin (see app/Filament), not through this API.

Route::get('/content-items', [ContentItemController::class, 'index'])->name('content-items.index');
Route::get('/content-items/{slug}', [ContentItemController::class, 'show'])->name('content-items.show');
Route::get('/content-items/{slug}/related', [ContentItemController::class, 'related'])->name('content-items.related');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::get('/profile', [SiteProfileController::class, 'show'])->name('profile.show');

Route::get('/search', SearchController::class)->name('search');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:10,1');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1');

Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
