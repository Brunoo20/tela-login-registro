<?php

namespace Library;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Exception;

class GoogleClient
{
    // declara o provedor do Google como uma propriedade somente leitura
    public readonly Google $provider;

    // Propriedade privada para armazenar os dados do usuário autenticado
    private ?GoogleUser $user = null;

    public function __construct()
    {
        $this->provider = new Google([
            'clientId' => $_ENV['GOOGLE_CLIENT_ID'],
            'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'redirectUri' => $_ENV['GOOGLE_REDIRECT_URI'],
        ]);
    }


    /**
     * Verifica se o usuário foi autorizado.
     * Valida parâmetros 'code' e 'state' na URL de redirecionamento.
     *
     * @return bool Verdadeiro se autorizado, falso caso contrário.
     */
    public function authorized()
    {
        // sanitiza e valida parâmetros de entrada
        $code = filter_input(INPUT_GET, 'code', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $state = filter_input(INPUT_GET, 'state', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        if (!$code || !$state) {
            error_log('Parâmetros code ou state ausentes ou inválidos.');

            return false;
        }

        $stateData = json_decode($state, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($stateData['auth']) || $stateData['auth'] !== 'google') {
            error_log('Estado inválido ou corrompido: ' . json_last_error_msg());
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $this->user = $this->provider->getResourceOwner($token);
            error_log('Usuário autenticado com sucesso: ' . json_encode([
                'user_id' => $this->user->getId(),
                'timestamp' => date('Y-m-d H:i:s'),
            ]));

            return true;
        } catch(IdentityProviderException $e) {
            error_log('Erro ao obter token de acesso do Google: ' . $e->getMessage() . ' (Código: ' . $e->getCode() . ')');

            return false;
        } catch(Exception $e) {
            error_log('Erro inesperado na autenticação Google: ' . $e->getMessage());

            return false;
        }
    }

    /**
    * Retorna os dados do usuário autenticado.
    *
    * @return GoogleUser|null Dados do usuário ou null se não autenticado.
    */
    public function getData(): ?GoogleUser
    {
        return $this->user;
    }

    /**
    * Gera o link de autenticação com escopo de permissões.
    *
    * @return string URL de autenticação.
    */
    public function generateAuthlink(): string
    {
        $state = json_encode(['auth' => 'google', 'timestamp' => time()]);

        return $this->provider->getAuthorizationUrl([
            'scope' => ['email', 'profile'],
            'state' => $state,
        ]);
    }
}
