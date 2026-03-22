# Home, Blog e Simuladores

## Novo mapa publico

- `/` agora renderiza a nova home institucional via `HomeController::index`.
- `/blog` passa a renderizar a antiga home editorial do portal via `HomeController::blog`.
- `/posts` continua existindo como archive/listagem tradicional via `HomeController::posts`.
- `/simuladores` passa a funcionar como hub visual centralizado via `HomeController::simulatorsHub`.
- As rotas individuais dos simuladores continuam identicas e seguem sendo resolvidas como paginas publicas do CMS:
  - `/simulador-de-risco-cambial`
  - `/aurum-simulador-de-custo-de-capital`
  - `/simulador-mercado-de-capitais`
  - `/simulador-de-custo-de-antecipacao`
  - `/simulador-consorcio`
- O alias legado `/simulador-aurum` agora faz redirecionamento permanente para `/aurum-simulador-de-custo-de-capital`.

## Protecao de links e SEO

- Nenhum slug publico de simulador foi trocado.
- O hub aponta para as URLs antigas, sem proxys, sem reescrita intermediaria e sem nesting de rotas.
- O sitemap passa a incluir:
  - raiz `/`
  - `/blog`
  - `/simuladores`
  - paginas publicas do CMS com `visibility = 1`, `need_auth = 0` e `page_type = page`
- A canonical continua autorreferente pelo header global, entao cada nova pagina se canoniza nela mesma.

## Deploy recomendado

1. Publicar controller, views, rotas e sitemap juntos.
2. Rodar a geracao de sitemap logo apos o deploy.
3. Validar manualmente:
   - `/`
   - `/blog`
   - `/simuladores`
   - todos os cinco simuladores
   - `/simulador-aurum`
4. Confirmar no sitemap final a presenca de `/blog` e `/simuladores`.
5. Reenviar o sitemap no Search Console.
6. Monitorar 404, hits em `/simulador-aurum` e eventuais backlinks antigos para a raiz.

## 301 opcional da raiz para `/blog`

Use o arquivo `docs/redirect-root-to-blog.example.htaccess` apenas em um cenario temporario de consolidacao de indexacao.

Nao habilite esse 301 ao mesmo tempo em que a nova home institucional precisa responder em `/`.
