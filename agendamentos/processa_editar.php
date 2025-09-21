<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

$agendamento_id = filter_input(INPUT_POST, 'agendamento_id', FILTER_VALIDATE_INT);
$data_agendamento = $_POST['data_agendamento'] ?? '';
$status = $_POST['status'] ?? '';
$notas = trim($_POST['notas'] ?? null);

if (!$agendamento_id || empty($data_agendamento) || empty($status)) {
    die("Erro: Dados essenciais em falta.");
}

// Validação do status
$status_permitidos = ['agendado', 'realizado', 'cancelado', 'remarcado'];
if (!in_array($status, $status_permitidos)) {
    die("Status inválido.");
}

try {
    $stmt = $pdo->prepare(
        "UPDATE agendamentos 
         SET data_agendamento = :data, status = :status, notas = :notas
         WHERE id = :id"
    );
    $stmt->execute([
        'data' => $data_agendamento,
        'status' => $status,
        'notas' => $notas,
        'id' => $agendamento_id
    ]);

    header('Location: ' . BASE_URL . '/dashboard/secretaria.php?sucesso_edicao=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar agendamento: " . $e->getMessage());
}
?>