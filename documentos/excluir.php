<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { die("Acesso inválido."); }

$doc_id = filter_input(INPUT_POST, 'doc_id', FILTER_VALIDATE_INT);
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);

if (!$doc_id || !$paciente_id) { die("Dados inválidos."); }

// Busca o documento para garantir a permissão e obter o caminho do ficheiro
$stmt = $pdo->prepare(
    "SELECT d.caminho_arquivo FROM documentos d
     JOIN pacientes p ON d.paciente_id = p.id
     WHERE d.id = :doc_id AND p.psicologo_id = :psicologo_id"
);
$stmt->execute(['doc_id' => $doc_id, 'psicologo_id' => $_SESSION['usuario_id']]);
$documento = $stmt->fetch();

if ($documento) {
    $caminho_fisico = __DIR__ . '/..' . $documento['caminho_arquivo'];

    $pdo->beginTransaction();
    try {
        // 1. Remove do banco de dados
        $deleteStmt = $pdo->prepare("DELETE FROM documentos WHERE id = :id");
        $deleteStmt->execute(['id' => $doc_id]);
        
        // 2. Remove o ficheiro físico do servidor
        if (file_exists($caminho_fisico)) {
            unlink($caminho_fisico);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao excluir o documento: " . $e->getMessage());
    }
}

header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_exclusao_doc=1');
exit;
?>