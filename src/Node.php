<?php

namespace pxgamer\DHT;

/**
 * Class Node
 */
class Node
{
    /**
     * @var string
     */
    private $nid;
    /**
     * @var string
     */
    private $ip;
    /**
     * @var string
     */
    private $port;

    /**
     * Node constructor.
     * @param string $nid
     * @param string $ip
     * @param string $port
     */
    public function __construct($nid, $ip, $port)
    {
        $this->nid = $nid;
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return array('nid' => $this->nid, 'ip' => $this->ip, 'port' => $this->port);
    }
}
