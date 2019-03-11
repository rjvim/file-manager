<?php

Route::prefix('file-manager')->group(function () {

	Route::namespace('Betalectic\FileManager\Http\Controllers')->group(function () {

        Route::get('/images', 'FileController@index');
		Route::post('/upload', 'FileController@uploadFiles');
		
    });
});


