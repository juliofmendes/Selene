<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Apenas permite o método POST para maior segurança em ações destrutivas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$evolucao_id = filter_input(INPUT_POST, 'evolucao_id', FILTER_VALIDATE_INT);
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);

if (!$evolucao_id || !$paciente_id) {
    die("Erro: IDs inválidos.");
}

try {
    // A consulta DELETE verifica também o psicologo_id para garantir
    // que um psicólogo não possa apagar evoluções de outro.
    $stmt = $pdo->prepare(
        "DELETE FROM evolucoes 
         WHERE id = :evolucao_id AND psicologo_id = :psicologo_id"
    );

    $stmt->execute([
        'evolucao_id' => $evolucao_id,
        'psicologo_id' => $_SESSION['usuario_id']
    ]);

    // Redireciona de volta para o dossiê do paciente
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_exclusao=1');
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir evolução: " . $e->getMessage());
}
?>