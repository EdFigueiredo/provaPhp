<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$db = "prova_php";

$conexao = new mysqli($servidor, $usuario, $senha, $db);

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}