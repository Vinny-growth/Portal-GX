# Instalação de uma nova instância white-label (Fase 5)

Provisiona uma instância nova da plataforma (ex.: **GX México**, **plataforma de Educação**)
a partir de um banco vazio — **sem conteúdo da GX**. Roda no stack atual (Apache/nginx +
PHP 8.3 + MySQL); Docker é um wrapper opcional (ver o fim).

O instalador é o comando `php spark app:setup`. Ele importa o **schema base da plataforma**
(`app/Database/schema/base_schema.sql` — só estrutura, 114 tabelas, zero dados/PII), semeia as
linhas mínimas de boot, grava o `brand_settings` pelos seus parâmetros, liga os módulos
escolhidos e cria o admin.

---

## 1. Pré-requisitos

- PHP 8.3, MySQL 5.7+/8.0, servidor web (Apache/nginx) apontando para `public/`.
- Código do repositório clonado no host (ex.: `/www/wwwroot/<marca>`).

## 2. Criar o banco + usuário (passo de operador/root)

O usuário de aplicação **não tem** privilégio de `CREATE DATABASE` (por segurança). Crie o
banco com um usuário administrativo do MySQL:

```sql
CREATE DATABASE gxmx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gxmx'@'localhost' IDENTIFIED BY 'senha-forte';
GRANT ALL PRIVILEGES ON gxmx_db.* TO 'gxmx'@'localhost';
FLUSH PRIVILEGES;
```

## 3. Configurar o `.env`

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://gx.mx/'

database.default.hostname = localhost
database.default.database = gxmx_db
database.default.username = gxmx
database.default.password = senha-forte
database.default.DBDriver = MySQLi

# White-label: gateway/CRM do módulo Courses (só se ligado) — ver §6
# COURSES_PAYMENT_GATEWAY = mercadopago
# COURSES_MP_ACCESS_TOKEN = ...
# COURSES_CRM_WEBHOOK_SECRET = ...
```

## 4. Rodar o instalador

Interativo (pergunta o que faltar):

```bash
php spark app:setup
```

Ou com flags (não-interativo). Simule antes com `--dry-run` (não escreve nada):

```bash
php spark app:setup --dry-run \
  --brand-name "GX México" --brand-email "hola@gx.mx" \
  --locale es-MX --currency MXN --timezone America/Mexico_City \
  --admin-user admin --admin-email admin@gx.mx --admin-pass "SenhaForte123" \
  --modules wealth,simulators

# revisado o dry-run, rode de verdade (remova --dry-run):
php spark app:setup --brand-name "GX México" ... --modules wealth,simulators
```

**Opções principais:**

| Flag | Descrição | Default |
|---|---|---|
| `--brand-name` | Nome de exibição da marca | *(pergunta)* |
| `--brand-legal` | Razão social | = brand-name |
| `--brand-email` | E-mail de contato | *(pergunta)* |
| `--locale` | `pt-BR` \| `es-MX` \| … (dirige idioma/moeda/formatação) | pt-BR |
| `--currency` | `BRL` \| `MXN` \| `USD` | BRL |
| `--timezone` | Fuso | America/Sao_Paulo |
| `--color-primary` / `--color-gold` / `--color-secondary` | Cores da marca (#hex) | tokens GX |
| `--admin-user` / `--admin-email` / `--admin-pass` | Credenciais do admin (Super Admin) | *(pergunta)* |
| `--modules` | `default` (usa enabled_default de cada módulo) \| `all` \| `none` \| lista `wealth,simulators` | default |
| `--db` / `--host` / `--user` / `--pass` | Sobrescreve a conexão (provisionar outro banco sem trocar o .env) | .env |
| `--dry-run` | Simula (não escreve) | — |
| `--force` | Reinstala por cima (banco já instalado) | — |

O instalador é **idempotente**: pula linhas que já existem; aborta se `brand_settings` já
existir (salvo `--force`).

## 5. Módulos: migrations e conteúdo

O `base_schema.sql` traz as tabelas dos módulos (Wealth, Simulators, Courses…) e marca as
migrations correspondentes como aplicadas. Desde 21/jul/2026, quando o `app:setup` roda contra
o banco **default do `.env`**, ele mesmo aplica ao final o delta de migrations (`migrate --all`)
e semeia os **dados atuariais** (`ActuarialSheetSeeder` — obrigatórios p/ o simulador de seguro;
sem eles a API retorna "Sem taxas para idade"). Com `--demo`, semeia também o conteúdo demo do
Courses. Se instalar num banco custom (`--db`/`--host`), aponte o `.env` pro novo banco e rode:

```bash
php spark migrate --all                  # migrations de todas as namespaces
php spark db:seed ActuarialSheetSeeder   # taxas/fatores da planilha atuarial
php spark db:seed "Modules\Courses\Database\Seeds\CoursesDemoSeeder"  # (opcional) demo
```

Cron recomendado com o módulo Courses ligado (reconciliação diária de status):

```bash
0 4 * * * cd /caminho/da/instalacao && php spark courses:expire-sweep
```

## 6. Configuração por módulo (se ligado)

- **Courses (educação):** defina no `.env` o gateway (`COURSES_PAYMENT_GATEWAY`,
  `COURSES_MP_ACCESS_TOKEN` ou `COURSES_STRIPE_SECRET`), o preço do plano
  (`COURSES_PLAN_AMOUNT`/`COURSES_PLAN_CURRENCY`) e o segredo do webhook do CRM
  (`COURSES_CRM_WEBHOOK_SECRET`). Ligue o módulo em `--modules` ou depois pela tabela `modules`.
  **Pagamentos reais exigem também os segredos de webhook**: `COURSES_STRIPE_WEBHOOK_SECRET`
  (obrigatório com Stripe — sem ele todo webhook é rejeitado) e `COURSES_MP_WEBHOOK_SECRET`
  (recomendado com MercadoPago — valida o header `x-signature`). O webhook só ativa pagamentos
  com valor/moeda iguais ao plano e revoga o acesso em estorno (`charge.refunded`/`refunded`).

## 7. Verificar

```bash
curl -I https://gx.mx/           # 200
# login admin em https://gx.mx/admin/login (credenciais do passo 4)
```

---

## Docker (opcional, Fase 5+)

Docker **não** muda a performance em VPS Linux (container ≈ nativo) e mantém a edição de
arquivos direto no host (bind-mount). Quando quiser reprodutibilidade, um `compose.yml` sobe
`app` (PHP-FPM) + `db` (MySQL) e roda o **mesmo** `php spark app:setup` dentro do container:

```bash
docker compose run --rm app php spark app:setup --brand-name "..." --modules ...
```

O instalador é agnóstico de infra — o passo 2 (criar DB) vira um serviço `db` no compose.
