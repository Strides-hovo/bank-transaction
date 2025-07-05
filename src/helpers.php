<?php



/**
 * @throws Exception
 */
function render( $template, $data)
{
    ob_start();
    extract($data);
    if (file_exists(__DIR__ . "/views/{$template}.php")) {
        include __DIR__ . "/views/{$template}.php";
    } else {
        throw new Exception("Template {$template} not found.");
    }
    return ob_get_clean();
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}



/**
 * @throws ReflectionException
 * @throws Exception
 */
function app($abstract = null, $params = []){

    $app = new \App\Application();
    if (!$abstract) return $app;

    if (!class_exists($abstract)){
        throw new ReflectionException('Class not found');
    }

    if (!isset($app->getContainer()[$abstract])){
        $app->setContainer($abstract, $params);
    }
    return $app;
}