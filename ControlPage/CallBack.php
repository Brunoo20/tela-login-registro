<?php

require_once '../Source/Config.php';
ob_start();
use Library\GoogleClient;
use Model\Authenticate;

require_once '../vendor/autoload.php';


try {
    $googleClient = new GoogleClient();
    if ($googleClient->authorized()) {
        $googleUser = $googleClient->getData();
        $auth = new Authenticate();
        $auth->authGoogle($googleUser);
    } else {
        error_log('Falha na autenticação Google: usuário não autorizado.');
        header('Location: /Tela_de_Login/index-login.php?error=auth_failed');
        exit;
    }
} catch(Exception $e) {
    error_log('Erro no login Google: ' . $e->getMessage());
    header('Location: /Tela_de_Login/index-login.php?error=auth_failed');
    exit;
}

ob_end_flush();
