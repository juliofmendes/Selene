<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Busca todos os pacientes e psicólogos para preencher os menus dropdown
$pacientes = $pdo->query("SELECT id, nome_completo FROM pacientes ORDER BY nome_completo ASC")->fetchAll();
$psicologos = $pdo->query("SELECT id, nome FROM usuarios WHERE nivel_acesso = 'psicologo' ORDER BY nome ASC")->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <h1>Novo Agendamento</h1>
    <a href="<?php echo BASE_URL; ?>/dashboard/secretaria.php">&larr; Voltar para a agenda</a>
    
    <div class="card">
        <form action="processa_novo.php" method="POST">
            <div class="form-group">
                <label for="paciente_id">Paciente</label>
                <select id="paciente_id" name="paciente_id" required>
                    <option value="">Selecione um paciente...</option>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo $paciente['id']; ?>"><?php echo htmlspecialchars($paciente['nome_completo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="psicologo_id">Psicólogo</label>
                <select id="psicologo_id" name="psicologo_id" required>
                    <option value="">Selecione um psicólogo...</option>
                    <?php foreach ($psicologos as $psicologo): ?>
                        <option value="<?php echo $psicologo['id']; ?>"><?php echo htmlspecialchars($psicologo['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data_agendamento">Data e Hora</label>
                <input type="datetime-local" id="data_agendamento" name="data_agendamento" required>
            </div>
            <div class="form-group">
                <label for="notas">Notas (opcional)</label>
                <textarea id="notas" name="notas" rows="4"></textarea>
            </div>
            <button type="submit">Salvar Agendamento</button>
        </form>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>