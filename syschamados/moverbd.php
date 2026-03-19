<?php
include('conexao.php');

// Obtém o ano e mês atuais
$anoMes = date('mY');
$nomeTabela = "mes_" . $anoMes;

// Cria uma nova tabela se ela não existir
$queryCriarTabela = "CREATE TABLE IF NOT EXISTS $nomeTabela LIKE resolvidos";
$mysqli->query($queryCriarTabela) or die($mysqli->error);

// Verifica se há dados duplicados antes de inserir na nova tabela
$queryVerificarDuplicatas = "SELECT * FROM resolvidos";
$resultado = $mysqli->query($queryVerificarDuplicatas);

if ($resultado->num_rows > 0) {
    // Move os dados da tabela 'resolvidos' para a nova tabela
    $queryMoveDados = "INSERT INTO $nomeTabela (id, problema_r, setor_r, nome_r, comentario_r, momento_registro_r, mes_resolvido_r) SELECT id, problema_r, setor_r, nome_r, comentario_r, momento_registro_r, mes_resolvido_r FROM resolvidos";
    $mysqli->query($queryMoveDados) or die($mysqli->error);

    // Limpa a tabela 'resolvidos'
    $queryLimparResolvidos = "TRUNCATE TABLE resolvidos";
    $mysqli->query($queryLimparResolvidos) or die($mysqli->error);
}

// Fecha a conexão com o banco de dados
$mysqli->close();

// Redireciona para admin.php
header('Location: admin.php');
?>
