<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

// Coleta de dados
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$psicologo_id = filter_input(INPUT_POST, 'psicologo_id', FILTER_VALIDATE_INT);
$servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
$data_agendamento = $_POST['data_agendamento'] ?? '';
$notas = trim($_POST['notas'] ?? null);
$secretaria_id = $_SESSION['usuario_id'];

if (!$paciente_id || !$psicologo_id || !$servico_id || empty($data_agendamento)) {
    die("Erro: Dados essenciais em falta.");
}

// Inicia uma transação para garantir que ambas as operações (criar agendamento e fatura) ocorram ou nenhuma ocorra.
$pdo->beginTransaction();

try {
    // 1. Cria o agendamento
    $stmtAgendamento = $pdo->prepare(
        "INSERT INTO agendamentos (paciente_id, psicologo_id, secretaria_id, servico_id, data_agendamento, notas)
         VALUES (:pid, :psid, :sid, :servid, :data, :notas)"
    );
    $stmtAgendamento->execute([
        'pid' => $paciente_id,
        'psid' => $psicologo_id,
        'sid' => $secretaria_id,
        'servid' => $servico_id,
        'data' => $data_agendamento,
        'notas' => $notas
    ]);
    $agendamento_id = $pdo->lastInsertId();

    // 2. Busca o valor do serviço para criar a fatura
    $stmtServico = $pdo->prepare("SELECT valor FROM servicos WHERE id = :id");
    $stmtServico->execute(['id' => $servico_id]);
    $servico = $stmtServico->fetch();
    $valor_servico = $servico['valor'];

    // 3. Cria a fatura associada
    $stmtFatura = $pdo->prepare(
        "INSERT INTO faturas (agendamento_id, servico_id, valor, data_emissao)
         VALUES (:agid, :servid, :valor, CURDATE())"
    );
    $stmtFatura->execute([
        'agid' => $agendamento_id,
        'servid' => $servico_id,
        'valor' => $valor_servico
    ]);

    // Se tudo correu bem, confirma as operações.
    $pdo->commit();

    header('Location: ' . BASE_URL . '/dashboard/secretaria.php?sucesso_agendamento=1');
    exit;

} catch (PDOException $e) {
    // Se algo deu errado, desfaz todas as operações.
    $pdo->rollBack();
    die("Erro ao criar agendamento e fatura: " . $e->getMessage());
}
?>