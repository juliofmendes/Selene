<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

$paciente_id = filter_input(INPUT_GET, 'paciente_id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Verifica se o paciente pertence a este psicólogo
$stmt = $pdo->prepare("SELECT nome_completo FROM pacientes WHERE id = :pid AND psicologo_id = :psid");
$stmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Verifica se já existe uma anamnese para decidir se é uma criação ou edição
$anamneseStmt = $pdo->prepare("SELECT dados_anamnese FROM anamneses WHERE paciente_id = :pid");
$anamneseStmt->execute(['pid' => $paciente_id]);
$anamnese_existente = $anamneseStmt->fetch();
$dados = $anamnese_existente ? json_decode($anamnese_existente['dados_anamnese'], true) : [];

require_once '../components/header.php';
?>
<div class="container">
    <h1>Anamnese - <?php echo htmlspecialchars($paciente['nome_completo']); ?></h1>
    <a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $paciente_id; ?>">&larr; Voltar ao Dossiê</a>

    <div class="card">
        <form action="processa_anamnese.php" method="POST">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
            
            <h3>Queixa Principal</h3>
            <div class="form-group">
                <label>Qual o motivo da consulta?</label>
                <textarea name="queixa_principal" rows="4"><?php echo htmlspecialchars($dados['queixa_principal'] ?? ''); ?></textarea>
            </div>

            <h3>Histórico de Saúde</h3>
            <div class="form-group">
                <label>Doenças relevantes, tratamentos anteriores, medicação atual.</label>
                <textarea name="historico_saude" rows="4"><?php echo htmlspecialchars($dados['historico_saude'] ?? ''); ?></textarea>
            </div>
            
            <h3>Histórico Familiar</h3>
            <div class="form-group">
                <label>Relações familiares, eventos marcantes, histórico de saúde mental na família.</label>
                <textarea name="historico_familiar" rows="4"><?php echo htmlspecialchars($dados['historico_familiar'] ?? ''); ?></textarea>
            </div>

            <button type="submit">Salvar Anamnese</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>