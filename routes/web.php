<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/docs', function () {
    return view('docs');
});
Route::get('/stats', 'StatisticsController@index');

Route::get('file/{file}', 'FileController@show')
    ->name('file.show')
    ->where('file', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::match(['PUT', 'PATCH'], 'file/{file}', 'FileController@update')
    ->name('file.update')
    ->where('file', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::group(['middleware' => 'feature:web.accept_file_upload'], function () {
    Route::resource('file', 'FileController', [
        'only' => [
            'create',
            'store',
        ],
    ]);
});
Route::group(['middleware' => 'feature:web.accept_remote_creation'], function () {
    Route::resource('url', 'UrlController', [
        'only' => [
            'create',
            'store',
            'show',
        ],
    ]);
});

Route::resource('file.aliases', 'AliasController', [
    'only' => [
        'store',
        'destroy',
    ],
]);

//  catch all
Route::get('{alias}', 'ServeFileController@serve')
    ->name('serve')
    ->where('alias', '(.*)');
