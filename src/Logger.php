<?php

namespace pxgamer\DHT;

/**
 * Class Logger
 */
class Logger
{
    /**
     * Log file name
     */
    const LOG_FILE = 'info_hash.log';

    /**
     * @param string $message
     */
    public static function write($message)
    {
        file_put_contents(self::LOG_FILE, $message . "\n", FILE_APPEND);
    }
}
