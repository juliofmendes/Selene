<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
if (!$paciente_id) { die("Erro: Paciente inválido."); }

// Organiza todos os dados do POST num array associativo
$dados_anamnese = [
    'queixa_principal' => trim($_POST['queixa_principal'] ?? ''),
    'historico_saude' => trim($_POST['historico_saude'] ?? ''),
    'historico_familiar' => trim($_POST['historico_familiar'] ?? '')
    // Futuros campos serão adicionados aqui
];

// Converte o array para o formato JSON
$dados_json = json_encode($dados_anamnese, JSON_UNESCAPED_UNICODE);

try {
    // Verifica se já existe uma anamnese para este paciente
    $stmtCheck = $pdo->prepare("SELECT id FROM anamneses WHERE paciente_id = :pid");
    $stmtCheck->execute(['pid' => $paciente_id]);
    $existente_id = $stmtCheck->fetchColumn();

    if ($existente_id) { // Se existe, atualiza (UPDATE)
        $stmt = $pdo->prepare("UPDATE anamneses SET dados_anamnese = :dados WHERE id = :id AND psicologo_id = :psid");
        $stmt->execute(['dados' => $dados_json, 'id' => $existente_id, 'psid' => $_SESSION['usuario_id']]);
    } else { // Se não existe, insere (INSERT)
        $stmt = $pdo->prepare("INSERT INTO anamneses (paciente_id, psicologo_id, dados_anamnese) VALUES (:pid, :psid, :dados)");
        $stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id'], 'dados' => $dados_json]);
    }

    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_anamnese=1');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar anamnese: " . $e->getMessage());
}
?>