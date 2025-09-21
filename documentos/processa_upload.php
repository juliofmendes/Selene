<?php
require_once '../auth/verifica_sessao.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/psicologo.php');
    exit;
}

$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$arquivo = $_FILES['documento'] ?? null;

if (!$paciente_id || empty($titulo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
    die("Erro: Dados em falta ou erro no upload.");
}

// Diretório de uploads (deve ter permissões de escrita)
$diretorio_upload = __DIR__ . '/../uploads/';
if (!is_dir($diretorio_upload)) {
    mkdir($diretorio_upload, 0755, true);
}

// Gera um nome de ficheiro único para evitar conflitos
$nome_arquivo_seguro = uniqid() . '-' . basename($arquivo['name']);
$caminho_completo = $diretorio_upload . $nome_arquivo_seguro;

if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO documentos (paciente_id, usuario_id, titulo, nome_arquivo, caminho_arquivo, tipo_mime, tamanho_arquivo)
             VALUES (:pid, :uid, :titulo, :nome, :caminho, :tipo, :tamanho)"
        );
        $stmt->execute([
            'pid' => $paciente_id,
            'uid' => $_SESSION['usuario_id'],
            'titulo' => $titulo,
            'nome' => $nome_arquivo_seguro,
            'caminho' => '/uploads/' . $nome_arquivo_seguro, // Caminho relativo para o URL
            'tipo' => $arquivo['type'],
            'tamanho' => $arquivo['size']
        ]);

        header('Location: ' . BASE_URL . '/pacientes/ver.php?id=' . $paciente_id . '&sucesso_upload=1');
        exit;
    } catch (PDOException $e) {
        // Se a inserção na BD falhar, remove o ficheiro para não deixar lixo
        unlink($caminho_completo);
        die("Erro ao salvar no banco de dados: " . $e->getMessage());
    }
} else {
    die("Erro: Falha ao mover o ficheiro para o diretório de uploads.");
}
?>