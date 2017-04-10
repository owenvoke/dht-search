<?php

namespace pxgamer\DHT\Bencode;

class Bencode
{
    static public function decode($str)
    {
        return Decode::decode($str);
    }

    static public function encode($value)
    {
        return Encode::encode($value);
    }
}