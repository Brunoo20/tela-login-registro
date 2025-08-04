<?php

date_default_timezone_set('America/Sao_Paulo');

require_once 'Source/Config.php';
// Lib loader
require_once 'Core/ClassLoader.php';

$al = new \Core\ClassLoader();
$al->addNamespace('Tela_de_Login', '');
$al->register();

// App loader
require_once 'Core/AppLoader.php';
$al = new \Core\AppLoader();
$al->addDirectory('Control');
$al->addDirectory('Model');
$al->register();

// Vendor
$loader = require 'vendor/autoload.php';
$loader->register();

use Session\Session;

$content = '';

new Session();

if (Session::getValue('enter')) {
    $template = file_get_contents('Templates/template.html');
    $class = '';
} else {
    $template = file_get_contents('Templates/login.html');
    $class = 'LoginForm';
}

if (isset($_GET['class']) and Session::getValue('enter')) {
    $class = $_GET['class'];
}

if (class_exists($class)) {
    try {
        $pagina = new $class();
        ob_start();
        $pagina->show();
        $content = ob_get_clean();
    } catch(Exception $e) {
        $content = $e->getMessage() . '<br>' . $e->getTraceAsString();
    }
} else {
    $content = "Class <br>{$class}</b> not found";
}
$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}', $class, $output);
echo $output;
