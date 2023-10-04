<?php
require_once ("conecta.php");
require_once ("functions.php");
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["valor"]) && isset($_POST["data_pagar"]) && isset($_POST["id_empresa"])) {
        $valor = $_POST["valor"];
        $dataPagar = $_POST["data_pagar"];
        $idEmpresa = $_POST["id_empresa"];
        
        $valorPago = calcularValorPago($valor, $dataPagar);
        
        $sql = "INSERT INTO tbl_conta_pagar (valor, data_pagar, pago, id_empresa)
                VALUES ('$valorPago', '$dataPagar', 0, $idEmpresa)";
        
        if ($conexao->query($sql) === TRUE) {
            header("Location: index.php");
        } else {
            echo "Erro ao inserir conta a pagar: " . $conexao->error;
        }
    }
}

if (isset($_GET["marcar_pago"])) {
    $idContaPagar = $_GET["marcar_pago"];
    $sql = "UPDATE tbl_conta_pagar SET pago = 1 WHERE id_conta_pagar = $idContaPagar";
    $conexao->query($sql);
}

if (isset($_GET["excluir"])) {
    $idContaPagar = $_GET["excluir"];
    $sql = "DELETE FROM tbl_conta_pagar WHERE id_conta_pagar = $idContaPagar";
    $conexao->query($sql);
}


$filtroEmpresa = "";
$filtroValor = "";
$filtroData = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["filtrar"])) {
    if (isset($_POST["filtro_empresa"])) {
        $filtroEmpresa = " AND e.id_empresa = " . $_POST["filtro_empresa"];
    }
    
    if (isset($_POST["filtro_valor"]) && isset($_POST["condicao_valor"])) {
        $condicaoValor = $_POST["condicao_valor"];
        $valorFiltro = $_POST["filtro_valor"];
        
        switch ($condicaoValor) {
            case "maior":
                $filtroValor = " AND cp.valor > $valorFiltro";
                break;
            case "menor":
                $filtroValor = " AND cp.valor < $valorFiltro";
                break;
            case "igual":
                $filtroValor = " AND cp.valor = $valorFiltro";
                break;
        }
    }
    
    if (isset($_POST["filtro_data"])) {
        $filtroData = " AND cp.data_pagar = '" . $_POST["filtro_data"] . "'";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas a Pagar</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Contas a Pagar</h1>
    
    <h2>Adicionar Conta a Pagar</h2>
    <form method="post">
        <label for="id_empresa">Empresa:</label>
        <select name="id_empresa" id="id_empresa">
            <?php
            $sql = "SELECT * FROM tbl_empresa";
            $result = $conexao->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["id_empresa"] . "'>" . $row["nome"] . "</option>";
            }
            ?>
        </select>
        <br>
        <label for="data_pagar">Data a ser pago:</label>
        <input type="date" name="data_pagar" id="data_pagar" required>
        <br>
        <label for="valor">Valor que será pago:</label>
        <input type="number" name="valor" id="valor" step="0.01" required>
        <br>
        <input type="submit" value="Inserir">
    </form>
    
    <h2>Lista de Contas a Pagar</h2>
    <form method="post">
        <label for="filtro_empresa">Filtrar por Empresa:</label>
        <select name="filtro_empresa" id="filtro_empresa">
            <option value="">Todas</option>
            <?php
            $sql = "SELECT * FROM tbl_empresa";
            $result = $conexao->query($sql);
            while ($row = $result->fetch_assoc()) {
                $selected = ($row["id_empresa"] == $_POST["filtro_empresa"]) ? "selected" : "";
                echo "<option value='" . $row["id_empresa"] . "' $selected>" . $row["nome"] . "</option>";
            }
            ?>
        </select>
        <br>
        <label for="filtro_valor">Filtrar por Valor:</label>
        <input type="number" name="filtro_valor" id="filtro_valor" step="0.01">
        <select name="condicao_valor" id="condicao_valor">
            <option value="maior">Maior que</option>
            <option value="menor">Menor que</option>
            <option value="igual">Igual a</option>
        </select>
        <br>
        <label for="filtro_data">Filtrar por Data:</label>
        <input type="date" name="filtro_data" id="filtro_data">
        <br>
        <input type="submit" name="filtrar" value="Filtrar">
    </form>
    
    <table>
        <tr>
            <th>Empresa</th>
            <th>Data a Pagar</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php
        $sql = "SELECT cp.id_conta_pagar, cp.valor, cp.data_pagar, cp.pago, e.nome ";
        $sql .= " FROM tbl_conta_pagar cp ";
        $sql .= "LEFT JOIN tbl_empresa e ON cp.id_empresa = e.id_empresa ";
        $sql .= " WHERE 1 $filtroEmpresa $filtroValor $filtroData ";
        $result = $conexao->query($sql);
        if ($result === false) {
            die("Erro na consulta SQL: " . $conexao->error);
        }
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["nome"] . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row["data_pagar"])) . "</td>";
            echo "<td>R$ " . number_format($row["valor"], 2, ',', '.') . "</td>";
            echo "<td>" . ($row["pago"] ? "Pago" : "Não Pago") . "</td>";
            echo "<td><a href='index.php?marcar_pago=" . $row["id_conta_pagar"] . "'>Marcar como Pago</a> | <a href='index.php?excluir=" . $row["id_conta_pagar"] . "'>Excluir</a></td>";
            echo "";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>

<?php
// Fechar a conexão com o banco de dados
$conexao->close();
?>