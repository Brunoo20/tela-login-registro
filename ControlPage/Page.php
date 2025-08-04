<?php

namespace ControlPage;

use Exception;
use Widgets\Base\Element;

/**
 * Page controller
 */
class Page extends Element
{
    public function __construct()
    {
        parent::__construct('div');
    }

    /**
    * Executa determinado método de acordo com os parâmetros recebidos
    */
    public function show()
    {
        if ($_GET) {
            $class = isset($_GET['class']) ? $_GET['class'] : null;
            $method = isset($_GET['method']) ? $_GET['method'] : null;

            if ($class) {
                try {
                    $object = $class == get_class($this) ? $this : new $class();
                    if (method_exists($object, $method)) {
                        call_user_func([$object, $method], $_GET);
                    }
                } catch(Exception $e) {
                    echo $e->getMessage();

                    return;
                }
            }
        }

        parent::show();
    }
}
