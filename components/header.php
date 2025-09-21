<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selene</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="logo"><strong>Selene</strong></div>
        <nav style="display: flex; gap: 1.5rem; align-items: center;">
            <?php if ($_SESSION['nivel_acesso'] === 'psicologo'): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">Dashboard</a>
            <?php elseif ($_SESSION['nivel_acesso'] === 'secretaria'): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard/secretaria.php">Agenda</a>
                <a href="<?php echo BASE_URL; ?>/financeiro/index.php">Financeiro</a>
            <?php elseif (in_array($_SESSION['nivel_acesso'], ['gestor', 'admin'])): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard/gestor.php">Dashboard Gestor</a>
                <a href="<?php echo BASE_URL; ?>/financeiro/index.php">Financeiro</a>
                <a href="<?php echo BASE_URL; ?>/admin/servicos.php">Serviços</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="button button-logout">Sair</a>
        </nav>
    </header>
    <main>
    <style>.button-logout { padding: 0.5rem 1rem; } </style>