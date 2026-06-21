<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DhlLabelController;
use App\Http\Controllers\CoaController;

Route::get('/', function () {
    return redirect()->route('shipments.index');
});

// DHL Shipments
Route::get('/shipments',                [DhlLabelController::class, 'index'])->name('shipments.index');
Route::get('/shipments/create',         [DhlLabelController::class, 'create'])->name('shipments.create');
Route::post('/shipments',               [DhlLabelController::class, 'store'])->name('shipments.store');
Route::get('/shipments/{id}/edit',      [DhlLabelController::class, 'edit'])->name('shipments.edit');
Route::put('/shipments/{id}',           [DhlLabelController::class, 'update'])->name('shipments.update');
Route::delete('/shipments/{id}',        [DhlLabelController::class, 'destroy'])->name('shipments.destroy');
Route::get('/shipments/{id}/preview',   [DhlLabelController::class, 'preview'])->name('dhl.preview');
Route::get('/shipments/{id}/download',  [DhlLabelController::class, 'download'])->name('dhl.download');

// COA
Route::get('/coa',                      [CoaController::class, 'index'])->name('coa.index');
Route::get('/coa/create',               [CoaController::class, 'create'])->name('coa.create');
Route::post('/coa',                     [CoaController::class, 'store'])->name('coa.store');
Route::get('/coa/{id}/edit',            [CoaController::class, 'edit'])->name('coa.edit');
Route::put('/coa/{id}',                 [CoaController::class, 'update'])->name('coa.update');
Route::delete('/coa/{id}',             [CoaController::class, 'destroy'])->name('coa.destroy');
Route::get('/coa/{id}/preview',         [CoaController::class, 'preview'])->name('coa.preview');
Route::get('/coa/{id}/download',        [CoaController::class, 'download'])->name('coa.download');
