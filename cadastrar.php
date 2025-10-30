<?php
include 'backend/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $nome = mysqli_real_escape_string($conn, trim($_POST['nome']));
    $usuario = mysqli_real_escape_string($conn, trim($_POST['usuario']));
    $senha = mysqli_real_escape_string($conn, trim(md5($_POST['senha'])));

      $sql = "SELECT COUNT(*) AS TOTAL FROM usuarios WHERE usuario = '$usuario'";
      $result = mysqli_query($conn, $sql);
      $row = mysqli_fetch_assoc($result);

        if ($row['TOTAL'] == 1) {
            $_SESSION['usuario_existe'] = true;
            header('Location: cadastrar.php');
           exit;
        }

        $sql = "INSERT INTO usuarios (nome, usuario, senha) VALUES ('$nome', '$usuario', '$senha')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['status_cadastro'] = true;
        }
            header('Location: login.php'); 
            exit;
}
          $conn->close();
?>

<!DOCTYPE html>
  <html lang="pt-BR">
    <head>

    <style> 
body {
    font-family: Arial, sans-serif;
    background: #eef2f7;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.login-container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 400px;
    text-align: center;
}
.login-container h1 { color: #ff3c00; margin-bottom: 20px; }
label { display: block; text-align: left; margin-bottom: 5px; font-weight: 600; }
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
button {
    width: 100%;
    padding: 12px;
    background-color: #ff0000ff;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
button:hover { background-color: #ff1702; }
p a { color: #ff3c00; text-decoration: none; font-weight: bold; }
.message { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; }
.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
      <meta charset="UTF-8">
          <title>Cadastro - Sistema de Notas</title>
            <link rel="stylesheet" href="style.css">
              </head>
  <body>
    <div class="container">
      <h1>Cadastro de Usuário</h1>

      <?php
        if (isset($_SESSION['usuario_existe'])) {
          echo "<div class='message error'>Usuário já existe!</div>";
          unset($_SESSION['usuario_existe']);
        }
        if (isset($_SESSION['status_cadastro'])) {
          echo "<div class='message success'>Cadastro realizado com sucesso!</div>";
          unset($_SESSION['status_cadastro']);
        }
    ?>

    <form method="POST" action="">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Usuário:</label>
        <input type="text" name="usuario" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit" name="submit">Cadastrar</button>
    </form>

    <a href="login.php"><button type="button">Já Possuo Login!</button></a>
</div>
</body>
</html>
