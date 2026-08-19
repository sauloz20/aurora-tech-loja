<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirPostComCsrf();
}
$acao = (string)($_POST['acao'] ?? '');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($acao === 'adicionar' && $id && $id > 0) {
    $stmt = $pdo->prepare('SELECT id, estoque FROM produtos WHERE id = :id AND status = :status');
    $stmt->execute(['id' => $id, 'status' => 'Ativo']);
    $produto = $stmt->fetch();
    if ($produto && (int)$produto['estoque'] > 0) {
        $quantidade = filter_var($_POST['quantidade'] ?? 1, FILTER_VALIDATE_INT);
        $quantidade = $quantidade !== false ? max(1, min((int)$quantidade, (int)$produto['estoque'])) : 1;
        adicionarAoCarrinho((int)$produto['id'], $quantidade);
        flash('sucesso', 'Produto adicionado ao carrinho.');
    } else {
        flash('erro', 'Produto indisponível.');
    }
    redirecionar('carrinho.php');
}
if ($acao === 'atualizar') {
    atualizarCarrinho(is_array($_POST['quantidade'] ?? null) ? $_POST['quantidade'] : []);
    flash('sucesso', 'Carrinho atualizado.');
    redirecionar('carrinho.php');
}
if ($acao === 'remover' && $id && $id > 0) {
    removerDoCarrinho($id);
    flash('sucesso', 'Item removido do carrinho.');
    redirecionar('carrinho.php');
}

$carrinho = obterCarrinho();
$itens = [];
$subtotal = 0.0;
if ($carrinho) {
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, nome, descricao, categoria, preco, estoque FROM produtos WHERE id IN ($placeholders) AND status = 'Ativo'");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $produto) {
        $quantidade = min((int)($carrinho[$produto['id']] ?? 1), (int)$produto['estoque']);
        if ($quantidade < 1) continue;
        $produto['quantidade'] = $quantidade;
        $produto['total'] = $quantidade * (float)$produto['preco'];
        $subtotal += $produto['total'];
        $itens[] = $produto;
    }
}
$frete = $subtotal > 0 && $subtotal < 299 ? 24.90 : 0;
$total = $subtotal + $frete;
$flash = obterFlash();
$qtdCarrinho = quantidadeCarrinho();
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Carrinho | Aurora Tech</title><link rel="stylesheet" href="CSS/estilo.css"></head><body>
<header class="topo topo-loja"><div class="container topo-conteudo"><a class="marca" href="index.php"><span class="marca-icone">A</span><span>Aurora<span class="marca-destaque">Tech</span></span></a><nav class="nav-loja"><a href="index.php">Continuar comprando</a><a href="listar.php">Administração</a><a class="carrinho-link" href="carrinho.php">Carrinho <span><?= $qtdCarrinho ?></span></a></nav></div></header>
<main class="container pagina-interna"><p class="eyebrow">SUA SELEÇÃO</p><h1>Seu carrinho</h1><?php if ($flash): ?><div class="alerta <?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div><?php endif; ?>
<?php if (!$itens): ?><div class="vazio"><h2>Seu carrinho está vazio</h2><p>Encontre algo especial para deixar sua rotina mais inteligente.</p><a class="botao" href="index.php">Explorar produtos</a></div><?php else: ?><div class="carrinho-layout"><form class="lista-carrinho" action="carrinho.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="acao" value="atualizar"><?php foreach ($itens as $item): ?><div class="item-carrinho"><div class="produto-mini categoria-<?= e(classeCategoria((string)$item['categoria'])) ?>"><?= e(inicialCategoria((string)$item['categoria'])) ?></div><div class="item-detalhe"><p class="produto-categoria"><?= e($item['categoria']) ?></p><h3><?= e($item['nome']) ?></h3><span><?= formatarPreco($item['preco']) ?> cada</span></div><label>Qtd.<input type="number" min="1" max="<?= (int)$item['estoque'] ?>" name="quantidade[<?= (int)$item['id'] ?>]" value="<?= (int)$item['quantidade'] ?>"></label><strong><?= formatarPreco($item['total']) ?></strong><button class="link perigo" type="submit" name="acao" value="remover" formaction="carrinho.php" onclick="this.form.id.value='<?= (int)$item['id'] ?>'">Remover</button></div><?php endforeach; ?><input type="hidden" name="id" value=""><button type="submit" class="botao secundario">Atualizar quantidades</button></form><aside class="resumo-pedido"><h2>Resumo</h2><div><span>Subtotal</span><strong><?= formatarPreco($subtotal) ?></strong></div><div><span>Frete</span><strong><?= $frete ? formatarPreco($frete) : 'Grátis' ?></strong></div><hr><div class="total"><span>Total</span><strong><?= formatarPreco($total) ?></strong></div><a class="botao botao-largo" href="checkout.php">Ir para checkout</a><small>Frete grátis para compras acima de R$ 299,00.</small></aside></div><?php endif; ?></main><footer class="rodape"><div class="container"><strong>Aurora<span class="marca-destaque">Tech</span></strong><span>Compra demonstrativa para o trabalho de CRUD.</span></div></footer></body></html>
