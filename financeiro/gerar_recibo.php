<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin', 'gestor', 'psicologo_autonomo']);
require_once '../config.php';
require_once '../libs/fpdf/fpdf.php'; // Incluímos a biblioteca FPDF

$fatura_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$fatura_id) { die("Fatura inválida."); }

// Busca todos os dados necessários para o recibo
$stmt = $pdo->prepare(
    "SELECT 
        f.id, f.valor, f.status, f.data_emissao, f.data_pagamento,
        pac.nome_completo AS paciente_nome, pac.cpf AS paciente_cpf,
        psi.nome AS psicologo_nome,
        ser.nome AS servico_nome
     FROM faturas AS f
     JOIN agendamentos AS ag ON f.agendamento_id = ag.id
     JOIN pacientes AS pac ON ag.paciente_id = pac.id
     JOIN usuarios AS psi ON ag.psicologo_id = psi.id
     JOIN servicos AS ser ON f.servico_id = ser.id
     WHERE f.id = :id"
);
$stmt->execute(['id' => $fatura_id]);
$fatura = $stmt->fetch();

if (!$fatura) { die("Fatura não encontrada ou acesso negado."); }

// --- INÍCIO DA GERAÇÃO DO PDF ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Cabeçalho (informações da clínica/profissional)
$pdf->Cell(0, 10, 'Recibo de Pagamento', 0, 1, 'C');
$pdf->Ln(10); // Pular linha
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Clínica Selene / ' . htmlspecialchars($fatura['psicologo_nome'])), 0, 1);
// Adicionar mais detalhes da clínica/profissional aqui (morada, NIF, etc.)
$pdf->Ln(15);

// Corpo do Recibo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalhes do Paciente', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Nome: ' . htmlspecialchars($fatura['paciente_nome'])), 0, 1);
// Adicionar CPF se existir
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Descrição dos Serviços'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(130, 8, utf8_decode(htmlspecialchars($fatura['servico_nome'])), 1);
$pdf->Cell(60, 8, utf8_decode('R$ ' . number_format($fatura['valor'], 2, ',', '.')), 1, 1, 'R');
$pdf->Ln(10);

// Rodapé do Recibo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 8, 'Total Pago:', 0);
$pdf->Cell(60, 8, utf8_decode('R$ ' . number_format($fatura['valor'], 2, ',', '.')), 0, 1, 'R');
$pdf->Ln(5);

$data_pagamento = $fatura['data_pagamento'] ? date('d/m/Y', strtotime($fatura['data_pagamento'])) : 'N/A';
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, utf8_decode('Pagamento recebido em: ' . $data_pagamento), 0, 1);
$pdf->Ln(20);

// Assinatura
$pdf->Cell(0, 10, '_________________________________________', 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode(htmlspecialchars($fatura['psicologo_nome'])), 0, 1, 'C');


// Saída do PDF
$pdf->Output('I', 'Recibo-' . $fatura['id'] . '.pdf');
?>