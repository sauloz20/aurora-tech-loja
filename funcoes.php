<?php
declare(strict_types=1);

function iniciarSessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function categoriasPermitidas(): array
{
    return ['Celulares', 'Notebooks', 'Tablets', 'Acessórios', 'Áudio', 'Casa inteligente'];
}

function statusPermitidos(): array
{
    return ['Ativo', 'Inativo'];
}

function classeCategoria(string $categoria): string
{
    $mapa = [
        'Celulares' => 'celulares',
        'Notebooks' => 'notebooks',
        'Tablets' => 'tablets',
        'Acessórios' => 'acessorios',
        'Áudio' => 'audio',
        'Casa inteligente' => 'casa-inteligente',
    ];

    return $mapa[$categoria] ?? 'geral';
}

function tokenCsrf(): string
{
    iniciarSessao();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarCsrf(?string $token): bool
{
    iniciarSessao();

    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function validarProduto(array $dados): array
{
    $erros = [];
    $produto = [
        'nome' => trim((string)($dados['nome'] ?? '')),
        'marca' => trim((string)($dados['marca'] ?? 'Aurora')),
        'modelo' => trim((string)($dados['modelo'] ?? '')),
        'descricao' => trim((string)($dados['descricao'] ?? '')),
        'especificacoes' => trim((string)($dados['especificacoes'] ?? '')),
        'categoria' => trim((string)($dados['categoria'] ?? '')),
        'preco' => trim((string)($dados['preco'] ?? '')),
        'estoque' => trim((string)($dados['estoque'] ?? '')),
        'ano' => trim((string)($dados['ano'] ?? '')),
        'status' => trim((string)($dados['status'] ?? '')),
    ];

    if ($produto['nome'] === '' || mb_strlen($produto['nome']) < 2 || mb_strlen($produto['nome']) > 120) {
        $erros[] = 'O nome é obrigatório e deve ter entre 2 e 120 caracteres.';
    }

    if ($produto['marca'] === '' || mb_strlen($produto['marca']) > 80) {
        $erros[] = 'A marca é obrigatória e deve ter no máximo 80 caracteres.';
    }

    if (mb_strlen($produto['modelo']) > 120) {
        $erros[] = 'O modelo deve ter no máximo 120 caracteres.';
    }

    if (mb_strlen($produto['especificacoes']) > 2000) {
        $erros[] = 'As especificações devem ter no máximo 2.000 caracteres.';
    }

    if ($produto['descricao'] === '' || mb_strlen($produto['descricao']) > 2000) {
        $erros[] = 'A descrição é obrigatória e deve ter no máximo 2.000 caracteres.';
    }

    if (!in_array($produto['categoria'], categoriasPermitidas(), true)) {
        $erros[] = 'Selecione uma categoria válida.';
    }

    $precoNormalizado = str_replace(',', '.', $produto['preco']);
    if ($precoNormalizado === '' || !is_numeric($precoNormalizado) || (float)$precoNormalizado < 0 || (float)$precoNormalizado > 99999999.99) {
        $erros[] = 'O preço deve ser numérico, estar entre zero e R$ 99.999.999,99 e não pode ser negativo.';
    } else {
        $produto['preco'] = number_format((float)$precoNormalizado, 2, '.', '');
    }

    if ($produto['estoque'] === '' || filter_var($produto['estoque'], FILTER_VALIDATE_INT) === false || (int)$produto['estoque'] < 0) {
        $erros[] = 'O estoque deve ser um número inteiro maior ou igual a zero.';
    } else {
        $produto['estoque'] = (string)(int)$produto['estoque'];
    }

    $anoAtual = (int)date('Y');
    if ($produto['ano'] === '' || filter_var($produto['ano'], FILTER_VALIDATE_INT) === false || (int)$produto['ano'] < 1900 || (int)$produto['ano'] > $anoAtual) {
        $erros[] = "O ano deve ser um número entre 1900 e {$anoAtual}.";
    } else {
        $produto['ano'] = (string)(int)$produto['ano'];
    }

    if (!in_array($produto['status'], statusPermitidos(), true)) {
        $erros[] = 'Selecione um status válido.';
    }

    return [$produto, $erros];
}

function redirecionar(string $url): never
{
    header("Location: {$url}");
    exit;
}

function flash(string $tipo, string $mensagem): void
{
    iniciarSessao();
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function obterFlash(): ?array
{
    iniciarSessao();
    $mensagem = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($mensagem) ? $mensagem : null;
}

function exigirPostComCsrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Solicitação inválida ou expirada. Recarregue a página e tente novamente.');
    }
}

function obterCarrinho(): array
{
    iniciarSessao();
    return is_array($_SESSION['carrinho'] ?? null) ? $_SESSION['carrinho'] : [];
}

function salvarCarrinho(array $carrinho): void
{
    iniciarSessao();
    $_SESSION['carrinho'] = $carrinho;
}

function quantidadeCarrinho(): int
{
    return array_sum(array_map('intval', obterCarrinho()));
}

function formatarPreco(float|string $preco): string
{
    return 'R$ ' . number_format((float)$preco, 2, ',', '.');
}

function adicionarAoCarrinho(int $id, int $quantidade = 1): void
{
    $carrinho = obterCarrinho();
    $carrinho[$id] = max(1, (int)($carrinho[$id] ?? 0) + $quantidade);
    salvarCarrinho($carrinho);
}

function atualizarCarrinho(array $quantidades): void
{
    $carrinho = [];
    foreach ($quantidades as $id => $quantidade) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $quantidade = filter_var($quantidade, FILTER_VALIDATE_INT);
        if ($id && $id > 0 && $quantidade !== false && $quantidade > 0) {
            $carrinho[$id] = min($quantidade, 99);
        }
    }
    salvarCarrinho($carrinho);
}

function removerDoCarrinho(int $id): void
{
    $carrinho = obterCarrinho();
    unset($carrinho[$id]);
    salvarCarrinho($carrinho);
}

function obterComparacao(): array
{
    iniciarSessao();
    return is_array($_SESSION['comparacao'] ?? null) ? $_SESSION['comparacao'] : [];
}

function salvarComparacao(array $ids): void
{
    iniciarSessao();
    $_SESSION['comparacao'] = array_values(array_unique(array_filter(array_map('intval', $ids))));
}

function alternarComparacao(int $id): void
{
    $ids = obterComparacao();
    if (in_array($id, $ids, true)) {
        $ids = array_values(array_diff($ids, [$id]));
    } elseif (count($ids) < 3) {
        $ids[] = $id;
    }
    salvarComparacao($ids);
}

function inicialCategoria(string $categoria): string
{
    return mb_strtoupper(mb_substr($categoria, 0, 1));
}
