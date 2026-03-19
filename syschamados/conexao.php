<?php
session_start();

date_default_timezone_set('America/Sao_Paulo');

$usuario = 'root';
$senha = '';
$database = 'syschamados';
$host = 'localhost';

$mysqli = new mysqli($host, $usuario, $senha, $database);


if ($mysqli->connect_error) {
    die("Falha ao conectar ao banco de dados: " . $mysqli->connect_error);
}

mysqli_set_charset($mysqli, "utf8mb4");

// Verifica se a coluna 'mes_resolvido' existe na tabela 'chamados'
$result = $mysqli->query("SHOW COLUMNS FROM chamados LIKE 'mes_resolvido'");
if ($result->num_rows == 0) {
    // Se a coluna não existe, adiciona ela à tabela 'chamados'
    $sql = "ALTER TABLE chamados ADD COLUMN mes_resolvido INT";
    if ($mysqli->query($sql) !== TRUE) {
        echo "Erro ao adicionar a coluna 'mes_resolvido': " . $mysqli->error;
    }
}

// Verifica se houve envio de dados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
    // Verifica se os campos necessários foram enviados
    if (isset($_POST['problema']) && isset($_POST['nome'])) {
        $problema = $_POST['problema'];
        $setor = $_SESSION['login'];
        $nome = $_POST['nome'];
        $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';
        $momento_registro = date('Y-m-d H:i:s');
        $mes_resolvido = date('n');

        $sql = $mysqli->prepare("INSERT INTO chamados (problema, setor, nome, comentario, momento_registro, mes_resolvido) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssss", $problema, $setor, $nome, $comentario, $momento_registro, $mes_resolvido);
        if (!$sql->execute()) {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var modal = new bootstrap.Modal(document.getElementById("modalErro"));
                        modal.show();
                    });
                  </script>';
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var modal = new bootstrap.Modal(document.getElementById("modalSucesso"));
                        modal.show();
                    });
                  </script>';
        }
    }
}

// Recupera o tipo de permissão do usuário atual
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $query = "SELECT tipo_permissao FROM usuarios WHERE login = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['tipo_permissao'] = $row['tipo_permissao'];
    }
}

// Lógica para logout
if (isset($_GET['logout'])) {
    echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var modal = new bootstrap.Modal(document.getElementById("modalLogout"));
                        modal.show();
                    });
                  </script>';
}
?>
