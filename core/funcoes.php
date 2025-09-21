<?php
/**
 * Cria uma notificação para um utilizador específico.
 *
 * @param PDO $pdo A instância da conexão com o banco de dados.
 * @param int $usuario_id O ID do utilizador que receberá a notificação.
 * @param string $mensagem O texto da notificação.
 * @param string|null $link O link opcional para a página relevante.
 * @return bool Retorna true em caso de sucesso, false em caso de falha.
 */
function criar_notificacao(PDO $pdo, int $usuario_id, string $mensagem, ?string $link = null): bool 
{
    // Verificação de sanidade para garantir que não estamos a tentar notificar um utilizador inválido.
    if ($usuario_id <= 0) {
        // die() é usado aqui temporariamente para depuração.
        die("Tentativa de criar notificação para um utilizador com ID inválido.");
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO notificacoes (usuario_id, mensagem, link) VALUES (:uid, :msg, :link)"
        );
        $stmt->execute([
            'uid' => $usuario_id,
            'msg' => $mensagem,
            'link' => $link
        ]);
        return true;
    } catch (PDOException $e) {
        // Em modo de diagnóstico, mostramos o erro exato.
        die("ERRO CRÍTICO ao criar notificação: " . $e->getMessage());
        return false;
    }
}
?>