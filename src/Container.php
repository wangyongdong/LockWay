<?php
namespace src\Lock;

class Container
{
    /**
     * closure function
     * @var
     */
    protected $binds;

    /**
     * Instance object
     * @var
     */
    protected $instances;

    /**
     * bind Bind a service object to a container
     * @param string $abstract
     * @param closure|Instance $anonymous
     */
    public function bind($abstract, $anonymous)
    {
        // is callback and Closure
        if($anonymous instanceof Closure) {
            $this->binds[$abstract] = $anonymous;
        } else {
            // is a Instantiation of a class
            $this->instances[$abstract] = $anonymous;
        }
    }

    /**
     * Take the object out of the container
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = array()) {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        //Handling closure anonymous classes
        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }
}