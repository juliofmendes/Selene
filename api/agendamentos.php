<?php
header('Content-Type: application/json');
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

$start = $_GET['start'];
$end = $_GET['end'];
$nivel_acesso = $_SESSION['nivel_acesso'];
$usuario_id = $_SESSION['usuario_id'];

try {
    $sql = "SELECT 
                ag.id, 
                ag.data_agendamento as start,
                pac.nome_completo as title,
                psi.cor as color,
                psi.nome as extended_prop_psicologo
            FROM agendamentos AS ag
            JOIN pacientes AS pac ON ag.paciente_id = pac.id
            JOIN usuarios AS psi ON ag.psicologo_id = psi.id
            WHERE ag.data_agendamento BETWEEN :start AND :end";
    
    $params = ['start' => $start, 'end' => $end];

    // Filtro de segurança: Psicólogos só veem os seus próprios agendamentos.
    if ($nivel_acesso === 'psicologo' || $nivel_acesso === 'psicologo_autonomo') {
        $sql .= " AND ag.psicologo_id = :psicologo_id";
        $params['psicologo_id'] = $usuario_id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($agendamentos as &$agendamento) {
        $agendamento['url'] = BASE_URL . '/agendamentos/editar.php?id=' . $agendamento['id'];
    }

    echo json_encode($agendamentos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar agendamentos.']);
}
?>