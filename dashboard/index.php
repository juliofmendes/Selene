<?php
require_once '../auth/verifica_sessao.php';

switch ($_SESSION['nivel_acesso']) {
    case 'psicologo':
        header('Location: psicologo.php');
        break;
    
    case 'secretaria':
        header('Location: secretaria.php');
        break;
    
    case 'gestor':
    case 'admin': // Admin também pode aceder ao dashboard de gestor
        header('Location: gestor.php');
        break;
    
    default:
        // Segurança
        header('Location: ../auth/logout.php');
        break;
}
exit;
?>