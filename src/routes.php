<?php

Route::group(['prefix' => 'ajaxable'], function () {
	Route::post('create', 'GlaivePro\Ajaxable\Controller@create')->name('ajaxable.create');
	Route::post('update', 'GlaivePro\Ajaxable\Controller@update')->name('ajaxable.update');
	Route::post('update-or-create', 'GlaivePro\Ajaxable\Controller@updateOrCreate')->name('ajaxable.updateOrCreate');
	Route::post('delete', 'GlaivePro\Ajaxable\Controller@delete')->name('ajaxable.delete');
	Route::post('control', 'GlaivePro\Ajaxable\Controller@control')->name('ajaxable.control');

	Route::post('put-file', 'GlaivePro\Ajaxable\Controller@putFile')->name('ajaxable.putFile');
	Route::post('remove-file', 'GlaivePro\Ajaxable\Controller@removeFile')->name('ajaxable.removeFile');
});
