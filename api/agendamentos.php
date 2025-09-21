<?php
// Define o tipo de conteúdo como JSON, que é o que o FullCalendar espera.
header('Content-Type: application/json');

require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// O FullCalendar envia as datas de início e fim da visualização atual.
$start = $_GET['start'];
$end = $_GET['end'];

try {
    // A consulta busca agendamentos dentro do intervalo de datas fornecido.
    $stmt = $pdo->prepare(
        "SELECT 
            ag.id, 
            ag.data_agendamento as start,
            pac.nome_completo as title
         FROM agendamentos AS ag
         JOIN pacientes AS pac ON ag.paciente_id = pac.id
         WHERE ag.data_agendamento BETWEEN :start AND :end"
    );
    $stmt->execute(['start' => $start, 'end' => $end]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Adiciona uma URL a cada evento para torná-lo clicável.
    foreach ($agendamentos as &$agendamento) {
        $agendamento['url'] = BASE_URL . '/agendamentos/editar.php?id=' . $agendamento['id'];
    }

    // Devolve os dados em formato JSON.
    echo json_encode($agendamentos);

} catch (PDOException $e) {
    // Em caso de erro, devolve um JSON de erro.
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar agendamentos.']);
}
?>