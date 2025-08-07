<?php

namespace Model;

use League\OAuth2\Client\Provider\GoogleUser;
use Database\Connection;
use PDO;
use Exception;

class Authenticate
{
    /**
     * Método para autenticação com Google.
     * Recebe um objeto GoogleUser e realiza a autenticação ou cadastro do usuário no sistema.
     * @param GoogleUser $googleUser
     * @throws Exception
     */
    public function authGoogle(GoogleUser $googleUser): void
    {
        try {
            $user = new User();

            // define os dados do GoogleUser na instância de User
            $user->setEmail($googleUser->getEmail());
            $user->setNome($googleUser->getName());
            $user->setGoogleId($googleUser->getId());
            $user->setAvatar($googleUser->getAvatar());

            // salva ou atualiza o usuário no banco (lógica de mesclagem)
            $user->saveOrUpdateGoogleUser();

            // Busca o usuário para armazenar na sessão
            $conn = Connection::open('database');
            $stmt = $conn->prepare('SELECT id, nome, email, avatar FROM usuarios WHERE email = :email');
            $stmt->execute(['email' => $googleUser->getEmail()]);
            $userFound = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userFound) {
                throw new Exception('Erro: Usuário não encontrado após salvar.');
            }

            // Inicia a sessão e armazena dados mínimos
            session_start();
            $_SESSION['user'] = [
                'id' => $userFound['id'],
                'nome' => $userFound['nome'],
                'email' => $userFound['email'],
                'avatar' => $userFound['avatar'],
            ];
            $_SESSION['enter'] = true;

            header('Location: /Tela_de_Login/index.php');
            exit;
        } catch(Exception $e) {
            error_log('Erro na autenticação Google: ' . $e->getMessage());

            throw new Exception('Falha na autenticação Google.', 0, $e);
        }
    }
}
