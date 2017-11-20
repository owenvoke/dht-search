<?php

namespace pxgamer\DHT;

use pxgamer\DHT\Actions\Response;

class DHT
{
    public static $node_id;
    public static $bootstrap_nodes;
    public static $server;
    public static $last_find;
    public static $threads;
    public static $table;
    public static $data;

    public static function start($node_endpoints = [])
    {
        self::$node_id = Base::get_node_id();
        self::$bootstrap_nodes = array_merge(
            [
                ['router.bittorrent.com', 6881],
                ['dht.transmissionbt.com', 6881],
                ['router.utorrent.com', 6881]
            ],
            $node_endpoints
        );

        Logger::write(date('Y-m-d H:i:s', time()) . " - Starting service...\n");
    }

    public static function timer()
    {
        if (strlen(self::$data) == 0) {
            return false;
        }

        $msg = Base::decode(self::$data);
        if (empty($msg['y'])) {
            return false;
        } else {
            return true;
        }
    }

    public static function auto()
    {
        if (count(self::$table) == 0) {
            return self::join();
        }

        while (count(self::$table)) {
            $node = array_shift(self::$table);

            self::find(array($node->ip, $node->port), $node->nid);
            sleep(0.005);
        }
    }

    public static function join()
    {
        foreach (self::$bootstrap_nodes as $node) {
            self::find(array(gethostbyname($node[0]), $node[1]));
        }
        return true;
    }

    public static function find($address, $id = null)
    {
        if (is_null($id)) {
            $mid = self::$node_id;
        } else {
            $mid = Base::get_neighbor($id, self::$node_id);
        }

        $msg = array(
            't' => Base::entropy(2),
            'y' => 'q',
            'q' => 'find_node',
            'a' => array(
                'id' => self::$node_id,
                'target' => $mid
            )
        );

        Response::send($msg, $address);
    }

    public static function get_nodes($len = 8)
    {
        global $table;

        if (count($table) <= $len) {
            return $table;
        }

        $nodes = array();

        for ($i = 0; $i < $len; $i++) {
            $nodes[] = $table[mt_rand(0, count($table) - 1)];
        }

        return $nodes;
    }

    public static function append($node)
    {
        if (!isset($node->nid[19])) {
            return false;
        }


        if ($node->nid == self::$node_id) {
            return false;
        }


        if (in_array($node, self::$table)) {
            return false;
        }


        if (count(self::$table) >= 200) {
            array_shift(self::$table);
        }

        return array_push(self::$table, $node);
    }
}
