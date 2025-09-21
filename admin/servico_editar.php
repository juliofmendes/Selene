<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../config.php';

$servico_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$servico_id) { header('Location: ' . BASE_URL . '/admin/servicos.php'); exit; }

// Lógica para salvar as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $valor = filter_var(str_replace(',', '.', $_POST['valor']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $status = $_POST['status'];
    if (!empty($nome) && is_numeric($valor)) {
        $stmt = $pdo->prepare("UPDATE servicos SET nome = :n, valor = :v, status = :s WHERE id = :id");
        $stmt->execute(['n' => $nome, 'v' => $valor, 's' => $status, 'id' => $servico_id]);
    }
    header('Location: ' . BASE_URL . '/admin/servicos.php?sucesso_edicao=1');
    exit;
}

// Lógica para buscar os dados do serviço
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
$stmt->execute(['id' => $servico_id]);
$servico = $stmt->fetch();
if (!$servico) { header('Location: ' . BASE_URL . '/admin/servicos.php'); exit; }

require_once '../components/header.php';
?>
<div class="container">
    <h1>Editar Serviço</h1>
    <a href="<?php echo BASE_URL; ?>/admin/servicos.php">&larr; Voltar para a lista</a>
    <div class="card">
        <form method="POST">
            <div class="form-group"><label>Nome do Serviço</label><input type="text" name="nome" value="<?php echo htmlspecialchars($servico['nome']); ?>" required></div>
            <div class="form-group"><label>Valor (R$)</label><input type="text" name="valor" value="<?php echo number_format($servico['valor'], 2, ',', '.'); ?>" required></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="ativo" <?php echo $servico['status'] == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                    <option value="inativo" <?php echo $servico['status'] == 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>