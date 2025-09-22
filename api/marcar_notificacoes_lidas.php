<?php
header('Content-Type: application/json');
require_once '../auth/verifica_sessao.php';
// A verificação de sessão já inclui o config.php, então $pdo está disponível

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE notificacoes SET lida = TRUE WHERE usuario_id = :uid AND lida = FALSE");
    $stmt->execute(['uid' => $_SESSION['usuario_id']]);
    
    // Verificamos se alguma linha foi de facto atualizada
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Notificações marcadas como lidas.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhuma notificação nova para marcar.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro na base de dados.']);
}
?>