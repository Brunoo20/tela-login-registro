<?php

namespace Widgets\Form;

use Widgets\Base\Element;

class Entry extends Field implements FormElementInterface
{
    protected $properties;

    public function show()
    {
        // atribui as propriedades da TAG
        $tag = new Element('input');
        $tag->class = 'field'; // classe CSS
        $tag->name = $this->name;  // nome da TAG
        $tag->value = $this->value; // valor da TAG
        $tag->type = 'text'; // tipo de input
        $tag->style = "width:{$this->size}"; // tamanho em pixels

        // se o campo não é editável
        if (!parent::getEditable()) {
            $tag->readonly = '1'; // desabilita a TAG input
        }

        if ($this->properties) {
            foreach ($this->properties as $property => $value) {
                $tag->$property = $value;
            }
        }

        $tag->show();
    }
}
