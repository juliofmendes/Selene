<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';

// Busca os agendamentos dos próximos 7 dias para todos os psicólogos
$stmt = $pdo->prepare(
    "SELECT 
        ag.id, ag.data_agendamento, ag.status,
        pac.nome_completo AS paciente_nome,
        psi.nome AS psicologo_nome
     FROM agendamentos AS ag
     JOIN pacientes AS pac ON ag.paciente_id = pac.id
     JOIN usuarios AS psi ON ag.psicologo_id = psi.id
     WHERE ag.data_agendamento >= CURDATE()
     ORDER BY ag.data_agendamento ASC"
);
$stmt->execute();
$agendamentos = $stmt->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Agenda da Clínica</h1>
        <a href="<?php echo BASE_URL; ?>/agendamentos/novo.php" class="button">Novo Agendamento</a>
    </div>

    <?php if (isset($_GET['sucesso_agendamento'])): ?>
        <div class="alert-sucesso">Novo agendamento criado com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_edicao'])): ?>
        <div class="alert-sucesso">Agendamento atualizado com sucesso!</div>
    <?php endif; ?>

    <div class="card">
        <h2>Próximos Agendamentos</h2>
        <?php if (count($agendamentos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Paciente</th>
                        <th>Psicólogo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['paciente_nome']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['psicologo_nome']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($agendamento['status'])); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/agendamentos/editar.php?id=<?php echo $agendamento['id']; ?>">Ver/Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Não há agendamentos futuros na agenda.</p>
        <?php endif; ?>
    </div>
</div>
<style>
    .alert-sucesso { background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
</style>
<?php require_once '../components/footer.php'; ?>