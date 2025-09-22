<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

$paciente_id = filter_input(INPUT_GET, 'paciente_id', FILTER_VALIDATE_INT);
$modelo_id = filter_input(INPUT_GET, 'modelo_id', FILTER_VALIDATE_INT);

if (!$paciente_id || !$modelo_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Busca os dados do modelo
$stmtModelo = $pdo->prepare("SELECT titulo, estrutura_json FROM anamnese_modelos WHERE id = :id");
$stmtModelo->execute(['id' => $modelo_id]);
$modelo = $stmtModelo->fetch();
if (!$modelo) { die("Modelo de anamnese não encontrado."); }

// VALIDAÇÃO CRÍTICA: Verificamos se o JSON é válido
$estrutura = json_decode($modelo['estrutura_json'], true);
if ($estrutura === null || !is_array($estrutura)) {
    die("Erro Crítico: A estrutura do modelo de anamnese (ID: $modelo_id) é inválida. Por favor, corrija-a no painel de administração.");
}

// Busca as respostas existentes, se houver
$stmtRespostas = $pdo->prepare("SELECT respostas_json FROM anamnese_respostas WHERE paciente_id = :pid AND modelo_id = :mid");
$stmtRespostas->execute(['pid' => $paciente_id, 'mid' => $modelo_id]);
$respostas_existentes = $stmtRespostas->fetchColumn();
$dados = $respostas_existentes ? json_decode($respostas_existentes, true) : [];

require_once '../components/header.php';
?>
<div class="container">
    <h1><?php echo htmlspecialchars($modelo['titulo']); ?></h1>
    <a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $paciente_id; ?>">&larr; Voltar ao Dossiê</a>

    <div class="card">
        <form action="processa_anamnese.php" method="POST">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
            <input type="hidden" name="modelo_id" value="<?php echo $modelo_id; ?>">
            
            <?php foreach ($estrutura as $campo): ?>
                <div class="form-group">
                    <label for="<?php echo htmlspecialchars($campo['name']); ?>"><?php echo htmlspecialchars($campo['label']); ?></label>
                    <?php if ($campo['tipo'] === 'textarea'): ?>
                        <textarea name="<?php echo htmlspecialchars($campo['name']); ?>" rows="5"><?php echo htmlspecialchars($dados[$campo['name']] ?? ''); ?></textarea>
                    <?php elseif ($campo['tipo'] === 'select' && isset($campo['options']) && is_array($campo['options'])): ?>
                        <select name="<?php echo htmlspecialchars($campo['name']); ?>">
                            <?php foreach ($campo['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo (isset($dados[$campo['name']]) && $dados[$campo['name']] == $option) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: // 'text', 'date', etc. ?>
                        <input type="<?php echo htmlspecialchars($campo['tipo']); ?>" name="<?php echo htmlspecialchars($campo['name']); ?>" value="<?php echo htmlspecialchars($dados[$campo['name']] ?? ''); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit">Salvar Anamnese</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>