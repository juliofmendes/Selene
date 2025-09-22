<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// --- LÓGICA DE BUSCA DE DADOS E KPIs ---

// 1. Busca dados do paciente
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// 2. Busca evoluções
$evolucoesStmt = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucoesStmt->execute(['pid' => $paciente_id]);
$evolucoes = $evolucoesStmt->fetchAll();

// 3. Busca documentos
$docStmt = $pdo->prepare("SELECT id, titulo, data_upload FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$docStmt->execute(['pid' => $paciente_id]);
$documentos = $docStmt->fetchAll();

// 4. CÁLCULO DOS KPIs DO PACIENTE
$total_sessoes = $pdo->prepare("SELECT COUNT(id) FROM agendamentos WHERE paciente_id = :pid AND status = 'realizado'");
$total_sessoes->execute(['pid' => $paciente_id]);
$kpi_total_sessoes = $total_sessoes->fetchColumn();

$ultima_sessao = $pdo->prepare("SELECT MAX(data_agendamento) FROM agendamentos WHERE paciente_id = :pid AND status = 'realizado'");
$ultima_sessao->execute(['pid' => $paciente_id]);
$kpi_ultima_sessao = $ultima_sessao->fetchColumn();

$total_pendente = $pdo->prepare(
    "SELECT SUM(f.valor) FROM faturas f 
     JOIN agendamentos ag ON f.agendamento_id = ag.id
     WHERE ag.paciente_id = :pid AND f.status = 'pendente'"
);
$total_pendente->execute(['pid' => $paciente_id]);
$kpi_total_pendente = $total_pendente->fetchColumn();

require_once '../components/header.php';
?>
<div class="container">
	<div class="dossie-header">
		<h1>Dossiê: <?php echo htmlspecialchars($paciente['nome_completo']); ?></h1>
		<div>
			<a href="<?php echo BASE_URL; ?>/anamnese/selecionar_modelo.php?paciente_id=<?php echo $paciente['id']; ?>" class="button">Anamnese</a>
			<a href="<?php echo BASE_URL; ?>/pacientes/editar.php?id=<?php echo $paciente['id']; ?>" class="button" style="margin-left: 1rem;">Editar Dados</a>
		</div>
	</div>
	</div>

    <div class="kpi-grid">
        <div class="card kpi-card"><h2>Sessões Realizadas</h2><p class="kpi-value"><?php echo $kpi_total_sessoes; ?></p></div>
        <div class="card kpi-card"><h2>Última Sessão</h2><p class="kpi-value-small"><?php echo $kpi_ultima_sessao ? date('d/m/Y', strtotime($kpi_ultima_sessao)) : 'N/A'; ?></p></div>
        <div class="card kpi-card"><h2>Financeiro</h2><p class="kpi-value-small <?php echo ($kpi_total_pendente > 0) ? 'kpi-pendente' : 'kpi-pago'; ?>"><?php echo ($kpi_total_pendente > 0) ? 'Pendente: R$ ' . number_format($kpi_total_pendente, 2, ',', '.') : 'Em dia'; ?></p></div>
        <div class="card kpi-card"><h2>Status Atual</h2><p class="kpi-value-small status status-<?php echo htmlspecialchars($paciente['status']); ?>"><?php echo ucfirst(htmlspecialchars($paciente['status'])); ?></p></div>
    </div>

    <div class="card">
        <h2>Evoluções Clínicas</h2>
        <form action="<?php echo BASE_URL; ?>/evolucoes/processa_adicionar.php" method="POST" class="form-secao">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group"><label>Título da Anotação</label><input type="text" name="titulo" required></div>
            <div class="form-group"><label>Evolução</label><textarea name="descricao" rows="5" required></textarea></div>
            <button type="submit">+ Adicionar Evolução</button>
        </form>
        <?php foreach ($evolucoes as $evolucao): ?>
        <div class="evolucao-item">
            <div class="evolucao-header"><strong><?php echo htmlspecialchars($evolucao['titulo']); ?></strong><span><?php echo date('d/m/Y H:i', strtotime($evolucao['data_evolucao'])); ?></span></div>
            <p><?php echo nl2br(htmlspecialchars($evolucao['descricao'])); ?></p>
            <div class="evolucao-actions action-icons"><a href="<?php echo BASE_URL; ?>/evolucoes/editar.php?id=<?php echo $evolucao['id']; ?>" class="action-icon icon-edit" title="Editar"><span class="material-symbols-rounded">edit</span></a><form action="<?php echo BASE_URL; ?>/evolucoes/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza?');"><input type="hidden" name="evolucao_id" value="<?php echo $evolucao['id']; ?>"><input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>"><button type="submit" class="action-icon icon-delete" title="Excluir"><span class="material-symbols-rounded">delete</span></button></form></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h2>Arquivo Digital</h2>
        <form action="<?php echo BASE_URL; ?>/documentos/processa_upload.php" method="POST" enctype="multipart/form-data" class="form-secao">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente['id']; ?>">
            <div class="form-group"><label>Título do Documento</label><input type="text" name="titulo" required></div>
            <div class="form-group"><label>Ficheiro</label><input type="file" name="documento" required></div>
            <button type="submit">+ Anexar Documento</button>
        </form>
        <?php if (count($documentos) > 0): ?>
        <table>
            <tbody>
            <?php foreach ($documentos as $doc): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></td>
                    <td class="action-icons"><a href="<?php echo BASE_URL; ?>/documentos/ver.php?id=<?php echo $doc['id']; ?>" target="_blank" class="action-icon icon-view" title="Ver"><span class="material-symbols-rounded">visibility</span></a><form action="<?php echo BASE_URL; ?>/documentos/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza?');"><input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>"><input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>"><button type="submit" class="action-icon icon-delete" title="Excluir"><span class="material-symbols-rounded">delete</span></button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; } .kpi-card { text-align: center; padding: 1.5rem; } .kpi-card h2 { font-size: 1.1em; color: #555; margin-bottom: 0.5rem; font-weight: 500; } .kpi-value { font-size: 2.2em; font-weight: 600; color: var(--cor-primaria); margin: 0; } .kpi-value-small { font-size: 1.2em; font-weight: 600; margin: 0; } .kpi-pago { color: #28a745; } .kpi-pendente { color: #dc3545; } .status { padding: 0.2rem 0.6rem; border-radius: 12px; color: white; font-size: 0.9em; display: inline-block; } .status-ativo { background-color: #28a745; } .status-inativo { background-color: #6c757d; } .status-alta { background-color: #17a2b8; } .dossie-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; } .form-secao { margin-bottom: 2rem; border-bottom: 1px solid var(--cor-borda); padding-bottom: 2rem; } .evolucao-item { border-bottom: 1px solid #eee; padding: 1.5rem 0; } .evolucao-item:last-child { border-bottom: none; } .evolucao-header { display: flex; justify-content: space-between; font-size: 0.9em; color: #555; margin-bottom: 0.5rem; } .evolucao-actions { justify-content: flex-start; }
</style>
<?php require_once '../components/footer.php'; ?>