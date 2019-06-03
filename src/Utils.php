<?php

namespace pxgamer\DHT;

/**
 * Class Utils.
 */
class Utils
{
    public function timer()
    {
        for ($i = 0; $i < MAX_PROCESS; $i++) {
            $process = new swoole_process(function () {
                DHT::auto();
            });
            $pid = $process->start();
            $threads[$pid] = $process;
            swoole_process::wait();
        }
    }
}
