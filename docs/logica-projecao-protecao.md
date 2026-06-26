# Lógica de Projeção de Proteção — Simulador GX (resumo para o Claude Code)

> Resumo funcional da lógica que roda hoje no portal (Simulador de Seguro de Vida
> Resgatável). Serve de base para construir uma **versão completa do simulador com
> planejamento financeiro**. Tudo aqui está implementado e verificado no código —
> as referências de arquivo estão no fim.

---

## 1. Conceito

O simulador transforma um **diagnóstico financeiro** em duas coisas:

1. **Proteção recomendada** — o capital de seguro ideal, dirigido pelo *objetivo* do cliente.
2. **Projeção de longo prazo** — como esse seguro (Whole Life com pagamento finito,
   WL10/WL20) forma uma **reserva resgatável** (cash value) que, em algum momento,
   **ultrapassa tudo o que foi pago** (break-even) — daí em diante a proteção "sai de graça".

O gancho de planejamento financeiro são **4 curvas ao longo da vida**: a renda que a
família perderia (*vida produtiva*), o *patrimônio* crescendo, a *reserva* do seguro e o
*total aportado*.

---

## 2. Entradas (diagnóstico)

| Campo | Uso |
|---|---|
| `idade` (14–65), `sexo` (M/F) | indexam as tabelas atuariais (taxa + fatores de resgate) |
| `estrategia` (WL10 / WL20) | define `pay_years` = 10 ou 20 (anos de aporte) |
| `renda_mensal` | dirige proteção familiar / aposentadoria + curva de vida produtiva |
| `patrimonio_imobiliario` + `patrimonio_financeiro` | `patrimonio` = soma → sucessão + curva de patrimônio |
| `dividas` | objetivo "quitar dívidas" |
| `uf` (estado) | alíquota de ITCMD para o custo de sucessão |
| `objetivo` | **chave** que escolhe a fórmula da proteção recomendada |

---

## 3. Passo 1 — Proteção recomendada (objetivo → capital)

O cliente **não digita** o capital; ele vem do objetivo. Fórmulas (valores arredondados
para R$ 10 mil no fim):

```
custo_sucessao = patrimonio × (ITCMD_uf + INVENTARIO_RATE)   // INVENTARIO_RATE = 8%

objetivo = "protecao_familiar"  →  capital = renda_mensal × 60          // ~5 anos de renda
objetivo = "sucessao"           →  capital = custo_sucessao             // liquidez p/ herdeiros
objetivo = "quitar_dividas"     →  capital = custo_sucessao + dividas
objetivo = "aposentadoria"      →  capital = max(0, renda_mensal − TETO_INSS) × 120   // 10 anos
```

- `TETO_INSS = 8.475,55`
- `INVENTARIO_RATE = 0,08` (6% advogado + 2% cartório)
- ITCMD por UF: tabela de referência (2–8%) em `QuotationGate::UF_ITCMD`
- O capital final vira a cobertura de **vida (WL)**; riders (Doenças Graves etc.) são opcionais.

> ⚠️ A versão antiga somava tudo e dava valores absurdos (R$ 13 mi). O modelo **dirigido por
> objetivo** é o correto — manter.

---

## 4. Passo 2 — Prêmio atuarial (por cobertura)

```
base    = capital / 1000           // exceto Renda Hospitalar, que usa capital cheio
liquido = base × taxa(idade, sexo, cobertura)
bruto   = liquido × (1 + IOF)      // IOF = 0,38%  → ×1,0038
mensal  = bruto × FRAC_MENSAL      // fracionamento mensal = ×0,09  (12×0,09 = 1,08 ⇒ +8% no mensal)
```

- A taxa sai da tabela `actuarial_rates` (idade × sexo), coluna conforme cobertura
  (WL10/WL20, DG-Plus, Invalidez, Renda Hospitalar, Morte Acidental…).
- O **WL é obrigatório** e é o que forma a reserva. Os riders só somam ao "total mensal".

---

## 5. Passo 3 — Projeção de longo prazo (reserva / cash value)

Loop ano a ano, da **idade+1 até 100** (ano 1 da planilha = idade+1):

```
i           = 1 .. (100 − idade)
idade_no_ano= idade + i
infl        = (1 + IPCA)^(i−1)            // IPCA = 5,5% a.a.; o 1º ano NÃO inflaciona

capital(i)      = capital × infl                      // capital segurado corrige por IPCA
aporte_mensal(i)= wl_mensal × infl                    // o prêmio também corrige
aporte_anual(i) = aporte_mensal(i) × 12 × 0,97352     // SÓ enquanto i ≤ pay_years (depois = 0: "apólice quitada")
pago_acum      += aporte_anual(i)

reserva(i)      = capital(i) × fator(idade, sexo, ano = i+1)   // fator: matriz de resgate
```

