<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isAdmin' => AdminMiddleware::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);
        
        // Replace ValidatePostSize middleware với custom version để cho phép upload lớn hơn
        // Lưu ý: Vẫn cần sửa php.ini để PHP thực sự chấp nhận upload lớn
        $middleware->replace(
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\CustomValidatePostSize::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
