 <?php

    use Phalcon\Di\FactoryDefault;
    use Phalcon\Loader;
    use Phalcon\Config;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\Application;

    use Phalcon\Db\Adapter\Pdo\Mysql;
    use Phalcon\Events\Manager;
    use Phalcon\Logger;
    use Phalcon\Logger\Adapter\Stream;

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
    $loader->registerNamespaces(
        [
            'MyApp\Listeners' => APP_PATH . '/listeners/'
        ]
    );

    $loader->register();

    $container = new FactoryDefault(); //creating the instance of factory default

    //set() method ->Registers a service in the services container
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
            $url->setBaseUri('/'); //allow you to set a prefix for all of your URLs
            return $url;
        }
    );



    $container->set(
        'db',
        function () {
            $eventsManager = new Manager();
            $adapter = new Stream('../app/logs/db.log');
            $logger  = new Logger(
                'messages',
                [
                    'main' => $adapter,
                ]
            );

            $eventsManager->attach(
                'db:beforeQuery',
                function ($event, $connection) use ($logger) {
                    $logger->info(
                        $connection->getSQLStatement()
                    );
                }
            );

            $connection = new Mysql(
                [
                    'host'     => 'mysql-server',
                    'username' => 'root',
                    'password' => 'secret',
                    'dbname'   => 'demo_db',
                ]

            );

            $connection->setEventsManager($eventsManager);
            return $connection;
        }
    );


    $application = new Application($container);

    try {
        // Handle the request
        $response = $application->handle(
            $_SERVER["REQUEST_URI"]
        );
        $response->send();
    } catch (\Exception $e) {
        echo 'Exception: ', $e->getMessage();
    }
