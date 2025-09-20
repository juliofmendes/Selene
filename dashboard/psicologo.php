<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php'; // Inclui nossa conexão com o banco

// Busca os pacientes associados a este psicólogo
// Usamos prepared statements para segurança máxima contra SQL Injection
$stmt = $pdo->prepare("SELECT id, nome_completo, status FROM pacientes WHERE psicologo_id = :psicologo_id ORDER BY nome_completo ASC");
$stmt->execute(['psicologo_id' => $_SESSION['usuario_id']]);
$pacientes = $stmt->fetchAll();

require_once '../components/header.php'; 
?>

<h1>Dashboard do Psicólogo</h1>
<p>Bem-vindo(a) de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>

<div class="card">
    <h2>Seus Pacientes</h2>
    <a href="<?php echo BASE_URL; ?>/pacientes/adicionar.php" class="button-add">+ Adicionar Novo Paciente</a>
    
    <?php if (count($pacientes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($paciente['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($paciente['status'])); ?></td>
                        <td><a href="#">Ver Dossiê</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não tem nenhum paciente cadastrado. Comece adicionando um novo paciente.</p>
    <?php endif; ?>
</div>

<?php
// Adicione um pouco de estilo ao header.php para a tabela e o card
?>