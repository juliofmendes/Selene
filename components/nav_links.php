<?php
$nivel = $_SESSION['nivel_acesso'] ?? '';

// Psicólogo e Psicólogo Autónomo partilham o link clínico
if ($nivel === 'psicologo' || $nivel === 'psicologo_autonomo') {
    echo '<a href="' . BASE_URL . '/dashboard/psicologo.php">Dashboard Clínico</a>';
}

// Secretaria e Psicólogo Autónomo partilham os links operacionais
if ($nivel === 'secretaria' || $nivel === 'psicologo_autonomo') {
    echo '<a href="' . BASE_URL . '/dashboard/secretaria.php">Agenda</a>';
    echo '<a href="' . BASE_URL . '/financeiro/index.php">Faturas</a>';
}

// Gestor e Admin partilham os links de gestão
if ($nivel === 'gestor' || $nivel === 'admin') {
    echo '<a href="' . BASE_URL . '/dashboard/gestor.php">Dashboard Gestor</a>';
    echo '<a href="' . BASE_URL . '/financeiro/relatorios.php">Relatórios</a>';
}

// Admin tem links exclusivos
if ($nivel === 'admin') {
    echo '<a href="' . BASE_URL . '/admin/index.php">Administração</a>';
}
?>