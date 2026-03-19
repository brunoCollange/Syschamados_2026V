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

// Verificar se o formulário foi enviado
if (isset($_POST['editar'])) {
    $login = $_POST['login'];
    $tipo_permissao = $_POST['tipo_permissao'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $senhas_nao_conferem = false; // Flag para controle do modal de erro
    $atualizacao_sucesso = false; // Flag para controle do modal de sucesso

    // Verificar se a senha foi preenchida e se a confirmação coincide
    if (!empty($senha)) {
        if ($senha === $confirmar_senha) {
            // Senha e confirmação coincidem, atualizar a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET login = '$login', tipo_permissao = '$tipo_permissao', senha = '$senhaHash' WHERE id = '$id'";
        } else {
            // Senhas não coincidem, define a flag
            $senhas_nao_conferem = true;
        }
    } else {
        // Se nenhuma senha foi fornecida, apenas atualize login e tipo de permissão
        $sql = "UPDATE usuarios SET login = '$login', tipo_permissao = '$tipo_permissao' WHERE id = '$id'";
    }

    if (!$senhas_nao_conferem && $mysqli->query($sql)) {
        $atualizacao_sucesso = true; // Atualização bem-sucedida, exibir modal de sucesso
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
    <title>Editar Usuário</title>
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

    <div class="container bg-light p-4 border rounded shadow text-start mt-4 mb-4" style="max-width: 500px;">
        <h4 class="mb-4 text-center"><b>Editar Usuário</b></h4>
        <form method="post">
            <div class="mb-3">
                <label for="login" class="form-label"><b>Login:</b></label>
                <input type="text" class="form-control rounded-3" id="login" name="login" value="<?php echo $usuario['login']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label"><b>Senha:</b></label>
                <input type="password" class="form-control rounded-3" id="senha" name="senha" placeholder="Deixe em branco para não alterar">
            </div>
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label"><b>Confirmar Senha:</b></label>
                <input type="password" class="form-control rounded-3" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a nova senha">
            </div>
            <div class="mb-3">
                <label for="tipo_permissao" class="form-label"><b>Permissão:</b></label>
                <select class="form-select rounded-3" id="tipo_permissao" name="tipo_permissao" required>
                    <option value="admin" <?php echo ($usuario['tipo_permissao'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="usuario" <?php echo ($usuario['tipo_permissao'] == 'usuario') ? 'selected' : ''; ?>>Usuário</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <a href="gerenciar_usuarios.php" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-success" name="editar">Salvar</button>
            </div>
        </form>
    </div>

    <!-- Modal de erro de confirmação de senha -->
    <div class="modal fade" id="senhaModal" tabindex="-1" aria-labelledby="senhaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="senhaModalLabel">Erro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    As senhas não coincidem. Por favor, tente novamente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de sucesso ao alterar user -->
    <div class="modal fade" id="sucessoModal" tabindex="-1" aria-labelledby="sucessoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sucessoModalLabel">Sucesso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    As alterações foram salvas com sucesso!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="confirmarSucessoBtn">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Verifica se a variável PHP está setada para exibir o modal de erro
        <?php if (isset($senhas_nao_conferem) && $senhas_nao_conferem): ?>
            var senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'), {});
            senhaModal.show();
        <?php endif; ?>

        // Verifica se houve sucesso na atualização e exibe o modal de confirmação
        <?php if (isset($atualizacao_sucesso) && $atualizacao_sucesso): ?>
            var sucessoModal = new bootstrap.Modal(document.getElementById('sucessoModal'), {});
            sucessoModal.show();

            // Redireciona para 'gerenciar_usuarios.php' quando o botão "Ok" for clicado
            document.getElementById('confirmarSucessoBtn').addEventListener('click', function() {
                window.location.href = 'gerenciar_usuarios.php';
            });
        <?php endif; ?>
    </script>

</body>

<footer>
    <p>Developed by &copy;Bruno Collange - V26.1</p>
</footer>

</html>