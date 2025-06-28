<?php

namespace App;

use Exception;

class App
{

    private static $instance = null;
    public static $container = [];


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function run()
    {
        if (empty(self::$container)) {
            self::$container['db'] = self::connect();
            self::$container['db']
                ->createTableTransactions()
                ->createTableAccounts();
        }

        $routes = require_once __DIR__ . '/routes.php';

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($routes[$requestUri][$method])) {
            $action = $routes[$requestUri][$method];
            list($controllerName, $actionName) = explode('@', $action, 2);
            $controllerClass = 'App\\Controllers\\' . $controllerName;


            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $actionName)) {
                    echo $controller->$actionName();
                    exit;
                }
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }


    /**
     * @throws Exception
     */
    public static function getContainer($key)
    {
        if (isset(self::$container[$key])) {
            return self::$container[$key];
        }

        throw new Exception("Container key '{$key}' not found.");
    }


    private function __construct()
    {
    }




    /**
     * @throws Exception
     */
    public function __clone()
    {
        throw new Exception('Clone is not allowed');
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Unserializing is not allowed');
    }


    private static function connect()
    {
        $host = getenv('MYSQL_HOST');
        $user = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');
        $dbName = getenv('MYSQL_DATABASE');

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

        return new DB(
            $dsn,
            $user,
            $password
        );
    }
}