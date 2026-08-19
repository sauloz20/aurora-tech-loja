# Aurora Tech — Loja virtual em PHP

Este projeto evolui o CRUD de produtos do trabalho da Proz Educação para uma loja virtual demonstrativa. A aplicação mantém a separação entre interface, processamento, validação e acesso ao banco, atendendo ao CRUD obrigatório e adicionando vitrine, busca, filtros por categoria, carrinho, checkout demonstrativo e painel de pedidos. O catálogo inclui celulares, notebooks, tablets, acessórios, áudio e casa inteligente. Cada produto possui marca, modelo, descrição e especificações técnicas.

## Requisitos

- PHP 8.1 ou superior, com `pdo_mysql` e `mbstring`.
- MySQL 8.0 ou MariaDB compatível.
- Apache ou servidor embutido do PHP.

## Instalação

1. Em uma instalação nova, importe `banco_importacao.sql` no MySQL/phpMyAdmin. Esse arquivo é a versão limpa recomendada e cria o banco, as tabelas e os produtos. O arquivo `banco.sql` também permanece disponível para instalações que precisem da migração. O script cria o banco, as tabelas `produtos`, `pedidos` e `pedido_itens`, além de doze produtos de demonstração distribuídos entre celulares, notebooks, tablets, acessórios, áudio e casa inteligente. O schema também permite marca, modelo e especificações técnicas.
2. Configure a conexão. A opção mais simples é copiar `config.local.example.php` para `config.local.php` e preencher os dados do seu MySQL/MariaDB. Esse arquivo é ignorado pelo Git. Também é possível usar as variáveis `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASSWORD`, que têm prioridade sobre o arquivo local. Os padrões são `127.0.0.1`, `3306`, `crud_produtos`, `root` e senha vazia.
3. Inicie o servidor na raiz do projeto:

```bash
php -S localhost:8000
```

4. Acesse <http://localhost:8000>.

### Exemplo com XAMPP, WAMP ou Laragon

Inicie o Apache e o MySQL/MariaDB no painel do seu ambiente. Abra o phpMyAdmin, crie ou importe o arquivo `banco.sql` e confirme que existe o banco `crud_produtos`. Depois copie `config.local.example.php` para `config.local.php`. Para uma instalação local padrão, deixe `host` como `127.0.0.1`, `port` como `3306`, `name` como `crud_produtos`, `user` como `root` e `password` vazia. Se o seu MySQL tiver senha para `root`, informe-a no campo `password`.

Se a mensagem de conexão aparecer, as causas mais comuns são: serviço MySQL parado, banco `crud_produtos` ainda não importado, usuário ou senha incorretos, porta diferente de `3306`, ou extensão `pdo_mysql` desabilitada no PHP.

## Roteiro de demonstração do trabalho

Comece pela vitrine em `index.php`, mostrando a variedade de produtos, a busca, o filtro por categoria e o contador do carrinho. Em seguida, entre em um produto e clique em **Adicionar**. No carrinho, altere a quantidade e demonstre o cálculo do subtotal, frete e total.

No checkout, preencha nome, e-mail, CEP, endereço, número e forma de pagamento. O pagamento é propositalmente demonstrativo: o sistema não processa cartão real. Ao confirmar, uma transação grava o pedido e seus itens no MySQL, reduz o estoque e abre a confirmação do pedido.

Para demonstrar os requisitos do CRUD, abra **Administração > Produtos**. A partir daí, cadastre um produto com marca, modelo e especificações, veja-o na tabela, edite seus dados e exclua-o com confirmação. O **Dashboard** mostra indicadores, estoque baixo e pedidos recentes. O painel **Pedidos** mostra os pedidos gerados pelo checkout. Na vitrine, abra um produto detalhado e teste a comparação de até três itens.

## Requisitos do PDF atendidos

| Requisito | Implementação |
| --- | --- |
| CREATE | `cadastro.php` recebe o formulário e `salvar.php` executa `INSERT` preparado |
| READ | `listar.php` lista produtos e `index.php` exibe a vitrine com `SELECT` |
| UPDATE | `editar.php` carrega o produto e `atualizar.php` executa `UPDATE` |
| DELETE | `listar.php` envia `POST` protegido e `excluir.php` executa `DELETE` |
| Separação | Telas, handlers, `funcoes.php`, `conexao.php` e SQL ficam separados |
| Validação PHP | `validarProduto()` e validações do checkout no backend |
| Banco MySQL | `banco.sql` cria banco, chave primária, produtos, pedidos e itens |
| Segurança | PDO preparado, CSRF, sessão protegida e `htmlspecialchars()` |
| Interface | Vitrine variada, página detalhada, comparação, carrinho, checkout, dashboard e painel de pedidos |
| Regra de negócio | Estoque baixo, baixa de estoque na compra e frete grátis acima de R$ 299 |

## Limites da versão acadêmica

O checkout simula a escolha do meio de pagamento e não realiza cobrança real. Para uma operação comercial, seria necessário integrar um gateway, validar webhooks, implementar autenticação, incluir política de privacidade, configurar frete real, e-mail transacional, backups e monitoramento. Essa separação deixa claro na apresentação o que foi implementado para o curso e o que seria necessário para produção.
