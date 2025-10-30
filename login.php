<?php
session_start();
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

<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #e3f0ff 0%, #f7faff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    color: #000;
  }

  .container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 40px;
    width: 360px;
    text-align: center;
    border: 1px solid #d0e3ff;
  }

  h1 {
    color: #003366;
    font-size: 22px;
    margin-bottom: 25px;
    border-bottom: 2px solid #0056b3;
    display: inline-block;
    padding-bottom: 5px;
  }

  form {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #003366;
  }

  input {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #aaa;
    margin-bottom: 15px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
  }

  input:focus {
    border-color: #0056b3;
    box-shadow: 0 0 4px rgba(0, 86, 179, 0.3);
  }

  button {
    background-color: #0056b3;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 5px;
  }

  button:hover {
    background-color: #003d80;
  }

  p {
    margin-top: 15px;
    font-size: 14px;
  }

  p a {
    color: #003366;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: 0.3s;
  }

  p a:hover {
    border-bottom: 1px solid #003366;
  }

  .message {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
  }

  .error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
</style>
</head>

<body>
  <div class="container">
    <h1>Login</h1>

    <?php
      if (isset($_SESSION['login_erro'])) {
        echo "<div class='message error'>".$_SESSION['login_erro']."</div>";
        unset($_SESSION['login_erro']);
      }
    ?>

    <form method="POST" action="login.php">
      <label for="usuario">Usuário:</label>
      <input type="text" id="usuario" name="usuario" required>

      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" required>

      <button type="submit" name="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
  </div>
</body>
</html>
