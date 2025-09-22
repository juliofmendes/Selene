<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/anamnese_modelos.php');
    exit;
}

$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? null);
$estrutura_json = $_POST['estrutura_json'] ?? '';

// Validação simples
if (empty($titulo) || empty($estrutura_json) || json_decode($estrutura_json) === null) {
    die("Erro: Título é obrigatório e a estrutura deve ser um JSON válido.");
}

try {
    $stmt = $pdo->prepare("INSERT INTO anamnese_modelos (titulo, descricao, estrutura_json) VALUES (:titulo, :desc, :json)");
    $stmt->execute(['titulo' => $titulo, 'desc' => $descricao, 'json' => $estrutura_json]);

    header('Location: ' . BASE_URL . '/admin/anamnese_modelos.php?sucesso=1');
    exit;
} catch (PDOException $e) {
    die("Erro ao salvar o modelo de anamnese: " . $e->getMessage());
}
?>