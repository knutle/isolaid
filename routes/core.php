<?php

use Illuminate\Support\Facades\Route;
use Knutle\Isolaid\Http\Controllers\RouteListController;

Route::get('/routes.json', [RouteListController::class, 'routes']);

Route::get('/', [RouteListController::class, 'index']);
Route::get('/default', [RouteListController::class, 'default']);

if (file_exists($userRoutesFile = base_path('routes/isolaid.php'))) {
    include $userRoutesFile;
}
