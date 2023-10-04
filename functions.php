<?php
require_once ("conecta.php");

function calcularValorPago($valor, $dataPagar) {
    $hoje = date('Y-m-d');
    
    if ($dataPagar < $hoje) {
        // Acréscimo de 10%
        return $valor * 1.10;
    } elseif ($dataPagar > $hoje) {
        // Desconto de 5% 
        return $valor * 0.95;
    } else {
        return $valor;
    }
}

function listarContasPagar($conexao) {
    $sql = "SELECT cp.id_conta_pagar, cp.valor, cp.data_pagar, cp.pago, e.nome
            FROM tbl_conta_pagar cp
            LEFT JOIN tbl_empresa e ON cp.id_empresa = e.id_empresa";
    $result = $conexao->query($sql);
    return $result;
}
