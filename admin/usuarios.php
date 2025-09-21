<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']); // Apenas o admin principal pode gerir utilizadores
require_once '../config.php';

// Busca todos os utilizadores para listar
$usuarios = $pdo->query("SELECT id, nome, email, nivel_acesso FROM usuarios ORDER BY nome ASC")->fetchAll();

require_once '../components/header.php';
?>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Gestão de Utilizadores</h1>
        <a href="<?php echo BASE_URL; ?>/admin/usuario_novo.php" class="button">Novo Utilizador</a>
    </div>

    <div class="card">
        <h2>Utilizadores do Sistema</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($usuario['nivel_acesso'])); ?></td>
                        <td><a href="usuario_editar.php?id=<?php echo $usuario['id']; ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>