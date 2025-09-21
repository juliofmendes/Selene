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
$docStmt = $pdo->prepare("SELECT id, titulo, data_upload, nome_arquivo FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$docStmt->execute(['pid' => $paciente_id]);
$documentos = $docStmt->fetchAll();


require_once '../components/header.php';
?>
<div class="container">
    <div class="card">
        <h2>Arquivo Digital</h2>
        <hr style="margin: 2rem 0;">
        <h3>Documentos Anexados</h3>
        <?php if (count($documentos) > 0): ?>
            <table>
                <tbody>
                <?php foreach ($documentos as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></td>
                        <td style="text-align: right;">
                            <a href="<?php echo BASE_URL; ?>/documentos/ver.php?id=<?php echo $doc['id']; ?>" target="_blank" class="button">Ver</a>
                            <form action="<?php echo BASE_URL; ?>/documentos/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza?');" style="display:inline;">
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
<?php require_once '../components/footer.php'; ?>