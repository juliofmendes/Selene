<?php
header('Content-Type: application/json');
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE notificacoes SET lida = TRUE WHERE usuario_id = :uid AND lida = FALSE");
        $stmt->execute(['uid' => $_SESSION['usuario_id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Notificações marcadas como lidas.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma notificação nova para marcar.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro na base de dados.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
}
?>