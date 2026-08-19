<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

exigirPostComCsrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('erro', 'ID inválido.');
    redirecionar('listar.php');
}

$stmt = $pdo->prepare('DELETE FROM produtos WHERE id = :id');
$stmt->execute(['id' => $id]);
$excluido = $stmt->rowCount() > 0;

flash(
    $excluido ? 'sucesso' : 'erro',
    $excluido ? 'Produto excluído com sucesso.' : 'Produto não encontrado.'
);
redirecionar('listar.php');
