<?php

// Library loader
require_once 'Core/ClassLoader.php';


$al = new Core\ClassLoader();
$al->addNamespace('Tela_de_login', '');
$al->register();

// Application loader
require_once 'Core/AppLoader.php';

$al = new Core\AppLoader();
$al->addDirectory('Control');
$al->addDirectory('Model');
$al->register();

// Vendor
$loader = require 'vendor/autoload.php';
$loader->register();

use Session\Session;

new Session();
if (!Session::getValue('enter')) {
    header('Location: index-login.php');
}

// lê o conteúdo do template
$template = file_get_contents('Templates/template.html');
$content = '';
$class = 'Home';

if ($_GET) {
    $class = $_GET['class'];

    if (class_exists($class)) {
        try {
            $pagina = new $class(); // instancia a classe
            ob_start(); // inicia controle de output
            $pagina->show(); // exibe página
            $content = ob_get_contents(); // lê conteúdo gerado
            ob_end_clean(); // finaliza controle de output
        } catch (Exception $e) {
            $content = $e->getMessage() . '<br>' . $e->getTraceAsString();
        }
    } else {
        $content = "Class <br>{$class}</b> not found";
    }
}

// injeta conteúdo gerado dentro do template
$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}', $class, $output); // exibe saída gerada
echo $output;
