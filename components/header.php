<?php
// Garantimos que a sessão já foi iniciada e que a configuração está carregada
// A ordem de inclusão nos ficheiros principais deve ser: verifica_sessao.php -> header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Incluímos o config.php apenas se a variável $pdo não estiver definida
if (!isset($pdo)) {
    require_once __DIR__ . '/../config.php';
}

// Lógica para buscar notificações não lidas para o utilizador logado
$notificacoes_nao_lidas = [];
if (isset($_SESSION['usuario_id'])) {
    try {
        $notificacaoStmt = $pdo->prepare("SELECT * FROM notificacoes WHERE usuario_id = :uid AND lida = FALSE ORDER BY data_criacao DESC LIMIT 5");
        $notificacaoStmt->execute(['uid' => $_SESSION['usuario_id']]);
        $notificacoes_nao_lidas = $notificacaoStmt->fetchAll();
    } catch (PDOException $e) {
        // Ignora o erro silenciosamente para não quebrar o layout em caso de falha na BD
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selene</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="logo"><strong>Selene</strong></div>
        <nav class="main-nav">
            <?php require __DIR__ . '/nav_links.php'; ?>
            
            <div class="nav-separator"></div>

            <div class="notificacoes-container">
                <div id="notificacoes-bell" class="notificacoes-bell">
                    <span>🔔</span>
                    <?php if (count($notificacoes_nao_lidas) > 0): ?>
                        <span class="notificacoes-count"><?php echo count($notificacoes_nao_lidas); ?></span>
                    <?php endif; ?>
                </div>
                <div id="notificacoes-dropdown" class="notificacoes-dropdown">
                    <div class="notificacoes-header">Notificações</div>
                    <?php if (count($notificacoes_nao_lidas) > 0): ?>
                        <?php foreach ($notificacoes_nao_lidas as $notificacao): ?>
                            <a href="<?php echo BASE_URL . ($notificacao['link'] ?? '#'); ?>" class="notificacao-item">
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
    
    <style>
        .main-nav { display: flex; gap: 1.5rem; align-items: center; }
        .nav-separator { border-left: 1px solid #ddd; height: 20px; }
        .button-logout { padding: 0.5rem 1rem; }
        .notificacoes-container { position: relative; }
        .notificacoes-bell { cursor: pointer; position: relative; font-size: 1.5rem; line-height: 1; }
        .notificacoes-count { position: absolute; top: -5px; right: -8px; background-color: #dc3545; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .notificacoes-dropdown { display: none; position: absolute; top: calc(100% + 10px); right: 0; background-color: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; z-index: 1000; }
        .notificacoes-dropdown.show { display: block; }
        .notificacoes-header { padding: 1rem; font-weight: bold; border-bottom: 1px solid #eee; }
        .notificacao-item, .notificacao-item-vazio { display: block; padding: 1rem; border-bottom: 1px solid #eee; color: #333; text-decoration: none; font-size: 0.9em; }
        .notificacao-item:last-child { border-bottom: none; }
        .notificacao-item:hover { background-color: #f8f9fa; }
        .notificacao-item-vazio { color: #888; text-align: center; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bell = document.getElementById('notificacoes-bell');
            const dropdown = document.getElementById('notificacoes-dropdown');
            const countBadge = document.querySelector('.notificacoes-count');

            if (bell) {
                bell.addEventListener('click', function(event) {
                    event.stopPropagation();
                    dropdown.classList.toggle('show');
                    
                    // Se houver notificações e o dropdown for aberto, marca como lidas
                    if (dropdown.classList.contains('show') && countBadge) {
                        // Faz uma chamada assíncrona para a nossa API para marcar as notificações como lidas
                        fetch('<?php echo BASE_URL; ?>/api/marcar_notificacoes_lidas.php', { 
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Esconde o contador visualmente após a confirmação da API
                                countBadge.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Erro ao marcar notificações como lidas:', error));
                    }
                });
            }

            // Fecha o dropdown se o utilizador clicar fora dele
            document.addEventListener('click', function(event) {
                if (dropdown && dropdown.classList.contains('show') && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            });
        });
    </script>