# Changelog

Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/).
Versionamento semântico.

## [1.0.0] - 2026-10-26

### Added

- Login com a conta do `marketplace-auth` (`POST /auth/login`), token e usuário guardados na sessão do Laravel; só `seller` e `admin` entram.
- Middleware `EnsureSellerToken` em todas as telas, com redirect para o login ou 401 no envelope quando a chamada é JSON.
- Cadastro e edição da loja do vendedor (nome, endereço, descrição, chave Pix e logo) na tabela `stores`, única tabela própria.
- Produtos listados, criados, editados e desativados pela API do `marketplace-catalog`, com o preço em reais no formulário e em centavos na API.
- Pedidos recebidos pela API do `marketplace-orders`, com a parte da loja destacada do total do pedido e detalhe por item.
- Painel inicial com receita do mês, pedidos pagos, produtos ativos e gráfico de vendas por dia a partir do `marketplace-analytics`, desenhado em canvas sem biblioteca.
- `RemoteApiException`: toda falha das APIs vira mensagem em pt-BR (flash, página de erro ou envelope JSON) sem vazar a resposta original.
- Interface em Blade com Bootstrap 5 e Bootstrap Icons via npm, tokens de cor em `:root`, tema escuro e componentes `card`, `status-badge`, `alert`, `empty-state` e `page-header`.
- Textos em `lang/pt_BR`, inclusive validação e paginação.
- `GET /health` com o estado do banco.
- Testes de feature com `Http::fake()` e SQLite em memória, e unitários do `CatalogClient`.
- Dockerfile multi-stage (Vite + PHP 8.3), workflow de CI com Pint e PHPUnit.
