<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'gestor', 'psicologo_autonomo']);
require_once '../config.php';

$nivel_acesso = strtolower($_SESSION['nivel_acesso']);
switch ($nivel_acesso) {
    case 'psicologo':
    case 'psicologo_autonomo': // Psicólogo autónomo também começa pelo dashboard clínico
        header('Location: psicologo.php');
        break;
    case 'secretaria':
        header('Location: secretaria.php');
        break;
    case 'gestor':
        header('Location: gestor.php');
        break;
    case 'admin':
        header('Location: ../admin/index.php'); // Admin agora vai para o painel de admin
        break;
    default:
        header('Location: ../auth/logout.php');
        break;
}
exit;
?>