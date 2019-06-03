<?php

namespace pxgamer\DHT\Actions;

use pxgamer\DHT\DHT;
use pxgamer\DHT\Base;
use pxgamer\DHT\Node;
use pxgamer\DHT\Logger;

/**
 * Class Request.
 */
class Request
{
    /**
     * @param array $msg
     * @param string $address
     * @return bool
     */
    public static function action($msg, $address)
    {
        switch ($msg['q']) {
            case 'ping':
                self::ping($msg, $address);
                break;
            case 'find_node':
                self::find($msg, $address);
                break;
            case 'get_peers':
                self::peers($msg, $address);
                break;
            case 'announce_peer':
                self::announce($msg, $address);
                break;
            default:
                return false;
        }
    }

    /**
     * @param array $msg
     * @param string $address
     */
    public static function ping($msg, $address)
    {
        $id = $msg['a']['id'];

        $msg = [
            't' => $msg['t'],
            'y' => 'r',
            'r' => [
                'id' => $nid,
            ],
        ];

        DHT::append(new Node($id, $address[0], $address[1]));

        Response::send($msg, $address);
    }

    /**
     * @param array $msg
     * @param string $address
     */
    public static function find($msg, $address)
    {
        $nodes = DHT::get_nodes(16);

        $id = $msg['a']['id'];

        $msg = [
            't' => $msg['t'],
            'y' => 'r',
            'r' => [
                'id' => self::$nid,
                'nodes' => Base::encode_nodes($nodes),
            ],
        ];

        DHT::append(new Node($id, $address[0], $address[1]));

        Response::send($msg, $address);
    }

    /**
     * @param $msg
     * @param $address
     */
    public static function peers($msg, $address)
    {
        $infohash = $msg['a']['info_hash'];

        $id = $msg['a']['id'];

        $msg = [
            't' => $msg['t'],
            'y' => 'r',
            'r' => [
                'id' => $nid,
                'nodes' => Base::encode_nodes(DHT::get_nodes()),
                'token' => substr($infohash, 0, 2),
            ],
        ];

        DHT::append(new Node($id, $address[0], $address[1]));

        Response::send($msg, $address);
    }

    /**
     * @param array $msg
     * @param string $address
     */
    public static function announce($msg, $address)
    {
        $infohash = $msg['a']['info_hash'];

        $token = $msg['a']['token'];

        if (substr($infohash, 0, 2) == $token) {
            Logger::write(date('Y-m-d H:i:s', time()).' 获取到info_hash: '.strtoupper(bin2hex($infohash)));
        }

        $msg = [
            't' => $msg['t'],
            'y' => 'r',
            'r' => [
                'id' => DHT::$node_id,
            ],
        ];

        Response::send($msg, $address);
    }
}
