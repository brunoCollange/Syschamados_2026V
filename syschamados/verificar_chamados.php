<?php
include('conexao.php');

$query = "SELECT COUNT(*) as totalChamados FROM chamados";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();

echo json_encode(['totalChamados' => $row['totalChamados']]);
