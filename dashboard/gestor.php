<?php
require_once '../auth/verifica_sessao.php';
autorizar(['gestor', 'admin']); // Apenas gestores e admins
require_once '../config.php';

// --- CÁLCULO DOS KPIs ---

// 1. Receita Total (Soma de todas as faturas pagas)
$receita_total = $pdo->query("SELECT SUM(valor) FROM faturas WHERE status = 'paga'")->fetchColumn();

// 2. Faturação Pendente (Soma de todas as faturas pendentes)
$pendente_total = $pdo->query("SELECT SUM(valor) FROM faturas WHERE status = 'pendente'")->fetchColumn();

// 3. Total de Sessões Realizadas
$sessoes_realizadas = $pdo->query("SELECT COUNT(id) FROM agendamentos WHERE status = 'realizado'")->fetchColumn();

// 4. Média por Sessão
$media_por_sessao = ($sessoes_realizadas > 0) ? $receita_total / $sessoes_realizadas : 0;

// 5. Novos Pacientes (nos últimos 30 dias)
$novos_pacientes = $pdo->query("SELECT COUNT(id) FROM pacientes WHERE data_criacao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

// 6. Faturação por Psicólogo (Top 5)
$receita_por_psicologo = $pdo->query(
    "SELECT u.nome, SUM(f.valor) as total_faturado
     FROM faturas f
     JOIN agendamentos ag ON f.agendamento_id = ag.id
     JOIN usuarios u ON ag.psicologo_id = u.id
     WHERE f.status = 'paga'
     GROUP BY u.id
     ORDER BY total_faturado DESC
     LIMIT 5"
)->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <h1>Dashboard do Gestor</h1>
    <p>Uma visão geral da performance e saúde financeira da clínica.</p>

    <div class="kpi-grid">
        <div class="card kpi-card">
            <h2>Receita Total</h2>
            <p class="kpi-value">R$ <?php echo number_format($receita_total ?? 0, 2, ',', '.'); ?></p>
        </div>
        <div class="card kpi-card">
            <h2>Pendente</h2>
            <p class="kpi-value">R$ <?php echo number_format($pendente_total ?? 0, 2, ',', '.'); ?></p>
        </div>
        <div class="card kpi-card">
            <h2>Sessões Realizadas</h2>
            <p class="kpi-value"><?php echo $sessoes_realizadas ?? 0; ?></p>
        </div>
        <div class="card kpi-card">
            <h2>Ticket Médio</h2>
            <p class="kpi-value">R$ <?php echo number_format($media_por_sessao, 2, ',', '.'); ?></p>
        </div>
    </div>

    <div class="card">
        <h2>Performance por Psicólogo (Top 5 - Receita)</h2>
        <table>
            <thead>
                <tr>
                    <th>Psicólogo</th>
                    <th>Total Faturado (Pago)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receita_por_psicologo as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td>R$ <?php echo number_format($item['total_faturado'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .kpi-card { text-align: center; }
    .kpi-card h2 { font-size: 1.2em; color: #555; margin-bottom: 0.5rem; }
    .kpi-value { font-size: 2.5em; font-weight: 600; color: var(--cor-primaria); margin: 0; }
</style>

<?php require_once '../components/footer.php'; ?>