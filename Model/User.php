<?php

namespace Model;

use Database\Connection;

use Exception;
use PDO;

class User
{
    private $email;
    private $password;
    private $nome;
    private $googleId;
    private $avatar;

    public function setEmail($mail)
    {
        $this->email = $mail;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }


    /**
     * verifica se o e-mail já está registrado
     * @param string $email
     * @return bool Retorna true se as credenciais forem válidas, false caso contrário
     * @throws Exception Em caso de erro na conexão ou consulta
     */
    public static function emailExists($email)
    {
        try {
            $conn = Connection::open('database');
            $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch(Exception $e) {
            throw new Exception('Erro ao verificar e-mail: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Salva um novo usuário no banco de dados
     * @return void
     * @throws Exception Em caso de erro na conexão ou inserção
     */
    public function save()
    {
        try {
            $conn = Connection::open('database');

            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha, criado_em) VALUES (:nome, :email, :senha, NOW()');
            $stmt->execute([
                'nome' => $this->nome,
                'email' => $this->email,
                'senha' => $passwordHash,
            ]);
        } catch(Exception $e) {
            throw new Exception('Erro ao salvar usuário: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Salva ou atualiza um usuário do Google no banco de dados
     * @return void
     * @throws Exception Em caso de erro na conexão ou inserção
     */
    public function saveOrUpdateGoogleUser()
    {
        try {
            $conn = Connection::open('database');

            // Verifica se o e-mail já existe
            $stmt = $conn->prepare('SELECT id, google_id FROM usuarios WHERE email = :email');
            $stmt->execute(['email' => $this->email]);
            $googleUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($googleUser) {
                // se o e-mail existe, atualiza o google_id (se não estiver preenchido)
                if (!$googleUser['google_id']) {
                    $stmt = $conn->prepare(
                        'UPDATE usuarios SET google_id = :google_id , nome = :nome, avatar = :avatar
                        WHERE id = :id'
                    );
                    $stmt->execute([
                        'google_id' => $this->googleId,
                        'nome' => $this->nome,
                        'avatar' => $this->avatar,
                        'id' => $googleUser['id'],
                    ]);
                    error_log('Conta local vinculada ao Google ID: ' . $this->googleId);
                }
            } else {
                // Se o e-mail não existe, cria novo usuário
                $stmt = $conn->prepare(
                    'INSERT INTO usuarios (nome, email, google_id, avatar, criado_em)
                    VALUES (:nome, :email, :google_id, :avatar, NOW())'
                );
                $stmt->execute([
                    'nome' => $this->nome,
                    'email' => $this->email,
                    'google_id' => $this->googleId,
                    'avatar' => $this->avatar,
                ]);
                error_log('Novo usuário Google criado: ' . $this->email);
            }
        } catch(Exception $e) {
            throw new Exception('Erro ao salvar/atualizar usuário do Google: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Valida as credenciais do usuário local
     * @param string $email
     * @param string $password
     * @return bool Retorna true se as credenciais forem válidas, false caso contrário
     * @throws Exception Em caso de erro na conexão ou consulta
     */
    public static function validateCredentials($email, $password)
    {
        try {
            // Conectar ao banco de dados
            $conn = Connection::open('database');

            // Consultar o usuário pelo email
            $stmt = $conn->prepare('SELECT senha FROM usuarios WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar se o usuário existe e se a senha está correta
            if ($user && password_verify($password, $user['senha'])) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception('Erro ao validar credenciais: ' . $e->getMessage(), 0, $e);
        }
    }
}
