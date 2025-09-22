<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'gestor']);
require_once '../config.php';

// Busca todas as faturas, juntando informações do paciente e do serviço
$stmt = $pdo->prepare(
    "SELECT 
        f.id, f.valor, f.status, f.data_emissao, f.data_pagamento,
        pac.nome_completo AS paciente_nome,
        ser.nome AS servico_nome
     FROM faturas AS f
     JOIN agendamentos AS ag ON f.agendamento_id = ag.id
     JOIN pacientes AS pac ON ag.paciente_id = pac.id
     JOIN servicos AS ser ON f.servico_id = ser.id
     ORDER BY f.data_emissao DESC, f.status ASC"
);
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
<style>
    .status-financeiro { padding: 0.2rem 0.5rem; border-radius: 4px; color: white; font-size: 0.8em; }
    .status-pendente { background-color: #ffc107; color: #333; }
    .status-paga { background-color: #28a745; }
    .button-small { padding: 0.4rem 0.8rem; font-size: 0.8em; }
</style>
<?php require_once '../components/footer.php'; ?>