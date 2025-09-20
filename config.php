<?php
// --- LÓGICA DE CONEXÃO BASEADA EM VARIÁVEIS DE AMBIENTE ---
// Este arquivo agora é SEGURO para ser enviado ao GitHub.

// 1. Lendo as variáveis do ambiente do servidor
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$base_url = getenv('BASE_URL');

// 2. Definindo as constantes para uso na aplicação
define('BASE_URL', $base_url);

// 3. Conexão PDO
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em caso de falha, o erro será logado no servidor.
    // Nunca exiba o erro em produção.
    http_response_code(500);
    die("Erro interno do servidor.");
} 
?>