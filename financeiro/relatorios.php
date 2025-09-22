<?php
require_once '../auth/verifica_sessao.php';
autorizar(['gestor', 'admin']);
require_once '../config.php';

// --- LÓGICA DE FILTRAGEM AVANÇADA ---
$filtros = [
    'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
    'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
    'status' => $_GET['status'] ?? 'todos',
    'psicologo_id' => $_GET['psicologo_id'] ?? 'todos'
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
if ($filtros['psicologo_id'] !== 'todos') {
    $sql .= " AND ag.psicologo_id = :psicologo_id";
    $params['psicologo_id'] = $filtros['psicologo_id'];
}
$sql .= " ORDER BY f.data_emissao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faturas = $stmt->fetchAll();

// Busca a lista de psicólogos para o filtro
$psicologos = $pdo->query("SELECT id, nome FROM usuarios WHERE nivel_acesso LIKE 'psicologo%' ORDER BY nome ASC")->fetchAll();

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
                <div class="form-group"><label>Data de Início</label><input type="date" name="data_inicio" value="<?php echo htmlspecialchars($filtros['data_inicio']); ?>"></div>
                <div class="form-group"><label>Data de Fim</label><input type="date" name="data_fim" value="<?php echo htmlspecialchars($filtros['data_fim']); ?>"></div>
                <div class="form-group">
                    <label>Psicólogo</label>
                    <select name="psicologo_id">
                        <option value="todos">Todos os Psicólogos</option>
                        <?php foreach ($psicologos as $psicologo): ?>
                            <option value="<?php echo $psicologo['id']; ?>" <?php echo $filtros['psicologo_id'] == $psicologo['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($psicologo['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="todos" <?php echo $filtros['status'] == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="paga" <?php echo $filtros['status'] == 'paga' ? 'selected' : ''; ?>>Paga</option>
                        <option value="pendente" <?php echo $filtros['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                <button type="submit">Aplicar Filtros</button>
                <a href="exportar_csv.php?<?php echo http_build_query($filtros); ?>" class="button">Exportar para CSV</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Resultados Filtrados</h2>
        <table>
            </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>