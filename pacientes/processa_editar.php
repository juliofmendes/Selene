<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Garante que o acesso é via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); 
    exit;
}

// 1. Coletar e validar TODOS os dados do formulário
$paciente_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome_completo = trim($_POST['nome_completo'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = trim($_POST['telefone'] ?? null);
$data_nascimento = $_POST['data_nascimento'] ?? null;
$status = trim($_POST['status'] ?? 'ativo');

// Validação de dados essenciais
if (!$paciente_id || empty($nome_completo)) {
    die("Erro: Dados essenciais em falta.");
}

// Garante que o status é um dos valores permitidos para segurança
$status_permitidos = ['ativo', 'inativo', 'alta'];
if (!in_array($status, $status_permitidos)) {
    $status = 'ativo'; // Padrão de segurança
}

try {
    // 2. A instrução SQL com o número correto de tokens
    $stmt = $pdo->prepare(
        "UPDATE pacientes 
         SET nome_completo = :nome, email = :email, telefone = :tel, data_nascimento = :data_nasc, status = :status
         WHERE id = :id AND psicologo_id = :pid"
    );
    
    // 3. O array de execução com o número correspondente de variáveis
    $stmt->execute([
        'nome' => $nome_completo,
        'email' => $email,
        'tel' => $telefone,
        'data_nasc' => !empty($data_nascimento) ? $data_nascimento : null,
        'status' => $status,
        'id' => $paciente_id,
        'pid' => $_SESSION['usuario_id'] // Verificação de segurança
    ]);

    // Redireciona de volta para a página de visualização após o sucesso
    header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso=1');
    exit;

} catch (PDOException $e) {
    // Em produção, isto deveria ser logado e não exibido
    die("Erro ao atualizar paciente: " . $e->getMessage());
}
?>