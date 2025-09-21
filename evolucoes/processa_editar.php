<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']); // Garante que apenas psicólogos podem adicionar evoluções
require_once '../config.php';

// 1. Validação do Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

// 2. Coleta e Validação dos Dados
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$psicologo_id = $_SESSION['usuario_id']; // O psicólogo logado é o autor

if (!$paciente_id || empty($titulo) || empty($descricao)) {
    // Idealmente, redirecionar com uma mensagem de erro.
    // Por agora, terminamos para evitar inserção de dados inválidos.
    die("Erro: Todos os campos são obrigatórios.");
}

// 3. Inserção Segura no Banco de Dados
try {
    $stmt = $pdo->prepare(
        "INSERT INTO evolucoes (paciente_id, psicologo_id, titulo, descricao) 
         VALUES (:paciente_id, :psicologo_id, :titulo, :descricao)"
    );

    $stmt->execute([
        'paciente_id' => $paciente_id,
        'psicologo_id' => $psicologo_id,
        'titulo' => $titulo,
        'descricao' => $descricao
    ]);

    // 4. Redirecionamento com Feedback de Sucesso
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_evolucao=1');
    exit;

} catch (PDOException $e) {
    // Em produção, logar o erro em vez de o exibir.
    die("Erro ao adicionar evolução: " . $e->getMessage());
}

?>