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
        // Em um sistema real, logaríamos o erro.
        // error_log("Erro ao criar notificação: " . $e->getMessage());
        return false;
    }
}
?>