<?php
// Configuração de conexão MySQL
// Carrega variáveis do .env
function env($key) {
    static $env;
    if (!$env) {
        $env = [];
        if (file_exists(__DIR__.'/.env')) {
            foreach (file(__DIR__.'/.env') as $line) {
                if (preg_match('/^([A-Z0-9_]+)=(.*)$/', trim($line), $m)) {
                    $env[$m[1]] = $m[2];
                }
            }
        }
    }
    return $env[$key] ?? '';
}
$host = env('MYSQL_HOST');
$user = env('MYSQL_USER');
$pass = env('MYSQL_PASS');
$db = env('MYSQL_DB');
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}
// Funções de criptografia ponta a ponta
function crypto_encrypt($data) {
    $key = env('CRYPTO_KEY');
    $iv = substr(hash('sha256', $key), 0, 16);
    return openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
}
function crypto_decrypt($data) {
    $key = env('CRYPTO_KEY');
    $iv = substr(hash('sha256', $key), 0, 16);
    return openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv);
}
?>