<?php
namespace GlaivePro\Ajaxable\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    private $model;

    /**
     * Set up the test environment.
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = 'GlaivePro\Ajaxable\Tests\Model';
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
