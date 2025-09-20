<?php
// Inicia a sessão em todas as páginas
session_start();

// Verifica se o usuário NÃO está logado
// Se não existir a session 'usuario_id', redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    // Garante que o caminho para o login está correto a partir da raiz do projeto
    // Assumindo que a pasta do projeto é /adm/selene/
    header('Location: /adm/selene/auth/login.php'); 
    exit;
}

// Opcional: Verificar nível de acesso específico (adicionaremos isso depois)
// Ex: if ($_SESSION['nivel_acesso'] != 'psicologo') { ... } 
?>