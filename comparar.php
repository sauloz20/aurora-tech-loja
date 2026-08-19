<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirPostComCsrf();
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id && $id > 0) alternarComparacao($id);
    redirecionar('comparar.php');
}
$ids = obterComparacao();
$produtos = [];
if ($ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, nome, marca, modelo, categoria, preco, estoque, especificacoes FROM produtos WHERE id IN ($placeholders) AND status = 'Ativo'");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Comparar produtos | Aurora Tech</title><link rel="stylesheet" href="CSS/estilo.css"></head><body><header class="topo topo-loja"><div class="container topo-conteudo"><a class="marca" href="index.php"><span class="marca-icone">A</span><span>Aurora<span class="marca-destaque">Tech</span></span></a><nav class="nav-loja"><a href="index.php">Loja</a><a href="carrinho.php">Carrinho</a></nav></div></header><main class="container pagina-interna"><p class="eyebrow">DECIDA COM MAIS CLAREZA</p><h1>Comparar produtos</h1><?php if (!$produtos): ?><div class="vazio"><h2>Escolha produtos para comparar</h2><p>Abra qualquer produto e clique em “Adicionar à comparação”. Você pode comparar até três itens.</p><a class="botao" href="index.php">Explorar catálogo</a></div><?php else: ?><div class="comparacao-grid"><?php foreach ($produtos as $produto): ?><article class="comparacao-card"><div class="produto-imagem categoria-<?= e(classeCategoria((string)$produto['categoria'])) ?>"><span><?= e(inicialCategoria((string)$produto['categoria'])) ?></span></div><p class="produto-categoria"><?= e($produto['categoria']) ?></p><h2><?= e($produto['nome']) ?></h2><strong class="comparacao-preco"><?= formatarPreco($produto['preco']) ?></strong><dl><dt>Marca e modelo</dt><dd><?= e($produto['marca']) ?> <?= e($produto['modelo']) ?></dd><dt>Estoque</dt><dd><?= (int)$produto['estoque'] ?> unidade(s)</dd><?php foreach (array_slice(array_filter(array_map('trim', explode('|', (string)$produto['especificacoes']))), 0, 4) as $especificacao): ?><dt>Especificação</dt><dd><?= e($especificacao) ?></dd><?php endforeach; ?></dl><form method="post"><input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><button class="link perigo" type="submit">Remover da comparação</button></form></article><?php endforeach; ?></div><?php endif; ?></main><footer class="rodape"><div class="container"><strong>Aurora<span class="marca-destaque">Tech</span></strong><span>Compare até três produtos antes de escolher.</span></div></footer></body></html>
