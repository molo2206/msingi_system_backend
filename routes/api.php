<?php

use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\Google2FAController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\RessourceHasPermissionController;
use App\Http\Controllers\RessourcesController;
use App\Http\Controllers\SecteurActivityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHasCompanyController;
use App\Models\SecteurActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('forget-password', 'forgetPasswordRequest');
        Route::post('verify-and-change-password', 'verifyAndChangePassword');
        Route::post('verify', 'verifyValidateAccount');
    });
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(Google2FAController::class)->group(function () {
            Route::post('enable2FA', 'enableTwoFactorAuthentication');
            Route::post('generate_qr', 'getQRCode');
            Route::post('verify2FA', 'verifyTwoFactorCode');
        });
    });
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('logout', 'logout');
            Route::get('get-profile', 'getProfile');
            Route::post('change-password', 'changePassword');
            Route::put('update-profile', 'updateProfile');
            Route::post('update-profile-picture', 'editProfilePicture');

        });
    });
});

Route::group(['prefix' => 'departement'], function () {
    Route::group(['middleware' => 'auth:sanctum', 'is_admin'], function () {
        Route::controller(DepartementController::class)->group(function () {
            Route::get('list_departement', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
        });
    });
});

Route::group(['prefix' => 'fonction'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(FonctionController::class)->group(function () {
            Route::get('list_fonction/{id}', 'show');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
        });
    });
});

Route::group(['prefix' => 'user_has_company'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(UserHasCompanyController::class)->group(function () {
            Route::post('store', 'store');
        });
    });
});

Route::group(['prefix' => 'secteur'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(SecteurActivityController::class)->group(function () {
            Route::get('list_secteur', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
        });
    });
});

Route::group(['prefix' => 'module'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(ModulesController::class)->group(function () {
            Route::get('list_module', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('status/{id}', 'status');
        });
    });
});

Route::group(['prefix' => 'ressource'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(RessourcesController::class)->group(function () {
            Route::get('list_ressource', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('status/{id}', 'status');
        });
    });
});

Route::group(['prefix' => 'plan'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(PlansController::class)->group(function () {
            Route::get('getplan', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('status/{id}', 'status');
        });
    });
});

Route::group(['prefix' => 'abonnement'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['middleware' => 'is_admin'], function () {
            Route::controller(AbonnementController::class)->group(function () {
                Route::post('store', 'store');
            });
        });
    });
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'company'], function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::post('store', 'store');
            Route::get('login/{id}', 'loginCompany');
            Route::get('byuser', 'getUserCompanies');
            Route::group(['middleware' => 'is_company'], function () {
                Route::put('update', 'update');
            });
        });
    });
});


Route::group(['prefix' => 'permission'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(RessourceHasPermissionController::class)->group(function () {
            Route::post('affectation', 'Affectation');
        });
    });
});
