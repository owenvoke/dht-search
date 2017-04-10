<?php

namespace pxgamer\DHT\Actions;

use pxgamer\DHT\Base;

class Response
{
    public static function action($msg, $address)
    {

        if (!isset($msg['r']['nodes']) || !isset($msg['r']['nodes'][1])) {
            return false;
        }

        $nodes = Base::decode_nodes($msg['r']['nodes']);

        foreach ($nodes as $node) {

            append($node);
        }
    }

    public static function send($msg, $address)
    {
        $serv->sendto($address[0], $address[1], Base::encode($msg));
    }
}