<?php

namespace Dialog;

use Widgets\Base\Element;

class Message extends Element
{
    public function __construct($type, $message)
    {
        $div = new Element('div');

        if ($type == 'info') {
            $div->class = 'alert alert-info mt-2 ms-2';
        } elseif ($type == 'error') {
            $div->class = 'alert alert-danger ms-2';
        }

        $div->add($message);
        $div->show();
    }
}
