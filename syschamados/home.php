<?php
include('conexao.php');
date_default_timezone_set('America/Sao_Paulo');

$consulta = "SELECT * FROM chamados";
$consulta_r = "SELECT * FROM resolvidos ORDER BY id DESC";
$con = $mysqli->query($consulta) or die($mysqli->error);
$con_r = $mysqli->query($consulta_r) or die($mysqli->error);

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

$login = $_SESSION['login'];

// Consulta para pegar os chamados em aberto do usuário logado
$queryChamados = "SELECT * FROM chamados WHERE setor = ?";
$stmtChamados = $mysqli->prepare($queryChamados);
$stmtChamados->bind_param("s", $login);
$stmtChamados->execute();
$resultChamados = $stmtChamados->get_result();

// Consulta para pegar os chamados resolvidos do usuário logado
$queryResolvidos = "SELECT * FROM resolvidos WHERE setor_r = ?";
$stmtResolvidos = $mysqli->prepare($queryResolvidos);
$stmtResolvidos->bind_param("s", $login);
$stmtResolvidos->execute();
$resultResolvidos = $stmtResolvidos->get_result();

// Função para exibir os chamados de um determinado mês do usuário logado
function exibirChamadosMes($mes, $ano, $login, $conexao)
{
    $nomeTabela = "mes_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . $ano;
    $consultaMes = "SELECT * FROM $nomeTabela WHERE setor_r = ? ORDER BY id ASC";
    $stmtMes = $conexao->prepare($consultaMes);
    $stmtMes->bind_param("s", $login);
    $stmtMes->execute();
    $result = $stmtMes->get_result();

    $tableID = "tabela_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . $ano; // Identificador único para cada tabela, baseado no mês e ano

    echo '<table id="' . $tableID . '" class="table tabela-arredondada">
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
    echo '</table>';

    return $tableID;
}

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

// Verifica se há chamados em aberto para o usuário atual
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $queryChamados = "SELECT * FROM chamados WHERE setor = ?";
    $stmtChamados = $mysqli->prepare($queryChamados);
    $stmtChamados->bind_param("s", $login);
    $stmtChamados->execute();
    $resultChamados = $stmtChamados->get_result();
    $tem_chamados_abertos = $resultChamados->num_rows > 0;
}

// Verifica se há chamados resolvidos para o usuário atual
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $queryResolvidos = "SELECT * FROM resolvidos WHERE setor_r = ?";
    $stmtResolvidos = $mysqli->prepare($queryResolvidos);
    $stmtResolvidos->bind_param("s", $login);
    $stmtResolvidos->execute();
    $resultResolvidos = $stmtResolvidos->get_result();
    $tem_chamados_resolvidos = $resultResolvidos->num_rows > 0;
}

// Contagem de chamados em aberto do usuário logado
$queryCountChamados = "SELECT COUNT(*) as totalChamados FROM chamados WHERE setor = ?";
$stmtCountChamados = $mysqli->prepare($queryCountChamados);
$stmtCountChamados->bind_param("s", $login);
$stmtCountChamados->execute();
$resultCountChamados = $stmtCountChamados->get_result();
$rowCountChamados = $resultCountChamados->fetch_assoc();
$totalChamados = $rowCountChamados['totalChamados'];

