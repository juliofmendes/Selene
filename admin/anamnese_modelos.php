<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../config.php';

// Busca todos os modelos existentes
$modelos = $pdo->query("SELECT id, titulo, status FROM anamnese_modelos ORDER BY titulo ASC")->fetchAll();

require_once '../components/header.php';
?>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Modelos de Anamnese</h1>
        <a href="<?php echo BASE_URL; ?>/admin/modelo_anamnese_novo.php" class="button">Novo Modelo</a>
    </div>

    <div class="card">
        <h2>Modelos Disponíveis</h2>
        <table>
            <thead><tr><th>Título</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($modelos as $modelo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($modelo['titulo']); ?></td>
                        <td><?php echo ucfirst($modelo['status']); ?></td>
                        <td class="action-icons">
                            <a href="#" class="action-icon icon-edit" title="Editar"><span class="material-symbols-rounded">edit</span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>