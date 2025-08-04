<?php

namespace Widgets\Wrapper;

use Widgets\Form\Form;
use Widgets\Base\Element;
use Widgets\Form\Button;
use Widgets\Form\LinkGoogle;

/**
 * Decora formulários no formato Bootstrap
 */
class FormWrapper
{
    private $decorated;

    public function __construct(Form $form)
    {
        $this->decorated = $form;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->decorated, $method], $parameters);
    }

    public function show()
    {
        $wrapper = new Element('div');
        $wrapper->class = 'd-flex justify-content-center align-items-center  mt-5';

        // container do login
        $container = new Element('div');
        $container->class = 'bg-white border rounded shadow p-4';
        $container->style = 'max-width: 400px; width: 100%';

        // título
        $title = new Element('h2');
        $title->class = 'text-center text-teal mb-4';
        $title->add($this->decorated->getTitle());

        $container->add($title);

        // formulário
        $form = new Element('form');
        $form->class = 'w-100';
        $form->enctype = 'multipart/form-data';
        $form->method = 'post'; // método de transferência
        $form->name = $this->decorated->getName();

        foreach ($this->decorated->getFields() as $field) {
            $formGroup = new Element('div');
            $formGroup->class = 'mb-3';

            $label = new Element('label');
            $label->class = 'form-label';
            $label->add($field->getLabel());

            $field->class = 'form-control';
            $formGroup->add($label);
            $formGroup->add($field);

            $form->add($formGroup);
        }

        // botões
        $i = 0;

        foreach ($this->decorated->getActions() as $label => $action) {
            if ($label === 'Entre com o Google') {
                continue;
            } elseif ($label === 'Inscreva-se com o Google') {
                continue;
            }
            $name = strtolower(str_replace(',', '_', $label));
            $button = new Button($name);
            $button->setFormName($this->decorated->getName());
            $button->setAction($action, $label);

            $button->class = 'btn w-100 ' . (($i == 0) ? 'btn-primary' : 'btn-secondary mt-2');

            $form->add($button);
            $i++;
        }

        // Instância do LinkGoogle
        $linkGoogle = new LinkGoogle($name);
        $linkGoogle->setIcon('fab fa-google');
        $linkGoogle->setAction($action, $label);
        $linkGoogle->class = 'btn btn-danger w-100 mt-2 position-relative text-center';

        // separador
        $separator = new Element('div');
        $separator->class = 'd-flex align-items-center my-3';

        $lineLeft = new Element('hr');
        $lineLeft->class = 'flex-grow-1';

        $text = new Element('span');
        $text->class = 'mx-2 text-muted fw-semibold';
        $text->add('ou');

        $lineRight = new Element('hr');
        $lineRight->class = 'flex-grow-1';

        $separator->add($lineLeft);
        $separator->add($text);
        $separator->add($lineRight);

        // rodapé com link
        $footer = new Element('div');
        $footer->class = 'mt-4 pt-2 border-top text-center bg-body-tertiary width=100%';

        $link = new Element('a');
        $link->class = 'fw-bold text-decoration-none text-primary';


        if ($this->decorated->getName() === 'form_login') {
            $footer->add('Ainda não tem conta? ');
            $link->href = 'index-subscribe.php';
            $link->add('Cadastre-se');
        } else {
            $footer->add('Já tem uma conta? ');
            $link->href = 'index-login.php';
            $link->add('Faça login');
        }

        $footer->add($link);

        // monta a estrutura
        $container->add($form);
        $container->add($separator);
        $container->add($linkGoogle);
        $container->add($footer);

        $wrapper->add($container);

        //exibe
        $wrapper->show();
    }
}
