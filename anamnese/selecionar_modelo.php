<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';

$paciente_id = filter_input(INPUT_GET, 'paciente_id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Busca o nome do paciente para o título
$paciente_nome = $pdo->prepare("SELECT nome_completo FROM pacientes WHERE id = :id");
$paciente_nome->execute(['id' => $paciente_id]);
$paciente_nome = $paciente_nome->fetchColumn();

// Busca todos os modelos de anamnese ativos
$modelos = $pdo->query("SELECT id, titulo, descricao FROM anamnese_modelos WHERE status = 'ativo' ORDER BY titulo ASC")->fetchAll();

require_once '../components/header.php';
?>
<div class="container">
    <h1>Iniciar Anamnese para <?php echo htmlspecialchars($paciente_nome); ?></h1>
    <p>Selecione o modelo de formulário que deseja utilizar.</p>
    <a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $paciente_id; ?>">&larr; Voltar ao Dossiê</a>

    <div class="modelo-selecao-grid">
        <?php foreach ($modelos as $modelo): ?>
            <a href="preencher.php?paciente_id=<?php echo $paciente_id; ?>&modelo_id=<?php echo $modelo['id']; ?>" class="card-link">
                <div class="card">
                    <h2><?php echo htmlspecialchars($modelo['titulo']); ?></h2>
                    <p><?php echo htmlspecialchars($modelo['descricao'] ?: 'Nenhuma descrição adicional.'); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (count($modelos) === 0): ?>
            <p>Nenhum modelo de anamnese foi criado ainda. Peça a um administrador para criar um.</p>
        <?php endif; ?>
    </div>
</div>
<style>
    .modelo-selecao-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
</style>
<?php require_once '../components/footer.php'; ?>