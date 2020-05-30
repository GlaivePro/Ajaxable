<?php
namespace GlaivePro\Ajaxable\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $model;

    /**
     * Set up the test environment.
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = '\GlaivePro\Ajaxable\Tests\Models\Model';
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Load package service provider
     * 
     * @param  \Illuminate\Foundation\Application $app
     * 
     * @return GlaivePro\Ajaxable\ServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [\GlaivePro\Ajaxable\ServiceProvider::class];
    }
}
