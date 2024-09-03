<?php
require('C:\xampp\htdocs\TCCSenai\FPDF\fpdf.php');

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

$filtro = isset($_GET['filtro-impr']) ? $conn->real_escape_string($_GET['filtro-impr']) : '';
$subFiltro = isset($_GET['sub-filtro']) ? $conn->real_escape_string($_GET['sub-filtro']) : '';
$searchInput = isset($_GET['search-input']) ? $conn->real_escape_string($_GET['search-input']) : '';

// echo "$subFiltro";

$sql = "SELECT nome, serie, datas AS horario, status FROM paineladm JOIN cartoes ON cartoes_id = id WHERE 1=1";

if ($filtro === 'Completo') {
    $sql .= " AND DATE(datas) = CURDATE()";
} elseif ($filtro === 'Serie' && !empty($subFiltro)) {
    $sql .= " AND serie = ?";
} elseif ($filtro === 'Nome' && !empty($searchInput)) {
    $sql .= " AND nome LIKE ?";
} elseif ($filtro === 'codigo' && !empty($searchInput)) {
    $sql .= " AND cartoes_id = ?";
} elseif ($filtro === 'Entrada') {
    $sql .= " AND status = 'Entrada'";
} elseif ($filtro === 'Saida') {
    $sql .= " AND status = 'Saida'";
} elseif ($filtro === 'Ausente') {
    $sql .= " AND status = 'Ausente'";
}

$stmt = $conn->prepare($sql);

if ($filtro === 'Serie' && !empty($subFiltro)) {
    $stmt->bind_param('s', $subFiltro);
} elseif (($filtro === 'Nome' || $filtro === 'codigo') && !empty($searchInput)) {
    $searchTerm = ($filtro === 'Nome') ? "%$searchInput%" : $searchInput;
    $stmt->bind_param('s', $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Relatorio de Painel ADM', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 10, 'Nome', 1);
$pdf->Cell(60, 10, 'Serie', 1);
$pdf->Cell(60, 10, 'Horario', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, $row['nome'], 1);
    $pdf->Cell(60, 10, $row['serie'], 1);
    $pdf->Cell(60, 10, $row['horario'], 1);
    $pdf->Cell(30, 10, $row['status'], 1);
    $pdf->Ln();
}

$conn->close();

// Envia o PDF para o navegador
$filename = 'relatorio_painel_adm_' . date('Ymd_His') . '.pdf';
$pdf->Output('I', $filename);
?>
