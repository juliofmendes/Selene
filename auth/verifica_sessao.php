<?php
// Inicia a sessão em todas as páginas
session_start();

// INCLUIR O CONFIG PARA TER ACESSO ÀS CONSTANTES COMO BASE_URL
require_once __DIR__ . '/../config.php';

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
?>