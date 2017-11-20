<?php

namespace pxgamer\DHT\Bencode;

/**
 * Class Bencode
 */
class Bencode
{
    /**
     * @param string $str
     * @return array|bool|string
     */
    public static function decode($str)
    {
        return Decode::decode($str);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function encode($value)
    {
        return Encode::encode($value);
    }
}
