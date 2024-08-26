<?php
// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";
$porta = 3306;

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém os dados do formulário via POST
$nome_cartao = $_POST['card-name'];
$info_cartao = $_POST['card-info'];
$serie_cartao = $_POST['card-serie'];

// Prepara a consulta SQL para evitar SQL Injection
$stmt = $conn->prepare("INSERT INTO cartoes (nome, info, serie) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome_cartao, $info_cartao, $serie_cartao );

// Executa a consulta
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Cartão salvo com sucesso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro: " . $stmt->error]);
}

// Fecha a conexão
$stmt->close();
$conn->close();
?>