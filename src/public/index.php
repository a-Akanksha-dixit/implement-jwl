<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Escaper;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Router;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$container = new FactoryDefault();



$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);


/**
 * register config
 */
$container->set(
    'config',
    function () {
        $file_name = '../app/components/config.php';
        $factory  = new ConfigFactory();
        return $factory->newInstance('php', $file_name);
    }
);
/**
 * register db service using config file
 */
$container->set(
    'db',
    function () {
        $db = $this->get('config')->db;
        return new Mysql(
            [
                'host'     => $db->host,
                'username' => $db->username,
                'password' => $db->password,
                'dbname'   => $db->dbname,
            ]
        );
    }
);
/**
 * register escaper class
 */
$container->set(
    'escaper',
    function () {
        return new Escaper();
    }
);
/**
 * register namespace service
 */
$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/components',
        'App\Listeners' => APP_PATH . '/listener',
    ]
);
// Register the flash service with custom CSS classes
$container->set(
    'flash',
    function () {
        return new FlashDirect();
    }
);
// Register Event manager
$eventsManager = new EventsManager();
$eventsManager->attach(
    'notifications',
    new App\Listeners\NotificationsListener()
);
$application->setEventsManager($eventsManager);
$eventsManager->attach(
    'application:beforeHandleRequest',
    new App\Listeners\NotificationsListener()
);
$container->set(
    'EventsManager',
    $eventsManager
);


// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
