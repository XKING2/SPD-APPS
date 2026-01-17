<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware ->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'check_status' => app\Http\Middleware\CheckStatus::class,
            'biodata.valid' => \App\Http\Middleware\checkbiodata::class,
            'check.login' => \App\Http\Middleware\CheckLogin::class,
            'otp.not.pending' => \App\Http\Middleware\EnsureOtpNotPending::class,
            'otpsessions' => \App\Http\Middleware\otpsessions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
