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

// Consulta para obter todos os registros
$sql = "SELECT * FROM mensagens ORDER BY data DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Texto</th>
                <th>Data</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . htmlspecialchars($row["texto"]) . "</td>
                <td>" . $row["data"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhuma mensagem encontrada.";
}

// Fecha a conexão
$conn->close();
?>
