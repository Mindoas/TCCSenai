<?php
header('Content-Type: application/json');

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['erro' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

date_default_timezone_set('America/Sao_Paulo');

// Obtém o código do aluno enviado na solicitação POST
$data = json_decode(file_get_contents('php://input'), true);
$codigo = isset($data['codigo']) ? $data['codigo'] : '';

if (empty($codigo)) {
    echo json_encode(['erro' => 'Código do aluno não fornecido']);
    exit();
}

$sql = "SELECT id, nome, flag FROM cartoes WHERE info = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['erro' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    echo json_encode(['erro' => 'Erro na execução da consulta: ' . $stmt->error]);
    exit();
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_cartao = $row['id'];
    $nome = $row['nome'];
    $flag = $row['flag'];


    // Define o status baseado na flag
    switch ($flag) {
        case 0:
            $status = 'ENTRADA';
            $mensagem = "Aluno marcado como presente (entrada)";
            break;
        case 1:
            $status = 'SAIDA';
            $mensagem = "Aluno marcado como ausente (saída)";
            break;
        default:
            $status = 'AUSENTE';
            $mensagem = "Estado do aluno não definido";
            break;
    }

    // Atualiza a flag no banco de dados
    $nova_flag = ($flag + 1) % 3;
    $sql_update_flag = "UPDATE cartoes SET flag = ? WHERE id = ?";
    $stmt_update_flag = $conn->prepare($sql_update_flag);
    
    if (!$stmt_update_flag) {
        echo json_encode(['erro' => 'Erro na preparação da consulta de atualização: ' . $conn->error]);
        exit();
    }

    $stmt_update_flag->bind_param("ii", $nova_flag, $id_cartao);
    $stmt_update_flag->execute();

    if ($stmt_update_flag->error) {
        echo json_encode(['erro' => 'Erro ao atualizar a presença no banco de dados: ' . $stmt_update_flag->error]);
        exit();
    }

    // Define a data atual
    $data_atual = date('Y-m-d H:i:s');

    // Insere o registro na tabela paineladm com o status atualizado
    $sql_insert = "INSERT INTO paineladm (datas, cartoes_id, status) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    if (!$stmt_insert) {
        echo json_encode(['erro' => 'Erro na preparação da consulta de inserção: ' . $conn->error]);
        exit();
    }

    $stmt_insert->bind_param("sis", $data_atual, $id_cartao, $status);
    $stmt_insert->execute();
    
    if ($stmt_insert->error) {
        echo json_encode(['erro' => 'Erro ao inserir no paineladm: ' . $stmt_insert->error]);
        exit();
    }
    
    echo json_encode(['existe' => true, 'nome' => $nome, 'mensagem' => $mensagem]);
    $stmt_insert->close();
    $stmt_update_flag->close();

} else {
    echo json_encode(['existe' => false]);
}

$stmt->close();
$conn->close();
?>
