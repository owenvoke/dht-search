<?php

namespace pxgamer\DHT;

class Logger
{
    const LOG_FILE = 'info_hash.log';

    public static function write($message)
    {
        file_put_contents(self::LOG_FILE, $message . "\n", FILE_APPEND);
    }
}