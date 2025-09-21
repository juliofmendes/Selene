<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']);
require_once '../config.php';
require_once '../components/header.php';
?>
<div class="container">
    <h1>Criar Novo Utilizador</h1>
    <a href="<?php echo BASE_URL; ?>/admin/usuarios.php">&larr; Voltar para a lista</a>

    <div class="card">
        <form action="processa_usuario_novo.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha Provisória</label>
                <input type="password" name="senha" required>
            </div>
            <div class="form-group">
                <label for="nivel_acesso">Nível de Acesso</label>
                <select name="nivel_acesso" required>
                    <option value="psicologo">Psicólogo</option>
                    <option value="secretaria">Secretaria</option>
                    <option value="gestor">Gestor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Criar Utilizador</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>