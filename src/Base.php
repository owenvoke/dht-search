<?php

namespace pxgamer\DHT;

use pxgamer\DHT\Bencode\Bencode;

class Base
{
    public static function hash2int($str)
    {
        return hexdec(bin2hex($str));
    }

    public static function entropy($length = 20)
    {
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= chr(mt_rand(0, 255));
        }

        return $str;
    }

    public static function get_node_id()
    {
        return sha1(self::entropy(), true);
    }

    public static function get_neighbor($target, $nid)
    {
        return substr($target, 0, 10) . substr($nid, 10, 10);
    }

    public static function encode($msg)
    {
        return Bencode::encode($msg);
    }

    public static function decode($msg)
    {
        return Bencode::decode($msg);
    }

    public static function encode_nodes($nodes)
    {

        if (count($nodes) == 0) {
            return $nodes;
        }

        $n = '';


        foreach ($nodes as $node) {
            $n .= pack('a20Nn', $node->nid, ip2long($node->ip), $node->port);
        }

        return $n;
    }

    public static function decode_nodes($msg)
    {

        if ((strlen($msg) % 26) != 0) {
            return array();
        }

        $n = array();


        foreach (str_split($msg, 26) as $s) {
            $r = unpack('a20nid/Nip/np', $s);
            $n[] = new Node($r['nid'], long2ip($r['ip']), $r['p']);
        }

        return $n;
    }
}
