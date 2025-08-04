<?php

use ControlPage\Page;
use ControlPage\Action;
use Widgets\Form\Form;
use Widgets\Form\Entry;
use Widgets\Form\Password;
use Widgets\Wrapper\FormWrapper;
use Dialog\Message;
use Model\User;
use Utils\Validator;
use Library\GoogleClient;

class SubscribeForm extends Page
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        // instancia o formulário
        $this->form = new FormWrapper(new Form('form_subscribe'));
        $this->form->setTitle('Inscreva-se');

        // cria os campos com regras de validação
        $subscribe = new Entry('email', ['required', 'email']);
        $password = new Password('password', 'required', 'min:6');

        $subscribe->placeholder = 'exemplo@gmail.com';
        $password->placeholder = 'Senha';

        // Adiciona os campos ao formulário
        $this->form->addField('Seu e-mail', $subscribe, 200);
        $this->form->addField('Sua senha', $password, 200);
        $this->form->addAction('Inscreva-se', new Action([$this, 'onSubscribe']));
        $this->form->addAction('Inscreva-se com o Google', new Action([$this, 'onSubscribeGoogle']));

        parent::add($this->form);
    }

    public function onSubscribe($param)
    {
        try {
            $data = $this->form->getData();

            // Valida se o e-mail está vazio
            if (Validator::isEmpty($data->email, 'e-mail')) {
                return;
            }

            // Valida se o e-mail tem formato válido
            if (!Validator::isEmail($data->email)) {
                return;
            }

            if (Validator::isEmpty($data->password, 'senha')) {
                return;
            }

            // Verifica se o e-mail já está registrado
            if (User::emailExists($data->email)) {
                new Message('error', 'E-mail já está registrado.');

                return;
            }

            // cria um novo usuário
            $user = new User();
            $user->setEmail($data->email);
            $user->setPassword(password_hash($data->password, PASSWORD_DEFAULT)); // criptografa a senha
            $user->save(); // salva o usuário no banco de dados
            new Message('success', 'Inscrição realizada com sucesso!');
            exit;
        } catch(Exception $e) {
            new Message('error', 'Erro ao inscrever: ' . $e->getMessage());
        }
    }

    public function onSubscribeGoogle($param)
    {
        try {
            $googleClient = new GoogleClient();
            $authUrl = $googleClient->generateAuthlink();

            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit;
        } catch (Exception $e) {
            error_log('Erro ao gerar URL de autenticação Google: ' . $e->getMessage());
            new Message('error', 'Falha ao iniciar autenticação com Google');
        }
    }
}
