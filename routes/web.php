<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DhlLabelController;
use App\Http\Controllers\CoaController;

Route::get('/', function () {
    return redirect()->route('shipments.index');
});

// DHL Label
Route::get('/shipments', [DhlLabelController::class, 'index'])->name('shipments.index');
Route::get('/shipments/{id}/preview', [DhlLabelController::class, 'preview'])->name('dhl.preview');
Route::get('/shipments/{id}/download', [DhlLabelController::class, 'download'])->name('dhl.download');

// COA
Route::get('/coa', [CoaController::class, 'index'])->name('coa.index');
Route::get('/coa/{id}/preview', [CoaController::class, 'preview'])->name('coa.preview');
Route::get('/coa/{id}/download', [CoaController::class, 'download'])->name('coa.download');
