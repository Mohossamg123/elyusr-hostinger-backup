<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\EditorApprovalMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocalizationMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

          $middleware->api(prepend: [
             LocalizationMiddleware::class,
           ]);



    $middleware->alias([
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'editor.approval' => \App\Http\Middleware\EditorApprovalMiddleware::class,
]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
