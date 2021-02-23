<?php

use App\Http\Controllers\Newsletter\NewsletterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/all_emails', [NewsletterController::class, 'all_emails']);
Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
Route::patch('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
