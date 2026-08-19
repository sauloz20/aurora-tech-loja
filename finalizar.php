<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

exigirPostComCsrf();
iniciarSessao();
$dados = [
    'nome' => trim((string)($_POST['nome'] ?? '')),
    'email' => trim((string)($_POST['email'] ?? '')),
    'cep' => trim((string)($_POST['cep'] ?? '')),
    'endereco' => trim((string)($_POST['endereco'] ?? '')),
    'numero' => trim((string)($_POST['numero'] ?? '')),
    'pagamento' => trim((string)($_POST['pagamento'] ?? '')),
];
$erros = [];
if (mb_strlen($dados['nome']) < 3 || mb_strlen($dados['nome']) > 120) $erros[] = 'Informe seu nome completo.';
if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) $erros[] = 'Informe um e-mail válido.';
if (!preg_match('/^\d{5}-?\d{3}$/', $dados['cep'])) $erros[] = 'Informe um CEP válido.';
if ($dados['endereco'] === '' || mb_strlen($dados['endereco']) > 180) $erros[] = 'Informe o endereço de entrega.';
if ($dados['numero'] === '' || mb_strlen($dados['numero']) > 20) $erros[] = 'Informe o número do endereço.';
if (!in_array($dados['pagamento'], ['Pix', 'Cartão', 'Boleto'], true)) $erros[] = 'Selecione uma forma de pagamento válida.';

$carrinho = obterCarrinho();
if (!$carrinho) $erros[] = 'Seu carrinho está vazio.';
if ($erros) {
    $_SESSION['checkout_erros'] = $erros;
    $_SESSION['checkout_valores'] = $dados;
    redirecionar('checkout.php');
}

try {
    $pdo->beginTransaction();
    $itens = [];
    $subtotal = 0.0;
    foreach ($carrinho as $id => $quantidade) {
        $stmt = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE id = :id AND status = :status FOR UPDATE');
        $stmt->execute(['id' => $id, 'status' => 'Ativo']);
        $produto = $stmt->fetch();
        $quantidade = (int)$quantidade;
        if (!$produto || $quantidade < 1 || $quantidade > (int)$produto['estoque']) {
            throw new RuntimeException('Um dos produtos ficou sem estoque.');
        }
        $totalItem = $quantidade * (float)$produto['preco'];
        $subtotal += $totalItem;
        $itens[] = [$produto, $quantidade, $totalItem];
    }
    $frete = $subtotal < 299 ? 24.90 : 0;
    $total = $subtotal + $frete;
    $stmt = $pdo->prepare('INSERT INTO pedidos (cliente_nome, cliente_email, cep, endereco, numero, pagamento, subtotal, frete, total, status) VALUES (:nome, :email, :cep, :endereco, :numero, :pagamento, :subtotal, :frete, :total, :status)');
    $stmt->execute(['nome' => $dados['nome'], 'email' => $dados['email'], 'cep' => $dados['cep'], 'endereco' => $dados['endereco'], 'numero' => $dados['numero'], 'pagamento' => $dados['pagamento'], 'subtotal' => $subtotal, 'frete' => $frete, 'total' => $total, 'status' => 'Recebido']);
    $pedidoId = (int)$pdo->lastInsertId();
    $itemStmt = $pdo->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, produto_nome, preco_unitario, quantidade, total) VALUES (:pedido_id, :produto_id, :produto_nome, :preco, :quantidade, :total)');
    $estoqueStmt = $pdo->prepare('UPDATE produtos SET estoque = estoque - :quantidade_subtrair WHERE id = :id AND estoque >= :quantidade_min');
    foreach ($itens as [$produto, $quantidade, $totalItem]) {
        $itemStmt->execute(['pedido_id' => $pedidoId, 'produto_id' => $produto['id'], 'produto_nome' => $produto['nome'], 'preco' => $produto['preco'], 'quantidade' => $quantidade, 'total' => $totalItem]);
        $estoqueStmt->execute(['quantidade_subtrair' => $quantidade, 'quantidade_min' => $quantidade, 'id' => $produto['id']]);
    }
    $pdo->commit();
    $_SESSION['carrinho'] = [];
    $_SESSION['ultimo_pedido'] = ['id' => $pedidoId, 'total' => $total];
    redirecionar('pedido.php?id=' . $pedidoId);
} catch (Throwable $e) {
    error_log('Falha ao finalizar pedido: ' . $e->getMessage());
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['checkout_erros'] = ['Não foi possível concluir o pedido agora. Verifique o estoque e tente novamente.'];
    $_SESSION['checkout_valores'] = $dados;
    redirecionar('checkout.php');
}
