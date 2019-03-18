<?php

Route::prefix('file-manager')->group(function () {

	Route::namespace('Betalectic\FileManager\Http\Controllers')->group(function () {

        Route::get('tags', 'FileController@tags');
        Route::get('/', 'FileController@index');
        Route::delete('/{id}', 'FileController@destroy');
        Route::put('/{id}', 'FileController@update');
        Route::post('/upload-save', 'UploadSaveController@store');
		
    });
});


