<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (!$paciente_id || empty($titulo) || empty($descricao)) {
    die("Erro: Dados essenciais em falta.");
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO evolucoes (paciente_id, psicologo_id, titulo, descricao, data_evolucao) 
         VALUES (:pid, :psid, :titulo, :descricao, NOW())"
    );
    $stmt->execute([
        'pid' => $paciente_id,
        'psid' => $_SESSION['usuario_id'],
        'titulo' => $titulo,
        'descricao' => $descricao
    ]);

    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_evolucao=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao adicionar evolução: " . $e->getMessage());
}
?>