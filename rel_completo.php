<?php
require('C:\xampp\htdocs\TCC\TCCSenai-1\FPDF\fpdf.php');

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obtenha o filtro selecionado
$filtro = isset($_GET['filtro-impr']) ? $_GET['filtro-impr'] : '';
$subFiltro = isset($_GET['sub-filtro']) ? $_GET['sub-filtro'] : '';
$searchInput = isset($_GET['search-input']) ? $_GET['search-input'] : '';

// Criação da classe PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Relatório de Alunos', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Instância do PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');

// Construção da consulta SQL conforme o filtro selecionado
if ($filtro === 'Ausente') {
    $sql = "SELECT cartoes.nome, cartoes.serie, cartoes.flag, paineladm.datas 
            FROM cartoes 
            LEFT JOIN paineladm ON paineladm.cartoes_id = cartoes.id
            WHERE cartoes.flag = 0";
    
    if ($subFiltro !== 'Geral') {
        $sql .= " AND cartoes.serie = '$subFiltro'";
    }

} else {
    $sql = "SELECT paineladm.datas, cartoes.nome, cartoes.serie, paineladm.status 
            FROM paineladm 
            LEFT JOIN cartoes ON paineladm.cartoes_id = cartoes.id";

    switch ($filtro) {
        case 'Completo':
            $sql .= " WHERE DATE(paineladm.datas) = CURDATE()"; // Ajuste conforme o campo de data
            break;

        case 'Serie':
            if ($subFiltro !== 'Geral') {
                $sql .= " WHERE cartoes.serie = '$subFiltro'";
            }
            break;

        case 'Nome':
            $sql .= " WHERE cartoes.nome LIKE '%$searchInput%'";
            break;

        case 'codigo':
            $sql .= " WHERE cartoes.info = '$searchInput'";
            break;

        case 'Entrada':
        case 'Saida':
            $sql .= " WHERE paineladm.status = '$filtro'";
            if ($subFiltro !== 'Geral') {
                $sql .= " AND cartoes.serie = '$subFiltro'";
            }
            break;
    }
}

// Executa a consulta
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Cabeçalhos da tabela
    $pdf->Cell(50, 10, 'Nome', 1);
    $pdf->Cell(40, 10, 'Serie', 1);
    $pdf->Cell(30, 10, 'Data', 1);   // Adicionando coluna de Data
    $pdf->Cell(30, 10, 'Hora', 1);   // Adicionando coluna de Hora
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    while ($row = $result->fetch_assoc()) {
        $status = isset($row['flag']) ? ($row['flag'] == 1 ? 'Entrada' : ($row['flag'] == 2 ? 'Saida' : 'Ausente')) : $row['status'];

        // Extração de data e hora da coluna 'datas'
        $dataHora = explode(' ', $row['datas']);
        $data = $dataHora[0];
        $hora = $dataHora[1];

        // Preenchendo as células com os dados corretos
        $pdf->Cell(50, 10, $row['nome'], 1);
        $pdf->Cell(40, 10, $row['serie'], 1);
        $pdf->Cell(30, 10, $data, 1); // Preenchendo a data
        $pdf->Cell(30, 10, $hora, 1); // Preenchendo a hora
        $pdf->Cell(30, 10, $status, 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Nenhum registro encontrado.', 0, 1);
}

// Fechar conexão
$conn->close();

// Geração do PDF
$pdf->Output('D', 'Relatorio_Filtrado.pdf');
?>
