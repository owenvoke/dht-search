<?php

namespace pxgamer\DHT;

use pxgamer\DHT\Bencode\Bencode;

class Base
{
    static public function hash2int($str)
    {
        return hexdec(bin2hex($str));
    }

    static public function entropy($length = 20)
    {
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= chr(mt_rand(0, 255));
        }

        return $str;
    }

    static public function get_node_id()
    {
        return sha1(self::entropy(), true);
    }

    static public function get_neighbor($target, $nid)
    {
        return substr($target, 0, 10) . substr($nid, 10, 10);
    }

    static public function encode($msg)
    {
        return Bencode::encode($msg);
    }

    static public function decode($msg)
    {
        return Bencode::decode($msg);
    }

    static public function encode_nodes($nodes)
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

    static public function decode_nodes($msg)
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