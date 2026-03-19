<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

$consulta = "SELECT * FROM chamados";
$consulta_r = "SELECT * FROM resolvidos ORDER BY id DESC";
$con = $mysqli->query($consulta) or die($mysqli->error);
$con_r = $mysqli->query($consulta_r) or die($mysqli->error);

if (isset($_POST['change_one'])) {
    $id_chamado = $_POST['id_chamado'];
    $sql = $mysqli->prepare("
    INSERT INTO `resolvidos` 
        (id, problema_r, setor_r, nome_r, comentario_r, momento_registro_r, mes_resolvido_r) 
    SELECT id, problema, setor, nome, comentario, momento_registro, mes_resolvido 
    FROM chamados 
    WHERE id = ?
    ");
    $sql->bind_param("i", $id_chamado);
    $sql->execute();
    $sql_delete = $mysqli->prepare("DELETE FROM chamados WHERE id = ?");
    $sql_delete->bind_param("i", $id_chamado);
    $sql_delete->execute();
    header('Location: admin.php');
}

// Verifica se há chamados em aberto
$tem_chamados_abertos = $con->num_rows > 0;

// Contagem de chamados em aberto
$queryCountChamados = "SELECT COUNT(*) as totalChamados FROM chamados";
$resultCountChamados = $mysqli->query($queryCountChamados);
$rowCountChamados = $resultCountChamados->fetch_assoc();
$totalChamados = $rowCountChamados['totalChamados'];
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
    <title>Painel Admin</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Solicita permissão para notificação
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
        });
    </script>
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
                    <li><a class="dropdown-item" href="resolvidos.php"><img src="images/check-square.svg"> Resolvidos</a></li>
                    <li><a class="dropdown-item" href="historico.php"><img src="images/clock-history.svg"> Histórico</a></li>
                    <li><a class="dropdown-item" href="gerenciar_usuarios.php"><img src="images/people.svg"> Gerenciar Usuários</a></li>
                    <li><a class="dropdown-item" href="admin.php"><img src="images/refresh.svg"> Atualizar</a></li>
                    <li><a class="dropdown-item text-danger" href="?logout"><img src="images/box-arrow-right.svg"> Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <h2 class="text-center">Painel Admin</h2>
        <!-- Chamados em aberto -->
        <div class="tab-pane fade show active" id="aberto" role="tabpanel" aria-labelledby="aberto-tab">
            <h4 class="text-center rounded mt-3">Em aberto:</h4>
            <?php if ($tem_chamados_abertos) { ?>
                <table class="table tabela-arredondada" style="margin:0 auto;">
                    <tr class="text-center align-middle">
                        <td class="p-3 mb-2 bg-danger text-white"><b>ID Chamado</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Problema</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Usuário</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Nome</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Comentário</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Data e Hora do Registro</b></td>
                        <td class="p-3 mb-2 bg-danger text-white"><b>Ação</b></td>
                    </tr>
                    <?php while ($dado = $con->fetch_array()) { ?>
                        <tr>
                            <td class="p-3 mb-2"><?php echo str_pad($dado["id"], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="p-3 mb-2"><?php echo $dado["problema"]; ?></td>
                            <td class="p-3 mb-2"><?php echo $dado["setor"]; ?></td>
                            <td class="p-3 mb-2"><?php echo $dado["nome"]; ?></td>
                            <td class="p-3 mb-2"><?php echo $dado["comentario"]; ?></td>
                            <td class="p-3 mb-2"><?php echo date('d/m/Y H:i:s', strtotime($dado["momento_registro"])); ?></td>
                            <td class="p-3 mb-2">
                                <form method="POST" class="text-center">
                                    <input type="hidden" name="id_chamado" value="<?php echo $dado['id']; ?>">
                                    <button type="submit" style="background: none; border: none; color:#2e8b57; text-decoration:underline;" name="change_one"><b>Mover para "Resolvidos"</b></button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <h6 class="totalregistro">Total de Registros: <?php echo $totalChamados; ?></h6>
            <?php } else { ?>
                <h5 class="text-center mt-3">Nenhum chamado em aberto no momento.</h5>
            <?php } ?>
        </div>
    </div>

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
                    <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function moveParaHistorico() {
            window.location.href = 'moverbd.php';
        }
    </script>

    <script>
        document.getElementById('imprimirTabela').addEventListener('click', function() {
            var tabelaResolvidos = document.getElementById('tabelaResolvidos').outerHTML;
            var novaJanela = window.open('', '', 'width=800,height=600');
            novaJanela.document.write('<html><head><title>Tabela Resolvidos</title>');
            novaJanela.document.write('<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">');
            novaJanela.document.write('</head><body>');
            novaJanela.document.write('<h1 class="text-center">Tabela de Chamados Resolvidos</h1>');
            novaJanela.document.write(tabelaResolvidos);
            novaJanela.document.write('</body></html>');
            novaJanela.document.close();
            novaJanela.print();
        });
    </script>

    <script>
        // Função para atualizar a página a cada 30 segundos
        setInterval(function() {
            location.reload();
        }, 30000); // 30000 milissegundos = 30 segundos
    </script>

    <script>
        function atualizarHoraBrasilia() {
            // Obter a data e hora atual ajustada para o fuso horário de Brasília (GMT-3)
            var options = {
                timeZone: 'America/Sao_Paulo',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            };
            var formatter = new Intl.DateTimeFormat([], options);
            var dataHoraBrasilia = formatter.format(new Date());

            // Exibir a data e a hora no elemento HTML
            document.getElementById('dataHoraBrasilia').innerHTML = dataHoraBrasilia;
        }

        // Atualizar a hora a cada segundo
        setInterval(atualizarHoraBrasilia, 1000);

        // Atualizar imediatamente ao carregar a página
        atualizarHoraBrasilia();
    </script>
    <div id="dataHoraBrasilia"></div>

    <script>
        let ultimaContagem = null;
        let primeiraExecucao = true;

        if (Notification.permission !== "granted") {
            Notification.requestPermission().then(function(permission) {
                console.log("Permissão de notificação:", permission);
                if (permission === "granted") {
                    verificarNovosChamados();
                }
            });
        } else {
            verificarNovosChamados();
        }

        function verificarNovosChamados() {
            fetch('verificar_chamados.php?nocache=' + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    console.log("Dados recebidos:", data);

                    if (!data || typeof data.totalChamados === 'undefined') {
                        console.warn("Resposta inesperada:", data);
                        return;
                    }

                    console.log("Chamados atuais:", data.totalChamados, "Último conhecido:", ultimaContagem);

                    if (primeiraExecucao) {
                        ultimaContagem = data.totalChamados;
                        primeiraExecucao = false;
                        console.log("Inicialização completa, contagem registrada:", ultimaContagem);
                        return;
                    }

                    if (data.totalChamados > ultimaContagem) {
                        ultimaContagem = data.totalChamados;
                        console.log("Nova notificação será exibida");

                        if (Notification.permission === "granted") {
                            new Notification("Novo chamado recebido!", {
                                body: "Um novo chamado foi aberto.",
                                icon: "favicon.ico"
                            });
                        }

                        const alerta = document.getElementById("alertaChamado");
                        if (alerta) {
                            alerta.style.display = "block";
                            setTimeout(() => alerta.style.display = "none", 5000);
                            alerta.onclick = () => alerta.style.display = "none";
                        }
                    }
                })
                .catch(error => console.error("Erro na verificação de chamados:", error))
                .finally(() => {
                    setTimeout(verificarNovosChamados, 5000);
                });
        }
    </script>
   <?php
    $sql = "SELECT * from chamados";
    if ($result = mysqli_query($mysqli, $sql)) {
        $rowcount = mysqli_num_rows($result);
        // Display result
        if ($rowcount !== 0) {
            echo
            '<audio id="audio" autoplay controls>
            <source src="som2.mp3" type="audio/mp3">
            </audio>';
        }
    }
    ?>
</body>
<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>