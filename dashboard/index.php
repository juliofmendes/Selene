<?php
require_once '../auth/verifica_sessao.php';

// Converte o nível de acesso para minúsculas para uma verificação robusta
$nivel_acesso = strtolower($_SESSION['nivel_acesso']);

switch ($nivel_acesso) {
    case 'psicologo':
        header('Location: psicologo.php');
        break;
    
    case 'secretaria':
        header('Location: secretaria.php');
        break;
    
    case 'gestor':
    case 'admin':
        header('Location: gestor.php');
        break;
    
    default:
        // Se o nível de acesso for desconhecido, desloga o utilizador por segurança.
        header('Location: ../auth/logout.php');
        break;
}
exit;
?>