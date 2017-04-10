<?php

namespace pxgamer\DHT;

class Utils
{
    public function timer()
    {
        for ($i = 0; $i < MAX_PROCESS; $i++) {
            $process = new swoole_process(function () {
                auto_find_node();
            });
            $pid = $process->start();
            $threads[$pid] = $process;
            swoole_process::wait();
        }
    }
}