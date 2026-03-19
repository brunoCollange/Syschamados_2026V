<?php
date_default_timezone_set('America/Sao_Paulo');
include('conexao.php');

$erroLogin = '';

if (isset($_SESSION['login'])) {
    header('Location: home.php');
    exit();
}

if (isset($_POST['acao'])) {
    $loginForm = $_POST['login'];
    $senhaForm = $_POST['senha'];

    // Consulta SQL para verificar se há um usuário com o login fornecido
    $sql = "SELECT senha FROM usuarios WHERE login = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $loginForm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Usuário encontrado, verificar senha
        $usuario = $result->fetch_assoc();
        $senhaHash = $usuario['senha'];

        // Verificar se a senha fornecida corresponde ao hash armazenado
        if (password_verify($senhaForm, $senhaHash)) {
            $_SESSION['login'] = $loginForm;
            header('Location: admin.php');
            exit();
        } else {
            $erroLogin = "Usuário ou Senha inválidos, tente novamente.";
        }
    } else {
        $erroLogin = "Usuário ou Senha inválidos, tente novamente.";
    }
}

include('login.php');
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
    <title>SysChamados</title>
</head>

</html>