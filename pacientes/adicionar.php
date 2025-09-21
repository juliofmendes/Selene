<?php
require_once '../auth/verifica_sessao.php';
// A autorização é herdada, pois apenas utilizadores logados com acesso ao dashboard de psicólogo chegam aqui.
// No futuro, podemos adicionar uma chamada explícita a autorizar() se as regras de negócio mudarem.
require_once '../components/header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Adicionar Novo Paciente</h1>
        <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">&larr; Voltar ao Dashboard</a>
    </div>

    <div class="card">
        <p>Preencha as informações básicas para cadastrar um novo paciente no seu dossiê.</p>
        <form action="processa_adicionar.php" method="POST">
            <div class="form-group">
                <label for="nome_completo">Nome Completo</label>
                <input type="text" id="nome_completo" name="nome_completo" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone">
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento">
            </div>
            <button type="submit">Cadastrar Paciente</button>
        </form>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>