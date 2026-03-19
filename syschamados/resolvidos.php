<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

// Consulta para pegar chamados resolvidos
$consulta_resolvidos = "SELECT * FROM resolvidos WHERE mes_resolvido_r = MONTH(CURRENT_DATE())";
$con_resolvidos = $mysqli->query($consulta_resolvidos) or die($mysqli->error);

// Verifica se há chamados resolvidos
$tem_chamados_resolvidos = $con_resolvidos->num_rows > 0;

// Contagem de chamados resolvidos no mês atual
$queryCountResolvidos = "SELECT COUNT(*) as totalResolvidos FROM resolvidos WHERE mes_resolvido_r = MONTH(CURRENT_DATE())";
$resultCountResolvidos = $mysqli->query($queryCountResolvidos);
$rowCountResolvidos = $resultCountResolvidos->fetch_assoc();
$totalResolvidos = $rowCountResolvidos['totalResolvidos'];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Painel Resolvidos</title>
</head>

<body>
    <header>
        <img class="logo" src="images/logoWhite.png">
        <div class="d-flex flex-column align-items-end position-relative">
            <?php echo "<h6 class='me-3'>Usuário Logado: <u>" . $_SESSION['login'] . "</u></h6>"; ?>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Menu
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="home.php"><img src="images/house.svg"> Home</a></li>
                    <li><a class="dropdown-item" href="historico.php"><img src="images/clock-history.svg"> Histórico</a></li>
                    <li><a class="dropdown-item" href="gerenciar_usuarios.php"><img src="images/people.svg"> Gerenciar Usuários</a></li>
                    <li><a class="dropdown-item" href="admin.php"><img src="images/refresh.svg"> Atualizar</a></li>
                    <li><a class="dropdown-item" href="admin.php"><img src="images/person-gear.svg"> Admin</a></li>
                    <li><a class="dropdown-item text-danger" href="?logout"><img src="images/box-arrow-right.svg"> Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <h2 class="text-center">Painel Admin</h2>
        <h4 class="text-center">Chamados Resolvidos (Mês Atual)</h4>
        <?php if ($tem_chamados_resolvidos) { ?>
            <table class="table tabela-arredondada" style="margin:0 auto;">
                <tr class="text-center align-middle">
                    <td class="p-3 mb-2 bg-dark text-white"><b>ID Chamado</b></td>
                    <td class="p-3 mb-2 bg-dark text-white"><b>Problema</b></td>
                    <td class="p-3 mb-2 bg-dark text-white"><b>Usuário</b></td>
                    <td class="p-3 mb-2 bg-dark text-white"><b>Nome</b></td>
                    <td class="p-3 mb-2 bg-dark text-white"><b>Comentário</b></td>
                    <td class="p-3 mb-2 bg-dark text-white"><b>Data e Hora do Registro</b></td>
                </tr>
                <?php while ($dado = $con_resolvidos->fetch_array()) { ?>
                    <tr>
                        <td class="p-3 mb-2"><?php echo str_pad($dado["id"], 4, '0', STR_PAD_LEFT); ?></td>
                        <td class="p-3 mb-2"><?php echo $dado["problema_r"]; ?></td>
                        <td class="p-3 mb-2"><?php echo $dado["setor_r"]; ?></td>
                        <td class="p-3 mb-2"><?php echo $dado["nome_r"]; ?></td>
                        <td class="p-3 mb-2"><?php echo $dado["comentario_r"]; ?></td>
                        <td class="p-3 mb-2"><?php echo date('d/m/Y H:i:s', strtotime($dado["momento_registro_r"])); ?></td>
                    </tr>
                <?php } ?>
            </table>
            <h6 class="totalregistro">Total de Registros: <?php echo $totalResolvidos; ?></h6>
        <?php } else { ?>
            <h4 class="text-center mt-3">Nenhum chamado resolvido neste mês.</h4>
        <?php } ?>
    </div>
</body>

</html>