<?php namespace Castiron;

use App;
use October\Rain\Support\ModuleServiceProvider;

class ServiceProvider extends ModuleServiceProvider
{

    public function boot()
    {
        App::make('Castiron\Config\Importer')->startListening();
    }

    public function register()
    {
        parent::register('castiron');
        App::singleton('Castiron\Config\Importer');
    }
}
