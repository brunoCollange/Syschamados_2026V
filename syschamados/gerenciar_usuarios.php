<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

// Consultar usuários no banco de dados
$sql = "SELECT * FROM usuarios";
$resultado = $mysqli->query($sql);

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
    <title>Gerenciar Usuários</title>
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
                    <li><a class="dropdown-item" href="admin.php"><img src="images/arrow-return-left.svg"> Voltar</a></li>
                    <li><a class="dropdown-item text-danger" href="?logout"><img src="images/box-arrow-right.svg"> Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <h2>Lista de Usuários</h2>
            </div>
            <div class="col">
                <div style="text-align:end;">
                    <a class="btn btn-success" href="criar_usuario.php"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                        </svg> Novo Usuário</a><br>
                </div>
            </div>
        </div>

        <table class="table tabela-arredondada text-center"><br>
            <thead>
                <tr class="text-center align-middle">
                    <th class="p-2 bg-dark text-light">Login</th>
                    <th class="p-2 bg-dark text-light">Permissão</th>
                    <th class="p-2 bg-dark text-light">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $resultado->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $usuario['login']; ?></td>
                        <td><?php echo $usuario['tipo_permissao']; ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn text-warning effect-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                </svg>
                            </a>

                            <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn text-danger effect-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
                    <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
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