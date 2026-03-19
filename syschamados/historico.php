<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

// Função para exibir os chamados de um determinado mês
function exibirChamadosMes($mes, $ano, $conexao, &$registrosEncontrados)
{
    $nomeTabela = "mes_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . $ano;
    $consultaMes = "SELECT * FROM $nomeTabela ORDER BY id ASC"; // Ordenar por ID em ordem crescente
    $result = $conexao->query($consultaMes) or die($conexao->error);

    $tableID = "tabela_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . $ano; // Identificador único para cada tabela, baseado no mês e ano

    if ($result->num_rows > 0) {
        $registrosEncontrados = true; // Marca que foi encontrado pelo menos um registro
        echo '<div class="content" style="margin: 0; padding: 0;">
                <table id="' . $tableID . '" class="table w-100" style="margin:0;">
                <tr class="text-center align-middle">
                    <td class="border border, p-3 mb-2 bg-dark text-white">ID Chamado</td>
                    <td class="border border, p-3 mb-2 bg-dark text-white">Problema</td>
                    <td class="border border, p-3 mb-2 bg-dark text-white">Usuário</td>
                    <td class="border border, p-3 mb-2 bg-dark text-white">Nome</td>
                    <td class="border border, p-3 mb-2 bg-dark text-white">Comentário</td>
                    <td class="border border, p-3 mb-2 bg-dark text-white">Data e Hora do Registro</td>
                </tr>';
        while ($dado = $result->fetch_array()) {
            echo '<tr>
                    <td>' . str_pad($dado["id"], 5, '0', STR_PAD_LEFT) . '</td>
                    <td>' . $dado["problema_r"] . '</td>    
                    <td>' . $dado["setor_r"] . '</td>
                    <td>' . $dado["nome_r"] . '</td>
                    <td>' . $dado["comentario_r"] . '</td>
                    <td>' . date('d/m/Y H:i:s', strtotime($dado["momento_registro_r"])) . '</td>
                </tr>';
        }
        echo '</table></div>';
    }

    return $tableID; // Retorna o ID da tabela para ser usado no botão de impressão
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico</title>
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
                    <li><a class="dropdown-item" href="admin.php"><img src="images/person-gear.svg"> Admin</a></li>
                    <li><a class="dropdown-item" href="home.php"><img src="images/house.svg"> Home</a></li>
                    <li><a class="dropdown-item text-danger" href="?logout"><img src="images/box-arrow-right.svg"> Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="text-center">
        <?php
        $registrosEncontrados = false;

        // Obtém os meses disponíveis no banco de dados
        $consulta = "SHOW TABLES LIKE 'mes_%'";
        $result = $mysqli->query($consulta);

        $anosMeses = [];

        while ($row = $result->fetch_array()) {
            $nomeTabela = $row[0];
            $ano = substr($nomeTabela, -4); // Obtém o ano dos nomes das tabelas
            $mes = substr($nomeTabela, 4, 2);

            $anosMeses[] = ["ano" => $ano, "mes" => $mes];
        }

        // Ordena os anos e meses
        usort($anosMeses, function ($a, $b) {
            return ($a['ano'] == $b['ano']) ? ($a['mes'] - $b['mes']) : ($a['ano'] - $b['ano']);
        });

        echo '<div class="container-fluid px-4">
        <div class="row g-4 justify-content-center">';

        foreach ($anosMeses as $anoMes) {
            $ano = $anoMes['ano'];
            $mes = $anoMes['mes'];

            $collapseId = 'collapse_' . $mes . $ano;
            $tableID = 'tabela_' . str_pad($mes, 2, '0', STR_PAD_LEFT) . $ano;

            echo '
            <div class="col-12 col-md-6">

                <div class="card shadow-sm">
                    <div class="card-body text-center">

                        <button 
                            class="btn btn-dark w-100 mb-2"
                            data-bs-toggle="collapse"
                            data-bs-target="#' . $collapseId . '">
                            Registro Chamados ' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '/' . $ano . '
                        </button>

                        <button 
                            class="btn btn-outline-dark w-100"
                            onclick="imprimirTabela(\'#' . $tableID . '\')">
                            Imprimir <img src="images/printer-fill.svg">
                        </button>

                    </div>
                </div>

                <div class="collapse mt-3" id="' . $collapseId . '">
                    <div class="card card-body">';

            exibirChamadosMes($mes, $ano, $mysqli, $registrosEncontrados);

            echo '  </div>
                </div>

            </div>';
        }
        echo '</div></div>';

        if (!$registrosEncontrados) {
            echo '<div class="content">
                    <h4 class="text-center m-5">Não há histórico para mostrar no momento.</h4>
                  </div>';
        }
        ?>
    </div>

    <script>
        function imprimirTabela(idTabela) {
            var tabela = document.querySelector(idTabela);
            var janelaDeImpressao = window.open('', '', 'width=1000, height=800');
            janelaDeImpressao.document.open();
            janelaDeImpressao.document.write('<html><head><title>&nbsp</title>');
            janelaDeImpressao.document.write('<style>');
            janelaDeImpressao.document.write('@media print { body { width: 100%; margin: 0; padding: 0; } .content { width: 100%; margin: 0 auto; padding: 0; max-width: 100%; overflow-x: auto; } table { page-break-inside: avoid; font-size: 10px; border-collapse: collapse; width: 95%; margin: 0 auto; } th, td { padding: 8px; border: 1px solid black; text-align: center; } .titulo { display: table-caption; caption-side: top; text-align: center; font-size: 18px; margin-bottom: 20px; } @page { size: A4; margin: 20mm 15mm; } }');
            janelaDeImpressao.document.write('</style>');
            janelaDeImpressao.document.write('</head><body>');
            janelaDeImpressao.document.write('<table>' + tabela.innerHTML + '</table>');
            janelaDeImpressao.document.write('</body></html>');
            janelaDeImpressao.document.close();
            janelaDeImpressao.print();
            janelaDeImpressao.close();
        }
    </script>

    <!-- Modal de Confirmação de Logout -->
    <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogoutLabel">Encerrar Sessão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <b>Você tem certeza que deseja sair?</b>
                </div>
                <div class="modal-footer">
                    <a href="historico.php" class="btn btn-secondary">Cancelar</a>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </div>
            </div>
        </div>
    </div>

</body>

<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>