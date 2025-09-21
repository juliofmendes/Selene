<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// ... (toda a lógica inicial de busca de paciente e evoluções permanece a mesma) ...
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }
// ... (busca de evoluções) ...
$evolucaoStmt = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucaoStmt->execute(['pid' => $paciente_id]);
$evolucoes = $evolucaoStmt->fetchAll();

// --- NOVA LÓGICA PARA BUSCAR OS DOCUMENTOS ---
$docStmt = $pdo->prepare("SELECT id, titulo, data_upload FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$docStmt->execute(['pid' => $paciente_id]);
$documentos = $docStmt->fetchAll();


require_once '../components/header.php';
?>
<div class="container">
    <h1>Dossiê do Paciente: <?php echo htmlspecialchars($paciente['nome_completo']); ?></h1>

    <div class="card">
        <h2>Arquivo Digital</h2>
        <form action="<?php echo BASE_URL; ?>/documentos/processa_upload.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
            <div class="form-group">
                <label for="titulo">Título do Documento</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="documento">Ficheiro</label>
                <input type="file" name="documento" required>
            </div>
            <button type="submit">Fazer Upload</button>
        </form>
        <hr style="margin: 2rem 0;">
        <h3>Documentos Anexados</h3>
        <?php if (count($documentos) > 0): ?>
            <ul>
                <?php foreach ($documentos as $doc): ?>
                    <li>
                        <?php echo htmlspecialchars($doc['titulo']); ?> 
                        (<?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?>)
                        - <a href="#">Ver</a> | <a href="#">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum documento anexado a este paciente.</p>
        <?php endif; ?>
    </div>
    
    </div>
<?php require_once '../components/footer.php'; ?>