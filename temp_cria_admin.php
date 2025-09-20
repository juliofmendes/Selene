<?php
require_once 'config.php';

$nome = "Júlio Mendes";
$email = "juliofmendes@gmail.com";
$senha_texto_puro = "!D3k72yh3";
$nivel_acesso = "Admin"; // Ou 'admin'

// A Mágica da Criptografia - NUNCA armazene senhas em texto puro
$senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (:nome, :email, :senha, :nivel)");
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha_hash,
        'nivel' => $nivel_acesso
    ]);
    echo "<h1>Usuário Administrador criado com sucesso!</h1>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Senha:</strong> " . htmlspecialchars($senha_texto_puro) . "</p>";
    echo "<h2>AVISO: DELETE ESTE ARQUIVO ('temp_cria_admin.php') IMEDIATAMENTE!</h2>";

} catch (PDOException $e) {
    die("Erro ao criar usuário: " . $e->getMessage());
}
?>