<?php

namespace Widgets\Form;

use ControlPage\ActionInterface;
use Widgets\Base\Element;

class LinkGoogle extends Field implements FormElementInterface
{
    private $action;
    private $label;
    private $icon;


    /**
     * Define a ação e o rótulo do link.
     * @param ActionInterface $action Ação do link.
     * @param string $label Rótulo do link.
     * @throws \InvalidArgumentException Se o rótulo estiver vazio.
     */
    public function setAction(ActionInterface $action, string $label): void
    {
        if (empty(trim($label))) {
            throw new \InvalidArgumentException('O rótulo não pode estar vazio.');
        }
        $this->action = $action;
        $this->label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Define o ícone do link (ex.: 'fab fa-google').
     * @param string $icon Classe do ícone.
     * @throws \InvalidArgumentException Se o ícone estiver vazio.
     */
    public function setIcon(string $icon): void
    {
        if (empty(trim($icon))) {
            throw new \InvalidArgumentException('O ícone não pode estar vazio.');
        }
        $this->icon = htmlspecialchars($icon, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Exibe o link estilizado.
     * @throws \RuntimeException Se ação ou ícone não estiverem definidos.
     */
    public function show(): void
    {
        if (!isset($this->action) || !isset($this->icon)) {
            throw new \RuntimeException('Ação e ícone devem ser definidos antes de exibir o link.');
        }

        $href = $this->action->serialize();

        $tag = new Element('a');
        $tag->href = $href;
        $tag->add("<i class='{$this->icon} position-absolute start-0 top-50 translate-middle-y ps-2'></i>{$this->label}");


        // Aplica propriedades adicionais com validação
        if (!empty($this->properties)) {
            foreach ($this->properties as $property => $value) {
                if (is_string($property) && is_scalar($value)) {
                    $tag->$property = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        $tag->show();
    }
}
