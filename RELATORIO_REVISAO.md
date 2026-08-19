# Relatório de revisão — CRUD de Produtos

**Projeto analisado:** `sauloz20/crud-php-produtos`  
**Escopo:** revisão de segurança, integridade do banco, fluxo CRUD, usabilidade e manutenção.

## Síntese executiva

O projeto é um CRUD PHP pequeno e didático, com separação básica entre conexão, funções de validação, telas e handlers. A base já utilizava PDO com prepared statements e escapava parte das saídas HTML, mas havia problemas que impediriam uma instalação limpa e deixariam a exclusão vulnerável a requisições indesejadas. A revisão priorizou segurança de requisições, configuração de ambiente, bootstrap do banco e consistência dos formulários.

## Achados principais

| Prioridade | Achado | Impacto | Situação |
| --- | --- | --- | --- |
| Crítica | `banco.sql` não fechava o `CREATE TABLE` e não criava o banco | Instalação limpa falhava | Corrigido |
| Alta | Exclusão era feita por `GET` | Um link, crawler ou requisição forjada poderia excluir registros | Corrigido com `POST` + CSRF |
| Alta | Não havia proteção CSRF nos formulários de escrita | Operações poderiam ser disparadas por terceiros | Corrigido |
| Média | Credenciais do banco estavam fixas em `conexao.php` | Configuração insegura e difícil de transportar | Corrigido com variáveis de ambiente |
| Média | O cadastro não reaproveitava todos os valores após erro | Perda de dados digitados e pior experiência | Corrigido |
| Média | `display_errors` estava ativado na tela de cadastro | Risco de exposição de detalhes internos | Removido |
| Baixa | Ausência de documentação de instalação | Maior dificuldade para executar o projeto | README adicionado |

## Alterações realizadas

A camada comum em `funcoes.php` agora centraliza a inicialização de sessão com `HttpOnly`, `SameSite=Lax` e `secure` quando HTTPS está ativo. Também foram adicionadas geração e validação de tokens CSRF, uma função para exigir `POST` protegido e escape de saída com `ENT_SUBSTITUTE`.

Os endpoints `salvar.php`, `atualizar.php` e `excluir.php` passaram a exigir método `POST` e token CSRF válido. A exclusão deixou de usar query string e agora utiliza um formulário próprio na listagem, mantendo a confirmação no navegador e evitando exclusões acidentais por simples navegação.

O formulário de cadastro passou a preservar categoria, status, preço, estoque e ano quando a validação falha. O modo de depuração explícito foi removido. A conexão PDO agora aceita `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASSWORD`, registra o erro no log do servidor e não expõe a mensagem interna da exceção ao usuário.

O esquema SQL foi completado com criação idempotente do banco, `InnoDB`, limites coerentes, coluna `atualizado_em` e índices para status e categoria. O README documenta requisitos, configuração e execução local.

## Validação executada

A validação sintática foi executada com PHP 8.3.6 para todos os arquivos PHP, sem erros. Também foi executado `git diff --check`, sem problemas de whitespace. Não foi realizado teste de integração contra MySQL porque não havia um servidor MySQL configurado no ambiente de revisão; recomenda-se importar `banco.sql` e executar o fluxo completo antes de publicar.

## Recomendações para uma próxima versão

A aplicação ainda não possui autenticação ou autorização. Se for exposta à internet, deve-se implementar login, controle de acesso por operação e gerenciamento seguro de credenciais. Também é recomendável adicionar testes automatizados para `validarProduto`, testes de integração para cada operação CRUD, paginação e busca na listagem, tratamento de mensagens de erro de banco e um pipeline de CI que execute lint e testes a cada alteração.

## Arquivos entregues

O diretório revisado contém o código alterado, o script SQL corrigido, o README de instalação e este relatório. As alterações estão prontas para revisão e commit no repositório original.
