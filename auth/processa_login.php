<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || empty($senha)) {
    header('Location: login.php?erro=1');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    // Autenticação bem-sucedida
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
    
    // Redireciona para o dashboard principal, que por sua vez redirecionará para o correto
    header('Location: ../dashboard/index.php');
    exit;
} else {
    // Falha na autenticação
    header('Location: login.php?erro=1');
    exit;
}