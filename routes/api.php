<?php

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

Route::resource('files', 'Api\FilesController', [
    'only' => [
        'store',
        'show',
    ],
]);

Route::resource('files.aliases', 'Api\FilesAliasController', [
    'only' => [
        'store',
    ],
]);

Route::resource('aliases', 'Api\AliasController', [
    'only' => [
        'show',
    ],
]);

Route::resource('statistics', 'Api\StatisticsController', [
    'only' => [
        'index',
    ],
]);
