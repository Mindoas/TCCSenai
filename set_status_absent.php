<?php
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

// Atualiza a flag de status para 0 (Ausente) para todos os registros
$updateSql = "UPDATE cartoes SET flag = 0 WHERE flag != 0";
if ($conn->query($updateSql) === TRUE) {
    echo "Status atualizado com sucesso para 'Ausente'.";
} else {
    echo "Erro ao atualizar status: " . $conn->error;
}

// Deleta todos os registros da tabela paineladm
$deleteSql = "DELETE FROM paineladm";
if ($conn->query($deleteSql) === TRUE) {
    echo "Registros deletados com sucesso.";
} else {
    echo "Erro ao deletar registros: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();

// Redireciona de volta para a página inicial ou onde preferir
header('Location: indexAdmin.html');
exit();
?>
