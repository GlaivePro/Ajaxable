<?php

namespace GlaivePro\Ajaxable;

use Illuminate\Support\ServiceProvider;

class AjaxableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
		$this->loadRoutesFrom(__DIR__.'/routes.php');
		
		$this->loadViewsFrom(__DIR__.'/views', 'ajaxable');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}