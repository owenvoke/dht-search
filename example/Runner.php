<?php

use pxgamer\DHT\Actions\Request;
use pxgamer\DHT\Actions\Response;
use pxgamer\DHT\Base;
use pxgamer\DHT\Logger;

require '../vendor/autoload.php';


$serv = null;

$this->node_id = Base::get_node_id();

$table = array();

$last_find = time();

$threads = [];



Logger::write(date('Y-m-d H:i:s', time()) . " - Starting service...\n");

$serv = new swoole_server('0.0.0.0', 6882, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
$serv->set(array(
    'worker_num' => WORKER_NUM,
    'daemonize' => false,
    'max_request' => MAX_REQUEST,
    'dispatch_mode' => 2,
    'log_file' => ABSPATH . '/error.log'
));
$serv->on('WorkerStart', function ($serv, $worker_id) {
    swoole_timer_tick(AUTO_FIND_TIME, 'timer');
    auto_find_node();
});
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {

    if (strlen($data) == 0) {
        return false;
    }


    $msg = Base::decode($data);
    if (empty($msg['y'])) {
        return false;
    }


    $fdinfo = $serv->connection_info($fd, $from_id);
    if (empty($fdinfo['remote_ip'])) {
        return false;
    }


    if ($msg['y'] == 'r') {

        if (array_key_exists('nodes', $msg['r'])) {
            Response::action($msg, array($fdinfo['remote_ip'], $fdinfo['remote_port']));
        }
    } elseif ($msg['y'] == 'q') {

        Request::action($msg, array($fdinfo['remote_ip'], $fdinfo['remote_port']));
    } else {
        return false;
    }
});


$serv->start();