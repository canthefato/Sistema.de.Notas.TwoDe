<?php
include 'backend/conexao.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {


    if(empty($_POST['usuario']) || empty($_POST['senha'])) {
        $_SESSION['login_erro'] = "Preencha todos os campos!";
        header('Location: login.php');
        exit;
    }

    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);

    $query = "SELECT * FROM usuarios WHERE usuario = '{$usuario}' AND senha = md5('{$senha}')";
    $result = mysqli_query($conn, $query);
    $row = mysqli_num_rows($result);

    if($row == 1){
        $_SESSION['usuario_logado'] = $usuario;
        header('Location: painel.php');
        exit;
    } else {
        $_SESSION['login_erro'] = "Usuário ou senha incorretos!";
        header('Location: login.php');
        exit;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Login - Sistema de Notas</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Login</h1>

    <form method="POST" action="login.php">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit" name="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="cadastrar.php">Cadastre-se aqui</a></p>
</div>
</body>
</html>