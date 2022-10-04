<?php

namespace Knutle\Isolaid\Http\Controllers;

use function collect;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use function ltrim;
use function view;

class RouteListController
{
    public function index(): View
    {
        return view('isolaid::index', [
            'routes' => static::getRoutes(),
        ]);
    }

    public function routes(): JsonResponse
    {
        return response()->json(static::getRoutes());
    }

    public function default(): View
    {
        return view('isolaid::default', [
            'title' => 'Default Preview',
        ]);
    }

    public static function getRoutes(): array
    {
        return collect(
            Route::getRoutes()->getRoutesByMethod()['GET']
        )->map(
            fn (\Illuminate\Routing\Route $route) => [
                'uri' => '/'.ltrim($route->uri(), '/'),
                'name' => $route->getName() ?? (string) Str::of($route->uri())->ltrim('/')->slug('.')->whenEmpty(fn () => 'index'),
                'description' => $route->getActionName(),
            ]
        )->reject(
            fn (array $route) => $route['uri'] == '/routes.json'
        )->toArray();
    }
}
