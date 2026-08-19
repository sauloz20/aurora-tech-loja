# Guia de apresentação — Aurora Tech

## Ordem recomendada

1. **Vitrine:** abra `index.php` e mostre a identidade visual, os doze produtos ativos, a busca e o filtro entre celulares, notebooks, tablets, acessórios, áudio e casa inteligente.
2. **Detalhe e comparação:** abra um celular ou notebook, mostre a página detalhada com marca, modelo e especificações, e adicione até três produtos à comparação.
3. **CREATE:** clique em Administração, abra o cadastro, preencha um novo produto com marca, modelo e especificações e salve. Explique que o formulário envia `POST` para `salvar.php`, que chama `validarProduto()` e executa `INSERT` com PDO.
4. **READ:** abra Produtos e mostre o registro na tabela. Explique que `listar.php` executa `SELECT` e que os dados são escapados com `htmlspecialchars()` por meio de `e()`.
5. **UPDATE:** clique em Editar, altere preço ou estoque e salve. Mostre que `editar.php` carrega o produto e `atualizar.php` executa `UPDATE`.
6. **DELETE:** use Excluir, confirme a ação e mostre que a operação ocorre via `POST` protegido por token CSRF.
7. **Dashboard:** abra o dashboard e mostre total de produtos, produtos ativos, estoque baixo, pedidos e vendas demonstrativas.
8. **Venda:** volte à vitrine, adicione um produto, altere a quantidade no carrinho e avance para checkout.
9. **Validação:** tente enviar um e-mail inválido ou deixar campos obrigatórios vazios. Mostre que a validação acontece no PHP, independentemente do HTML.
10. **Pedido:** conclua o checkout demonstrativo e abra Administração > Pedidos para exibir o pedido criado e o estoque reduzido.

## Fala técnica curta

O projeto mantém a separação exigida no trabalho: os arquivos de tela renderizam HTML; os handlers recebem `POST`, validam e redirecionam; `funcoes.php` concentra regras reutilizáveis; `conexao.php` cria a conexão PDO; e `banco.sql` define as tabelas e relacionamentos.

A loja amplia o CRUD sem esconder os requisitos originais. O cadastro de produtos continua sendo o núcleo do trabalho, enquanto carrinho, checkout e pedidos demonstram como o mesmo domínio poderia evoluir para um sistema real. O pagamento é somente simulado, porque o objetivo é demonstrar o fluxo e não processar uma cobrança verdadeira.

## Critérios e evidências

| Critério do trabalho | Evidência para mostrar |
| --- | --- |
| CRUD completo | Cadastro, tabela, edição e exclusão funcionando |
| Separação front/back | Arquivos separados como `cadastro.php`, `salvar.php`, `funcoes.php` e `conexao.php` |
| Validação backend | `validarProduto()` e validação do checkout em `finalizar.php` |
| Banco MySQL | `banco.sql`, tabelas `produtos`, `pedidos` e `pedido_itens` |
| Organização | Código dividido por responsabilidade e README |
| Interface | Vitrine Aurora Tech, carrinho, checkout e painel administrativo |
