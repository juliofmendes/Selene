<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']); // Apenas admins e gestores podem definir serviços
require_once '../config.php';

// Lógica para processar o formulário de novo serviço/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome']);
    $valor = filter_var($_POST['valor'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (!empty($nome) && !empty($valor)) {
        if ($servico_id) { // Edição
            $stmt = $pdo->prepare("UPDATE servicos SET nome = :nome, valor = :valor WHERE id = :id");
            $stmt->execute(['nome' => $nome, 'valor' => $valor, 'id' => $servico_id]);
        } else { // Novo
            $stmt = $pdo->prepare("INSERT INTO servicos (nome, valor) VALUES (:nome, :valor)");
            $stmt->execute(['nome' => $nome, 'valor' => $valor]);
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']); // Recarrega a página
    exit;
}

// Busca todos os serviços
$servicos = $pdo->query("SELECT * FROM servicos ORDER BY nome ASC")->fetchAll();
require_once '../components/header.php';
?>

<div class="container">
    <h1>Gestão de Serviços da Clínica</h1>
    <p>Defina os tipos de consulta e os seus respetivos valores.</p>

    <div class="card">
        <h2>Adicionar Novo Serviço</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome do Serviço (ex: Consulta Individual)</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label for="valor">Valor (use ponto para decimais, ex: 150.00)</label>
                <input type="text" name="valor" pattern="[0-9.]+" required>
            </div>
            <button type="submit">Salvar Serviço</button>
        </form>
    </div>

    <div class="card">
        <h2>Serviços Atuais</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $servico): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                        <td>R$ <?php echo number_format($servico['valor'], 2, ',', '.'); ?></td>
                        <td><a href="#">Editar</a> | <a href="#">Inativar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>