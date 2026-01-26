<?php

use App\Http\Middleware\RoleAuthorizeMiddleware;
use App\Http\Middleware\VerifiedMiddleware;
use Illuminate\Support\Facades\Route;

Route::
        namespace('App\Http\Controllers')->group(function () {

            Route::get('', 'Health\HealthController');

            Route::prefix('auth')->group(function () {
                Route::post('sign-up', 'Auth\AuthController@signUp');
                Route::post('sign-in', 'Auth\AuthController@signIn');
                Route::post('send-password-reset', 'Auth\AuthController@sendPasswordReset');
                Route::post('reset-password', 'Auth\AuthController@resetPassword');

                Route::middleware('auth:sanctum')->group(function () {
                    Route::get('user', 'Auth\AuthController@user');
                    Route::post('verify-email', 'Auth\AuthController@verifyEmail');
                    Route::post('resend-email-verification', 'Auth\AuthController@resendEmailVerification');
                });
            });

            Route::prefix('newsletter')->group(function () {
                Route::post('subscribe', 'Newsletter\SubscriptionsController@subscribe');
            });

            Route::prefix('programs')->group(function () {
                Route::middleware([
                    'auth:sanctum',
                    VerifiedMiddleware::class,
                    RoleAuthorizeMiddleware::class . ':admin'
                ])->group(function () {
                    Route::post('', 'Program\ProgramsController@store');
                    Route::post('/{program}', 'Program\ProgramsController@update');
                    Route::delete('/{program}', 'Program\ProgramsController@destroy');
                });

                Route::get('', 'Program\ProgramsController@viewAll');
                Route::get('{program}', 'Program\ProgramsController@view');
            });

        });
