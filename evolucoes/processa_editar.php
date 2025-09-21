<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$evolucao_id = filter_input(INPUT_POST, 'evolucao_id', FILTER_VALIDATE_INT);
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (!$evolucao_id || !$paciente_id || empty($titulo) || empty($descricao)) {
    die("Erro: Dados essenciais em falta.");
}

try {
    $stmt = $pdo->prepare(
        "UPDATE evolucoes SET titulo = :titulo, descricao = :descricao 
         WHERE id = :id AND psicologo_id = :pid"
    );
    $stmt->execute([
        'titulo' => $titulo,
        'descricao' => $descricao,
        'id' => $evolucao_id,
        'pid' => $_SESSION['usuario_id']
    ]);

    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_edicao_evolucao=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar evolução: " . $e->getMessage());
}
?>