<?php namespace Castiron\Components;

use Cms\Classes\ComponentBase as Base;

abstract class ComponentBase extends Base
{
    protected $vars = [];
    protected $varsConf = [];

    public function onRun()
    {
        $this->loadVariables();
    }

    /**
     * This function adds variables to the twig template context. Since
     * it uses the ComponentProperty, those functions won't be invoked
     * until the variable is used in the template.
     */
    protected function loadVariables()
    {
        foreach ($this->vars as $key) {
            $conf = isset($this->varsConf[$key]) ? $this->varsConf[$key] : null;
            $this->controller->vars[$key] = new ComponentProperty($this, $key, $conf);
        }
    }
}
