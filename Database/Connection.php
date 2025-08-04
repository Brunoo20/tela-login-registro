<?php

namespace Database;

use Dotenv\Dotenv;
use Exception;
use PDO;

final class Connection
{
    /**
     * Não podem existir instâncias de Connection
     */
    private function __construct()
    {
    }

    /**
     * Recebe o nome do conector de BD e instancia o objeto PDO
     */
    public static function open($name)
    {
        $envFile = 'database.env';
        $envPath = __DIR__ . '/../Config';

        if (file_exists("{$envPath}/{$envFile}")) {
            $dotenv = Dotenv::createImmutable($envPath, $envFile);
            $dotenv->load();
        } else {
            throw new Exception("Arquivo de ambiente '{$envFile}' não encontrado em '{$envPath}'");
        }

        // lê as informações contidas no arquivo
        $user = $_ENV['DB_USER'] ?? null;
        $pass = $_ENV['DB_PASS'] ?? null;
        $name = $_ENV['DB_NAME'] ?? null;
        $host = $_ENV['DB_HOST'] ?? null;
        $type = $_ENV['DB_TYPE'] ?? null;
        $port = $_ENV['DB_PORT'] ?? null;

        // verifica se as configurações obrigatórias estão definidas
        if (!$user || !$name || !$host || !$type) {
            throw new Exception('Configurações do banco de dados não encontradas no .env');
        }

        //descobre qual o tipo (driver) de banco de dados a ser utilizado

        switch ($type) {
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name}; user={$user}; password={$pass}; host=$host;port={$port}");

                break;
            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);

                break;
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                $conn->query('PRAGMA foreign_Keys = ON');

                break;
            case 'ibase':
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);

                break;
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);

                break;
            case 'mssql':
                $conn = new PDO("dblib:host={$host}, 1443;dbname={$name}", $user, $pass);

                break;
        }

        // define para que o PDO lance exceções na ocorrência de erros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}
