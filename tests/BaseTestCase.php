<?php

namespace Castiron\Tests;

use DB;
use App;
use Event;
use Model;
use System\Classes\PluginManager;
use Castiron\Tools\VersionManager;

// Not using October's TestCase
use Illuminate\Foundation\Testing\TestCase;


abstract class BaseTestCase extends TestCase
{

    protected $resetPlugins = [];

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        if (!empty($this->resetPlugins)) {

            // The version manager caches some stuff and can't easily be reset, so let's use our
            // own version manager that has a reset() function.
            $app->bind('System\Classes\VersionManager', 'Castiron\Tools\VersionManager');

            // Hack attack to put our plugin on the radar now so that it
            // can be found later when we reset/refresh the plugin.

            $pm = PluginManager::instance();
            foreach ($this->resetPlugins as $pluginName) {
                if (!$pm->hasPlugin($pluginName)) {

                    $app['path.plugins'] = base_path('plugins');
                    $pm->loadPlugins();
                }
            }
        }

        $app['cache']->setDefaultDriver('array');
        $app->setLocale('en');

        return $app;
    }


    /**
     * Start database transactions.
     * If overriding, call this first.
     */
    public function setUp()
    {
        parent::setUp();

        /**
         * OMG! Do not forget this mofo!! Since october saves some info about your
         * models' event callbacks in a static variable, that info could still linger
         * between tests causing you one heck of a situation.
         */
        Model::flushEventListeners();


        if (!empty($this->resetPlugins)) {
            VersionManager::instance()->reset();
            foreach ($this->resetPlugins as $pluginName) {
                PluginManager::instance()->refreshPlugin($pluginName);
            }
        }
    }

    /**
     * @param string $contains Only show statements that have this value
     */
    protected function showSQL($contains = 'castiron')
    {
        $pdo = DB::connection()->getPdo();

        Event::listen('illuminate.query', function($query, $bindings, $time) use ($pdo, $contains)
        {
            $bindings = array_map([$pdo, 'quote'], $bindings);
            $sql = str_replace_array('\?', $bindings, $query);

            if (!$contains || strpos($sql, $contains) !== false) {
                echo "\n$sql\n";
            }
        });
    }

    /**
     * End database transactions.
     * If overriding, call this last.
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}
