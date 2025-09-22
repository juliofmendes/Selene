<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$modelo_id = filter_input(INPUT_POST, 'modelo_id', FILTER_VALIDATE_INT);

if (!$paciente_id || !$modelo_id) { die("Erro: Dados inválidos."); }

// Busca a estrutura do modelo para saber quais campos esperar
$stmtModelo = $pdo->prepare("SELECT estrutura_json FROM anamnese_modelos WHERE id = :id");
$stmtModelo->execute(['id' => $modelo_id]);
$estrutura = json_decode($stmtModelo->fetchColumn(), true);

// Coleta dinamicamente as respostas com base na estrutura do modelo
$respostas = [];
foreach ($estrutura as $campo) {
    $nome_campo = $campo['name'];
    if (isset($_POST[$nome_campo])) {
        $respostas[$nome_campo] = trim($_POST[$nome_campo]);
    }
}

$respostas_json = json_encode($respostas, JSON_UNESCAPED_UNICODE);

try {
    // Verifica se já existe uma resposta para este paciente e modelo (lógica "upsert")
    $stmtCheck = $pdo->prepare("SELECT id FROM anamnese_respostas WHERE paciente_id = :pid AND modelo_id = :mid");
    $stmtCheck->execute(['pid' => $paciente_id, 'mid' => $modelo_id]);
    $existente_id = $stmtCheck->fetchColumn();

    if ($existente_id) { // UPDATE
        $stmt = $pdo->prepare("UPDATE anamnese_respostas SET respostas_json = :json WHERE id = :id AND psicologo_id = :psid");
        $stmt->execute(['json' => $respostas_json, 'id' => $existente_id, 'psid' => $_SESSION['usuario_id']]);
    } else { // INSERT
        $stmt = $pdo->prepare("INSERT INTO anamnese_respostas (paciente_id, modelo_id, psicologo_id, respostas_json) VALUES (:pid, :mid, :psid, :json)");
        $stmt->execute(['pid' => $paciente_id, 'mid' => $modelo_id, 'psid' => $_SESSION['usuario_id'], 'json' => $respostas_json]);
    }

    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_anamnese=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao salvar respostas da anamnese: " . $e->getMessage());
}
?>