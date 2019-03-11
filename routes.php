<?php


Route::prefix('file-manager')->group(function () {

	Route::namespace('Betalectic\FileManger\Http\Controllers')->group(function () {

        Route::post('/images', 'FilesController@store');
        Route::get('/images', 'FileController@index');
		Route::post('/upload', 'FilesController@uploadFiles');


    });
});


