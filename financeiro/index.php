<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'gestor', 'psicologo_autonomo']);
require_once '../config.php';
// ... (lógica de busca de faturas permanece a mesma) ...
$stmt = $pdo->prepare("SELECT f.id, f.valor, f.status, ... FROM faturas f ...");
$stmt->execute();
$faturas = $stmt->fetchAll();
require_once '../components/header.php';
?>
<div class="container">
    <h1>Dashboard Financeiro</h1>
    <div class="card">
        <h2>Faturas Registadas</h2>
        <table>
            <thead><tr><th>Status</th><th>Emissão</th><th>Paciente</th><th>Serviço</th><th>Valor</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($faturas as $fatura): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fatura['paciente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($fatura['servico_nome']); ?></td>
                        <td>R$ <?php echo number_format($fatura['valor'], 2, ',', '.'); ?></td>
                        <td class="action-icons">
                            <?php if ($fatura['status'] == 'pendente'): ?>
                                <form action="processa_pagamento.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="fatura_id" value="<?php echo $fatura['id']; ?>">
                                    <button type="submit" class="action-icon icon-paid" title="Marcar como Paga">
                                        <span class="material-symbols-rounded">price_check</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="gerar_recibo.php?id=<?php echo $fatura['id']; ?>" target="_blank" class="action-icon icon-view" title="Gerar Recibo">
                                    <span class="material-symbols-rounded">receipt_long</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>