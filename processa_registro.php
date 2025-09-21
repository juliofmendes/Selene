<?php
// Incluímos o config para ter a conexão com o banco ($pdo)
require_once 'config.php';

// Verificamos se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido.");
}

// 1. Coletar e validar os dados do formulário
$nome = trim($_POST['nome'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha_texto_puro = $_POST['senha'] ?? '';
$nivel_acesso = $_POST['nivel_acesso'] ?? '';

// Validação simples
if (empty($nome) || empty($email) || empty($senha_texto_puro) || empty($nivel_acesso)) {
    die("<h1>Erro</h1><p>Todos os campos são obrigatórios.</p><a href='temp_register.php'>Voltar</a>");
}

// 2. Criptografar a senha - O passo mais importante
$senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

// 3. Preparar e executar a inserção no banco de dados
try {
    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (:nome, :email, :senha, :nivel_acesso)"
    );

    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha_hash,
        'nivel_acesso' => $nivel_acesso
    ]);

    // 4. Fornecer feedback claro de sucesso
    echo "<h1>Sucesso!</h1>";
    echo "<p>O usuário <strong>" . htmlspecialchars($nome) . "</strong> foi criado com sucesso no banco de dados.</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Senha usada:</strong> " . htmlspecialchars($senha_texto_puro) . "</p>";
    echo "<hr>";
    echo "<h2>Ações Recomendadas:</h2>";
    echo "<ol>";
    echo "<li><a href='auth/login.php' target='_blank'>Tente fazer o login em uma nova aba.</a></li>";
    echo "<li><strong>DELETE OS ARQUIVOS `temp_register.php` E `processa_registro.php` IMEDIATAMENTE!</strong></li>";
    echo "</ol>";

} catch (PDOException $e) {
    // Fornecer feedback de erro detalhado (seguro para uma ferramenta temporária)
    die("<h1>Erro ao Inserir no Banco de Dados</h1><p>Ocorreu um erro: " . $e->getMessage() . "</p><a href='temp_register.php'>Voltar</a>");
}
?>