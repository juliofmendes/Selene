<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';

$agendamento_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$agendamento_id) {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

// Busca o agendamento e os nomes associados
$stmt = $pdo->prepare(
    "SELECT ag.*, pac.nome_completo as paciente_nome, psi.nome as psicologo_nome
     FROM agendamentos ag
     JOIN pacientes pac ON ag.paciente_id = pac.id
     JOIN usuarios psi ON ag.psicologo_id = psi.id
     WHERE ag.id = :id"
);
$stmt->execute(['id' => $agendamento_id]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    header('Location: ' . BASE_URL . '/dashboard/secretaria.php');
    exit;
}

require_once '../components/header.php';
?>
<div class="container">
    <h1>Editar Agendamento</h1>
    <a href="<?php echo BASE_URL; ?>/dashboard/secretaria.php">&larr; Voltar para a agenda</a>

    <div class="card">
        <p><strong>Paciente:</strong> <?php echo htmlspecialchars($agendamento['paciente_nome']); ?></p>
        <p><strong>Psicólogo:</strong> <?php echo htmlspecialchars($agendamento['psicologo_nome']); ?></p>
        
        <form action="processa_editar.php" method="POST">
            <input type="hidden" name="agendamento_id" value="<?php echo $agendamento['id']; ?>">
            
            <div class="form-group">
                <label for="data_agendamento">Data e Hora</label>
                <input type="datetime-local" id="data_agendamento" name="data_agendamento" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($agendamento['data_agendamento'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="agendado" <?php echo $agendamento['status'] == 'agendado' ? 'selected' : ''; ?>>Agendado</option>
                    <option value="realizado" <?php echo $agendamento['status'] == 'realizado' ? 'selected' : ''; ?>>Realizado</option>
                    <option value="cancelado" <?php echo $agendamento['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    <option value="remarcado" <?php echo $agendamento['status'] == 'remarcado' ? 'selected' : ''; ?>>Remarcado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notas">Notas (opcional)</label>
                <textarea id="notas" name="notas" rows="4"><?php echo htmlspecialchars($agendamento['notas']); ?></textarea>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>