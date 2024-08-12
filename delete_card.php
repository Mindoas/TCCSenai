<?php
// Configurações do banco de dados
$servidor = "10.138.50.12";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";
$porta = 3306;

// Cria a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);

// Verifica a conexão
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Conexão falhou: ' . $conn->connect_error]);
    exit();
}

// Obtém o ID do cartão
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Prepara a consulta SQL para excluir o cartão
if ($id > 0 && $stmt = $conn->prepare("DELETE FROM cartoes WHERE id = ?")) {
    $stmt->bind_param("i", $id);

    // Executa a consulta
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cartão excluído com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o cartão: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido ou erro ao preparar a consulta.']);
}

// Fecha a conexão
$conn->close();
?>
