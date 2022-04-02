<?php

// declare(strict_types=1);
// use Exception;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
// use Throwable;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
require_once BASE_PATH.'/vendor/autoload.php';
$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Console' => APP_PATH.'/console',
    ]
);
$loader->register();

$container  = new CliDI();
$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('MyApp\Console');
$container->setShared('dispatcher', $dispatcher);

// $container->setShared('config', function () {
//     return include 'app/config/config.php';
// });


$console = new Console($container);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    $console->handle($arguments);
} catch (PhalconException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
