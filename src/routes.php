<?php

// TODO: Research the cost of the number of routes. If it turns out to
// be a problem we can cut it down to 4 or 7. Or make some optional.

Route::group(['prefix' => 'ajaxable', 'middleware' => 'web', 'namespace' => 'GlaivePro\Ajaxable'], function () {

	// Media routes
	Route::group(['prefix' => 'media'], function () {
		// TODO: make it moar similar to the other routes. Put in a separate con troller.
		Route::post('add', 'Controller@addMedia')->name('ajaxable.media.add');
		Route::get('retrieve', 'Controller@getMedia')->name('ajaxable.media.retrieve');
		Route::match(['post', 'delete'], 'delete', 'Controller@deleteMedia')->name('ajaxable.media.delete');
	});

	// List -- must come before GET /{optional?}
	Route::get('list', 'Controller@list')->name('ajaxable.list');

	// Retrieve
	Route::post('retrieve', 'Controller@retrieve')->name('ajaxable.retrieve');  // everything supports POST
	Route::get('read', 'Controller@retrieve')->name('ajaxable.read');  // route name alias
	Route::get('/{optional?}', 'Controller@retrieve')->name('ajaxable.get');  // catch-all for GET verb, includes GET retrieve

	// Update
	Route::post('update', 'Controller@update')->name('ajaxable.update');
	Route::patch('/{optional?}', 'Controller@update')->name('ajaxable.patch');  // catch-all for PATCH verb

	// Delete
	Route::post('delete', 'Controller@delete')->name('ajaxable.remove');
	Route::delete('/{optional?}', 'Controller@delete')->name('ajaxable.delete');

	// Update or create
	Route::post('update-or-create', 'Controller@updateOrCreate')->name('ajaxable.updateOrCreate');
	Route::put('/{optional?}', 'Controller@updateOrCreate')->name('ajaxable.put');

	// Control
	Route::post('control', 'Controller@control')->name('ajaxable.control');

	// Create -- comes last because it catches all POST routes not matched yet
	Route::post('create', 'Controller@create')->name('ajaxable.create');
	Route::post('/{optional?}', 'Controller@create')->name('ajaxable.post');  // catch-all for POST verb
});
