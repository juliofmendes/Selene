<?php
require_once '../auth/verifica_sessao.php';
require_once '../components/header.php';
?>

<h1>Adicionar Novo Paciente</h1>
<p>Preencha as informações básicas para cadastrar um novo paciente.</p>

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

<?php
// Incluir um futuro footer.php aqui
?>
    </main>
</body>
</html>