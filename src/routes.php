<?php

Route::group(['prefix' => 'ajaxable', 'middleware' => 'web'], function () {
	// CRUD
	Route::post('create', 'GlaivePro\Ajaxable\Controller@create')->name('ajaxable.create');
	Route::get('retrieve', 'GlaivePro\Ajaxable\Controller@retrieve')->name('ajaxable.retrieve');
	Route::match(['post', 'patch'], 'update', 'GlaivePro\Ajaxable\Controller@update')->name('ajaxable.update');
	Route::match(['post', 'delete'], 'delete', 'GlaivePro\Ajaxable\Controller@delete')->name('ajaxable.remove');
	
	// Extras
	Route::match(['post', 'put'], 'update-or-create', 'GlaivePro\Ajaxable\Controller@updateOrCreate')->name('ajaxable.updateOrCreate');
	Route::get('list', 'GlaivePro\Ajaxable\Controller@list')->name('ajaxable.list');
	Route::post('control', 'GlaivePro\Ajaxable\Controller@control')->name('ajaxable.control');

	// Media
	Route::post('add-media', 'GlaivePro\Ajaxable\Controller@addMedia')->name('ajaxable.addMedia');
	Route::get('get-media', 'GlaivePro\Ajaxable\Controller@getMedia')->name('ajaxable.getMedia');
	Route::match(['post', 'delete'], 'delete-media', 'GlaivePro\Ajaxable\Controller@deleteMedia')->name('ajaxable.deleteMedia');

	// Verbs
	Route::post('/', 'GlaivePro\Ajaxable\Controller@create')->name('ajaxable.post');
	Route::get('/', 'GlaivePro\Ajaxable\Controller@retrieve')->name('ajaxable.get');
	Route::patch('/', 'GlaivePro\Ajaxable\Controller@update')->name('ajaxable.patch');
	Route::delete('/', 'GlaivePro\Ajaxable\Controller@delete')->name('ajaxable.delete');
	Route::put('/', 'GlaivePro\Ajaxable\Controller@updateOrCreate')->name('ajaxable.put');
});
