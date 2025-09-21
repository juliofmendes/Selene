<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin']);
require_once '../components/header.php';
?>
<div class="container">
    <h1>Painel de Administração</h1>
    <p>Gestão central dos parâmetros e utilizadores do sistema Selene.</p>

    <div class="admin-grid">
        <a href="<?php echo BASE_URL; ?>/admin/usuarios.php" class="card-link">
            <div class="card">
                <h2>Gestão de Utilizadores</h2>
                <p>Adicionar, editar e gerir os acessos dos colaboradores.</p>
            </div>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/servicos.php" class="card-link">
            <div class="card">
                <h2>Gestão de Serviços</h2>
                <p>Definir os tipos de consulta e os seus respetivos valores.</p>
            </div>
        </a>
        </div>
</div>
<style>
    .admin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
    .card-link { text-decoration: none; color: inherit; }
    .card-link .card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.2s ease-in-out; }
</style>
<?php require_once '../components/footer.php'; ?>