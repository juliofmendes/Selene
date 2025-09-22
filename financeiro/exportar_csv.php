<?php
require_once '../auth/verifica_sessao.php';
autorizar(['gestor', 'admin']);
require_once '../config.php';

// --- LÓGICA DE FILTRAGEM (idêntica à dos relatórios) ---
$filtros = [
    'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
    'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
    'status' => $_GET['status'] ?? 'todos',
    'psicologo_id' => $_GET['psicologo_id'] ?? 'todos'
];

$sql = "SELECT 
            f.data_emissao, f.status, pac.nome_completo AS paciente, 
            psi.nome AS psicologo, ser.nome AS servico, f.valor
        FROM faturas AS f
        JOIN agendamentos AS ag ON f.agendamento_id = ag.id
        JOIN pacientes AS pac ON ag.paciente_id = pac.id
        JOIN usuarios AS psi ON ag.psicologo_id = psi.id
        JOIN servicos AS ser ON f.servico_id = ser.id
        WHERE f.data_emissao BETWEEN :data_inicio AND :data_fim";

$params = ['data_inicio' => $filtros['data_inicio'], 'data_fim' => $filtros['data_fim']];

if ($filtros['status'] !== 'todos') {
    $sql .= " AND f.status = :status";
    $params['status'] = $filtros['status'];
}
if ($filtros['psicologo_id'] !== 'todos') {
    $sql .= " AND ag.psicologo_id = :psicologo_id";
    $params['psicologo_id'] = $filtros['psicologo_id'];
}
$sql .= " ORDER BY f.data_emissao ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- LÓGICA DE GERAÇÃO DO CSV ---
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Relatorio_Financeiro_Selene.csv');

$output = fopen('php://output', 'w');

// Cabeçalho do CSV
fputcsv($output, ['Data Emissao', 'Status', 'Paciente', 'Psicologo', 'Servico', 'Valor']);

// Dados
foreach ($faturas as $fatura) {
    fputcsv($output, $fatura);
}

fclose($output);
exit;
?>