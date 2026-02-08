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
        Route::get('categories', 'Program\ProgramsController@viewCategories');
        Route::get('{program}', 'Program\ProgramsController@view');
        Route::get('{program}/related', 'Program\ProgramsController@viewRelated');
        Route::get('{program}/reviews', 'Program\ProgramsController@reviews');
    });

    Route::prefix('events')->group(function () {
        Route::get('', 'Event\EventsController@viewAll');
        Route::get('categories', 'Event\EventsController@viewCategories');
        Route::get('{event}', 'Event\EventsController@view');

        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::post('', 'Event\EventsController@store');
            Route::delete('/{event}/images', 'Event\EventsController@deleteImage');
            Route::post('/{event}', 'Event\EventsController@update');
            Route::delete('/{event}', 'Event\EventsController@destroy');
            Route::delete('/tickets/{ticket}', 'Event\EventsController@destroyTicket');
        });
    });

    Route::prefix('roles')->group(function () {
        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::get('/all', 'OpenRoles\OpenRolesController@viewAllWithoutPagination');
            Route::post('', 'OpenRoles\OpenRolesController@store');
            Route::patch('{role}', 'OpenRoles\OpenRolesController@update');
            Route::delete('{role}', 'OpenRoles\OpenRolesController@destroy');
        });

        Route::get('', 'OpenRoles\OpenRolesController@viewAll');
        Route::get('{role}', 'OpenRoles\OpenRolesController@view');
    });

    Route::prefix('job-applications')->group(function () {
        Route::post('', 'OpenRoles\ApplicationController@store');
        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::get('', 'OpenRoles\ApplicationController@viewAll');
            Route::get('{application}', 'OpenRoles\ApplicationController@view');
            Route::delete('{application}', 'OpenRoles\ApplicationController@destroy');
        });
    });

    Route::prefix('settings')->group(function () {
        Route::post('site-login', 'Settings\SettingsController@siteLogin');
        Route::get('check-site-lock-status', 'Settings\SettingsController@checkSiteLockStatus');
    });

});
