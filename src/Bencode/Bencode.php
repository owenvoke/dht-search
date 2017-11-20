<?php

namespace pxgamer\DHT\Bencode;

class Bencode
{
    public static function decode($str)
    {
        return Decode::decode($str);
    }

    public static function encode($value)
    {
        return Encode::encode($value);
    }
}
