<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) redirecionar('index.php');
$stmt = $pdo->prepare('SELECT * FROM pedidos WHERE id = :id');
$stmt->execute(['id' => $id]);
$pedido = $stmt->fetch();
if (!$pedido) redirecionar('index.php');
$stmt = $pdo->prepare('SELECT * FROM pedido_itens WHERE pedido_id = :id ORDER BY id');
$stmt->execute(['id' => $id]);
$itens = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Pedido #<?= (int)$pedido['id'] ?> | Aurora Tech</title><link rel="stylesheet" href="CSS/estilo.css"></head><body><header class="topo topo-loja"><div class="container topo-conteudo"><a class="marca" href="index.php"><span class="marca-icone">A</span><span>Aurora<span class="marca-destaque">Tech</span></span></a><nav class="nav-loja"><a href="index.php">Voltar à loja</a></nav></div></header><main class="container pagina-interna"><section class="sucesso-pedido"><div class="check-pedido">✓</div><p class="eyebrow">PEDIDO RECEBIDO</p><h1>Obrigado, <?= e(explode(' ', $pedido['cliente_nome'])[0]) ?>.</h1><p>Seu pedido <strong>#<?= (int)$pedido['id'] ?></strong> foi registrado com sucesso. Esta é uma confirmação demonstrativa para o projeto acadêmico.</p><div class="pedido-status"><span>Status atual</span><strong><?= e($pedido['status']) ?></strong></div><div class="pedido-timeline"><span class="ativo">Recebido</span><span>Pago</span><span>Enviado</span><span>Entregue</span></div><div class="pedido-detalhes"><h2>Resumo da compra</h2><?php foreach ($itens as $item): ?><div class="linha-pedido"><span><?= (int)$item['quantidade'] ?>x <?= e($item['produto_nome']) ?></span><strong><?= formatarPreco($item['total']) ?></strong></div><?php endforeach; ?><hr><div class="linha-pedido"><span>Frete</span><strong><?= (float)$pedido['frete'] ? formatarPreco($pedido['frete']) : 'Grátis' ?></strong></div><div class="total"><span>Total</span><strong><?= formatarPreco($pedido['total']) ?></strong></div></div><a class="botao" href="index.php">Continuar explorando</a></section></main><footer class="rodape"><div class="container"><strong>Aurora<span class="marca-destaque">Tech</span></strong><span>Obrigado por escolher uma tecnologia mais simples.</span></div></footer></body></html>
