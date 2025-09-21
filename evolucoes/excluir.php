<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Apenas permite o método POST para maior segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$evolucao_id = filter_input(INPUT_POST, 'evolucao_id', FILTER_VALIDATE_INT);
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);

// Verificação rigorosa dos IDs recebidos
if (!$evolucao_id || !$paciente_id) {
    // Se os IDs não forem válidos, redireciona com uma mensagem de erro
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&erro_exclusao=1');
    exit;
}

try {
    // Prepara a consulta de exclusão, garantindo que o psicólogo
    // só pode apagar as suas próprias evoluções.
    $stmt = $pdo->prepare(
        "DELETE FROM evolucoes 
         WHERE id = :evolucao_id AND psicologo_id = :psicologo_id"
    );

    $stmt->execute([
        'evolucao_id' => $evolucao_id,
        'psicologo_id' => $_SESSION['usuario_id']
    ]);

    // **A VERIFICAÇÃO CRUCIAL:**
    // Verificamos quantas linhas foram afetadas pela operação.
    // Se for maior que 0, a exclusão foi um sucesso.
    if ($stmt->rowCount() > 0) {
        // Redireciona com mensagem de sucesso
        header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_exclusao=1');
    } else {
        // Se 0 linhas foram afetadas, significa que a evolução não foi encontrada
        // ou não pertencia a este psicólogo. Redireciona com uma mensagem de erro.
        header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&erro_exclusao=2');
    }
    exit;

} catch (PDOException $e) {
    // Em caso de erro de base de dados, redireciona com um erro genérico
    // (e em produção, deveríamos logar o erro $e->getMessage())
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&erro_exclusao=3');
    exit;
}
?>