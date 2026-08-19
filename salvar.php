<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

exigirPostComCsrf();

[$produto, $erros] = validarProduto($_POST);
if ($erros) {
    iniciarSessao();
    $_SESSION['formulario'] = $produto;
    $_SESSION['erros'] = $erros;
    redirecionar('cadastro.php');
}

$sql = 'INSERT INTO produtos (nome, marca, modelo, descricao, especificacoes, categoria, preco, estoque, ano, status) VALUES (:nome, :marca, :modelo, :descricao, :especificacoes, :categoria, :preco, :estoque, :ano, :status)';
$stmt = $pdo->prepare($sql);
$stmt->execute($produto);

flash('sucesso', 'Produto cadastrado com sucesso.');
redirecionar('listar.php');
