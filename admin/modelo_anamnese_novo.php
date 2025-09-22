<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../components/header.php';
?>
<div class="container">
    <h1>Novo Modelo de Anamnese</h1>
    <a href="anamnese_modelos.php">&larr; Voltar para a lista</a>
    <div class="card">
        <form action="processa_modelo_anamnese.php" method="POST">
            <div class="form-group">
                <label for="titulo">Título do Modelo (ex: Anamnese Infantil)</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição (opcional)</label>
                <textarea name="descricao" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="estrutura_json">Estrutura do Formulário (em formato JSON)</label>
                <textarea name="estrutura_json" rows="15" required placeholder='[{"label": "Queixa Principal", "tipo": "textarea", "name": "queixa_principal"}]'></textarea>
                <small>Defina os campos do formulário aqui. Use os tipos "text", "textarea", "date", "select" com uma array de "options".</small>
            </div>
            <button type="submit">Salvar Modelo</button>
        </form>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>