<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona se o acesso não for via POST
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

// 1. Coletar e limpar os dados
$nome_completo = trim($_POST['nome_completo'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = trim($_POST['telefone'] ?? null);
$data_nascimento = $_POST['data_nascimento'] ?? null;

// O ID do psicólogo vem da sessão, garantindo que ele só pode adicionar pacientes para si mesmo
$psicologo_id = $_SESSION['usuario_id'];

// Validação mínima
if (empty($nome_completo)) {
    // Aqui poderíamos ter um sistema de mensagens de erro mais robusto no futuro
    die("O nome completo é obrigatório.");
}

// 2. Preparar e executar a inserção
try {
    $stmt = $pdo->prepare(
        "INSERT INTO pacientes (psicologo_id, nome_completo, email, telefone, data_nascimento) 
         VALUES (:psicologo_id, :nome_completo, :email, :telefone, :data_nascimento)"
    );

    $stmt->execute([
        'psicologo_id' => $psicologo_id,
        'nome_completo' => $nome_completo,
        'email' => $email,
        'telefone' => $telefone,
        'data_nascimento' => !empty($data_nascimento) ? $data_nascimento : null // Garante que datas vazias sejam nulas
    ]);

    // 3. Redirecionar de volta para o dashboard após o sucesso
    // No futuro, podemos adicionar uma mensagem de sucesso na sessão
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;

} catch (PDOException $e) {
    // Em um sistema de produção, logaríamos este erro em vez de exibi-lo
    die("Erro ao cadastrar paciente: " . $e->getMessage());
}
?>