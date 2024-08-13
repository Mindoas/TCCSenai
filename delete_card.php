<?php
// Configurações do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";

// Cria a conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Conexão falhou: ' . $conn->connect_error
    ]);
    exit();
}

// Obtém o ID do cartão enviado via POST
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Log para debug
error_log("ID recebido: " . $id);

// Verifica se o ID é válido
if ($id > 0) {
    // Prepara a consulta SQL para deletar o cartão
    $stmt = $conn->prepare("DELETE FROM cartoes WHERE id = ?");
    
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta: " . $conn->error);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao preparar a consulta.'
        ]);
    } else {
        // Vincula o parâmetro e executa a consulta
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cartão excluído com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Nenhum cartão encontrado com o ID fornecido.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro ao excluir o cartão: ' . $stmt->error
            ]);
        }

        // Fecha a declaração
        $stmt->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID inválido.'
    ]);
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
