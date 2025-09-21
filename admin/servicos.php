<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../config.php';

$servico_para_edicao = null;

// Lógica para processar o formulário de novo serviço/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ação de Inativar
    if (isset($_POST['inativar_servico_id'])) {
        $inativar_id = filter_input(INPUT_POST, 'inativar_servico_id', FILTER_VALIDATE_INT);
        if ($inativar_id) {
            // Usamos 'status' em vez de remover, para manter o histórico
            $stmt = $pdo->prepare("UPDATE servicos SET status = 'inativo' WHERE id = :id");
            $stmt->execute(['id' => $inativar_id]);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?sucesso_inativar=1');
            exit;
        }
    } else { // Ação de Salvar (Novo ou Edição)
        $servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome']);
        // Correção para aceitar valores monetários com vírgula e convertê-los para o formato do banco
        $valor_br = $_POST['valor'];
        $valor = str_replace(['.', ','], ['', '.'], $valor_br);

        if (!empty($nome) && is_numeric($valor)) {
            if ($servico_id) { // Edição
                $stmt = $pdo->prepare("UPDATE servicos SET nome = :nome, valor = :valor WHERE id = :id");
                $stmt->execute(['nome' => $nome, 'valor' => $valor, 'id' => $servico_id]);
            } else { // Novo
                $stmt = $pdo->prepare("INSERT INTO servicos (nome, valor) VALUES (:nome, :valor)");
                $stmt->execute(['nome' => $nome, 'valor' => $valor]);
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?sucesso_salvar=1');
        exit;
    }
}

// Lógica para carregar um serviço para edição
if (isset($_GET['editar_id'])) {
    $editar_id = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);
    if ($editar_id) {
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
        $stmt->execute(['id' => $editar_id]);
        $servico_para_edicao = $stmt->fetch();
    }
}

// Busca todos os serviços ATIVOS para a lista principal
$servicos = $pdo->query("SELECT * FROM servicos WHERE status = 'ativo' ORDER BY nome ASC")->fetchAll();
require_once '../components/header.php';
?>

<div class="container">
    <h1>Gestão de Serviços da Clínica</h1>
    <p>Defina os tipos de consulta e os seus respetivos valores.</p>

    <?php if (isset($_GET['sucesso_salvar'])): ?>
        <div class="alert-sucesso" style="margin-bottom: 1rem;">Serviço salvo com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_inativar'])): ?>
        <div class="alert-sucesso" style="margin-bottom: 1rem;">Serviço inativado com sucesso!</div>
    <?php endif; ?>

    <div class="card">
        <h2><?php echo $servico_para_edicao ? 'Editar Serviço' : 'Adicionar Novo Serviço'; ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="servico_id" value="<?php echo $servico_para_edicao['id'] ?? ''; ?>">
            <div class="form-group">
                <label for="nome">Nome do Serviço (ex: Consulta Individual)</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($servico_para_edicao['nome'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="valor">Valor (ex: 150,00)</label>
                <input type="text" name="valor" value="<?php echo $servico_para_edicao ? number_format($servico_para_edicao['valor'], 2, ',', '.') : ''; ?>" required>
            </div>
            <button type="submit">Salvar Serviço</button>
            <?php if ($servico_para_edicao): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-left: 1rem;">Cancelar Edição</a>
            <?php endif; ?>
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
                        <td>
                            <a href="?editar_id=<?php echo $servico['id']; ?>">Editar</a> | 
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Tem a certeza que deseja inativar este serviço?');">
                                <input type="hidden" name="inativar_servico_id" value="<?php echo $servico['id']; ?>">
                                <button type="submit" style="background:none; border:none; color:var(--cor-primaria); cursor:pointer; padding:0; text-decoration:underline;">Inativar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (count($servicos) === 0): ?>
                    <tr>
                        <td colspan="3">Nenhum serviço ativo encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>