<?php
include('conexao.php');
unset($_SESSION['login']);
session_destroy();
header('Location:index.php');
exit();
?>
