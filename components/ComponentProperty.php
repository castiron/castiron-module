<?php namespace Castiron\Components;

/**
 * From a component controller, you can add variables to the
 * view via the controller:
 *
 * $this->controller->vars['myProp'] = $this->myProp();
 *
 * Then you don't need to use __SELF__ anymore. Woot! The only problem
 * is that you've just invoked a function that may or may not be used in
 * your template. It'd be better if you could lazily load that value:
 *
 * $this->controller->vars['myProp'] = new ComponentProperty($this, 'myProp');
 *
 * Boom. Now your function `myProp` will only be called if it is used in the template.
 *
 * Class ComponentProperty
 * @package Castiron\Lib\Components
 */
class ComponentProperty implements \Countable, \IteratorAggregate
{
    /** @var ComponentBase The component we're accessing from the view. */
    protected $component;

    /** @var string The property or method on the component */
    protected $name;

    /** @var bool True if we accessed the object already */
    protected $initialized = false;

    /** @var array Components can be configured. */
    protected $conf = [];
    protected $defaultConf = [
        'once' => true,
    ];

    /** @var mixed This is the real value from the component */
    protected $obj;

    /**
     * @param ComponentBase $component
     * @param string $name
     * @param array $conf
     */
    public function __construct(ComponentBase $component, $name, $conf = [])
    {
        $this->component = $component;
        $this->name = $name;
        if ($conf == null) $conf = [];
        $this->conf = array_merge($this->defaultConf, $conf);
    }

    /**
     * This always returns the result of accessing the
     * property/method from the component.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function obj()
    {
        if ($this->initialized && $this->conf['once']) return $this->obj;
        $this->initialized = true;
        $this->obj = $this->access($this->component, $this->name);
        return $this->obj;
    }

    /**
     * The template is requesting a property/method
     * on the $obj produced by our component.
     *
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        return $this->access($this->obj(), $name);
    }

    /**
     * Always return true. Eloquent/October models
     * are tricky to check if a method/property exists or not
     * because of the dynamic attributes.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return true;
    }

    /**
     * if invoked directly, the template is using $obj as string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->obj();
    }

    /**
     * Our best guess at accessing object values.
     *
     * @param mixed $obj
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    protected function access($obj, $name)
    {
        if (method_exists($obj, $name)) {
            return $obj->$name();
        }
        if (property_exists($obj, $name)) {
            return $obj->$name;
        }
        try {
            $out = $obj->$name;
        } catch (\Exception $e) {
            return null;
        }
        return $out;
    }

    public function getIterator()
    {
        $res = $this->access($this->obj(), 'getIterator');
        if (!$res) {
            $res = new \ArrayIterator($res);
        }
        return $res;
    }

    public function count()
    {
        return $this->access($this->obj(), 'count');
    }


}
