<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

$evolucao_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$evolucao_id) {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

// Busca a evolução, garantindo que ela pertence ao psicólogo logado
$stmt = $pdo->prepare("SELECT * FROM evolucoes WHERE id = :id AND psicologo_id = :pid");
$stmt->execute(['id' => $evolucao_id, 'pid' => $_SESSION['usuario_id']]);
$evolucao = $stmt->fetch();

if (!$evolucao) {
    // Se não encontrar, redireciona por segurança
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

require_once '../components/header.php';
?>
<div class="container">
    <h1>Editar Evolução</h1>
    <a href="<?php echo BASE_URL; ?>/pacientes/ver.php?id=<?php echo $evolucao['paciente_id']; ?>">&larr; Voltar para o dossiê</a>
    
    <div class="card">
        <form action="processa_editar.php" method="POST">
            <input type="hidden" name="evolucao_id" value="<?php echo $evolucao['id']; ?>">
            <input type="hidden" name="paciente_id" value="<?php echo $evolucao['paciente_id']; ?>">
            
            <div class="form-group">
                <label for="titulo">Título da Sessão/Anotação</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($evolucao['titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição Detalhada (Evolução)</label>
                <textarea id="descricao" name="descricao" rows="10" required><?php echo htmlspecialchars($evolucao['descricao']); ?></textarea>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>