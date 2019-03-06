<?php


Route::prefix('file')->group(function () {

    Route::namespace('Betalectic\FileManger\Http\Controllers')->group(function () {
        Route::get('/', 'FileController@index');

    });
});


