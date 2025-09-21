<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// ... (toda a lógica de validação do ID do paciente e busca do paciente que já existe continua igual até aqui) ...
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :paciente_id AND psicologo_id = :psicologo_id");
$stmt->execute(['paciente_id' => $paciente_id, 'psicologo_id' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();

if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

// --- NOVA LÓGICA PARA PROCESSAR O FORMULÁRIO DE EVOLUÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_evolucao = date('Y-m-d H:i:s'); // Data e hora atuais

    if (!empty($titulo) && !empty($descricao)) {
        $insertStmt = $pdo->prepare(
            "INSERT INTO evolucoes (paciente_id, psicologo_id, data_evolucao, titulo, descricao) VALUES (:pid, :psid, :data, :titulo, :desc)"
        );
        $insertStmt->execute([
            'pid' => $paciente_id,
            'psid' => $_SESSION['usuario_id'],
            'data' => $data_evolucao,
            'titulo' => $titulo,
            'desc' => $descricao
        ]);
        // Redireciona para a mesma página para evitar reenvio do formulário
        header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id);
        exit;
    }
}

// --- NOVA LÓGICA PARA BUSCAR AS EVOLUÇÕES EXISTENTES ---
$evolucaoStmt = $pdo->prepare("SELECT * FROM evolucoes WHERE paciente_id = :pid ORDER BY data_evolucao DESC");
$evolucaoStmt->execute(['pid' => $paciente_id]);
$evolucoes = $evolucaoStmt->fetchAll();

require_once '../components/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Dossiê do Paciente</h1>
    <a href="<?php echo BASE_URL; ?>/pacientes/editar.php?id=<?php echo $paciente['id']; ?>" class="button">Editar Dossiê</a>
</div>
<a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">&larr; Voltar para a lista</a>

<div class="card-paciente-info">
    <h2><?php echo htmlspecialchars($paciente['nome_completo']); ?></h2>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($paciente['email'] ?: 'Não informado'); ?></p>
    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($paciente['telefone'] ?: 'Não informado'); ?></p>
</div>

<div class="card-evolucao">
    <h2>Nova Evolução</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="titulo">Título da Sessão/Anotação</label>
            <input type="text" name="titulo" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição Detalhada (Evolução)</label>
            <textarea name="descricao" rows="10" required></textarea>
        </div>
        <button type="submit">Adicionar Evolução</button>
    </form>
</div>

<div class="historico-evolucoes">
    <h2>Histórico de Evoluções</h2>
    <?php if (count($evolucoes) > 0): ?>
        <?php foreach ($evolucoes as $evolucao): ?>
            <div class="card-evolucao-item">
                <div style="display: flex; justify-content: space-between;">
                    <h3><?php echo htmlspecialchars($evolucao['titulo']); ?></h3>
                    <div>
                        <a href="#">Editar</a> | <a href="#">Excluir</a>
                    </div>
                <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($evolucao['data_evolucao'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($evolucao['descricao'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma evolução registrada para este paciente.</p>
    <?php endif; ?>
</div>

<?php require_once '../components/footer.php'; ?>
    </main>
</body>
</html>