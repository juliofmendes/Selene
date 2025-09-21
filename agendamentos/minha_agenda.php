<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

// Busca TODOS os agendamentos deste psicólogo
$stmt = $pdo->prepare(
    "SELECT 
        ag.id, ag.data_agendamento, ag.status,
        pac.nome_completo AS paciente_nome
     FROM agendamentos AS ag
     JOIN pacientes AS pac ON ag.paciente_id = pac.id
     WHERE ag.psicologo_id = :psicologo_id
     ORDER BY ag.data_agendamento DESC" // Ordem decrescente para ver os mais recentes primeiro
);
$stmt->execute(['psicologo_id' => $_SESSION['usuario_id']]);
$agendamentos = $stmt->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <h1>Minha Agenda Completa</h1>
    <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">&larr; Voltar para o dashboard</a>

    <div class="card">
        <?php if (count($agendamentos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Paciente</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($agendamento['status'])); ?></td>
                            <td><a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $agendamento['paciente_id']; ?>">Ir para Dossiê</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum agendamento encontrado no seu histórico.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>