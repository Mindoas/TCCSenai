<?php
// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "senai123";
$banco = "meu_banco_de_dados";

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o texto do formulário
$texto = $_POST['texto'];

// Prepara a consulta SQL para evitar SQL Injection
$stmt = $conn->prepare("INSERT INTO mensagens (texto) VALUES (?)");
$stmt->bind_param("s", $texto);

// Executa a consulta
if ($stmt->execute()) {
    echo "Texto salvo com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fecha a declaração e a conexão
$stmt->close();
$conn->close();
?>
