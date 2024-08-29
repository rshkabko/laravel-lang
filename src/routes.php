<?php

use Flamix\Lang\Controllers\LangController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/change/{lang}', [LangController::class, 'change'])->name('lang.change');
