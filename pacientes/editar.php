<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// Lógica para buscar os dados atuais do paciente (similar ao ver.php)
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :id AND psicologo_id = :pid");
$stmt->execute(['id' => $paciente_id, 'pid' => $_SESSION['usuario_id']]);
$paciente = $stmt->fetch();
if (!$paciente) { header('Location: ' . BASE_URL . '/dashboard/psicologo.php'); exit; }

require_once '../components/header.php';
?>

<h1>Editar Dossiê do Paciente</h1>
<form action="processa_editar.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
    
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($paciente['nome_completo']); ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($paciente['email']); ?>">
    </div>
    <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($paciente['telefone']); ?>">
    </div>
    <div class="form-group">
        <label for="data_nascimento">Data de Nascimento</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($paciente['data_nascimento']); ?>">
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="ativo" <?php echo $paciente['status'] == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
            <option value="inativo" <?php echo $paciente['status'] == 'inativo' ? 'selected' : ''; ?>>Inativo</option>
            <option value="alta" <?php echo $paciente['status'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
        </select>
    </div>

    <button type="submit">Salvar Alterações</button>
</form>

<?php require_once '../components/footer.php'; ?>