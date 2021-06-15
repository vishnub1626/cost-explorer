<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CostExplorerController;

Route::get('/explorer', [CostExplorerController::class, 'index']);
