<?php

use App\Http\Controllers\AwbController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/shipments/{shipment}/awb', [AwbController::class, 'show'])
    ->middleware('auth')
    ->name('shipments.awb');
