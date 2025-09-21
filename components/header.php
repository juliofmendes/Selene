<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Selene</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="logo"><strong>Selene</strong> | Projeto</div>
        <nav style="display: flex; gap: 1.5rem;">
            <?php if ($_SESSION['nivel_acesso'] === 'psicologo'): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/agendamentos/minha_agenda.php">Minha Agenda</a>
            <?php elseif ($_SESSION['nivel_acesso'] === 'secretaria'): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard/secretaria.php">Agenda da Clínica</a>
            <?php endif; ?>

            <?php if (in_array($_SESSION['nivel_acesso'], ['admin', 'gestor'])): ?>
                <a href="<?php echo BASE_URL; ?>/admin/servicos.php">Configurações</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/auth/logout.php">Sair</a>
        </nav>
    </header>
    <main>