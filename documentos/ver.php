<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

$doc_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$doc_id) { die("ID de documento inválido."); }

// Busca o documento e verifica se o utilizador logado (psicólogo) tem permissão
$stmt = $pdo->prepare(
    "SELECT d.* FROM documentos d
     JOIN pacientes p ON d.paciente_id = p.id
     WHERE d.id = :doc_id AND p.psicologo_id = :psicologo_id"
);
$stmt->execute(['doc_id' => $doc_id, 'psicologo_id' => $_SESSION['usuario_id']]);
$documento = $stmt->fetch();

if (!$documento) {
    die("Acesso negado ou documento não encontrado.");
}

$caminho_fisico = __DIR__ . '/..' . $documento['caminho_arquivo'];

if (file_exists($caminho_fisico)) {
    header('Content-Type: ' . $documento['tipo_mime']);
    header('Content-Disposition: inline; filename="' . basename($documento['nome_arquivo']) . '"');
    header('Content-Length: ' . $documento['tamanho_arquivo']);
    readfile($caminho_fisico);
    exit;
} else {
    die("Erro: Ficheiro não encontrado no servidor.");
}
?>