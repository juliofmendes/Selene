<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit;
}

$paciente_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
// ... (coletar todos os outros campos do formulário como em processa_adicionar.php) ...
$nome_completo = trim($_POST['nome_completo'] ?? '');
// ...

if (!$paciente_id || empty($nome_completo)) {
    die("Dados inválidos.");
}

try {
    $stmt = $pdo->prepare(
        "UPDATE pacientes SET nome_completo = :nome, email = :email, telefone = :tel, data_nascimento = :data_nasc, status = :status
         WHERE id = :id AND psicologo_id = :pid"
    );
    $stmt->execute([
        'nome' => $nome_completo,
        // ... (bind de todos os outros parâmetros) ...
        'id' => $paciente_id,
        'pid' => $_SESSION['usuario_id']
    ]);

    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id);
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar paciente: " . $e->getMessage());
}
?>