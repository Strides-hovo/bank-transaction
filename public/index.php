<?php


use App\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';

$env = new Dotenv(dirname(__DIR__) . '/src');
$env->load();


$app = App::getInstance();
$app->run();