**Destaques extraídos da curva:**
- **Break-even** = primeiro ano em que `reserva ≥ pago_acum` (a reserva supera o pago).
- **Reserva aos 65**, **reserva final (100)** e **múltiplo** = reserva_final / pago_total.
- **Fim dos aportes** = idade + pay_years (quanto foi aportado e a reserva naquele ponto).

Constantes: `IPCA=0,055 · IOF=0,0038 · FRAC_MENSAL=0,09 · APORTE_FATOR=0,97352`.

---

## 6. Passo 4 — As 4 curvas de planejamento financeiro

É o coração visual do relatório. Para cada idade (cortado em `MAX_IDADE = 90`):

| Curva | Cor | Fórmula | Significado |
|---|---|---|---|
| **Vida produtiva** | dourado tracejado | `renda_mensal × 12 × max(0, 65 − idade)` | renda futura que a família perderia num imprevisto — decresce até a aposentadoria |
| **Patrimônio** | azul | `patrimonio × (1 + 0,055)^(idade − idade_inicial)` | evolução patrimonial projetada |
| **Reserva resgatável** | verde | `reserva(i)` (passo 3) | o que o seguro acumula e devolve |
| **Total aportado** | vermelho | `pago_acum` (passo 3) | tudo o que foi pago |

Narrativa: *"sua vida produtiva vale hoje R$ X — é o que o seguro garante; com o tempo o
patrimônio cresce e a reserva passa a ultrapassar o que você pagou."*

- `PATRIMONIO_CRESC = 0,055` (5,5% a.a.)
- `IDADE_APOSENTADORIA = 65`

---

## 7. Gate / segurança (manter na versão completa)

- **Todo o cálculo é server-side.** O *preview* devolve as curvas **indexadas 0–100** (sem R$).
- Os valores em **R$ só saem após gravar o lead** (endpoint de *unlock*).
- O blur/cadeado no gráfico é só estética sobre o dado ausente — o número real nem trafega antes.
- Endpoints: `api/quotation/preview` (sem R$) e `api/quotation/unlock` (dossiê em R$ + grava lead → CRM/Meta).

---

## 8. Onde está no código (fonte da verdade)

| Lógica | Arquivo |
|---|---|
| Prêmio + projeção + destaques | `app/Libraries/QuotationEngine.php` |
| Recomendação por objetivo, ITCMD/UF, parse de entrada, dossiê | `app/Libraries/QuotationGate.php` |
| Diagnóstico, `computeRecommendation()`, gráficos, relatório | `app/Views/simulators/seguro_resgatavel.php` |
| Tabelas atuariais | `actuarial_rates` (taxas) + `reserve_factors` (matriz de resgate) |
| Dados reais (planilha) | `app/Database/data/sheet_actuarial.json` |
| Endpoints | `ApiController::quotationPreview` / `quotationUnlock` |
| Identidade visual | `colors_and_type.css` + `_seguro_resgatavel_head.php` (ver briefing de design) |

---

## 9. Para a "versão completa com planejamento financeiro"

Sugestões de evolução, reaproveitando o que já existe:

1. **Independência financeira**: além da curva de patrimônio simples (5,5%), permitir
   *aporte mensal de investimento* + *retorno-alvo real* e calcular o ano em que o
   patrimônio sustenta a renda desejada (já existe lógica parecida em
   `WealthManagerController::computeFinancialIndependence`).
2. **Fluxo de caixa**: usar `renda − despesas` para derivar a capacidade de aporte e
   confrontar com o prêmio do seguro (quanto sobra para investir).
3. **Metas/objetivos** com horizonte e valor-alvo (aposentadoria, imóvel, educação dos
   filhos), cada uma virando uma curva/projeção.
4. **Alocação de carteira** recomendada por perfil de risco (renda fixa / ações / exterior),
   exibida em tabela mono + KPIs (ver o exemplo no briefing de design).
5. **Seguro como parte do plano**: posicionar a reserva resgatável como uma classe de ativo
   protegida e a vida produtiva como o "passivo" que ela cobre — integrar as 4 curvas num
   único painel de planejamento.
6. Manter **gate server-side**, **identidade GX Nexus** e o **relatório imprimível (PDF/A4)**.
