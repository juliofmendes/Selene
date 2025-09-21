<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$psicologo_id = filter_input(INPUT_POST, 'psicologo_id', FILTER_VALIDATE_INT);
$data_agendamento = $_POST['data_agendamento'] ?? '';
$notas = trim($_POST['notas'] ?? null);
$secretaria_id = $_SESSION['usuario_id']; // Guarda quem fez o agendamento

if (!$paciente_id || !$psicologo_id || empty($data_agendamento)) {
    die("Erro: Dados essenciais em falta.");
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO agendamentos (paciente_id, psicologo_id, secretaria_id, data_agendamento, notas)
         VALUES (:pid, :psid, :sid, :data, :notas)"
    );
    $stmt->execute([
        'pid' => $paciente_id,
        'psid' => $psicologo_id,
        'sid' => $secretaria_id,
        'data' => $data_agendamento,
        'notas' => $notas
    ]);

    header('Location: ' . BASE_URL . '/dashboard/secretaria.php?sucesso_agendamento=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao criar agendamento: " . $e->getMessage());
}
?>