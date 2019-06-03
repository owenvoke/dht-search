<?php

namespace pxgamer\DHT;

use pxgamer\DHT\Actions\Response;

/**
 * Class DHT.
 */
class DHT
{
    public const TABLE_MAX_LENGTH = 200;

    /**
     * @var string
     */
    public static $node_id;
    /**
     * @var
     */
    public static $bootstrap_nodes;
    /**
     * @var
     */
    public static $server;
    /**
     * @var
     */
    public static $last_find;
    /**
     * @var
     */
    public static $threads;
    /**
     * @var
     */
    public static $table;
    /**
     * @var
     */
    public static $data;

    /**
     * @param array $node_endpoints
     */
    public static function start($node_endpoints = [])
    {
        self::$node_id = Base::get_node_id();
        self::$bootstrap_nodes = array_merge(
            [
                ['router.bittorrent.com', 6881],
                ['dht.transmissionbt.com', 6881],
                ['router.utorrent.com', 6881],
            ],
            $node_endpoints
        );

        Logger::write(date('Y-m-d H:i:s', time())." - Starting service...\n");
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public static function auto()
    {
        if (count(self::$table) == 0) {
            return self::join();
        }

        while (count(self::$table)) {
            $node = array_shift(self::$table);

            self::find([$node->ip, $node->port], $node->nid);
            sleep(0.005);
        }
    }

    /**
     * @return bool
     */
    public static function join()
    {
        foreach (self::$bootstrap_nodes as $node) {
            self::find([gethostbyname($node[0]), $node[1]]);
        }

        return true;
    }

    /**
     * @param      $address
     * @param null $id
     */
    public static function find($address, $id = null)
    {
        if (is_null($id)) {
            $mid = self::$node_id;
        } else {
            $mid = Base::get_neighbor($id, self::$node_id);
        }

        $msg = [
            't' => Base::entropy(2),
            'y' => 'q',
            'q' => 'find_node',
            'a' => [
                'id' => self::$node_id,
                'target' => $mid,
            ],
        ];

        Response::send($msg, $address);
    }

    /**
     * @param int $len
     * @return array
     */
    public static function get_nodes($len = 8)
    {
        global $table;

        if (count($table) <= $len) {
            return $table;
        }

        $nodes = [];

        for ($i = 0; $i < $len; $i++) {
            $nodes[] = $table[mt_rand(0, count($table) - 1)];
        }

        return $nodes;
    }

    /**
     * @param $node
     * @return bool|int
     */
    public static function append($node)
    {
        if (! isset($node->nid[19])) {
            return false;
        }

        if ($node->nid == self::$node_id) {
            return false;
        }

        if (in_array($node, self::$table)) {
            return false;
        }

        if (count(self::$table) >= self::TABLE_MAX_LENGTH) {
            array_shift(self::$table);
        }

        return array_push(self::$table, $node);
    }
}
