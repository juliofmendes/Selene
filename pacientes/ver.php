<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

// 1. Validar o ID do Paciente
// Verificamos se um ID foi passado pela URL e se é um número
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) {
    // Se não houver ID ou se não for válido, redireciona para o dashboard
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

// 2. Buscar o Paciente no Banco de Dados com Segurança
// A consulta inclui "psicologo_id = ?" para garantir que o psicólogo
// só possa ver pacientes que lhe pertencem. Esta é uma verificação de segurança CRÍTICA.
$stmt = $pdo->prepare(
    "SELECT * FROM pacientes WHERE id = :paciente_id AND psicologo_id = :psicologo_id"
);
$stmt->execute([
    'paciente_id' => $paciente_id,
    'psicologo_id' => $_SESSION['usuario_id']
]);
$paciente = $stmt->fetch();

// 3. Verificar se o Paciente Foi Encontrado
// Se a consulta não retornar nenhum resultado, significa que o paciente não existe ou não pertence a este psicólogo.
if (!$paciente) {
    // Redireciona para o dashboard por segurança
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

// Se chegamos até aqui, o paciente é válido e pertence ao psicólogo logado.
require_once '../components/header.php';
?>

<h1>Dossiê do Paciente</h1>
<a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">&larr; Voltar para a lista de pacientes</a>

<div class="card-paciente-info">
    <h2><?php echo htmlspecialchars($paciente['nome_completo']); ?></h2>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($paciente['email'] ?: 'Não informado'); ?></p>
    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($paciente['telefone'] ?: 'Não informado'); ?></p>
    <p><strong>Data de Nascimento:</strong> <?php echo $paciente['data_nascimento'] ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : 'Não informada'; ?></p>
    <p><strong>Status:</strong> <span class="status-<?php echo htmlspecialchars($paciente['status']); ?>"><?php echo htmlspecialchars(ucfirst($paciente['status'])); ?></span></p>
    <p><strong>Paciente desde:</strong> <?php echo date('d/m/Y', strtotime($paciente['data_criacao'])); ?></p>
</div>

<div class="card-evolucao">
    <h2>Evolução e Prontuário</h2>
    <p><em>(Em breve, aqui será o espaço para adicionar e visualizar as evoluções, anotações e o histórico do paciente.)</em></p>
</div>

<?php
// Adicionar um futuro footer.php
?>
    </main>
</body>
</html>