<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Validação do ID do paciente
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Busca segura dos dados do paciente
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :paciente_id AND psicologo_id = :psicologo_id");
$stmt->execute(['paciente_id' => $paciente_id, 'psicologo_id' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// Processamento do formulário de nova evolução
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_evolucao'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    if (!empty($titulo) && !empty($descricao)) {
        $insertStmt = $pdo->prepare("INSERT INTO evolucoes (paciente_id, psicologo_id, data_evolucao, titulo, descricao) VALUES (:pid, :psid, NOW(), :titulo, :desc)");
        $insertStmt->execute(['pid' => $paciente_id, 'psid' => $_SESSION['usuario_id'], 'titulo' => $titulo, 'desc' => $descricao]);
        header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_nova_evolucao=1');
        exit;
    }
}

// Busca do histórico de evoluções
$evolucaoStmt = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucaoStmt->execute(['pid' => $paciente_id]);
$evolucoes = $evolucaoStmt->fetchAll();

require_once '../components/header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
        <div>
            <h1>Dossiê do Paciente</h1>
            <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">&larr; Voltar para a lista de pacientes</a>
        </div>
        <a href="<?php echo BASE_URL; ?>/pacientes/editar.php?id=<?php echo $paciente['id']; ?>" class="button">Editar Dossiê</a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert-sucesso">Informações do paciente atualizadas com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_nova_evolucao'])): ?>
        <div class="alert-sucesso">Nova evolução adicionada com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_edicao_evolucao'])): ?>
        <div class="alert-sucesso">Evolução atualizada com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_exclusao'])): ?>
        <div class="alert-sucesso">Evolução excluída com sucesso!</div>
    <?php endif; ?>

    <div class="card">
        <h2><?php echo htmlspecialchars($paciente['nome_completo']); ?></h2>
        <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($paciente['status'])); ?></p>
    </div>

    <div class="card">
        <h2>Nova Evolução</h2>
        <form method="POST" action="">
            <input type="hidden" name="nova_evolucao" value="1">
            <div class="form-group"><label for="titulo">Título</label><input type="text" name="titulo" required></div>
            <div class="form-group"><label for="descricao">Descrição</label><textarea name="descricao" rows="8" required></textarea></div>
            <button type="submit">Adicionar Evolução</button>
        </form>
    </div>

    <div class="card">
        <h2>Histórico de Evoluções</h2>
        <?php if (count($evolucoes) > 0): ?>
            <?php foreach ($evolucoes as $evolucao): ?>
                <div class="card-evolucao-item">
                    <div style="display: flex; justify-content: space-between;">
                        <h3><?php echo htmlspecialchars($evolucao['titulo']); ?></h3>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <a href="<?php echo BASE_URL; ?>/evolucoes/editar.php?id=<?php echo $evolucao['id']; ?>">Editar</a>
                            <form action="<?php echo BASE_URL; ?>/evolucoes/excluir.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja excluir esta evolução? Esta ação é irreversível.');">
                                <input type="hidden" name="evolucao_id" value="<?php echo $evolucao['id']; ?>">
                                <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                                <button type="submit" class="button-link-delete">Excluir</button>
                            </form>
                        </div>
                    </div>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($evolucao['data_evolucao'])); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($evolucao['descricao'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma evolução registrada para este paciente.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .alert-sucesso { background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
    .card-evolucao-item { border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; }
    .card-evolucao-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .button-link-delete { background: none; border: none; color: #dc3545; text-decoration: underline; cursor: pointer; padding: 0; font-size: 1em; }
</style>

<?php
// ...
    // Mensagens de Sucesso
    if (isset($_GET['sucesso'])):
        // ...
    elseif (isset($_GET['sucesso_exclusao'])):
        echo '<div class="alert-sucesso">Evolução excluída com sucesso!</div>';
    // NOVA SEÇÃO DE ERROS
    elseif (isset($_GET['erro_exclusao'])):
        echo '<div class="alert-erro">Não foi possível excluir a evolução. Verifique se ela existe e tente novamente.</div>';
    endif; 
?>

<style>
    /* Adicione este estilo para a mensagem de erro, junto do .alert-sucesso */
    .alert-erro { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
</style>

<?php require_once '../components/footer.php'; ?>