# Relatório final — Aurora Tech

## Resultado

O CRUD original foi transformado em uma loja virtual acadêmica funcional, mantendo os requisitos do trabalho e adicionando recursos de catálogo, operação e experiência de compra.

## Funcionalidades implementadas

| Área | Recursos |
| --- | --- |
| Vitrine | Catálogo com 12 produtos, busca, filtros e cards por categoria |
| Produto | Página detalhada com marca, modelo, descrição, especificações, estoque e quantidade |
| Comparação | Comparação de até três produtos lado a lado |
| Carrinho | Adição, quantidade, remoção, subtotal, frete e total |
| Checkout | Validação de nome, e-mail, CEP, endereço e pagamento demonstrativo |
| Pedidos | Transação MySQL, itens históricos, baixa de estoque e confirmação |
| Dashboard | Indicadores de produtos, ativos, estoque baixo, pedidos e vendas demonstrativas |
| Administração | CRUD de produtos, marca, modelo, especificações e painel de pedidos |
| Segurança | PDO preparado, CSRF, sessão protegida e saída escapada |
| Qualidade | Testes de funções, lint PHP e workflow de GitHub Actions |

## Catálogo

A loja trabalha com celulares, notebooks, tablets, acessórios, áudio e casa inteligente. Cada categoria possui dois produtos de demonstração com preço, estoque e especificações técnicas.

## Requisitos do trabalho

O sistema continua atendendo CREATE, READ, UPDATE e DELETE. O cadastro utiliza `INSERT`, a listagem utiliza `SELECT`, a edição utiliza `UPDATE` e a exclusão utiliza `DELETE`. As telas e os processadores permanecem separados, as validações principais estão em `funcoes.php`, o banco é MySQL e as consultas com dados de usuário usam prepared statements.

## Validações executadas

Todos os arquivos PHP passaram no lint com PHP 8.3. Os testes automatizados de `validarProduto()` passaram, incluindo preço negativo, estoque inválido, categoria inválida, status inválido, ano futuro, marca vazia e inicial UTF-8. As páginas `/`, `/produto.php?id=1`, `/comparar.php`, `/dashboard.php`, `/carrinho.php`, `/listar.php` e `/pedidos.php` retornaram HTTP 200.

Também foi executado um fluxo completo de compra: dois produtos foram adicionados ao carrinho, o checkout foi concluído com pagamento demonstrativo, o pedido foi criado, os itens foram gravados e o estoque do produto foi reduzido corretamente.

## Limites acadêmicos

O pagamento é demonstrativo e não cobra cartões ou Pix reais. Para uma loja comercial, ainda seriam necessários autenticação de administradores, gateway de pagamento, webhooks, frete real, políticas de privacidade, backups, e-mails transacionais e monitoramento.
