<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$itens = [];
$subtotal = 0.0;
foreach (obterCarrinho() as $id => $quantidade) {
    $stmt = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE id = :id AND status = :status');
    $stmt->execute(['id' => $id, 'status' => 'Ativo']);
    $produto = $stmt->fetch();
    if ($produto && (int)$produto['estoque'] > 0) {
        $quantidade = min((int)$quantidade, (int)$produto['estoque']);
        $produto['quantidade'] = $quantidade;
        $produto['total'] = $quantidade * (float)$produto['preco'];
        $subtotal += $produto['total'];
        $itens[] = $produto;
    }
}
if (!$itens) redirecionar('carrinho.php');
$frete = $subtotal < 299 ? 24.90 : 0;
$total = $subtotal + $frete;
iniciarSessao();
$erros = $_SESSION['checkout_erros'] ?? [];
$valores = $_SESSION['checkout_valores'] ?? [];
unset($_SESSION['checkout_erros'], $_SESSION['checkout_valores']);
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Checkout | Aurora Tech</title><link rel="stylesheet" href="CSS/estilo.css"></head><body>
<header class="topo topo-loja"><div class="container topo-conteudo"><a class="marca" href="index.php"><span class="marca-icone">A</span><span>Aurora<span class="marca-destaque">Tech</span></span></a><nav class="nav-loja"><a href="carrinho.php">Voltar ao carrinho</a></nav></div></header>
<main class="container pagina-interna"><p class="eyebrow">ÚLTIMO PASSO</p><h1>Finalizar pedido</h1><?php if ($erros): ?><div class="alerta erro"><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?><div class="checkout-layout"><form class="card formulario" action="finalizar.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>"><h2>Dados para entrega</h2><label>Nome completo<input type="text" name="nome" required maxlength="120" value="<?= e($valores['nome'] ?? '') ?>"></label><label>E-mail<input type="email" name="email" required maxlength="160" value="<?= e($valores['email'] ?? '') ?>"></label><label>CEP<input type="text" name="cep" required pattern="[0-9]{5}-?[0-9]{3}" maxlength="9" placeholder="00000-000" value="<?= e($valores['cep'] ?? '') ?>"></label><label>Endereço<input type="text" name="endereco" required maxlength="180" value="<?= e($valores['endereco'] ?? '') ?>"></label><label>Número<input type="text" name="numero" required maxlength="20" value="<?= e($valores['numero'] ?? '') ?>"></label><label>Forma de pagamento<select name="pagamento" required><option value="">Selecione</option><option value="Pix" <?= ($valores['pagamento'] ?? '') === 'Pix' ? 'selected' : '' ?>>Pix (demonstração)</option><option value="Cartão" <?= ($valores['pagamento'] ?? '') === 'Cartão' ? 'selected' : '' ?>>Cartão (demonstração)</option><option value="Boleto" <?= ($valores['pagamento'] ?? '') === 'Boleto' ? 'selected' : '' ?>>Boleto (demonstração)</option></select></label><div class="acoes"><button class="botao" type="submit">Confirmar pedido</button><a class="botao secundario" href="carrinho.php">Voltar</a></div></form><aside class="resumo-pedido"><h2>Seu pedido</h2><?php foreach ($itens as $item): ?><div class="linha-pedido"><span><?= (int)$item['quantidade'] ?>x <?= e($item['nome']) ?></span><strong><?= formatarPreco($item['total']) ?></strong></div><?php endforeach; ?><hr><div><span>Subtotal</span><strong><?= formatarPreco($subtotal) ?></strong></div><div><span>Frete</span><strong><?= $frete ? formatarPreco($frete) : 'Grátis' ?></strong></div><div class="total"><span>Total</span><strong><?= formatarPreco($total) ?></strong></div></aside></div></main></body></html>
