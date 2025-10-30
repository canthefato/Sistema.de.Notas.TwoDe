<?php
session_start();
include 'backend/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $nome = mysqli_real_escape_string($conn, trim($_POST['nome']));
    $usuario = mysqli_real_escape_string($conn, trim($_POST['usuario'])); // <- novo campo
    $documento = mysqli_real_escape_string($conn, trim($_POST['documento']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $senha = mysqli_real_escape_string($conn, trim(md5($_POST['senha'])));
    $tipo = mysqli_real_escape_string($conn, trim($_POST['tipo']));

    // Verifica se e-mail já existe
    $sql = "SELECT COUNT(*) AS TOTAL FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['TOTAL'] == 1) {
        $_SESSION['usuario_existe'] = true;
        header('Location: cadastro.php');
        exit;
    }

    // Verifica se o nome de usuário já existe
    $sql = "SELECT COUNT(*) AS TOTAL FROM usuarios WHERE usuario = '$usuario'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['TOTAL'] == 1) {
        $_SESSION['usuario_existe_nome'] = true;
        header('Location: cadastro.php');
        exit;
    }

    // Inserção no banco
    $sql = "INSERT INTO usuarios (nome_completo_razao_social, usuario, email, documento, senha) 
            VALUES ('$nome', '$usuario', '$email', '$documento', '$senha')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['status_cadastro'] = true;
    }

    header('Location: cadastro.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro - Sistema</title>

<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f0f4f8;
    color: #1c1c1c;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }

  .container {
    background-color: white;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    max-width: 420px;
    width: 100%;
    text-align: center;
  }

  h1 {
    color: #004b8d;
    margin-bottom: 25px;
  }

  label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    text-align: left;
  }

  input, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
  }

  button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
  }

  button:hover {
    background-color: #0056b3;
  }

  .toggle {
    margin-bottom: 10px;
    display: flex;
    gap: 15px;
    justify-content: center;
  }

  .toggle label {
    font-weight: normal;
    cursor: pointer;
  }

  .message {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
  }

  .success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
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
  <h1>Cadastro</h1>

  <?php
    if (isset($_SESSION['usuario_existe'])) {
      echo "<div class='message error'>E-mail já cadastrado!</div>";
      unset($_SESSION['usuario_existe']);
    }
    if (isset($_SESSION['usuario_existe_nome'])) {
      echo "<div class='message error'>Nome de usuário já em uso!</div>";
      unset($_SESSION['usuario_existe_nome']);
    }
    if (isset($_SESSION['status_cadastro'])) {
      echo "<div class='message success'>Cadastro realizado com sucesso!</div>";
      unset($_SESSION['status_cadastro']);
    }
  ?>

  <div class="toggle">
    <label><input type="radio" name="tipo" value="cpf" checked onclick="mudarDocumento('CPF')"> Pessoa Física</label>
    <label><input type="radio" name="tipo" value="cnpj" onclick="mudarDocumento('CNPJ')"> Empresa</label>
  </div>

  <form method="POST" action="">
    <input type="hidden" id="tipo" name="tipo" value="cpf">

    <label for="nome">Nome Completo / Razão Social:</label>
    <input type="text" id="nome" name="nome" placeholder="Digite o nome completo / Razão Social" required>

    <label for="usuario">Usuário:</label>
    <input type="text" id="usuario" name="usuario" placeholder="Escolha um nome de usuário" required>

    <label id="labelDocumento" for="documento">CPF:</label>
    <input type="text" id="documento" name="documento" placeholder="Digite o CPF" required>

    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" placeholder="exemplo@email.com" required>

    <label for="senha">Senha:</label>
    <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>

    <button type="submit" name="submit">Cadastrar</button>
  </form>
</div>

<script>
function mudarDocumento(tipo) {
  const label = document.getElementById('labelDocumento');
  const input = document.getElementById('documento');
  const tipoHidden = document.getElementById('tipo');

  if (tipo === 'CPF') {
    label.textContent = 'CPF:';
    input.placeholder = 'Digite o CPF';
    tipoHidden.value = 'cpf';
  } else {
    label.textContent = 'CNPJ:';
    input.placeholder = 'Digite o CNPJ';
    tipoHidden.value = 'cnpj';
  }
}
</script>

</body>
</html>
