<?php
// Incluímos o verifica_sessao.php apenas para garantir que existe uma sessão ativa.
// A função autorizar() foi REMOVIDA daqui, pois este ficheiro é apenas um router.
require_once '../auth/verifica_sessao.php';

// Converte o nível de acesso para minúsculas para uma verificação robusta
$nivel_acesso = strtolower($_SESSION['nivel_acesso']);

// A lógica de redirecionamento permanece a mesma
switch ($nivel_acesso) {
    case 'psicologo':
    case 'psicologo_autonomo':
        header('Location: psicologo.php');
        break;
    
    case 'secretaria':
        header('Location: secretaria.php');
        break;
    
    case 'gestor':
        header('Location: gestor.php');
        break;
    
    case 'admin':
        header('Location: ../admin/index.php');
        break;
    
    default:
        // Se o nível de acesso for desconhecido, desloga o utilizador por segurança.
        header('Location: ../auth/logout.php');
        break;
}
exit;
?>