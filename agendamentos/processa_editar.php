<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'psicologo_autonomo']);
require_once '../config.php';
require_once '../core/funcoes.php'; // Inclui o nosso ficheiro de funções

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

// ... (coleta e validação de dados do formulário)
$agendamento_id = filter_input(INPUT_POST, 'agendamento_id', FILTER_VALIDATE_INT);
$data_agendamento = $_POST['data_agendamento'] ?? '';
$status = $_POST['status'] ?? '';
$notas = trim($_POST['notas'] ?? null);

if (!$agendamento_id || empty($data_agendamento) || empty($status)) {
    die("Erro: Dados essenciais em falta.");
}

try {
    // CORREÇÃO DE SEGURANÇA: Busca o psicólogo_id de forma segura
    $stmtPsicologo = $pdo->prepare("SELECT psicologo_id FROM agendamentos WHERE id = :id");
    $stmtPsicologo->execute(['id' => $agendamento_id]);
    $psicologo_id = $stmtPsicologo->fetchColumn();

    // Atualiza o agendamento
    $stmtUpdate = $pdo->prepare(
        "UPDATE agendamentos SET data_agendamento = :data, status = :status, notas = :notas WHERE id = :id"
    );
    $stmtUpdate->execute([
        'data' => $data_agendamento,
        'status' => $status,
        'notas' => $notas,
        'id' => $agendamento_id
    ]);

    // Dispara a notificação para o psicólogo
    if ($psicologo_id) {
        $mensagem = "O agendamento de " . date('d/m H:i', strtotime($data_agendamento)) . " foi atualizado para: " . ucfirst($status);
        $link = '/agendamentos/minha_agenda.php';
        criar_notificacao($pdo, $psicologo_id, $mensagem, $link);
    }

    header('Location: ' . BASE_URL . '/dashboard/secretaria.php?sucesso_edicao=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar agendamento: " . $e->getMessage());
}
?>