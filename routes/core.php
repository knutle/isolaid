<?php

use Illuminate\Support\Facades\Route;
use Knutle\IsoView\Http\Controllers\RouteListController;
use Knutle\IsoView\IsoView;

Route::get('/routes.json', [RouteListController::class, 'routes']);

Route::get('/', [RouteListController::class, 'index']);
Route::get('/default', [RouteListController::class, 'default']);

if (file_exists($userRoutesFile = IsoView::getRootPackagePath('routes/isoview.php'))) {
    include $userRoutesFile;
}
