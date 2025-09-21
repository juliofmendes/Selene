<?php
require_once '../auth/verifica_sessao.php';
// CORREÇÃO: Adicionado 'psicologo_autonomo' à lista de níveis autorizados.
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

$psicologo_id = $_SESSION['usuario_id'];

// O resto do ficheiro permanece exatamente o mesmo, pois a lógica de busca
// de pacientes e agendamentos já está correta e baseada no ID do utilizador logado.

// Busca os pacientes deste psicólogo
$pacientesStmt = $pdo->prepare("SELECT id, nome_completo, status FROM pacientes WHERE psicologo_id = :pid ORDER BY nome_completo ASC");
$pacientesStmt->execute(['pid' => $psicologo_id]);
$pacientes = $pacientesStmt->fetchAll();

// Busca os próximos 5 agendamentos deste psicólogo
$agendamentosStmt = $pdo->prepare(
    "SELECT ag.id, ag.data_agendamento, pac.nome_completo AS paciente_nome
     FROM agendamentos AS ag
     JOIN pacientes AS pac ON ag.paciente_id = pac.id
     WHERE ag.psicologo_id = :pid AND ag.data_agendamento >= NOW() AND ag.status = 'agendado'
     ORDER BY ag.data_agendamento ASC
     LIMIT 5"
);
$agendamentosStmt->execute(['pid' => $psicologo_id]);
$agendamentos = $agendamentosStmt->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <h1>Dashboard do Psicólogo</h1>
    <p>Bem-vindo(a) de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>

    <div class="card">
        <h2>Próximos Agendamentos</h2>
        <?php if (count($agendamentos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Paciente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top: 1rem; text-align: right;">
                <a href="<?php echo BASE_URL; ?>/agendamentos/minha_agenda.php">Ver agenda completa &rarr;</a>
            </div>
        <?php else: ?>
            <p>Você não tem agendamentos futuros.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Seus Pacientes</h2>
        <a href="<?php echo BASE_URL; ?>/pacientes/adicionar.php" class="button">+ Adicionar Novo Paciente</a>
        <?php if (count($pacientes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paciente['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($paciente['status'])); ?></td>
                            <td><a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $paciente['id']; ?>">Ver Dossiê</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Você ainda não tem nenhum paciente cadastrado.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>