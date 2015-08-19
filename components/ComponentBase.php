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
            $this->addViewFunction($key);
        }
        foreach ($this->funcsNotCached as $key) {
            $this->addViewFunction($key, false);
        }
    }

    /**
     * Adds a function to the twig context.
     *
     * @param string $funcName
     * @param bool|true $once If true, wraps the function in a closure so the callable is only invoked once.
     */
    protected function addViewFunction($funcName, $once = true)
    {

        if ($once) {
            $realCallable = function($context) use ($funcName) {
                static $results = [];

                // use the __SELF__ object from the context so that
                // our callable only applies to the relevant component
                $obj = $context['__SELF__'];
                if (!$obj) return;

                // return early if we have a cached result
                foreach ($results as $result) {
                    if ($result['obj'] === $obj) {
                        return $result['return'];
                    }
                }

                // call the function with the given args
                $args = func_get_args();
                array_shift($args);
                $result = call_user_func_array([$obj, $funcName], $args);

                // save the result for later
                $results[] = [
                    'obj' => $obj,
                    'return' => $result,
                ];


                return $result;
            };
        } else {
            $realCallable = function($context) use ($funcName) {
                // use the __SELF__ object from the context so that
                // our callable only applies to the relevant component
                $obj = $context['__SELF__'];
                if (!$obj) return;

                // call the function with the given args
                $args = func_get_args();
                array_shift($args);
                return call_user_func_array([$obj, $funcName], $args);
            };
        }



        $funcObj = new \Twig_SimpleFunction($funcName, $realCallable, [
            'needs_context' => true,
        ]);
        $this->twig->addFunction($funcName, $funcObj);


    }



}
