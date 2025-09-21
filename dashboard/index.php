<?php
require_once '../auth/verifica_sessao.php';

switch ($_SESSION['nivel_acesso']) {
    case 'psicologo':
        header('Location: psicologo.php');
        break;
    
    case 'secretaria':
        header('Location: secretaria.php');
        break;
    
    // Outros casos no futuro
    // case 'gestor': ...
    
    default:
        // Segurança: se o nível for desconhecido, desloga.
        header('Location: ../auth/logout.php');
        break;
}
exit;
?>