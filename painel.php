<?php
session_start();
include 'backend/conexao.php';

$totalEntrada = 0;
$totalSaida = 0;

if(isset($_POST['submit'])){
    $numero = mysqli_real_escape_string($conn, $_POST['numero']);
    $data = mysqli_real_escape_string($conn, $_POST['data_emissao']);
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $valor = floatval($_POST['valortotal']);
    $identificacao = mysqli_real_escape_string($conn, $_POST['identificacao']);

    $sql = "INSERT INTO notas (numero, data_emissao, tipo, valortotal, identificacao) VALUES ('$numero', '$data', '$tipo', $valor, '$identificacao')";
    if($conn->query($sql)){
        $_SESSION['msg'] = "Nota fiscal salva com sucesso!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Erro ao salvar a nota: " . $conn->error;
        $_SESSION['msg_type'] = "error";
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$result = $conn->query("SELECT * FROM notas ORDER BY data_emissao DESC");
$notas = [];
if($result){
    while($row = $result->fetch_assoc()){
        $notas[] = $row;
        if($row['tipo'] == 'entrada') $totalEntrada += $row['valortotal'];
        if($row['tipo'] == 'saida') $totalSaida += $row['valortotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Controle de Notas Fiscais</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="logo">TD</div>
    <h1 class="site-title">TwoDé Contabilidade</h1>
    <h2 class="page-title">Controle de Notas Fiscais</h2>

    <?php
    if(isset($_SESSION['msg'])){
        $class = $_SESSION['msg_type'] === 'success' ? 'success' : 'error';
        echo "<div class='message $class'>".$_SESSION['msg']."</div>";
        unset($_SESSION['msg'], $_SESSION['msg_type']);
    }
    ?>

    <form method="POST" action="">
        <div class="form-columns">
            <div class="form-column">
                <label for="numero">Número da Nota Fiscal:</label>
                <input type="text" id="numero" name="numero" required>
                <label for="data_emissao">Data de Emissão:</label>
                <input type="date" id="data_emissao" name="data_emissao" required>
                <label for="identificacao">Identificação:</label>
                <input type="text" id="identificacao" name="identificacao" required>
            </div>
            <div class="form-column">
                <label for="tipo">Tipo de Nota:</label>
                <select id="tipo" name="tipo" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                </select>
                <label for="valortotal">Valor (R$):</label>
                <input type="number" id="valortotal" name="valortotal" step="0.01" min="0" required>
            </div>
        </div>
        <button type="submit" name="submit">Adicionar Nota Fiscal</button>
    </form>

    <div id="resumo">
        Total Entrada: R$ <?php echo number_format($totalEntrada, 2, ',', '.'); ?><br>
        Total Saída: R$ <?php echo number_format($totalSaida, 2, ',', '.'); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($notas)){
                foreach($notas as $nota){
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($nota['numero'])."</td>";
                    echo "<td>".date('d/m/Y', strtotime($nota['data_emissao']))."</td>";
                    echo "<td>".ucfirst(htmlspecialchars($nota['tipo']))."</td>";
                    echo "<td>R$ ".number_format($nota['valortotal'], 2, ',', '.')."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>Nenhuma nota fiscal cadastrada</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
