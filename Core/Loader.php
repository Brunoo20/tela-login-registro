<?php

namespace Core;

use Exception;

class Loader
{
    /**
     * Carrega uma classe do namespace TelaDeLogin\Control apenas com o nome simples
     * Exemplo: carregarClasseControlador('Home') => TelaDeLogin\Control\Home
     */

    public static function loadControllerClass(string $simpleClass)
    {
        $classComplete = "TelaDeLogin\\Control\\{$simpleClass}";

        if (class_exists(($classComplete))) {
            return new $classComplete();
        }

        throw new Exception("Classe {$classComplete} não encontrada");
    }
}
