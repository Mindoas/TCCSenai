<?php
header('Content-Type: application/json');

// Conectar ao banco de dados (ajuste com as credenciais corretas)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meu_banco_frequencia";

// Cria uma nova conexão com o banco de dados usando MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve um erro na conexão
if ($conn->connect_error) {
    echo json_encode(['erro' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit();
}

// Define o fuso horário para Horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Obtém o código do aluno enviado na solicitação POST
$data = json_decode(file_get_contents('php://input'), true);
$codigo = isset($data['codigo']) ? $data['codigo'] : ''; // Usa operador ternário para definir um valor padrão

// Valida se o código foi fornecido
if (empty($codigo)) {
    echo json_encode(['erro' => 'Código do aluno não fornecido']);
    exit();
}

// Prepara a consulta SQL para evitar injeção de SQL
$sql = "SELECT id, nome FROM cartoes WHERE info = ?";
$stmt = $conn->prepare($sql);

// Verifica se houve um erro na preparação da consulta
if (!$stmt) {
    echo json_encode(['erro' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit();
}

// Associa o parâmetro à consulta
$stmt->bind_param("s", $codigo);
$stmt->execute(); // Executa a consulta
$result = $stmt->get_result(); // Obtém o resultado da consulta

// Verifica se houve um erro ao obter o resultado
if ($result === false) {
    echo json_encode(['erro' => 'Erro na execução da consulta: ' . $stmt->error]);
    exit();
}

if ($result->num_rows > 0) {
    // Se o código do aluno foi encontrado, obtém o nome e ID
    $row = $result->fetch_assoc(); // Busca a linha do resultado
    $id_cartao = $row['id'];
    $nome = $row['nome'];
    $data_atual = date('Y-m-d H:i:s'); // Obtém a data e hora atual no fuso horário de Brasília
    
    // Insere o registro na tabela painel_adm
    $sql_insert = "INSERT INTO paineladm (datas, cartoes_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    // Verifica se houve um erro na preparação da consulta de inserção
    if (!$stmt_insert) {
        echo json_encode(['erro' => 'Erro na preparação da consulta de inserção: ' . $conn->error]);
        exit();
    }
    
    // Associa os parâmetros à consulta de inserção
    $stmt_insert->bind_param("si", $data_atual, $id_cartao);
    $stmt_insert->execute(); // Executa a consulta de inserção
    
    // Verifica se houve um erro na execução da consulta de inserção
    if ($stmt_insert->error) {
        echo json_encode(['erro' => 'Erro ao inserir no painel_adm: ' . $stmt_insert->error]);
        exit();
    }
    
    // Envia uma resposta JSON indicando que o aluno foi encontrado e inclui o nome
    echo json_encode(['existe' => true, 'nome' => $nome]);
    
    // Fecha a declaração preparada de inserção
    $stmt_insert->close();
} else {
    // Se o código do aluno não foi encontrado, envia uma resposta JSON indicando isso
    echo json_encode(['existe' => false]);
}

// Fecha a declaração preparada e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
