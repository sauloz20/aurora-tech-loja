<?php
declare(strict_types=1);

require_once __DIR__ . '/../funcoes.php';

function verificar(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException('Falhou: ' . $mensagem);
    }
}

$base = [
    'nome' => 'Produto de teste',
    'marca' => 'Aurora',
    'modelo' => 'Teste 1',
    'descricao' => 'Descrição válida para o produto.',
    'especificacoes' => 'Tela | Memória | Bateria',
    'categoria' => 'Celulares',
    'preco' => '100,50',
    'estoque' => '3',
    'ano' => (string)date('Y'),
    'status' => 'Ativo',
];

[, $erros] = validarProduto($base);
verificar($erros === [], 'produto válido deve passar');

$casosInvalidos = [
    ['preco' => '-1'],
    ['estoque' => '-2'],
    ['categoria' => 'Categoria inventada'],
    ['status' => 'Pendente'],
    ['ano' => (string)((int)date('Y') + 1)],
    ['marca' => ''],
];

foreach ($casosInvalidos as $alteracoes) {
    [, $erros] = validarProduto(array_merge($base, $alteracoes));
    verificar($erros !== [], 'dados inválidos devem ser rejeitados');
}

verificar(classeCategoria('Casa inteligente') === 'casa-inteligente', 'classe segura de categoria');
verificar(inicialCategoria('Áudio') === 'Á', 'inicial UTF-8 de categoria');

echo "Todos os testes de funções passaram.\n";
