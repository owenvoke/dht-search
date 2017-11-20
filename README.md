# dht

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Style CI][ico-styleci]][link-styleci]
[![Code Coverage][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A collection of tools for searching DHT via PHP.

## Structure

```
src/
tests/
vendor/
```

## Install

Via Composer

``` bash
$ composer require pxgamer/dht
```

## Usage

``` php
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
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email owzie123@gmail.com instead of using the issue tracker.

## Credits

- [pxgamer][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/pxgamer/dht.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/pxgamer/dht/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/87832777/shield
[ico-code-quality]: https://img.shields.io/codecov/c/github/pxgamer/dht.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/pxgamer/dht.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/pxgamer/dht
[link-travis]: https://travis-ci.org/pxgamer/dht
[link-styleci]: https://styleci.io/repos/87832777
[link-code-quality]: https://codecov.io/gh/pxgamer/dht
[link-downloads]: https://packagist.org/packages/pxgamer/dht
[link-author]: https://github.com/pxgamer
[link-contributors]: ../../contributors
