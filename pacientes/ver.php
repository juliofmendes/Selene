<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// --- LÓGICA DE BUSCA DE DADOS E KPIs ---

// 1. Busca dados do paciente (como antes)
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// 2. Busca evoluções e documentos (como antes)
$evolucoes = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucoes->execute(['pid' => $paciente_id]);
$evolucoes = $evolucoes->fetchAll();

$documentos = $pdo->prepare("SELECT id, titulo, data_upload FROM documentos WHERE paciente_id = :pid ORDER BY data_upload DESC");
$documentos->execute(['pid' => $paciente_id]);
$documentos = $documentos->fetchAll();

// 3. CÁLCULO DOS KPIs DO PACIENTE
// Total de sessões realizadas
$total_sessoes = $pdo->prepare("SELECT COUNT(id) FROM agendamentos WHERE paciente_id = :pid AND status = 'realizado'");
$total_sessoes->execute(['pid' => $paciente_id]);
$kpi_total_sessoes = $total_sessoes->fetchColumn();

// Data da última sessão
$ultima_sessao = $pdo->prepare("SELECT MAX(data_agendamento) FROM agendamentos WHERE paciente_id = :pid AND status = 'realizado'");
$ultima_sessao->execute(['pid' => $paciente_id]);
$kpi_ultima_sessao = $ultima_sessao->fetchColumn();

// Valor total pendente
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
        <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php" class="button">&larr; Voltar</a>
    </div>

    <div class="kpi-grid">
        <div class="card kpi-card">
            <h2>Sessões Realizadas</h2>
            <p class="kpi-value"><?php echo $kpi_total_sessoes; ?></p>
        </div>
        <div class="card kpi-card">
            <h2>Última Sessão</h2>
            <p class="kpi-value-small"><?php echo $kpi_ultima_sessao ? date('d/m/Y', strtotime($kpi_ultima_sessao)) : 'N/A'; ?></p>
        </div>
        <div class="card kpi-card">
            <h2>Financeiro</h2>
            <p class="kpi-value-small <?php echo ($kpi_total_pendente > 0) ? 'kpi-pendente' : 'kpi-pago'; ?>">
                <?php echo ($kpi_total_pendente > 0) ? 'Pendente: R$ ' . number_format($kpi_total_pendente, 2, ',', '.') : 'Em dia'; ?>
            </p>
        </div>
        <div class="card kpi-card">
            <h2>Status Atual</h2>
            <p class="kpi-value-small status status-<?php echo htmlspecialchars($paciente['status']); ?>"><?php echo ucfirst(htmlspecialchars($paciente['status'])); ?></p>
        </div>
    </div>

    <div class="card">
        <h2>Evoluções Clínicas</h2>
        </div>
    <div class="card">
        <h2>Arquivo Digital</h2>
        </div>
</div>

<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .kpi-card { text-align: center; padding: 1.5rem; }
    .kpi-card h2 { font-size: 1.1em; color: #555; margin-bottom: 0.5rem; font-weight: 500; }
    .kpi-value { font-size: 2.2em; font-weight: 600; color: var(--cor-primaria); margin: 0; }
    .kpi-value-small { font-size: 1.2em; font-weight: 600; margin: 0; }
    .kpi-pago { color: #28a745; }
    .kpi-pendente { color: #dc3545; }
    .status { padding: 0.2rem 0.6rem; border-radius: 12px; color: white; font-size: 0.9em; display: inline-block; }
    .status-ativo { background-color: #28a745; }
    .status-inativo { background-color: #6c757d; }
    .status-alta { background-color: #17a2b8; }
    /* ... outros estilos ... */
</style>

<?php require_once '../components/footer.php'; ?>