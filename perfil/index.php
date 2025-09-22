<?php
require_once '../auth/verifica_sessao.php';
// Não é necessária a função autorizar(), pois todos os utilizadores logados podem aceder ao seu perfil.
require_once '../config.php';

// Busca os dados atuais do utilizador logado para preencher o formulário
$stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

require_once '../components/header.php';
?>
<div class="container">
    <h1>Meu Perfil</h1>
    <p>Gira as suas informações pessoais e de segurança.</p>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert-sucesso" style="margin-bottom: 1rem;">Perfil atualizado com sucesso!</div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert-erro" style="margin-bottom: 1rem;">Ocorreu um erro. A palavra-passe atual pode estar incorreta.</div>
    <?php endif; ?>

    <div class="card">
        <h2>Alterar Dados</h2>
        <form action="processa_perfil.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                <small>O email não pode ser alterado.</small>
            </div>
            <hr style="margin: 2rem 0;">
            <h3>Alterar Palavra-passe</h3>
            <div class="form-group">
                <label for="senha_atual">Palavra-passe Atual (deixe em branco se não quiser alterar)</label>
                <input type="password" name="senha_atual">
            </div>
            <div class="form-group">
                <label for="nova_senha">Nova Palavra-passe</label>
                <input type="password" name="nova_senha">
            </div>
            <div class="form-group">
                <label for="confirma_nova_senha">Confirmar Nova Palavra-passe</label>
                <input type="password" name="confirma_nova_senha">
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
<style>.alert-erro { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; }</style>
<?php require_once '../components/footer.php'; ?>