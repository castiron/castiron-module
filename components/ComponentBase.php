<?php namespace Castiron\Components;

use Cms\Classes\ComponentBase as Base;

abstract class ComponentBase extends Base
{
    /** @var array */
    protected $funcs = [];
    /** @var array */
    protected $funcsNotCached = [];

    /** @var \Twig_Environment */
    protected $twig;


    public function init()
    {
        $this->twig = $this->controller->getTwig();
        $this->loadContext();
    }

    /**
     * Add functions to the twig context.
     */
    protected function loadContext()
    {
        foreach ($this->funcs as $key) {
            $this->addViewFunction($key, [$this, $key]);
        }
        foreach ($this->funcsNotCached as $key) {
            $this->addViewFunction($key, [$this, $key], false);
        }
    }

    /**
     * Adds a function to the twig context.
     *
     * @param string $name
     * @param callable $callable
     * @param bool|true $once If true, wraps the function in a closure so the callable is only invoked once.
     */
    protected function addViewFunction($name, $callable, $once = true)
    {
        if ($once) {
            $callable = function() use ($callable) {
                static $result = null;
                if ($result) return $result;
                $result = call_user_func($callable);
                return $result;
            };
        }
        $this->twig->addFunction($name, new \Twig_SimpleFunction($name, $callable));
    }
}
