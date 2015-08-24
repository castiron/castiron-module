<?php namespace Castiron\Config;

use Event;
use System\Traits\ConfigMaker;

class Importer
{
    use ConfigMaker;

    const IMPORT_KEY = '@import';


    public function startListening()
    {
        Event::listen('system.extendConfigFile', [$this, 'extendConfig']);
    }

    /**
     * Apply @import statements so that yaml files can pull in values from other files.
     *
     * @param $publicFile
     * @param $config
     * @return array
     */
    public function extendConfig($publicFile, $config)
    {
        return $this->parseImports($config);
    }

    /**
     * Recursively parses import statements and merges in the results
     *
     * @param $config
     * @return array
     */
    public function parseImports($config)
    {

        if (isset($config[static::IMPORT_KEY])) {
            $importedVal = $this->resolveImports($config['@import']);
            unset($config[static::IMPORT_KEY]);
            $newConfig = array_merge($config, $importedVal);
        } else {
            $newConfig = $config;
        }

        foreach ($config as $key =>  $val) {
            if (is_string($key) && is_array($val)) {
                $parsedVal = $this->parseImports($val);
                $newConfig[$key] = $parsedVal;
            }
        }
        return $newConfig;
    }

    /**
     * Loops through a list of file names and merges in the yaml config results
     *
     * @param $files
     * @return array
     * @throws \SystemException
     */
    public function resolveImports($files)
    {
        if (!is_array($files)) $files = [$files];
        $results = [];
        foreach ($files as $file) {
            $currentResults = $this->makeConfig($file);
            $results = array_merge($results, $currentResults);
        }
        return $results;
    }

    /**
     * Overriding this trait method so we only end up dealing with arrays
     *
     * @param array $config
     * @return array
     */
    public function makeConfigFromArray($config = [])
    {
        if (isset($config[static::IMPORT_KEY])) {
            unset($config[static::IMPORT_KEY]);
        }
        return $config;
    }
}
