<?php
require_once '../auth/verifica_sessao.php';
autorizar(['gestor', 'admin']);
require_once '../config.php';

// --- LÓGICA DE FILTRAGEM ---
$filtros = [
    'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
    'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
    'status' => $_GET['status'] ?? 'todos'
];

$sql = "SELECT 
            f.id, f.valor, f.status, f.data_emissao, f.data_pagamento,
            pac.nome_completo AS paciente_nome,
            psi.nome AS psicologo_nome
        FROM faturas AS f
        JOIN agendamentos AS ag ON f.agendamento_id = ag.id
        JOIN pacientes AS pac ON ag.paciente_id = pac.id
        JOIN usuarios AS psi ON ag.psicologo_id = psi.id
        WHERE f.data_emissao BETWEEN :data_inicio AND :data_fim";

$params = [
    'data_inicio' => $filtros['data_inicio'],
    'data_fim' => $filtros['data_fim']
];

if ($filtros['status'] !== 'todos') {
    $sql .= " AND f.status = :status";
    $params['status'] = $filtros['status'];
}

$sql .= " ORDER BY f.data_emissao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faturas = $stmt->fetchAll();

// Calcula os totais com base nos resultados filtrados
$total_faturado = array_sum(array_column($faturas, 'valor'));
$total_pago = array_sum(array_column(array_filter($faturas, fn($f) => $f['status'] == 'paga'), 'valor'));
$total_pendente = $total_faturado - $total_pago;

require_once '../components/header.php';
?>
<div class="container">
    <h1>Relatórios Financeiros</h1>
    
    <div class="card">
        <h2>Filtrar Faturas</h2>
        <form method="GET" action="">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="data_inicio">Data de Início</label>
                    <input type="date" name="data_inicio" value="<?php echo htmlspecialchars($filtros['data_inicio']); ?>">
                </div>
                <div class="form-group">
                    <label for="data_fim">Data de Fim</label>
                    <input type="date" name="data_fim" value="<?php echo htmlspecialchars($filtros['data_fim']); ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status">
                        <option value="todos" <?php echo $filtros['status'] == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="paga" <?php echo $filtros['status'] == 'paga' ? 'selected' : ''; ?>>Paga</option>
                        <option value="pendente" <?php echo $filtros['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" style="width: 100%; margin-top: 25px;">Aplicar Filtros</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="kpi-card"><h2>Total Faturado</h2><p class="kpi-value">R$ <?php echo number_format($total_faturado, 2, ',', '.'); ?></p></div>
        <div class="kpi-card"><h2>Total Recebido</h2><p class="kpi-value">R$ <?php echo number_format($total_pago, 2, ',', '.'); ?></p></div>
        <div class="kpi-card"><h2>Total Pendente</h2><p class="kpi-value">R$ <?php echo number_format($total_pendente, 2, ',', '.'); ?></p></div>
    </div>

    <div class="card">
        <h2>Resultados</h2>
        <table>
            <thead>
                <tr>
                    <th>Emissão</th>
                    <th>Status</th>
                    <th>Paciente</th>
                    <th>Psicólogo</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($faturas) > 0): ?>
                    <?php foreach ($faturas as $fatura): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($fatura['data_emissao'])); ?></td>
                            <td>
                                <span class="status-financeiro status-<?php echo htmlspecialchars($fatura['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($fatura['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($fatura['paciente_nome']); ?></td>
                            <td><?php echo htmlspecialchars($fatura['psicologo_nome']); ?></td>
                            <td>R$ <?php echo number_format($fatura['valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhuma fatura encontrada para os filtros selecionados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<style>
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: flex-end; }
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .kpi-card { text-align: center; }
    .kpi-card h2 { font-size: 1.2em; color: #555; margin-bottom: 0.5rem; }
    .kpi-value { font-size: 2.5em; font-weight: 600; color: var(--cor-primaria); margin: 0; }
    .status-financeiro { padding: 0.2rem 0.5rem; border-radius: 4px; color: white; font-size: 0.8em; }
    .status-pendente { background-color: #ffc107; color: #333; }
    .status-paga { background-color: #28a745; }
</style>
<?php require_once '../components/footer.php'; ?>