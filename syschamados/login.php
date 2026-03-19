<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Login</title>
</head>

<body>
    <header>
        <img class="logo" src="images/logoWhite.png">
        <h2 class="m-2">SysChamados</h2>
    </header>

    <?php if (!empty($erroLogin)) : ?>
        <div class="alert alert-danger text-center invaliduser" role="alert">
            <?php echo $erroLogin; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="painelogin">
        <img src="images/user.png"><br>
        <input class="loginput" type="text" name="login" placeholder="Digite seu Login" required><br>
        <div class="password-container">
            <input
                class="loginput"
                type="password"
                name="senha"
                id="senha"
                placeholder="Digite sua Senha"
                required>

            <!-- Olho que mostra a senha digitada (está desativado porque as senhas são criadas pelo TI 
            e não podem ser divulgadas, basta remover o comentário para ativar novamente).
                <i
                class="bi bi-eye-fill password-toggle"
                onclick="toggleSenha()"
                aria-label="Mostrar senha"></i> -->
        </div>

        <input class="submit btn btn-success" type="submit" name="acao" value="Entrar">
    </form>

    <script>
        function toggleSenha() {
            const input = document.getElementById('senha');
            const icon = document.querySelector('.password-toggle');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        }
    </script>

</body>
<footer class="loginfooter">
    <p>Developed by &copy;Bruno Collange - V25.1</p>
</footer>