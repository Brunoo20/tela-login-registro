<?php

namespace Utils;

use Dialog\Message;

class Validator
{
    public static function isEmpty($value, $fieldName)
    {
        if (empty(trim($value))) {
            new Message('error', "O campo {$fieldName} é obrigatório. ");

            return true;
        }

        return false;
    }

    public static function isEmail($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            new Message('error', 'Formato de e-mail inválido.');

            return false;
        }

        return true;
    }
}
