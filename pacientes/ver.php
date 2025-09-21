<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// --- LÓGICA DE BUSCA DE DADOS (Unificada) ---
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { 
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); 
    exit; 
}

// 1. Busca dados do paciente
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { 
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); 
    exit; 
}

// 2. Busca evoluções
$evolucaoStmt = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucaoStmt->execute(['pid' => $paciente_id]);
$evolucoes = $evolucaoStmt->fetchAll();

// 3. Busca documentos
$docStmt = $pdo->prepare("SELECT id, titulo, data_upload FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$docStmt->execute(['pid' => $paciente_id]);
$documentos = $docStmt->fetchAll();

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
        <form action="<?php echo BASE_URL; ?>/evolucoes/processa_adicionar.php" method="POST" style="margin-bottom: 2rem; border-bottom: 1px solid var(--cor-borda); padding-bottom: 2rem;">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group">
                <label for="titulo">Título da Sessão/Anotação</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição Detalhada (Evolução)</label>
                <textarea id="descricao" name="descricao" rows="5" required></textarea>
            </div>
            <button type="submit">+ Adicionar Evolução</button>
        </form>

        <?php if (count($evolucoes) > 0): ?>
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="evolucao-item">
                    <div class="evolucao-header">
                        <strong><?php echo htmlspecialchars($evolucao['titulo']); ?></strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($evolucao['data_evolucao'])); ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($evolucao['descricao'])); ?></p>
                    <div class="evolucao-actions">
                        <a href="<?php echo BASE_URL; ?>/evolucoes/editar.php?id=<?php echo $evolucao['id']; ?>">Editar</a>
                        <form action="<?php echo BASE_URL; ?>/evolucoes/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja excluir esta evolução?');">
                            <input type="hidden" name="evolucao_id" value="<?php echo $evolucao['id']; ?>">
                            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                            <button type="submit" class="link-delete">Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma evolução registada.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Arquivo Digital</h2>
        <form action="<?php echo BASE_URL; ?>/documentos/processa_upload.php" method="POST" enctype="multipart/form-data" style="margin-bottom: 2rem; border-bottom: 1px solid var(--cor-borda); padding-bottom: 2rem;">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group">
                <label for="titulo_doc">Título do Documento</label>
                <input type="text" id="titulo_doc" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="documento">Ficheiro (PDF, JPG, PNG, etc.)</label>
                <input type="file" id="documento" name="documento" required>
            </div>
            <button type="submit">+ Anexar Documento</button>
        </form>

        <?php if (count($documentos) > 0): ?>
            <table>
                <tbody>
                <?php foreach ($documentos as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></td>
                        <td style="text-align: right;">
                            <a href="<?php echo BASE_URL; ?>/documentos/ver.php?id=<?php echo $doc['id']; ?>" target="_blank" class="button">Ver</a>
                            <form action="<?php echo BASE_URL; ?>/documentos/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja excluir este documento?');" style="display:inline; margin-left: 0.5rem;">
                                <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                                <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                                <button type="submit" class="button button-delete">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum documento anexado.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .dossie-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
    .status { padding: 0.2rem 0.6rem; border-radius: 12px; color: white; font-size: 0.9em; }
    .status-ativo { background-color: #28a745; }
    .status-inativo { background-color: #6c757d; }
    .status-alta { background-color: #17a2b8; }
    .evolucao-item { border-bottom: 1px solid #eee; padding: 1rem 0; }
    .evolucao-item:last-child { border-bottom: none; }
    .evolucao-header { display: flex; justify-content: space-between; font-size: 0.9em; color: #555; margin-bottom: 0.5rem; }
    .evolucao-actions { display: flex; gap: 1rem; margin-top: 1rem; font-size: 0.9em; }
    .link-delete { background:none; border:none; color: #dc3545; cursor:pointer; text-decoration:underline; padding:0; }
</style>

<?php require_once '../components/footer.php'; ?>