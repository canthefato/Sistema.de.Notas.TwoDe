<?php
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_notas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<p class='message error'>Conexão falhou: " . $conn->connect_error . "</p>");
}

// Inserção de nova nota fiscal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $numero = $conn->real_escape_string($_POST['numero']);
    $data_emissao = $conn->real_escape_string($_POST['data_emissao']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $valortotal = floatval(str_replace(',', '.', $_POST['valortotal']));

    $sql = "INSERT INTO notas (numero, data_emissao, tipo, valortotal) VALUES ('$numero', '$data_emissao', '$tipo', $valortotal)";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Nota fiscal salva com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Erro ao salvar a nota: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Consulta de notas
$sql_select = "SELECT numero, data_emissao, tipo, valortotal FROM notas ORDER BY data_emissao DESC";
$result = $conn->query($sql_select);

$notas = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notas[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Controle de Nota Fiscal</title>
    <style>
        :root {
            --cor-primaria: #2c3e50;
            --cor-secundaria: #2980b9;
            --cor-fundo: #f4f6f9;
            --cor-sucesso: #d4edda;
            --cor-erro: #f8d7da;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--cor-fundo);
            margin: 0;
            padding: 30px 20px;
            color: var(--cor-primaria);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--cor-primaria);
            font-size: 32px;
            font-weight: 700;
            border-bottom: 2px solid var(--cor-secundaria);
            padding-bottom: 10px;
        }

        form {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--cor-primaria);
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            border-color: var(--cor-secundaria);
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: var(--cor-secundaria);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1f6391;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eaeaea;
            text-align: center;
            font-size: 15px;
        }

        th {
            background-color: var(--cor-secundaria);
            color: white;
            font-weight: 600;
        }

        #resumo {
            background: #ecf0f1;
            padding: 20px;
            border-left: 6px solid var(--cor-secundaria);
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            color: var(--cor-primaria);
            margin-bottom: 25px;
            text-align: center;
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 15px;
        }

        .success {
            background-color: var(--cor-sucesso);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: var(--cor-erro);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (min-width: 768px) {
            .form-columns {
                display: flex;
                gap: 20px;
            }

            .form-column {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Controle de Nota Fiscal</h1>

        <?php
        if (isset($_SESSION['message'])) {
            $message_class = $_SESSION['message_type'] === 'success' ? 'success' : 'error';
            $message_text = $_SESSION['message'];
            echo "<div class='message {$message_class}'>{$message_text}</div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <form method="POST" action="">
            <div class="form-columns">
                <div class="form-column">
                    <label for="numero">Número da Nota Fiscal:</label>
                    <input type="text" id="numero" name="numero" placeholder="Ex: 00123456" required />

                    <label for="data_emissao">Data de Emissão:</label>
                    <input type="date" id="data_emissao" name="data_emissao" required />
                </div>
                <div class="form-column">
                    <label for="tipo">Tipo de Nota:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="" disabled selected>Selecione o tipo</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>

                    <label for="valortotal">Valor (R$):</label>
                    <input type="number" id="valortotal" name="valortotal" step="0.01" min="0" placeholder="0.00" required />
                </div>
            </div>
            <button type="submit" name="submit">Adicionar Nota Fiscal</button>
        </form>

        <div id="resumo">
            <?php
            $total_entrada = 0;
            $total_saida = 0;

            if (!empty($notas)) {
                foreach ($notas as $nota) {
                    if ($nota['tipo'] === 'entrada') {
                        $total_entrada += $nota['valortotal'];
                    } else {
                        $total_saida += $nota['valortotal'];
                    }
                }

                echo "Total Entrada: R$ " . number_format($total_entrada, 2, ',', '.') . " | ";
                echo "Total Saída: R$ " . number_format($total_saida, 2, ',', '.');
            } else {
                echo "Nenhuma nota fiscal cadastrada.";
            }
            ?>
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
                if (!empty($notas)) {
                    foreach ($notas as $nota) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($nota['numero']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($nota['data_emissao'])) . "</td>";
                        echo "<td>" . ucfirst(htmlspecialchars($nota['tipo'])) . "</td>";
                        echo "<td>R$ " . number_format($nota['valortotal'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhuma nota fiscal cadastrada</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
