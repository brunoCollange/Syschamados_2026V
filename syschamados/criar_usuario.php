<?php
include('conexao.php');

// Condição para que apenas login de admin acesse a página
if ($_SESSION['tipo_permissao'] != 'admin') {
    header('Location: home.php');
    exit();
}

// Verificar se o formulário foi enviado
// Verificar se o formulário foi enviado
if (isset($_POST['criar'])) {
    $login = trim($_POST['login']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $tipo_permissao = $_POST['tipo_permissao'];

    // Validação de confirmação de senha
    if ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        // Verificar se o login já existe
        $sql = "SELECT * FROM usuarios WHERE login = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            $erro = "Erro na consulta: " . $mysqli->error;
        } else {
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Se o login já existir, exibir mensagem de erro
                $erro = "Usuário já existe. Por favor, escolha outro login.";
            } else {
                // Gerar hash seguro da senha
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // Inserir novo usuário no banco de dados
                $sql = "INSERT INTO usuarios (login, senha, tipo_permissao) VALUES (?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    $erro = "Erro na inserção: " . $mysqli->error;
                } else {
                    $stmt->bind_param("sss", $login, $senhaHash, $tipo_permissao);

                    if ($stmt->execute()) {
                        header('Location: gerenciar_usuarios.php');
                        exit();
                    } else {
                        $erro = "Erro ao criar usuário: " . $mysqli->error;
                    }
                }
            }
            $stmt->close();
        }
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
    <title>Criar Usuário</title>
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


    <div class="container mt-5">
        <h2>Criar Novo Usuário</h2>
        <?php
        if (isset($erro)) {
            echo "<div class='alert alert-danger'>$erro</div>";
        }
        ?>
        <form method="post">
            <div class="mb-3">
                <label for="login" class="form-label">Login:</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label">Confirmar Senha:</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>

            <div class="mb-3">
                <label for="tipo_permissao" class="form-label">Tipo de Permissão:</label>
                <select class="form-select" id="tipo_permissao" name="tipo_permissao" required>
                    <option value="usuario">Usuário</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="criar">Criar Usuário</button>
            <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>