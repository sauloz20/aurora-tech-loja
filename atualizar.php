<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

exigirPostComCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { flash('erro', 'ID inválido.'); redirecionar('listar.php'); }

[$produto, $erros] = validarProduto($_POST);
if ($erros) {
    iniciarSessao();
    $_SESSION['formulario'] = $produto;
    $_SESSION['erros'] = $erros;
    redirecionar('editar.php?id=' . $id);
}

$produto['id'] = $id;
$sql = 'UPDATE produtos SET nome = :nome, marca = :marca, modelo = :modelo, descricao = :descricao, especificacoes = :especificacoes, categoria = :categoria, preco = :preco, estoque = :estoque, ano = :ano, status = :status WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute($produto);

flash('sucesso', 'Produto atualizado com sucesso.');
redirecionar('listar.php');
