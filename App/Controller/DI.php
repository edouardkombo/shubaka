<?php

namespace App\Controller;

class DI
{
    /**
     * @var array
     */
    public $instances;

    public function setInstance(string $className, $instance)
    {
        return $this->instances[$className] = $instance;
    }

    /**
     * Build an instance of the given class.
     *
     * @throws \Exception
     */
    public function resolve($class)
    {
        $reflector = new \ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("[$class] is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Build up a list of dependencies for a given methods parameters.
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getDependencies($parameters)
    {
        $dependencies = array();

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            var_dump($dependency, $parameter);
            if (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                if (in_array($dependency->name, ['string', 'array', 'bool'])) {
                    continue;
                }

                if (in_array($dependency->name, array_keys($this->instances))) {
                    $dependencies[] = $this->instances[$dependency->name];
                } else {
                    $dependencies[] = $this->resolve($dependency->name);
                }
            }
        }

        return $dependencies;
    }

    /**
     * Determine what to do with a non-class value.
     *
     * @param ReflectionParameter $parameter
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function resolveNonClass(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
var_dump($parameter);
        throw new \Exception('Cannot resolve the unkown!?');
    }
}
