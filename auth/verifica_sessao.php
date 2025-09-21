<?php
session_start();
require_once __DIR__ . '/../config.php';

// Se não houver sessão de utilizador, redireciona para o login.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

/**
 * Função de segurança para verificar o nível de acesso.
 * Bloqueia o acesso se o utilizador não tiver um dos níveis permitidos.
 *
 * @param array $niveis_permitidos Array com os nomes dos níveis de acesso permitidos.
 */
function autorizar(array $niveis_permitidos) {
    if (!in_array($_SESSION['nivel_acesso'], $niveis_permitidos)) {
        // Se não tiver permissão, redireciona para o dashboard principal (que o redirecionará corretamente).
        header('Location: ' . BASE_URL . '/dashboard/index.php');
        exit;
    }
}
?>