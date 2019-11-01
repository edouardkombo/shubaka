<?php

namespace App\Controller;

use App\Architecture\Interfaces\ContainerInterface;

class ServiceContainer implements ContainerInterface
{
    private $services = [];

    public function load(string $configFilePath): self
    {
        //Load all the config params
        $configs = json_decode(file_get_contents($configFilePath), true);
        foreach ($configs as $key => $config) {
            $this->set($config['alias'], $config['class']);
        }

        return $this;
    }

    /**
     * Get class.
     *
     * @param mixed $id ID of class
     *
     * @return mixed
     *
     * @throws \Exception class not found
     */
    public function get(string $id)
    {
        $item = $this->_resolve($id);
        if (!($item instanceof \ReflectionClass)) {
            return $item;
        }

        return $this->_getInstance($item);
    }

    /**
     * Set class.
     *
     * @param string $key   key to register
     * @param mixed  $value value to register
     *
     * @return Container
     */
    public function set(string $key, string $value)
    {
        $this->services[$key] = $value;

        return $this;
    }

    /**
     * Does container have class?
     *
     * @param mixed $id ID of class
     *
     * @return bool
     */
    public function has(string $id)
    {
        try {
            $item = $this->_resolve($id);
        } catch (\Exception $e) {
            return false;
        }
        if ($item instanceof \ReflectionClass) {
            return $item->isInstantiable();
        }

        return isset($item);
    }

    /**
     * resolve service from ID.
     *
     * @param mixed $id ID of class
     *
     * @return mixed
     *
     * @throws \Exception ID not found
     */
    private function _resolve(string $id)
    {
        try {
            $name = $id;
            if (isset($this->services[$id])) {
                $name = $this->services[$id];
                if (is_callable($name)) {
                    return $name();
                }
            }

            return new \ReflectionClass($name);
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get instance of item.
     *
     * @param \ReflectionClass $item reflected class
     *
     * @return mixed
     */
    private function _getInstance(\ReflectionClass $item)
    {
        $constructor = $item->getConstructor();
        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $item->newInstance();
        }
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            if ($type = $param->getType()) {
                $params[] = $this->get($type->getName());
            }
        }

        return $item->newInstanceArgs($params);
    }
}
