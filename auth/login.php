<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Selene - Login</title>
    <meta name="robots" content="noindex, nofollow">
    <style> /* ... Adicione aqui os estilos do header.php para consistência ... */ </style>
</head>
<body>
    <div class="login-container">
        <h1>Bem-vindo(a) ao Selene</h1>
        <p>Por favor, insira suas credenciais para continuar.</p>
        
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