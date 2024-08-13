<?php
// Conexão com o banco de dados (ajuste conforme necessário)
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "meu_banco_frequencia";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para salvar o cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card-name']) && isset($_POST['card-info'])) {
    $nome = $_POST['card-name'];
    $info = $_POST['card-info'];

    $stmt = $pdo->prepare("INSERT INTO cards (nome, info) VALUES (:nome, :info)");
    if ($stmt->execute([':nome' => $nome, ':info' => $info])) {
        echo json_encode(['status' => 'success', 'message' => 'Cartão salvo com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar o cartão.']);
    }
    exit;
}

// Função para carregar os cartões
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM cards");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cards);
    exit;
}

// Função para excluir um cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM cards WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        echo json_encode(['status' => 'success', 'message' => 'Cartão excluído com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o cartão.']);
    }
    exit;
}
?>
