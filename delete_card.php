<?php
// Configurações do banco de dados
$servidor = "10.138.50.12"; // IP do servidor MySQL
$usuario = "root"; // Usuário do MySQL
$senha = ""; // Senha do MySQL
$banco = "meu_banco_frequencia"; // Nome do banco de dados

// Porta do MySQL (opcional, o padrão é 3306, substitua se necessário)
$porta = 3306;

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o ID do cartão
$id = $_POST['id'];

// Prepara a consulta SQL para excluir o cartão
$stmt = $conn->prepare("DELETE FROM cartoes WHERE id = ?");
$stmt->bind_param("i", $id);

// Executa a consulta
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Cartão excluído com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o cartão.']);
}

// Fecha a conexão
$stmt->close();
$conn->close();
?>

