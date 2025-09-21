<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']);
require_once '../config.php';

$usuarios = $pdo->query("SELECT id, nome, email, nivel_acesso FROM usuarios ORDER BY nome ASC")->fetchAll();

require_once '../components/header.php';
?>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Gestão de Utilizadores</h1>
        <a href="<?php echo BASE_URL; ?>/admin/usuario_novo.php" class="button">Novo Utilizador</a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert-sucesso">Novo utilizador criado com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_edicao'])): ?>
        <div class="alert-sucesso">Utilizador atualizado com sucesso!</div>
    <?php endif; ?>

    <div class="card">
        <h2>Utilizadores do Sistema</h2>
        <table>
            <thead><tr><th>Nome</th><th>Email</th><th>Nível</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $usuario['nivel_acesso']))); ?></td>
                        <td><a href="usuario_editar.php?id=<?php echo $usuario['id']; ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<style>.alert-sucesso { background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }</style>
<?php require_once '../components/footer.php'; ?>