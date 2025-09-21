<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Selene - Ferramenta de Cadastro</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; padding: 2rem; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input, select, button { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ferramenta de Criação de Usuário</h1>
        <p>Use este formulário para inserir um novo usuário no banco de dados com a senha já criptografada.</p>
        <form action="processa_registro.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="form-group">
                <label for="nivel_acesso">Nível de Acesso</label>
                <select id="nivel_acesso" name="nivel_acesso" required>
                    <option value="psicologo">Psicólogo</option>
                    <option value="secretaria">Secretaria</option>
                    <option value="gestor">Gestor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Criar Usuário</button>
        </form>
    </div>
</body>
</html>