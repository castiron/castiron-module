<?php namespace Castiron\Tools;

class VersionManager extends \System\Classes\VersionManager
{
    /**
     * When we "refresh" a plugin twice in a row, it only gets
     * refreshed the first time. So you need to reset it between refreshes.
     */
    public function reset()
    {
        // these are kinda just used as caches and need to be reset
        $this->databaseVersions = [];
        $this->databaseHistory = [];
        $this->fileVersions = [];
    }
}
