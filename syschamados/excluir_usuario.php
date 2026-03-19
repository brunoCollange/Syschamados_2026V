<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

// Verificar se o ID do usuário foi fornecido via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gerenciar_usuarios.php');
    exit();
}

$id = $_GET['id'];

// Consultar usuário pelo ID
$sql = "SELECT * FROM usuarios WHERE id = '$id'";
$resultado = $mysqli->query($sql);

if ($resultado->num_rows != 1) {
    header('Location: gerenciar_usuarios.php');
    exit();
}

$usuario = $resultado->fetch_assoc();

// Verificar se o formulário foi submetido
if (isset($_POST['excluir'])) {
    // Excluir usuário do banco de dados
    $sql = "DELETE FROM usuarios WHERE id = '$id'";
    if ($mysqli->query($sql)) {
        header('Location: gerenciar_usuarios.php');
        exit();
    } else {
        echo "Erro ao excluir usuário: " . $mysqli->error;
    }
}
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
    <title>Excluir Usuário</title>
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
                    <li><a class="dropdown-item" href="gerenciar_usuarios.php"><img src="images/arrow-return-left.svg"> Voltar</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="position-absolute top-50 start-50 translate-middle w-100" style="max-width: 500px;">
        <div class="container text-start p-4 bg-light border rounded shadow">
            <h2>Excluir Usuário</h2>
            <p>Você está prestes a excluir o usuário '<?php echo $usuario['login']; ?>'.</p>
            <p>Tem certeza de que deseja continuar?</p>
            <form method="post">
                <button type="submit" class="btn btn-danger" name="excluir">Sim, Excluir</button>
                <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

</body>

<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>