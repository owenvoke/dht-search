<?php

namespace pxgamer\DHT;

class Node
{
    private $nid;
    private $ip;
    private $port;

    public function __construct($nid, $ip, $port)
    {
        $this->nid = $nid;
        $this->ip = $ip;
        $this->port = $port;
    }

    public function __get($name)
    {

        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function to_array()
    {
        return array('nid' => $this->nid, 'ip' => $this->ip, 'port' => $this->port);
    }
}
