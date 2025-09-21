<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/usuarios.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';
$nivel_acesso = $_POST['nivel_acesso'] ?? '';

if (empty($nome) || !$email || empty($senha) || empty($nivel_acesso)) {
    die("Erro: Todos os campos são obrigatórios.");
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (:nome, :email, :senha, :nivel)");
    $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha_hash, 'nivel' => $nivel_acesso]);

    header('Location: ' . BASE_URL . '/admin/usuarios.php?sucesso=1');
    exit;
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Código de erro para entrada duplicada (email)
        die("Erro: Este endereço de email já está a ser utilizado.");
    }
    die("Erro ao criar utilizador: " . $e->getMessage());
}
?>