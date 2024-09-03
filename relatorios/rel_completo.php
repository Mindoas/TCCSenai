<?php
require('C:\xampp\htdocs\TCCSenai\FPDF\fpdf.php'); // Inclua o arquivo da biblioteca FPDF

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

// Cria uma nova conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve um erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Prepara a consulta SQL para obter os dados da tabela painel_adm
$sql = "SELECT nome, serie, datas AS horario, status
        FROM paineladm
        JOIN cartoes ON cartoes_id = id
        WHERE DATE(datas) = CURDATE()";

$result = $conn->query($sql);

// Verifica se houve um erro na execução da consulta
if ($result === false) {
    die("Erro na execução da consulta: " . $conn->error);
}

// Cria uma instância do FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Adiciona título ao relatório
$pdf->Cell(0, 10, 'Relatório de Painel ADM', 0, 1, 'C');
$pdf->Ln(10);

// Adiciona cabeçalhos da tabela
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 10, 'Nome', 1);
$pdf->Cell(60, 10, 'Serie', 1);
$pdf->Cell(60, 10, 'Horario', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Ln();

// Adiciona os dados da tabela
$pdf->SetFont('Arial', '', 10);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, $row['nome'], 1);
    $pdf->Cell(60, 10, $row['serie'], 1);
    $pdf->Cell(60, 10, $row['horario'], 1);
    $pdf->Cell(30, 10, $row['status'], 1);
    $pdf->Ln();
}

// Fecha a conexão com o banco de dados
$conn->close();

// Envia o PDF para o navegador
$pdf->Output('I', 'relatorio_painel_adm.pdf');
?>
