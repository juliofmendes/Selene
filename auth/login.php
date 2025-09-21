<?php
session_start();
require_once '../config.php'; // Incluímos para ter acesso a BASE_URL

// VERIFICAÇÃO DE ROBUSTEZ: Se o utilizador já estiver logado, redireciona-o.
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Selene - Login</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="card" style="width: 400px;">
        <h1>Bem-vindo(a) ao Selene</h1>
        <p>Por favor, insira as suas credenciais para continuar.</p>
        
        <?php if (isset($_GET['erro'])): ?>
            <p style="color: red;">Email ou senha inválidos. Tente novamente.</p>
        <?php endif; ?>

        <form action="processa_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>