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
            Route::post('logout', 'Auth\AuthController@logout');
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

    Route::prefix('coupons')->group(function () {
        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
        ])->group(function () {
            Route::get('/user', 'Coupon\CouponController@userCoupon');

            Route::middleware(RoleAuthorizeMiddleware::class . ':admin')->group(function () {
                Route::get('/', 'Coupon\CouponController@viewAll');
                Route::post('/', 'Coupon\CouponController@store');
                Route::get('/{coupon}', 'Coupon\CouponController@view');
                Route::patch('/{coupon}', 'Coupon\CouponController@update');
                Route::delete('/{coupon}', 'Coupon\CouponController@destroy');
            });
        });
    });

    Route::prefix('products')->group(function () {
        Route::get('', 'Product\ProductsController@viewAll');
        Route::get('overview', 'Product\ProductsController@overview');
        Route::get('categories', 'Product\ProductsController@viewCategories');
        Route::get('categories/all', 'Product\ProductsController@viewAllCategories');
        Route::get('categories/{category}', 'Product\ProductsController@viewCategory');
        Route::get('{product}', 'Product\ProductsController@view');

        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::post('', 'Product\ProductsController@store');
            Route::post('categories', 'Product\ProductsController@storeCategory');
            Route::patch('categories/{category}', 'Product\ProductsController@updateCategory');
            Route::delete('categories/{category}', 'Product\ProductsController@destroyCategory');
            Route::post('{product}', 'Product\ProductsController@update');
            Route::delete('{product}', 'Product\ProductsController@destroy');
        });
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
        Route::get('overview', 'OpenRoles\OpenRolesController@overview');
        Route::get('{role}', 'OpenRoles\OpenRolesController@view');
    });

    Route::prefix('podcasts')->group(function () {
        Route::get('', 'Podcast\PodcastsController@viewAll');
        Route::get('categories', 'Podcast\PodcastsController@viewCategories');
        Route::get('{podcast}', 'Podcast\PodcastsController@view');
        Route::get('{podcast}/related', 'Podcast\PodcastsController@viewRelatedPodcasts');
        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::post('', 'Podcast\PodcastsController@store');
            Route::post('{podcast}', 'Podcast\PodcastsController@update');
            Route::delete('{podcast}', 'Podcast\PodcastsController@destroy');
        });
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

    Route::prefix('blogs')->group(function () {

        Route::middleware([
            'auth:sanctum',
            VerifiedMiddleware::class,
            RoleAuthorizeMiddleware::class . ':admin'
        ])->group(function () {
            Route::post('', 'Blog\BlogsController@store');
            Route::post('{blog}', 'Blog\BlogsController@update');
            Route::delete('{blog}', 'Blog\BlogsController@destroy');
            Route::post('assets', 'Blog\BlogsController@uploadAsset');
            Route::get('assets', 'Blog\BlogsController@listAssets');
            Route::delete('assets/{asset}', 'Blog\BlogsController@destroyAsset');
        });
        Route::get('', 'Blog\BlogsController@viewAll');
        Route::get('categories', 'Blog\BlogsController@viewAllCategories');
        Route::get('{slug}', 'Blog\BlogsController@view');
    });

    Route::prefix('checkout')->group(function () {
        Route::middleware(['auth:sanctum',
            VerifiedMiddleware::class,
        ])->group(function () {
            Route::post('shop', 'Invoices\CheckoutController@shopCheckout');
            Route::post('event', 'Invoices\CheckoutController@eventCheckout');
        });
    });

    Route::prefix('invoice')->group(function () {
        Route::middleware(['auth:sanctum',
            VerifiedMiddleware::class,
        ])->group(function () {
            Route::get('purchases', 'Invoices\InvoicesController@purchases');
            Route::get('purchases/{item}', 'Invoices\InvoicesController@purchase');
        });
    });

    Route::prefix('billing-info')->group(function () {
        Route::middleware(['auth:sanctum',
            VerifiedMiddleware::class,
        ])->group(function () {
            Route::put('', 'Invoices\BillingInformationController@save');
            Route::get('', 'Invoices\BillingInformationController@view');
            Route::delete('', 'Invoices\BillingInformationController@destroy');
        });
    });

    Route::prefix('settings')->group(function () {
        Route::post('site-login', 'Settings\SettingsController@siteLogin');
        Route::get('shipping-fees', 'Settings\SettingsController@shippingFee');
        Route::get('check-site-lock-status', 'Settings\SettingsController@checkSiteLockStatus');
    });

    Route::prefix('webhook')->group(function () {
        Route::post('stripe', 'Webhook\WebhookController@stripe');
        Route::post('paystack', 'Webhook\WebhookController@paystack');
    });

});
