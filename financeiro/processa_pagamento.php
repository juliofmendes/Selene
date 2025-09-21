<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'gestor']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/financeiro/index.php');
    exit;
}

$fatura_id = filter_input(INPUT_POST, 'fatura_id', FILTER_VALIDATE_INT);

if (!$fatura_id) {
    die("Erro: ID da fatura inválido.");
}

try {
    $stmt = $pdo->prepare(
        "UPDATE faturas 
         SET status = 'paga', data_pagamento = CURDATE() 
         WHERE id = :id AND status = 'pendente'"
    );
    $stmt->execute(['id' => $fatura_id]);

    header('Location: ' . BASE_URL . '/financeiro/index.php?sucesso_pagamento=1');
    exit;

} catch (PDOException $e) {
    die("Erro ao registar pagamento: " . $e->getMessage());
}
?>