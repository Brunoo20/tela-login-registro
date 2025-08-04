<?php

namespace Session;

class Session
{
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public static function setValue($var, $value)
    {
        $_SESSION[$var] = $value;
    }

    public static function getValue($var)
    {
        return $_SESSION[$var];
    }

    public static function freeSession()
    {
        $_SESSION = [];
        session_destroy();
    }
}
