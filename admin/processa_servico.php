<?php
require_once '../auth/verifica_sessao.php';
autorizar(['admin', 'gestor']);
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome']);
    $valor = str_replace(['.', ','], ['', '.'], $_POST['valor']);

    if (!empty($nome) && is_numeric($valor)) {
        if ($servico_id) { // Edição
            $stmt = $pdo->prepare("UPDATE servicos SET nome = :nome, valor = :valor WHERE id = :id");
            $stmt->execute(['nome' => $nome, 'valor' => $valor, 'id' => $servico_id]);
        } else { // Novo
            $stmt = $pdo->prepare("INSERT INTO servicos (nome, valor) VALUES (:nome, :valor)");
            $stmt->execute(['nome' => $nome, 'valor' => $valor]);
        }
    }
    header('Location: servicos.php?sucesso_salvar=1');
    exit;
}
?>