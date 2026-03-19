<?php
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    http_response_code(403); // Acesso negado
    echo json_encode(['error' => 'Usuário não logado']);
    exit();
}

$login = $_SESSION['login'];

// Consulta todos os IDs resolvidos para o usuário (ordenados por ID para consistência)
$query = "SELECT id FROM resolvidos WHERE setor_r = ? ORDER BY id ASC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

$ids = [];
while ($row = $result->fetch_assoc()) {
    $ids[] = (int)$row['id'];
}

// Retorna JSON com a lista de IDs e o total
header('Content-Type: application/json');
echo json_encode([
    'totalResolvidos' => count($ids),
    'ids' => $ids // Lista de todos os IDs resolvidos
]);
?>