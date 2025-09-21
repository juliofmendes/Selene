<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']);
require_once '../config.php';

$usuario_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$usuario_id) { header('Location: ' . BASE_URL . '/admin/usuarios.php'); exit; }

// Lógica de POST para salvar as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $nivel_acesso = $_POST['nivel_acesso'];
    $senha = $_POST['senha']; // Senha opcional

    if (!empty($nome) && $email && !empty($nivel_acesso)) {
        if (!empty($senha)) { // Se uma nova senha for fornecida, atualiza
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :n, email = :e, nivel_acesso = :na, senha = :s WHERE id = :id");
            $stmt->execute(['n' => $nome, 'e' => $email, 'na' => $nivel_acesso, 's' => $senha_hash, 'id' => $usuario_id]);
        } else { // Caso contrário, mantém a senha atual
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :n, email = :e, nivel_acesso = :na WHERE id = :id");
            $stmt->execute(['n' => $nome, 'e' => $email, 'na' => $nivel_acesso, 'id' => $usuario_id]);
        }
    }
    header('Location: ' . BASE_URL . '/admin/usuarios.php?sucesso_edicao=1');
    exit;
}

// Lógica de GET para buscar os dados do utilizador
$stmt = $pdo->prepare("SELECT nome, email, nivel_acesso FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $usuario_id]);
$usuario = $stmt->fetch();
if (!$usuario) { header('Location: ' . BASE_URL . '/admin/usuarios.php'); exit; }

require_once '../components/header.php';
?>
<div class="container">
    <h1>Editar Utilizador</h1>
    <a href="<?php echo BASE_URL; ?>/admin/usuarios.php">&larr; Voltar para a lista</a>
    <div class="card">
        <form method="POST">
            <div class="form-group"><label>Nome</label><input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required></div>
            <div class="form-group">
                <label>Nível de Acesso</label>
                <select name="nivel_acesso" required>
                    <option value="psicologo" <?php echo $usuario['nivel_acesso'] == 'psicologo' ? 'selected' : ''; ?>>Psicólogo (Clínica)</option>
                    <option value="psicologo_autonomo" <?php echo $usuario['nivel_acesso'] == 'psicologo_autonomo' ? 'selected' : ''; ?>>Psicólogo (Autónomo)</option>
                    <option value="secretaria" <?php echo $usuario['nivel_acesso'] == 'secretaria' ? 'selected' : ''; ?>>Secretaria</option>
                    <option value="gestor" <?php echo $usuario['nivel_acesso'] == 'gestor' ? 'selected' : ''; ?>>Gestor</option>
                    <option value="admin" <?php echo $usuario['nivel_acesso'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nova Senha (deixe em branco para não alterar)</label>
                <input type="password" name="senha">
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>