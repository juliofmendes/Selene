<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Garante que o acesso é via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); 
    exit;
}

// 1. Coletar e validar os dados do formulário
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$psicologo_id = $_SESSION['usuario_id']; // O psicólogo logado é o autor

// Validação de dados essenciais
if (!$paciente_id || empty($titulo) || empty($descricao)) {
    // Idealmente, redirecionar com uma mensagem de erro
    die("Erro: Dados essenciais em falta.");
}

try {
    // 2. Preparar e executar a inserção na base de dados
    $stmt = $pdo->prepare(
        "INSERT INTO evolucoes (paciente_id, psicologo_id, titulo, descricao, data_evolucao) 
         VALUES (:pid, :psid, :titulo, :descricao, NOW())"
    );
    
    $stmt->execute([
        'pid' => $paciente_id,
        'psid' => $psicologo_id,
        'titulo' => $titulo,
        'descricao' => $descricao
    ]);

    // 3. Redirecionar de volta para o dossiê com uma mensagem de sucesso
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_evolucao=1');
    exit;

} catch (PDOException $e) {
    // Em produção, isto deveria ser logado e não exibido
    die("Erro ao adicionar evolução: " . $e->getMessage());
}

?>