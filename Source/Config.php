<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega o arquivo google.env da pasta Config
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../Config', 'google.env');
$dotenv->load();

// Valida as variáveis de ambiente
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? null;
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? null;
$redirectUrl = $_ENV['GOOGLE_REDIRECT_URI'] ?? null;
