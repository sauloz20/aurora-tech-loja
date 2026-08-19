<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$busca = trim((string)($_GET['busca'] ?? ''));
$categoria = trim((string)($_GET['categoria'] ?? ''));
$categorias = categoriasPermitidas();

$sql = 'SELECT id, nome, marca, modelo, descricao, especificacoes, categoria, preco, estoque, ano, status FROM produtos WHERE status = :status';
$params = ['status' => 'Ativo'];

if ($busca !== '') {
    $sql .= ' AND (nome LIKE :busca OR descricao LIKE :busca)';
    $params['busca'] = '%' . $busca . '%';
}
if (in_array($categoria, $categorias, true)) {
    $sql .= ' AND categoria = :categoria';
    $params['categoria'] = $categoria;
}
$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
$flash = obterFlash();
$qtdCarrinho = quantidadeCarrinho();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aurora Tech: tecnologia para deixar sua rotina mais inteligente.">
    <title>Aurora Tech | Tecnologia para o seu dia</title>
    <link rel="stylesheet" href="CSS/estilo.css">
</head>
<body>
<header class="topo topo-loja">
    <div class="container topo-conteudo">
        <a class="marca" href="index.php"><span class="marca-icone">A</span><span>Aurora<span class="marca-destaque">Tech</span></span></a>
        <nav class="nav-loja" aria-label="Navegação principal">
            <a href="index.php">Loja</a>
            <a href="#beneficios">Por que a Aurora?</a>
            <a href="listar.php">Administração</a>
            <a class="carrinho-link" href="carrinho.php">Carrinho <span><?= $qtdCarrinho ?></span></a>
        </nav>
    </div>
</header>

<main>
    <section class="hero-loja">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">TECNOLOGIA QUE ACOMPANHA VOCÊ</p>
                <h1>Pequenas escolhas.<br><em>Grandes descobertas.</em></h1>
                <p class="hero-texto">Produtos inteligentes para tornar seus dias mais leves, produtivos e conectados.</p>
                <a class="botao botao-claro" href="#produtos">Explorar coleção <span>→</span></a>
            </div>
            <div class="hero-orbe" aria-hidden="true"><div class="orbe-inner">✦</div><span class="orbe-label">smart<br>living</span></div>
        </div>
    </section>

    <section class="beneficios" id="beneficios">
        <div class="container beneficios-grid">
            <div><strong>Entrega cuidadosa</strong><span>Embalagem protegida<br>para chegar perfeito.</span></div>
            <div><strong>Compra segura</strong><span>Checkout simples e<br>dados protegidos.</span></div>
            <div><strong>Suporte próximo</strong><span>Fale com pessoas<br>que entendem.</span></div>
        </div>
    </section>

    <section class="container produtos-secao" id="produtos">
        <div class="secao-cabecalho"><div><p class="eyebrow">COLEÇÃO EM DESTAQUE</p><h2>Escolhas que fazem sentido</h2></div><p class="contador"><?= count($produtos) ?> produto(s) disponível(is)</p></div>
        <?php if ($flash): ?><div class="alerta <?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div><?php endif; ?>
        <form class="filtros" method="get" action="index.php">
            <label class="busca"><span class="sr-only">Buscar produtos</span><input type="search" name="busca" value="<?= e($busca) ?>" placeholder="O que você está procurando?"><button type="submit">Buscar</button></label>
            <label><span class="sr-only">Filtrar por categoria</span><select name="categoria" onchange="this.form.submit()"><option value="">Todas as categorias</option><?php foreach ($categorias as $opcao): ?><option value="<?= e($opcao) ?>" <?= $categoria === $opcao ? 'selected' : '' ?>><?= e($opcao) ?></option><?php endforeach; ?></select></label>
        </form>
        <div class="produtos-grid">
            <?php foreach ($produtos as $produto): ?>
                <article class="produto-card">
                    <div class="produto-imagem categoria-<?= e(classeCategoria((string)$produto['categoria'])) ?>"><span><?= e(inicialCategoria((string)$produto['categoria'])) ?></span><small><?= e($produto['categoria']) ?></small></div>
                    <div class="produto-info"><p class="produto-categoria"><?= e($produto['categoria']) ?></p><h3><a class="produto-titulo" href="produto.php?id=<?= (int)$produto['id'] ?>"><?= e($produto['nome']) ?></a></h3><p class="produto-descricao"><?= e($produto['descricao']) ?></p><div class="produto-rodape"><strong><?= formatarPreco($produto['preco']) ?></strong><?php if ((int)$produto['estoque'] > 0): ?><form action="carrinho.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>"><input type="hidden" name="acao" value="adicionar"><input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><button class="botao botao-pequeno" type="submit">Adicionar</button></form><?php else: ?><span class="esgotado">Esgotado</span><?php endif; ?></div></div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!$produtos): ?><div class="vazio"><h3>Nenhum produto encontrado</h3><p>Tente outro termo ou remova os filtros.</p><a class="botao" href="index.php">Limpar filtros</a></div><?php endif; ?>
    </section>
</main>
<footer class="rodape"><div class="container"><strong>Aurora<span class="marca-destaque">Tech</span></strong><span>Curadoria de tecnologia para uma vida mais simples.</span><span>© <?= date('Y') ?> Aurora Tech</span></div></footer>
</body>
</html>
