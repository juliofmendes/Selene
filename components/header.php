<?php
// Lógica para buscar notificações não lidas para o utilizador logado
if (isset($_SESSION['usuario_id']) && isset($pdo)) {
    $notificacaoStmt = $pdo->prepare("SELECT * FROM notificacoes WHERE usuario_id = :uid AND lida = FALSE ORDER BY data_criacao DESC");
    $notificacaoStmt->execute(['uid' => $_SESSION['usuario_id']]);
    $notificacoes_nao_lidas = $notificacaoStmt->fetchAll();
} else {
    $notificacoes_nao_lidas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<body>
    <header class="header">
        <div class="logo"><strong>Selene</strong></div>
        <nav style="display: flex; gap: 1.5rem; align-items: center;">
            <div class="notificacoes-container">
                <a href="#" id="notificacoes-bell" class="notificacoes-bell">
                    🔔
                    <?php if (count($notificacoes_nao_lidas) > 0): ?>
                        <span class="notificacoes-count"><?php echo count($notificacoes_nao_lidas); ?></span>
                    <?php endif; ?>
                </a>
                <div id="notificacoes-dropdown" class="notificacoes-dropdown">
                    <?php if (count($notificacoes_nao_lidas) > 0): ?>
                        <?php foreach ($notificacoes_nao_lidas as $notificacao): ?>
                            <a href="<?php echo BASE_URL . $notificacao['link']; ?>" class="notificacao-item">
                                <?php echo htmlspecialchars($notificacao['mensagem']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notificacao-item-vazio">Nenhuma nova notificação</div>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="button button-logout">Sair</a>
        </nav>
    </header>
    <main>
    <style> /* Estilos para o sino de notificação */ </style>
    <script> /* Script para mostrar/esconder o dropdown */ </script>