<?php
// Incluímos o guardião para garantir que o usuário está logado
require_once '../auth/verifica_sessao.php';

// A Lógica do Maestro:
// Verifica o nível de acesso armazenado na sessão e redireciona.
switch ($_SESSION['nivel_acesso']) {
    case 'psicologo':
        header('Location: psicologo.php');
        break;
    
    // Adicionaremos os outros casos aqui no futuro
    // case 'secretaria':
    //     header('Location: secretaria.php');
    //     break;
    // case 'gestor':
    //     header('Location: gestor.php');
    //     break;
    // case 'admin':
    //     header('Location: admin.php');
    //     break;
    
    default:
        // Se o nível de acesso for desconhecido ou inválido,
        // desloga o usuário por segurança.
        header('Location: ../auth/logout.php');
        break;
}

// Garante que nenhum outro código seja executado após o redirecionamento
exit;
?>