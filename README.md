# Contas a Pagar

Este é um projeto de controle financeiro de contas a pagar desenvolvido em PHP e MySQL.
## Requisitos

- Servidor web (por exemplo, Apache)
- PHP 7.x ou superior
- MySQL ou MariaDB

## Configuração

1. Clone ou faça o download deste repositório para o seu servidor web local ou hospedagem.
2. Importe o arquivo SQL `prova_php.sql` no seu banco de dados MySQL para criar as tabelas necessárias.

## Configuração do Banco de Dados

Antes de executar o projeto, certifique-se de configurar as variáveis de conexão com o banco de dados no arquivo `conexao.php`. Substitua as seguintes variáveis de acordo com as suas configurações:

```php
$servidor = "localhost";
$usuario = "usuario";
$senha = "";
$db = "banco_de_dados";
