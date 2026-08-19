<?php
require_once __DIR__ . '/funcoes.php';
iniciarSessao();

$valores = $_SESSION['formulario'] ?? [];
unset($_SESSION['formulario']);
$erros = $_SESSION['erros'] ?? [];
unset($_SESSION['erros']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar produto</title>
    <link rel="stylesheet" href="CSS/estilo.css">
</head>
<body>
<header class="topo">
    <div class="container">
        <h1>CRUD de Produtos</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="cadastro.php">Cadastrar</a>
            <a href="listar.php">Listar produtos</a>
        </nav>
    </div>
</header>

<main class="container">
    <section class="card">
        <h2>Cadastrar produto</h2>

        <?php if (!empty($erros)): ?>
            <div class="alerta erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="salvar.php" method="post" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>">
            <label>Nome
                <input type="text" name="nome" maxlength="120" required value="<?= e($valores['nome'] ?? '') ?>">
            </label>

            <label>Marca
                <input type="text" name="marca" maxlength="80" required value="<?= e($valores['marca'] ?? 'Aurora') ?>">
            </label>

            <label>Modelo
                <input type="text" name="modelo" maxlength="120" value="<?= e($valores['modelo'] ?? '') ?>">
            </label>

            <label>Descrição
                <textarea name="descricao" required maxlength="2000"><?= e($valores['descricao'] ?? '') ?></textarea>
            </label>

            <label>Especificações
                <textarea name="especificacoes" maxlength="2000" placeholder="Separe as especificações com | (ex.: 256 GB | 12 GB RAM | Tela AMOLED)"><?= e($valores['especificacoes'] ?? '') ?></textarea>
            </label>

            <label>Categoria
                <select name="categoria" required>
                    <option value="">Selecione</option>
                    <?php foreach (categoriasPermitidas() as $categoria): ?>
                        <option value="<?= e($categoria) ?>" <?= (($valores['categoria'] ?? '') === $categoria) ? 'selected' : '' ?>><?= e($categoria) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Preço (R$)
                <input type="number" name="preco" min="0" step="0.01" required value="<?= e($valores['preco'] ?? '') ?>">
            </label>

            <label>Estoque
                <input type="number" name="estoque" min="0" step="1" required value="<?= e($valores['estoque'] ?? '') ?>">
            </label>

            <label>Ano
                <input type="number" name="ano" min="1900" max="<?= date('Y') ?>" required value="<?= e($valores['ano'] ?? '') ?>">
            </label>

            <label>Status
                <select name="status" required>
                    <?php foreach (statusPermitidos() as $status): ?>
                        <option value="<?= e($status) ?>" <?= (($valores['status'] ?? 'Ativo') === $status) ? 'selected' : '' ?>><?= e($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="acoes">
                <button class="botao" type="submit">Salvar produto</button>
                <a class="botao secundario" href="listar.php">Cancelar</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
