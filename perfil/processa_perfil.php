<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/perfil/index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome = trim($_POST['nome'] ?? '');
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirma_nova_senha = $_POST['confirma_nova_senha'] ?? '';

try {
    // Atualiza sempre o nome
    $stmtNome = $pdo->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
    $stmtNome->execute(['nome' => $nome, 'id' => $usuario_id]);

    // Lógica para alterar a palavra-passe (apenas se os campos forem preenchidos)
    if (!empty($senha_atual) && !empty($nova_senha)) {
        if ($nova_senha !== $confirma_nova_senha) {
            header('Location: ' . BASE_URL . '/perfil/index.php?erro=confirmacao');
            exit;
        }

        // 1. Verifica se a palavra-passe atual está correta
        $stmtCheck = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
        $stmtCheck->execute(['id' => $usuario_id]);
        $usuario = $stmtCheck->fetch();

        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            // 2. Se estiver correta, atualiza para a nova
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmtUpdateSenha = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $stmtUpdateSenha->execute(['senha' => $nova_senha_hash, 'id' => $usuario_id]);
        } else {
            // Palavra-passe atual incorreta
            header('Location: ' . BASE_URL . '/perfil/index.php?erro=senha_invalida');
            exit;
        }
    }

    // Atualiza o nome na sessão para refletir a mudança imediatamente
    $_SESSION['usuario_nome'] = $nome;

    header('Location: ' . BASE_URL . '/perfil/index.php?sucesso=1');
    exit;

} catch (PDOException $e) {
    die("Erro ao atualizar perfil: " . $e->getMessage());
}
?>