<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::prefix('v1')->group(function () {
    Route::group(['middleware' => ['cors', 'json.response', 'throttle:60,1']], function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('password/reset/request', [AuthController::class, 'requestReset']);
        Route::post('password/reset', [AuthController::class, 'reset']);
        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'getAuthenticatedUser']);
            Route::post('me', [AuthController::class, 'updateProfile']);
            Route::post('permissions', [PermissionsController::class, 'store']);
            Route::patch('permissions/{uuid}', [PermissionsController::class, 'store']);
            Route::patch('permissions/admin/assign', [PermissionsController::class, 'assignPermissionsToAdmin']);
            Route::delete('permissions/{uuid}', [PermissionsController::class, 'destroy']);
            Route::get('permissions', [PermissionsController::class, 'index'])->middleware('permission:view_permissions');
            Route::post('roles', [RoleController::class, 'store'])->middleware('permission:create_role');
            Route::get('roles/{uuid}', [RoleController::class, 'show'])->middleware('permission:view_role');
            Route::patch('roles/{uuid}', [RoleController::class, 'store'])->middleware('permission:create_role');
            Route::delete('roles/{uuid}', [RoleController::class, 'destroy'])->middleware('permission:create_role');
            Route::get('roles', [RoleController::class, 'index'])->middleware('permission:view_role');
            Route::patch('roles/permissions/update', [RoleController::class, 'addOrRemovePermissions'])->middleware('permission:create_role');
            Route::post('posts', [PostController::class, 'store']);
            Route::patch('posts/{uuid}', [PostController::class, 'store']);
            Route::delete('posts/{uuid}', [PostController::class, 'destroy']);
            Route::get('posts/{uuid}', [PostController::class, 'view']);
            Route::get('posts', [PostController::class, 'index']);
            Route::post('posts/{uuid}/comments', [CommentsController::class, 'addComment']);
            Route::delete('posts/{uuid}/comments/{commentUuid}', [CommentsController::class, 'deleteComment']);
            Route::get('posts/{uuid}/comments', [CommentsController::class, 'fetchComments']);
        });
    });
});
