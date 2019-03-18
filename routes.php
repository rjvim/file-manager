<?php

Route::namespace('Betalectic\FileManager\Http\Controllers')->group(function () {

    Route::get('file-manager/tags', 'FileController@tags');
    Route::get('file-manager', 'FileController@index');
    Route::delete('file-manager/{id}', 'FileController@destroy');
    Route::put('file-manager/{id}', 'FileController@update');
    Route::post('file-manager/upload-save', 'UploadSaveController@store');
	
});