// Contagem de chamados resolvidos do usuário logado
$queryCountResolvidos = "SELECT COUNT(*) as totalResolvidos FROM resolvidos WHERE setor_r = ?";
$stmtCountResolvidos = $mysqli->prepare($queryCountResolvidos);
$stmtCountResolvidos->bind_param("s", $login);
$stmtCountResolvidos->execute();
$resultCountResolvidos = $stmtCountResolvidos->get_result();
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
    <title>Home</title>
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
                    <?php if ($_SESSION['tipo_permissao'] == 'admin') { ?>
                        <li><a class="dropdown-item" href="admin.php"><img src="images/person-gear.svg"> Admin</a></li>
                        <li><a class="dropdown-item" href="historico.php"><img src="images/clock-history.svg"> Histórico</a></li>
                        <li><a class="dropdown-item" href="gerenciar_usuarios.php"><img src="images/people.svg"> Gerenciar Usuários</a></li>
                    <?php } ?>

                    <li><a class="dropdown-item text-danger" href="?logout"><img src="images/box-arrow-right.svg"> Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="text-end">
            <a class="btn btn-success" href="home.php"><img src="images/refresh.svg"> Atualizar</a>
        </div>
        <ul class="nav nav-tabs border-dark" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="aberto-tab" data-bs-toggle="tab" data-bs-target="#aberto" type="button" role="tab" aria-controls="aberto" aria-selected="true"><b class="text-danger">Em Aberto</b></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="resolvido-tab" data-bs-toggle="tab" data-bs-target="#resolvido" type="button" role="tab" aria-controls="resolvido" aria-selected="false"><b class="text-success">Resolvidos (Mês Atual)</b></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="novo-tab" data-bs-toggle="tab" data-bs-target="#novo" type="button" role="tab" aria-controls="novo" aria-selected="false"><b class="text-primary">+ Novo Chamado</b></button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <!-- Ver chamados Em aberto -->
            <div class="tab-pane fade show active" id="aberto" role="tabpanel" aria-labelledby="aberto-tab">
                <h4 class="text-center mt-3"><b>Em Aberto:</b></h4>
                <?php if ($tem_chamados_abertos) { ?>
                    <div class="tale">
                        <table class="table tabela-arredondada" style="margin:0 auto;">
                            <tr class="text-center align-middle">
                                <td class="p-3 mb-2 bg-dark text-white"><b>ID Chamado</b></td>
                                <td class="p-3 mb-2 bg-dark text-white"><b>Problema</b></td>
                                <td class="p-3 mb-2 bg-dark text-white"><b>Nome</b></td>
                                <td class="p-3 mb-2 bg-dark text-white"><b>Comentário</b></td>
                                <td class="p-3 mb-2 bg-dark text-white"><b>Data e Hora do Registro</b></td>
                            </tr>
                            <?php while ($dado = $resultChamados->fetch_assoc()) { ?>
                                <tr>
                                    <td class="p-3 mb-2"><?php echo str_pad($dado["id"], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3 mb-2"><?php echo $dado["problema"]; ?></td>
                                    <td class="p-3 mb-2"><?php echo $dado["nome"]; ?></td>
                                    <td class="p-3 mb-2"><?php echo $dado["comentario"]; ?></td>
                                    <td class="p-3 mb-2"><?php echo date('d/m/Y H:i:s', strtotime($dado["momento_registro"])); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <h6 class="totalregistro">Total de Registros: <?php echo $totalChamados; ?></h6>
                <?php } else { ?>
                    <h5 class="text-center mt-3">Nenhum chamado em aberto no momento.</h5>
                <?php } ?>
                <br>
            </div>
            <!-- Ver chamados Resolvidos (Mês atual) -->
            <div class="tab-pane fade" id="resolvido" role="tabpanel" aria-labelledby="resolvido-tab">
                <h4 class="text-center mt-3"><b>Resolvidos (Mês Atual):</b></h4>
                <?php if ($tem_chamados_resolvidos) { ?>
                    <table class="table tabela-arredondada" style="margin:0 auto;">
                        <tr class="text-center align-middle">
                            <td class="p-3 mb-2 bg-dark text-white"><b>ID Chamado</b></td>
                            <td class="p-3 mb-2 bg-dark text-white"><b>Problema</b></td>
                            <td class="p-3 mb-2 bg-dark text-white"><b>Nome</b></td>
                            <td class="p-3 mb-2 bg-dark text-white"><b>Comentário</b></td>
                            <td class="p-3 mb-2 bg-dark text-white"><b>Data e Hora do Registro</b></td>
                        </tr>
                        <?php while ($dado = $resultResolvidos->fetch_assoc()) { ?>
                            <tr>
                                <td class="p-3 mb-2"><?php echo str_pad($dado["id"], 5, '0', STR_PAD_LEFT); ?></td>
                                <td class="p-3 mb-2"><?php echo $dado["problema_r"]; ?></td>
                                <td class="p-3 mb-2"><?php echo $dado["nome_r"]; ?></td>
                                <td class="p-3 mb-2"><?php echo $dado["comentario_r"]; ?></td>
                                <td class="p-3 mb-2"><?php echo date('d/m/Y H:i:s', strtotime($dado["momento_registro_r"])); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <h6 class="totalregistro">Total de Registros: <?php echo $totalResolvidos; ?></h6>
                <?php } else { ?>
                    <h5 class="text-center mt-3">Nenhum chamado resolvido no momento.</h5>
                <?php } ?>
                <br>
            </div>
            <!-- Abrir novo chamado -->
            <div class="tab-pane fade" id="novo" role="tabpanel" aria-labelledby="novo-tab">
                <div class="painelchamados">
                    <form method="post">
                        <div class="form-group">
                            <label for="problema">Problema: <b style="color: red;">*</b></label>
                            <textarea class="inputchamado" id="problema" type="text" name="problema" placeholder="Digite seu problema detalhadamente." required></textarea>
                        </div>
                        <input type="hidden" name="setor" value="<?php echo $_SESSION['login']; ?>">
                        <div class="form-group">
                            <label for="nome">Nome e Sobrenome: <b style="color: red;">*</b></label>
                            <input class="inputchamado" id="nome" type="text" name="nome" placeholder="Digite seu nome e sobrenome." required>
                        </div>
                        <div class="form-group">
                            <label for="comentario">Comentário:</label>
                            <textarea class="inputchamado" id="comentario" type="text" name="comentario" placeholder="Digite seu comentário."></textarea>
                        </div>
                        <input class="btn btn-success" name="acao" type="submit" style="padding: 5px 40px;">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Erro -->
    <div class="modal fade" id="modalErro" tabindex="-1" aria-labelledby="modalErroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalErroLabel">Erro ao enviar o chamado!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger">
                    <b>Houve um erro ao enviar o chamado. Por favor, tente novamente.</b>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Sucesso -->
    <div class="modal fade" id="modalSucesso" tabindex="-1" aria-labelledby="modalSucessoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSucessoLabel">Chamado enviado com sucesso!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-success">
                    <b>Seu chamado foi enviado com sucesso.</b>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
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
                    <a href="home.php" class="btn btn-secondary">Cancelar</a>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Solicita permissão para notificações se ainda não concedida
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
        });

        let idsConhecidos = new Set(); 
        let primeiraExecucao = true;

        if (Notification.permission !== "granted") {
            Notification.requestPermission().then(function(permission) {
                console.log("Permissão de notificação:", permission);
                if (permission === "granted") {
                    verificarNovosResolvidos();
                }
            });
        } else {
            verificarNovosResolvidos();
        }

        function verificarNovosResolvidos() {
            fetch('verificar_resolvidos.php?nocache=' + new Date().getTime())
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Dados recebidos (resolvidos):", data);

                    if (!data || typeof data.totalResolvidos === 'undefined' || !Array.isArray(data.ids)) {
                        console.warn("Resposta inesperada ou inválida:", data);
                        return;
                    }

                    console.log("IDs atuais:", data.ids, "IDs conhecidos:", Array.from(idsConhecidos));

                    if (primeiraExecucao) {
                        // Na primeira execução, registra todos os IDs conhecidos
                        data.ids.forEach(id => idsConhecidos.add(id));
                        primeiraExecucao = false;
                        console.log("Inicialização completa, IDs registrados:", Array.from(idsConhecidos));
                        return;
                    }

                    // Verifica quais IDs são novos (não conhecidos)
                    const novosIds = data.ids.filter(id => !idsConhecidos.has(id));

                    if (novosIds.length > 0) {
                        console.log("Novos IDs resolvidos encontrados:", novosIds);

                        // Notifica para cada novo ID (individual por chamado)
                        novosIds.forEach(id => {
                            if (Notification.permission === "granted") {
                                new Notification("Chamado Resolvido!", {
                                    body: `Seu chamado ID ${String(id).padStart(5, '0')} foi resolvido.`,
                                    icon: "favicon.ico"
                                });
                            }

                            const alerta = document.createElement("div");
                            alerta.style.cssText = "position:fixed;bottom:40px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:1000;cursor:pointer;";
                            alerta.innerHTML = `Seu chamado ID ${String(id).padStart(5, '0')} foi resolvido! Clique para fechar.`;
                            document.body.appendChild(alerta);
                            alerta.onclick = () => {
                                if (alerta.parentNode) alerta.parentNode.removeChild(alerta);
                            };
                        });

                        // Atualiza os IDs conhecidos
                        novosIds.forEach(id => idsConhecidos.add(id));
                    }
                })
                .catch(error => {
                    console.error("Erro na verificação de resolvidos:", error);
                })
                .finally(() => {
                    setTimeout(verificarNovosResolvidos, 5000); // Verifica a cada 5 segundos
                });
        }
    </script>

</body>

<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>