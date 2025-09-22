<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// --- LÓGICA DE BUSCA DE DADOS (Unificada) ---
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

$evolucoes = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucoes->execute(['pid' => $paciente_id]);
$evolucoes = $evolucoes->fetchAll();

$documentos = $pdo->prepare("SELECT id, titulo, data_upload FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$documentos->execute(['pid' => $paciente_id]);
$documentos = $documentos->fetchAll();

require_once '../components/header.php';
?>
<div class="container">
    <div class="dossie-header">
        <div>
            <h1>Dossiê: <?php echo htmlspecialchars($paciente['nome_completo']); ?></h1>
            <p>Status: <span class="status status-<?php echo htmlspecialchars($paciente['status']); ?>"><?php echo ucfirst(htmlspecialchars($paciente['status'])); ?></span></p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>/pacientes/editar.php?id=<?php echo $paciente['id']; ?>" class="button">Editar Dados</a>
            <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php" style="margin-left: 1rem;">&larr; Voltar</a>
        </div>
    </div>

    <div class="card">
        <h2>Evoluções Clínicas</h2>
        <form action="<?php echo BASE_URL; ?>/evolucoes/processa_adicionar.php" method="POST" class="form-secao">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group"><label>Título da Sessão/Anotação</label><input type="text" name="titulo" required></div>
            <div class="form-group"><label>Descrição Detalhada (Evolução)</label><textarea name="descricao" rows="5" required></textarea></div>
            <button type="submit"><span class="material-symbols-rounded">add</span> Adicionar Evolução</button>
        </form>

        <?php if (count($evolucoes) > 0): ?>
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="evolucao-item">
                    <div class="evolucao-header">
                        <strong><?php echo htmlspecialchars($evolucao['titulo']); ?></strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($evolucao['data_evolucao'])); ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($evolucao['descricao'])); ?></p>
                    <div class="evolucao-actions action-icons">
                        <a href="<?php echo BASE_URL; ?>/evolucoes/editar.php?id=<?php echo $evolucao['id']; ?>" class="action-icon icon-edit" title="Editar"><span class="material-symbols-rounded">edit</span></a>
                        <form action="<?php echo BASE_URL; ?>/evolucoes/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza?');">
                            <input type="hidden" name="evolucao_id" value="<?php echo $evolucao['id']; ?>">
                            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                            <button type="submit" class="action-icon icon-delete" title="Excluir"><span class="material-symbols-rounded">delete</span></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Arquivo Digital</h2>
        <form action="<?php echo BASE_URL; ?>/documentos/processa_upload.php" method="POST" enctype="multipart/form-data" class="form-secao">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group"><label>Título do Documento</label><input type="text" name="titulo" required></div>
            <div class="form-group"><label>Ficheiro</label><input type="file" name="documento" required></div>
            <button type="submit"><span class="material-symbols-rounded">upload_file</span> Anexar Documento</button>
        </form>

        <?php if (count($documentos) > 0): ?>
            <table>
                <tbody>
                <?php foreach ($documentos as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></td>
                        <td class="action-icons">
                            <a href="<?php echo BASE_URL; ?>/documentos/ver.php?id=<?php echo $doc['id']; ?>" target="_blank" class="action-icon icon-view" title="Ver"><span class="material-symbols-rounded">visibility</span></a>
                            <form action="<?php echo BASE_URL; ?>/documentos/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza?');">
                                <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                                <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                                <button type="submit" class="action-icon icon-delete" title="Excluir"><span class="material-symbols-rounded">delete</span></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Estilos específicos da página de Dossiê */
    .dossie-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
    .status { padding: 0.2rem 0.6rem; border-radius: 12px; color: white; font-size: 0.9em; }
    .status-ativo { background-color: #28a745; }
    .status-inativo { background-color: #6c757d; }
    .status-alta { background-color: #17a2b8; }
    .form-secao { margin-bottom: 2rem; border-bottom: 1px solid var(--cor-borda); padding-bottom: 2rem; }
    .evolucao-item { border-bottom: 1px solid #eee; padding: 1.5rem 0; }
    .evolucao-item:last-child { border-bottom: none; }
    .evolucao-header { display: flex; justify-content: space-between; font-size: 0.9em; color: #555; margin-bottom: 0.5rem; }
    .evolucao-actions { justify-content: flex-start; }
</style>

<?php require_once '../components/footer.php'; ?>