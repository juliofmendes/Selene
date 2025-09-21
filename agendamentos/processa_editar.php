<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';
require_once '../core/funcoes.php'; // Incluímos o nosso novo ficheiro de funções


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
    // Busca o ID do psicólogo associado ao agendamento para poder notificá-lo
    $psicologo_id = $pdo->query("SELECT psicologo_id FROM agendamentos WHERE id = $agendamento_id")->fetchColumn();

    $stmt = $pdo->prepare("UPDATE agendamentos SET data_agendamento = :data, status = :status, notas = :notas WHERE id = :id");
    // ... (execute) ...
    $stmt->execute([
        'data' => $data_agendamento,
        'status' => $status,
        'notas' => trim($_POST['notas'] ?? null),
        'id' => $agendamento_id
    ]);

    // Dispara a notificação para o psicólogo
    if ($psicologo_id) {
        $mensagem = "O agendamento para " . date('d/m H:i', strtotime($data_agendamento)) . " foi atualizado. Novo status: " . ucfirst($status);
        $link = '/agendamentos/minha_agenda.php';
        criar_notificacao($pdo, $psicologo_id, $mensagem, $link);
    }

    header('Location: ' . BASE_URL . '/dashboard/secretaria.php?sucesso_edicao=1');
    exit;
} catch (PDOException $e) { /* ... */ }
?>