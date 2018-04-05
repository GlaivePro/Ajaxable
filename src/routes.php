<?php

Route::group(['prefix' => 'ajaxable'], function () {
	Route::get('list', 'GlaivePro\Ajaxable\Controller@respondList')->name('ajaxable.list');
	
	Route::post('create', 'GlaivePro\Ajaxable\Controller@create')->name('ajaxable.create');
	Route::post('update', 'GlaivePro\Ajaxable\Controller@update')->name('ajaxable.update');
	Route::post('delete', 'GlaivePro\Ajaxable\Controller@delete')->name('ajaxable.delete');
	Route::post('control', 'GlaivePro\Ajaxable\Controller@control')->name('ajaxable.control');

	Route::post('put-file', 'GlaivePro\Ajaxable\Controller@putFile')->name('ajaxable.putFile');
	Route::post('remove-file', 'GlaivePro\Ajaxable\Controller@removeFile')->name('ajaxable.removeFile');
});
