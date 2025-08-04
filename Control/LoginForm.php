<?php

use ControlPage\Page;
use ControlPage\Action;
use Session\Session;
use Widgets\Form\Form;
use Widgets\Form\Entry;
use Widgets\Form\Password;
use Widgets\Wrapper\FormWrapper;
use Dialog\Message;
use Library\GoogleClient;
use Model\User;
use Utils\Validator;

class LoginForm extends Page
{
    private $form; // formulário

    public function __construct()
    {
        parent::__construct();

        // instancia o formulário
        $this->form = new FormWrapper(new Form('form_login'));
        $this->form->setTitle('Login');

        // Cria os campos com regras de validação
        $login = new Entry('email', ['required', 'email']);
        $password = new Password('password', 'required', 'min:6');
        $login->placeholder = 'exemplo@gmail.com';
        $password->placeholder = 'Senha';

        // Adiciona os campos ao formulário
        $this->form->addField('Seu e-mail', $login, 200);
        $this->form->addField('Sua senha', $password, 200);
        $this->form->addAction('Entrar', new Action([$this, 'onLogin']));
        $this->form->addAction('Entre com o Google', new Action([$this, 'onLoginGoogle']));

        parent::add($this->form);
    }

    public function onLogin($param)
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

            if (User::validateCredentials($data->email, $data->password)) {
                Session::setValue('enter', true);
                Session::setValue('email', $data->email);
                header('Location: index.php');
                exit;
            } else {
                new Message('error', 'E-mail ou senha inválidos');
            }
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function onLoginGoogle($param)
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

    public function onLogout($param)
    {
        Session::setValue('enter', false);
        header('Location: index-login.php');
    }
}
