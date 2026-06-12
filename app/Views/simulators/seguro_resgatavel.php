<?php
$whatsAppBaseUrl = $whatsAppBaseUrl ?? '';
$simulatorsHubUrl = $simulatorsHubUrl ?? base_url('simuladores');
$termsUrl = $termsUrl ?? '#';
$previewUrl = base_url('api/quotation/preview');
$unlockUrl  = base_url('api/quotation/unlock');
$heroImgRel = 'uploads/marketing/srs_hero.webp';
$heroImgUrl = is_file(FCPATH . $heroImgRel) ? base_url($heroImgRel) : '';
$reportCoverRel = 'uploads/marketing/srs_report_cover.webp';
$reportCoverUrl = is_file(FCPATH . $reportCoverRel) ? base_url($reportCoverRel) : '';
$ufList = \App\Libraries\QuotationGate::ufList();
$itcmdMap = \App\Libraries\QuotationGate::itcmdMap();
$inventarioRate = \App\Libraries\QuotationGate::INVENTARIO_RATE;
$ufPadrao = \App\Libraries\QuotationGate::UF_PADRAO;
?>
<div class="gx-srs">

  <!-- topbar -->
  <header class="gx-srs-topbar">
    <div class="gx-srs-wrap">
      <a href="<?= esc(base_url()); ?>" class="gx-srs-brand" aria-label="GX Capital">
        <img src="<?= getLogoFooter(); ?>" alt="GX Capital">
      </a>
      <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-srs-back">&larr; Todos os simuladores</a>
    </div>
  </header>

  <!-- hero -->
  <section class="gx-srs-hero<?= $heroImgUrl ? ' has-img' : ''; ?>"<?= $heroImgUrl ? ' style="background-image: linear-gradient(90deg, var(--gx-primary-dark) 0%, rgba(0,13,35,0.95) 40%, rgba(0,13,35,0.55) 70%, rgba(12,49,99,0.30) 100%), url(\'' . esc($heroImgUrl, 'attr') . '\');"' : ''; ?>>
    <div class="gx-srs-watermark" aria-hidden="true">GXC</div>
    <div class="gx-srs-wrap">
      <div class="gx-srs-eyebrow">Seguro de Vida Resgatável · Whole Life</div>
      <h1 class="gx-srs-headline">Você não paga seguro. Você <em>constrói patrimônio.</em></h1>
      <p class="gx-srs-sub">Um plano quitado em 10 anos, corrigido todo ano, que forma uma reserva resgatável enquanto blinda sua família hoje. Veja o ponto em que a reserva ultrapassa tudo o que você pagou — e a proteção passa a sair de graça.</p>
      <div class="gx-srs-hero-signals">
        <span class="gx-srs-chip">Pagamento finito</span>
        <span class="gx-srs-chip">Correção anual (IPCA)</span>
        <span class="gx-srs-chip">Reserva resgatável</span>
        <span class="gx-srs-chip">Proteções em vida</span>
      </div>
    </div>
  </section>

  <!-- main -->
  <main class="gx-srs-main">
    <div class="gx-srs-wrap">
      <div class="gx-srs-shell">

        <!-- form -->
        <form class="gx-srs-card" id="gx-srs-form" novalidate>
          <div class="gx-srs-card-title"><span>Diagnóstico · 2 minutos</span></div>
          <p class="gx-srs-intro">Responda como num planejamento financeiro. A partir das suas respostas calculamos a proteção ideal e projetamos a reserva que você constrói.</p>

          <!-- 1. Sobre você -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label">1 · Sobre você</div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-idade">Sua idade</label>
                <input class="gx-srs-input" type="number" id="gx-srs-idade" name="idade" min="14" max="65" value="35" inputmode="numeric" required>
              </div>
              <div class="gx-srs-field">
                <label>Sexo</label>
                <div class="gx-srs-seg" role="group" aria-label="Sexo">
                  <button type="button" data-seg="sexo" data-value="M" aria-pressed="true">Masculino</button>
                  <button type="button" data-seg="sexo" data-value="F" aria-pressed="false">Feminino</button>
                </div>
                <input type="hidden" name="sexo" id="gx-srs-sexo" value="M">
              </div>
            </div>
          </div>

          <!-- 2. Família -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label">2 · Quem depende de você</div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-dependentes">Pessoas que dependem da sua renda</label>
                <select class="gx-srs-input" id="gx-srs-dependentes" name="dependentes">
                  <option value="0">Ninguém — só eu</option>
                  <option value="1">1 pessoa</option>
                  <option value="2" selected>2 pessoas</option>
                  <option value="3">3 pessoas</option>
                  <option value="4">4 ou mais</option>
                </select>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-filhos">Filhos em formação</label>
                <select class="gx-srs-input" id="gx-srs-filhos" name="filhos">
                  <option value="0">Nenhum</option>
                  <option value="1" selected>1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4 ou mais</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 3. Vida financeira e patrimônio -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label">3 · Renda e patrimônio</div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-renda">Renda mensal da família (R$)</label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-renda" name="renda_mensal" inputmode="numeric" placeholder="Ex.: 30.000" value="30.000">
                <div class="gx-srs-hint">Padrão de vida que sua renda sustenta.</div>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-uf">Estado (define o ITCMD)</label>
                <select class="gx-srs-input" id="gx-srs-uf" name="estado">
                  <?php foreach ($ufList as $u): ?>
                    <option value="<?= esc($u['uf'], 'attr'); ?>"<?= $u['uf'] === $ufPadrao ? ' selected' : ''; ?>><?= esc($u['nome']); ?> (<?= esc($u['uf']); ?>)</option>
                  <?php endforeach; ?>
                </select>
                <div class="gx-srs-hint">Alíquota usada na sucessão.</div>
              </div>
            </div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-patr-imob">Patrimônio imobiliário (R$)</label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-patr-imob" name="patrimonio_imobiliario" inputmode="numeric" placeholder="Ex.: 1.500.000" value="0">
                <div class="gx-srs-hint">Imóveis: moradia, locação, terrenos.</div>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-patr-fin">Patrimônio financeiro (R$)</label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-patr-fin" name="patrimonio_financeiro" inputmode="numeric" placeholder="Ex.: 500.000" value="0">
                <div class="gx-srs-hint">Investimentos, empresas, aplicações.</div>
              </div>
            </div>
            <div class="gx-srs-field">
              <label for="gx-srs-dividas">Dívidas / financiamentos (R$)</label>
              <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-dividas" name="dividas" inputmode="numeric" placeholder="Ex.: 200.000" value="0">
              <div class="gx-srs-hint">Saldo que a família precisaria quitar num imprevisto.</div>
            </div>
          </div>

          <!-- 4. Objetivo -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label">4 · Seu objetivo</div>
            <div class="gx-srs-field">
              <label for="gx-srs-objetivo">O que mais importa para você?</label>
              <select class="gx-srs-input" id="gx-srs-objetivo" name="objetivo">
                <option value="protecao_familiar" selected>Proteger a renda da minha família</option>
                <option value="sucessao">Planejar sucessão com liquidez</option>
                <option value="quitar_dividas">Garantir a quitação de dívidas num imprevisto</option>
                <option value="aposentadoria">Complementar a aposentadoria</option>
              </select>
            </div>
            <div class="gx-srs-field">
              <label>Em quanto tempo quer quitar o plano?</label>
              <div class="gx-srs-seg" role="group" aria-label="Estratégia">
                <button type="button" data-seg="estrategia" data-value="WL10" aria-pressed="true">10 anos</button>
                <button type="button" data-seg="estrategia" data-value="WL20" aria-pressed="false">20 anos</button>
              </div>
              <input type="hidden" name="estrategia" id="gx-srs-estrategia" value="WL10">
            </div>
          </div>

          <!-- Proteção recomendada (calculada) -->
          <div class="gx-srs-reco">
            <div class="gx-srs-reco-label">Sua proteção recomendada</div>
            <div class="gx-srs-reco-insight" id="gx-srs-reco-insight">Preencha os campos acima para calcularmos.</div>
            <div class="gx-srs-field" style="margin-top:var(--space-4); margin-bottom:var(--space-3)">
              <label for="gx-srs-cap-vida">Capital segurado <span class="gx-srs-reco-edit">(ajuste se quiser)</span></label>
              <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-cap-vida" name="capital_vida" inputmode="numeric" value="150.000" required>
            </div>
            <label class="gx-srs-check">
              <input type="checkbox" id="gx-srs-dg-toggle" checked>
              <span>Incluir proteção contra <strong>Doenças Graves</strong> <small style="opacity:.65">(capital até R$ 1 mi)</small></span>
            </label>
            <input type="hidden" id="gx-srs-cap-dg" name="capital_dg_plus" value="200000">
          </div>

          <button type="submit" class="gx-srs-btn gx-srs-btn-primary" id="gx-srs-calc">Ver meu diagnóstico e projeção</button>
          <div class="gx-srs-status" id="gx-srs-form-status"></div>
        </form>

        <!-- result -->
        <section class="gx-srs-card">
          <div class="gx-srs-card-title"><span>Patrimônio x Aporte — projeção até os 100</span></div>

          <div class="gx-srs-placeholder" id="gx-srs-placeholder">
            Preencha seu perfil e clique em “Desenhar minha projeção”.
          </div>

          <div class="gx-srs-result" id="gx-srs-result">
            <div class="gx-srs-chartwrap">
              <canvas class="gx-srs-chart-canvas" id="gx-srs-chart" height="340"></canvas>
              <div class="gx-srs-lock">
                <div class="gx-srs-lock-badge" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="0"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 id="gx-srs-lock-title">Sua reserva vira lucro a partir de um certo ponto.</h3>
                <p id="gx-srs-lock-sub">Descubra o aporte mensal exato e o tamanho da sua reserva em Reais. Liberamos o relatório completo na hora.</p>
                <button type="button" class="gx-srs-btn gx-srs-btn-gold" id="gx-srs-unlock-cta">Desbloquear relatório</button>
              </div>
            </div>

            <div class="gx-srs-legend">
              <span><i class="gx-srs-dot red"></i> O que você paga (aporte acumulado)</span>
              <span><i class="gx-srs-dot green"></i> O que você junta (reserva acumulada)</span>
            </div>

            <div class="gx-srs-kpis">
              <div class="gx-srs-kpi is-navy">
                <div class="gx-srs-kpi-label">Aporte mensal</div>
                <div class="gx-srs-kpi-value gx-srs-blur" id="gx-srs-kpi-aporte">R$ ••••</div>
                <div class="gx-srs-kpi-sub">já com IOF</div>
              </div>
              <div class="gx-srs-kpi is-dark">
                <div class="gx-srs-kpi-label">Reserva projetada aos 65</div>
                <div class="gx-srs-kpi-value gx-srs-blur" id="gx-srs-kpi-reserva65">R$ ••••</div>
                <div class="gx-srs-kpi-sub">valor de resgate estimado</div>
              </div>
              <div class="gx-srs-kpi">
                <div class="gx-srs-kpi-label">Break-even</div>
                <div class="gx-srs-kpi-value" id="gx-srs-kpi-breakeven" style="color:var(--gx-primary)">—</div>
                <div class="gx-srs-kpi-sub" id="gx-srs-kpi-breakeven-sub">reserva ultrapassa o pago</div>
              </div>
            </div>

            <div class="gx-srs-celebrate" id="gx-srs-celebrate">
              <div class="gx-srs-eyebrow">Relatório liberado</div>
              <p class="gx-srs-kpi-sub" id="gx-srs-celebrate-text" style="color:#fff;opacity:.9;font-size:var(--fs-md)"></p>
              <?php if (!empty($whatsAppBaseUrl)): ?>
                <a class="gx-srs-btn gx-srs-btn-gold" id="gx-srs-wa" href="<?= esc($whatsAppBaseUrl); ?>" target="_blank" rel="noopener" style="width:auto;padding:0 var(--space-8);margin-top:var(--space-4)">Falar com um planejador</a>
              <?php endif; ?>
            </div>

            <div class="gx-srs-disclaimer">
              Simulação educativa. Os valores são <strong>projetados</strong> a partir de IPCA estimado (5,5% a.a.) e dos fatores de resgate da apólice; <strong>não constituem garantia</strong> de rentabilidade nem proposta de contratação. Condições, carências e coberturas seguem o regulamento do produto e a regulação da SUSEP. O prêmio inclui IOF de 0,38%.
            </div>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- relatório (documento de entrega) -->
  <section class="gx-srs-reportwrap" id="gx-srs-reportwrap" hidden>
    <div class="gx-srs-wrap">
      <article class="gx-srs-report" id="gx-srs-report">

        <div class="gx-srs-report-toolbar gx-srs-noprint">
          <span class="gx-srs-eyebrow">Relatório liberado · pronto para você</span>
          <button class="gx-srs-btn gx-srs-btn-primary" id="gx-srs-print" type="button" style="width:auto; padding:0 var(--space-6);">↓ Baixar relatório (PDF)</button>
        </div>

        <header class="gx-srs-report-cover"<?= $reportCoverUrl ? ' style="background-image: linear-gradient(100deg, var(--gx-primary-dark) 0%, rgba(0,13,35,0.94) 36%, rgba(0,13,35,0.55) 62%, rgba(0,13,35,0.20) 100%), url(\'' . esc($reportCoverUrl, 'attr') . '\');"' : ''; ?>>
          <div class="gx-srs-cover-inner">
            <img class="gx-srs-cover-logo" src="<?= getLogoFooter(); ?>" alt="GX Capital">
            <div class="gx-srs-cover-kicker">Planejamento de Proteção & Sucessão</div>
            <h2 class="gx-srs-cover-title">Relatório de<br>Diagnóstico Patrimonial</h2>
            <div class="gx-srs-cover-meta">Preparado para <strong id="gx-srs-rp-nome">—</strong><span id="gx-srs-rp-data">—</span></div>
          </div>
        </header>

        <section class="gx-srs-rp-section">
          <div class="gx-srs-rp-label">01 · Seu diagnóstico</div>
          <div class="gx-srs-rp-grid" id="gx-srs-rp-diag"></div>
        </section>

        <section class="gx-srs-rp-section">
          <div class="gx-srs-rp-label">02 · Proteção recomendada</div>
          <div class="gx-srs-rp-reco">
            <div class="gx-srs-rp-reco-num" id="gx-srs-rp-capital">—</div>
            <p class="gx-srs-rp-reco-txt" id="gx-srs-rp-reco-txt"></p>
          </div>
        </section>

        <section class="gx-srs-rp-section">
          <div class="gx-srs-rp-label">03 · Seu plano GX</div>
          <div class="gx-srs-rp-kpis" id="gx-srs-rp-kpis"></div>
          <table class="gx-srs-rp-table">
            <thead><tr><th>Cobertura</th><th>Capital segurado</th><th>Prêmio mensal</th></tr></thead>
            <tbody id="gx-srs-rp-cob"></tbody>
          </table>
        </section>

        <section class="gx-srs-rp-section">
          <div class="gx-srs-rp-label">04 · Vida produtiva, patrimônio e reserva</div>
          <div class="gx-srs-rp-chartwrap"><canvas id="gx-srs-rp-chart" height="320"></canvas></div>
          <p class="gx-srs-rp-caption" id="gx-srs-rp-caption"></p>
          <table class="gx-srs-rp-table">
            <thead><tr><th>Momento</th><th>Total aportado</th><th>Reserva resgatável</th></tr></thead>
            <tbody id="gx-srs-rp-marcos"></tbody>
          </table>
        </section>

        <section class="gx-srs-rp-section gx-srs-rp-suc" id="gx-srs-rp-suc" hidden>
          <div class="gx-srs-rp-label">05 · Blindagem sucessória</div>
          <p id="gx-srs-rp-suc-txt"></p>
        </section>

        <section class="gx-srs-rp-cta">
          <h3>O próximo passo é desenhar isso com um especialista GX.</h3>
          <ul class="gx-srs-rp-bullets">
            <li>Validação do capital ideal para o seu momento e objetivo.</li>
            <li>Estruturação da apólice resgatável com a melhor seguradora.</li>
            <li>Otimização tributária e de liquidez para a sucessão.</li>
            <li>Acompanhamento do plano ao longo dos anos, sem custo de consultoria.</li>
          </ul>
          <?php if (!empty($whatsAppBaseUrl)): ?>
            <a class="gx-srs-btn gx-srs-btn-gold gx-srs-noprint" id="gx-srs-rp-wa" href="<?= esc($whatsAppBaseUrl); ?>" target="_blank" rel="noopener" style="width:auto; padding:0 var(--space-8); background:var(--gx-primary);">Falar com um especialista agora</a>
          <?php endif; ?>
        </section>

        <footer class="gx-srs-report-foot">
          Documento gerado pelo simulador GX Capital. Os valores são <strong>projeções</strong> baseadas em IPCA estimado (5,5% a.a.), nos fatores de resgate da apólice e nas informações fornecidas; <strong>não constituem garantia</strong> de rentabilidade, oferta ou proposta de contratação. Custos de sucessão (ITCMD/inventário) são estimativas de referência e variam conforme a legislação estadual e o caso concreto. Condições, carências e coberturas seguem o regulamento do produto e a regulação da SUSEP. Prêmios incluem IOF de 0,38%.
        </footer>

      </article>
    </div>
  </section>

  <!-- modal de lead -->
  <div class="gx-srs-modal" id="gx-srs-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="gx-srs-modal-title">
    <div class="gx-srs-dialog">
      <button type="button" class="gx-srs-close" id="gx-srs-modal-close" aria-label="Fechar">&times;</button>
      <h2 id="gx-srs-modal-title">Veja o valor exato</h2>
      <p>Liberamos na hora o aporte mensal recomendado e a sua reserva projetada em Reais. Um planejador da GX pode validar o plano com você.</p>
      <form id="gx-srs-lead-form" novalidate>
        <div class="gx-srs-field">
          <label for="gx-srs-name">Nome</label>
          <input class="gx-srs-input" type="text" id="gx-srs-name" name="name" autocomplete="name" required>
        </div>
        <div class="gx-srs-field">
          <label for="gx-srs-email">E-mail</label>
          <input class="gx-srs-input" type="email" id="gx-srs-email" name="email" autocomplete="email" required>
        </div>
        <?= view('partials/_lead_phone_field', [
            'fieldIdPrefix' => 'gx-srs-phone',
            'label' => 'WhatsApp / Telefone',
            'hint' => '',
        ]); ?>
        <button type="submit" class="gx-srs-btn gx-srs-btn-primary" id="gx-srs-lead-submit">Desbloquear relatório</button>
        <div class="gx-srs-status" id="gx-srs-lead-status"></div>
      </form>
    </div>
  </div>
</div>

<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
(function () {
  'use strict';
  var PREVIEW_URL = <?= json_encode($previewUrl); ?>;
  var UNLOCK_URL  = <?= json_encode($unlockUrl); ?>;

  var form        = document.getElementById('gx-srs-form');
  var calcBtn     = document.getElementById('gx-srs-calc');
  var placeholder = document.getElementById('gx-srs-placeholder');
  var result      = document.getElementById('gx-srs-result');
  var formStatus  = document.getElementById('gx-srs-form-status');
  var modal       = document.getElementById('gx-srs-modal');
  var leadForm    = document.getElementById('gx-srs-lead-form');
  var leadStatus  = document.getElementById('gx-srs-lead-status');
  var leadSubmit  = document.getElementById('gx-srs-lead-submit');
  var chartEl     = document.getElementById('gx-srs-chart');
  var chart       = null;
  var lastInput   = null;   // input do perfil, reaproveitado no unlock

  // segmented toggles
  document.querySelectorAll('.gx-srs-seg button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var group = btn.getAttribute('data-seg');
      var val   = btn.getAttribute('data-value');
      document.querySelectorAll('.gx-srs-seg button[data-seg="' + group + '"]').forEach(function (b) {
        b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
      });
      document.getElementById('gx-srs-' + group).value = val;
    });
  });

  function brl(v) {
    return 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  function brlCompact(v) {
    var n = Number(v);
    if (n >= 1000000) return 'R$ ' + (n / 1000000).toFixed(1).replace('.', ',') + 'M';
    if (n >= 1000) return 'R$ ' + Math.round(n / 1000) + 'k';
    return 'R$ ' + Math.round(n);
  }
  function setStatus(el, msg) {
    if (!msg) { el.className = 'gx-srs-status'; el.textContent = ''; return; }
    el.className = 'gx-srs-status is-error';
    el.textContent = msg;
  }

  // ---- dinheiro (BRL) ----
  function moneyParse(v) {
    if (v == null) return 0;
    var s = String(v).replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.');
    var n = parseFloat(s);
    return isFinite(n) && n > 0 ? n : 0;
  }
  function moneyFmt0(n) {
    return Math.round(Number(n) || 0).toLocaleString('pt-BR');
  }
  function intVal(id) { var v = parseInt((document.getElementById(id) || {}).value || '0', 10); return isFinite(v) ? v : 0; }

  var capField    = document.getElementById('gx-srs-cap-vida');
  var dgToggle    = document.getElementById('gx-srs-dg-toggle');
  var dgHidden    = document.getElementById('gx-srs-cap-dg');
  var recoInsight = document.getElementById('gx-srs-reco-insight');
  var capitalUserEdited = false;

  function updateDg() {
    var cap = moneyParse(capField.value);
    // Doenças Graves: capital permitido entre R$ 160 mil e R$ 1 milhão (regra do produto).
    dgHidden.value = dgToggle.checked ? Math.min(1000000, Math.max(160000, cap || 160000)) : 0;
  }

  // Parâmetros da análise de necessidade
  var TETO_INSS = 8475.55;                               // teto de benefício do INSS
  var PATRIMONIO_CRESC = 0.055;                          // evolução patrimonial projetada (a.a.)
  var IDADE_APOSENTADORIA = 65;                          // fim da vida produtiva
  var ITCMD_BY_UF = <?= json_encode($itcmdMap); ?>;      // ITCMD por estado
  var INVENTARIO_RATE = <?= json_encode($inventarioRate); ?>; // adv 6% + cartório 2%
  var UF_PADRAO = <?= json_encode($ufPadrao); ?>;
  function pct(x) { return (Math.round(x * 1000) / 10).toString().replace('.', ',') + '%'; }

  // A proteção recomendada é DIRIGIDA PELO OBJETIVO selecionado.
  function computeRecommendation() {
    var renda = moneyParse(document.getElementById('gx-srs-renda').value);
    var imob  = moneyParse(document.getElementById('gx-srs-patr-imob').value);
    var fin   = moneyParse(document.getElementById('gx-srs-patr-fin').value);
    var div   = moneyParse(document.getElementById('gx-srs-dividas').value);

    var uf    = (document.getElementById('gx-srs-uf') || {}).value || UF_PADRAO;
    var itcmd = (ITCMD_BY_UF[uf] != null) ? ITCMD_BY_UF[uf] : ITCMD_BY_UF[UF_PADRAO];
    var obj   = (document.getElementById('gx-srs-objetivo') || {}).value || 'protecao_familiar';

    var patrimonio = imob + fin;
    var sucRate = itcmd + INVENTARIO_RATE;            // ITCMD(estado) + inventário (adv+cartório)
    var sucessao = patrimonio * sucRate;

    var capital = 0;
    var d = { objetivo: obj, uf: uf, itcmd: itcmd, sucRate: sucRate, sucessao: sucessao,
              patrimonio: patrimonio, div: div, renda: renda };

    if (obj === 'sucessao') {
      capital = sucessao;
    } else if (obj === 'quitar_dividas') {
      capital = sucessao + div;
    } else if (obj === 'aposentadoria') {
      d.complemento = Math.max(0, renda - TETO_INSS);
      capital = d.complemento * 120;                  // complementa por 10 anos
    } else {                                          // protecao_familiar
      capital = renda * 60;                           // 5 anos de renda p/ reorganização
    }

    d.capital = Math.max(0, Math.round(capital / 10000) * 10000);
    return d;
  }

  // Justificativa da recomendação (reutilizada na caixa e no relatório).
  function rationaleSentence(r) {
    if (r.objetivo === 'sucessao') {
      return 'cobre as custas de sucessão (' + pct(r.itcmd) + ' de ITCMD em ' + r.uf + ' + ' + pct(INVENTARIO_RATE)
        + ' de inventário/cartório = ' + pct(r.sucRate) + ') sobre o patrimônio de R$ ' + moneyFmt0(r.patrimonio)
        + ', para a família receber os bens sem precisar vendê-los.';
    }
    if (r.objetivo === 'quitar_dividas') {
      return 'quita R$ ' + moneyFmt0(r.div) + ' de dívidas e cobre R$ ' + moneyFmt0(r.sucessao)
        + ' de custas de sucessão (' + pct(r.sucRate) + ') sobre o patrimônio, sem comprometer o legado.';
    }
    if (r.objetivo === 'aposentadoria') {
      return 'complementa R$ ' + moneyFmt0(r.complemento) + '/mês (sua renda menos o teto do INSS de R$ 8.475,55) por 10 anos.';
    }
    return 'equivale a 5 anos (60 meses) da renda da família, o tempo para se reorganizar financeiramente após um imprevisto.';
  }

  function recompute() {
    var r = computeRecommendation();
    if (!capitalUserEdited) { capField.value = moneyFmt0(r.capital); }
    updateDg();
    recoInsight.innerHTML = (r.capital <= 0)
      ? 'Preencha os dados acima para estimarmos a proteção ideal.'
      : 'Sugerimos <strong>R$ ' + moneyFmt0(r.capital) + '</strong> — ' + rationaleSentence(r);
  }

  // formata os campos monetários enquanto o usuário digita
  document.querySelectorAll('.gx-srs-money').forEach(function (el) {
    el.addEventListener('input', function () {
      var digits = el.value.replace(/\D/g, '');
      el.value = digits ? Number(digits).toLocaleString('pt-BR') : '';
      if (el.id === 'gx-srs-cap-vida') { capitalUserEdited = true; updateDg(); }
    });
  });
  // recalcula a recomendação quando o diagnóstico muda
  ['gx-srs-idade', 'gx-srs-renda', 'gx-srs-uf', 'gx-srs-patr-imob', 'gx-srs-patr-fin', 'gx-srs-dividas', 'gx-srs-filhos', 'gx-srs-dependentes', 'gx-srs-objetivo'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) { el.addEventListener('input', recompute); el.addEventListener('change', recompute); }
  });
  dgToggle.addEventListener('change', updateDg);
  recompute();

  function readProfile() {
    return {
      idade: form.idade.value,
      sexo: form.sexo.value,
      estrategia: form.estrategia.value,
      capital_vida: moneyParse(capField.value),
      capital_dg_plus: moneyParse(dgHidden.value)
    };
  }

  function readDiagnostic() {
    return {
      dependentes: intVal('gx-srs-dependentes'),
      filhos: intVal('gx-srs-filhos'),
      renda_mensal: moneyParse(document.getElementById('gx-srs-renda').value),
      estado: (document.getElementById('gx-srs-uf') || {}).value || '',
      patrimonio_imobiliario: moneyParse(document.getElementById('gx-srs-patr-imob').value),
      patrimonio_financeiro: moneyParse(document.getElementById('gx-srs-patr-fin').value),
      dividas: moneyParse(document.getElementById('gx-srs-dividas').value),
      objetivo: (document.getElementById('gx-srs-objetivo') || {}).value || ''
    };
  }

  function toBody(obj) {
    var p = new URLSearchParams();
    Object.keys(obj).forEach(function (k) { if (obj[k] !== undefined && obj[k] !== null) p.append(k, obj[k]); });
    return p;
  }

  // ---- Chart ----
  function drawChart(labels, redData, greenData, opts) {
    opts = opts || {};
    var money = !!opts.money;
    if (chart) { chart.destroy(); }
    chart = new Chart(chartEl.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          { label: 'Aporte acumulado', data: redData, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.08)', borderWidth: 2, pointRadius: 0, tension: 0.15, fill: true },
          { label: 'Reserva acumulada', data: greenData, borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.12)', borderWidth: 2, pointRadius: 0, tension: 0.15, fill: true }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
          legend: { display: false },
          tooltip: money ? {
            callbacks: { label: function (c) { return c.dataset.label + ': ' + brl(c.parsed.y); } }
          } : { enabled: false }
        },
        scales: {
          x: { title: { display: true, text: 'Idade', font: { family: "'JetBrains Mono', monospace", size: 10 } },
               ticks: { maxTicksLimit: 10, font: { family: "'JetBrains Mono', monospace", size: 10 } },
               grid: { color: 'rgba(12,49,99,0.06)' } },
          y: { ticks: { display: money, font: { family: "'JetBrains Mono', monospace", size: 10 },
                        callback: function (v) { return money ? brlCompact(v) : ''; } },
               grid: { color: 'rgba(12,49,99,0.06)' } }
        }
      }
    });
  }

  // ---- Preview (gate fechado, sem R$) ----
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    setStatus(formStatus, '');
    var idade = parseInt(form.idade.value, 10);
    if (isNaN(idade) || idade < 14 || idade > 65) { setStatus(formStatus, 'Informe uma idade entre 14 e 65.'); return; }
    if (!(parseFloat(form.capital_vida.value) > 0)) { setStatus(formStatus, 'Informe o capital de vida inteira.'); return; }

    lastInput = readProfile();
    calcBtn.disabled = true; calcBtn.textContent = 'Calculando...';

    fetch(PREVIEW_URL, { method: 'POST', body: toBody(lastInput), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok || res.d.status !== 'success') { throw new Error(res.d.message || 'Não foi possível calcular agora.'); }
        var p = res.d.preview;
        placeholder.style.display = 'none';
        result.classList.add('is-on');
        result.classList.add('gx-srs-locked');   // mantém borrado/cadeado
        document.getElementById('gx-srs-celebrate').classList.remove('is-on');
        // reseta KPIs sensíveis
        document.getElementById('gx-srs-kpi-aporte').textContent = 'R$ ••••';
        document.getElementById('gx-srs-kpi-aporte').classList.add('gx-srs-blur');
        document.getElementById('gx-srs-kpi-reserva65').textContent = 'R$ ••••';
        document.getElementById('gx-srs-kpi-reserva65').classList.add('gx-srs-blur');
        // break-even é seguro (sem R$)
        document.getElementById('gx-srs-kpi-breakeven').textContent = p.breakeven_idade ? ('idade ' + p.breakeven_idade) : '—';
        document.getElementById('gx-srs-kpi-breakeven-sub').textContent = p.breakeven_ano ? ('no ano ' + p.breakeven_ano + ' da apólice') : 'reserva ultrapassa o pago';
        // teaser sem R$
        if (p.breakeven_idade) {
          document.getElementById('gx-srs-lock-title').textContent = 'Aos ' + p.breakeven_idade + ' anos sua reserva ultrapassa tudo que você pagou.';
        }
        if (p.multiplo_final) {
          document.getElementById('gx-srs-lock-sub').textContent = 'Ao final do plano, sua reserva chega a cerca de ' + String(p.multiplo_final).replace('.', ',') + 'x o total aportado. Veja os valores exatos em Reais — liberação na hora.';
        }
        // gráfico com a curva indexada (0–100), eixo R$ oculto + blur
        drawChart(p.labels, p.pago_idx, p.reserva_idx, { money: false });
        result.scrollIntoView({ behavior: 'smooth', block: 'center' });
      })
      .catch(function (err) { setStatus(formStatus, err.message); })
      .finally(function () { calcBtn.disabled = false; calcBtn.textContent = 'Desenhar minha projeção'; });
  });

  // ---- abrir modal ----
  function openModal() { modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); }
  function closeModal() { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); }
  document.getElementById('gx-srs-unlock-cta').addEventListener('click', openModal);
  document.getElementById('gx-srs-modal-close').addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });

  // ---- Unlock (grava lead, revela R$) ----
  leadForm.addEventListener('submit', function (e) {
    e.preventDefault();
    setStatus(leadStatus, '');
    if (!lastInput) { setStatus(leadStatus, 'Refaça a projeção antes de desbloquear.'); return; }
    if (!leadForm.reportValidity()) { return; }

    var phoneCountry = (leadForm.querySelector('[name="phone_country"]') || {}).value || 'BR';
    var phoneNumber  = (leadForm.querySelector('[name="phone"]') || {}).value || '';

    var payload = Object.assign({}, lastInput, readDiagnostic(), {
      name: document.getElementById('gx-srs-name').value,
      email: document.getElementById('gx-srs-email').value,
      phone: phoneNumber,
      phone_country: phoneCountry,
      landing_page: window.location.href
    });
    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (k) {
      var v = new URLSearchParams(window.location.search).get(k);
      if (v) payload[k] = v;
    });

    leadSubmit.disabled = true; leadSubmit.textContent = 'Liberando...';

    fetch(UNLOCK_URL, { method: 'POST', body: toBody(payload), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok || res.d.status !== 'success') { throw new Error(res.d.message || 'Não foi possível liberar agora.'); }
        revealDossier(res.d.dossie);
        closeModal();
      })
      .catch(function (err) { setStatus(leadStatus, err.message); })
      .finally(function () { leadSubmit.disabled = false; leadSubmit.textContent = 'Desbloquear relatório'; });
  });

  function revealDossier(dossie) {
    if (!dossie) return;
    // tira o cadeado e o blur
    result.classList.remove('gx-srs-locked');
    var aporte = document.getElementById('gx-srs-kpi-aporte');
    var res65  = document.getElementById('gx-srs-kpi-reserva65');
    aporte.classList.remove('gx-srs-blur');
    res65.classList.remove('gx-srs-blur');
    aporte.textContent = brl(dossie.premio.mensal_bruto);
    var r65 = dossie.destaques.reserva_aos_65;
    res65.textContent = r65 ? brl(r65) : '—';
    // redesenha o gráfico com R$ reais
    var s = dossie.serie_rs;
    drawChart(s.labels, s.pago, s.reserva, { money: true });
    // celebração
    var celebrate = document.getElementById('gx-srs-celebrate');
    var txt = 'Seu plano está quitado em ' + dossie.destaques.quitacao_ano + ' anos. ';
    if (r65) txt += 'Aos 65 você terá construído uma reserva projetada de ' + brl(r65) + '. ';
    txt += 'Um planejador da GX já recebeu sua simulação e pode estruturar o passo a passo com você.';
    document.getElementById('gx-srs-celebrate-text').textContent = txt;
    celebrate.classList.add('is-on');
    // monta e revela o relatório completo (documento de entrega)
    var rw = document.getElementById('gx-srs-reportwrap');
    rw.hidden = false;            // visível ANTES de desenhar o gráfico (evita canvas 0px)
    buildReport(dossie);
    rw.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ---- Relatório (documento de entrega) ----
  var COB_LABELS = { vida: 'Vida Inteira (resgatável)', dg_plus: 'Doenças Graves Plus', dg_basico: 'Doenças Graves Básico', invalidez: 'Invalidez', renda_hospitalar: 'Renda Hospitalar', morte_acidental: 'Morte Acidental' };
  var reportChart = null;

  function rpItem(k, v, isText) {
    return '<div class="gx-srs-rp-item"><div class="k">' + k + '</div><div class="v' + (isText ? ' txt' : '') + '">' + v + '</div></div>';
  }

  function buildReport(dossie) {
    var inp = lastInput || {};
    var diag = readDiagnostic();
    var r = computeRecommendation();
    var pr = dossie.premio, ds = dossie.destaques, s = dossie.serie_rs;

    var nome = (document.getElementById('gx-srs-name').value || '').trim() || 'Você';
    document.getElementById('gx-srs-rp-nome').textContent = nome;
    document.getElementById('gx-srs-rp-data').textContent = new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });

    var objLabels = { protecao_familiar: 'Proteger a renda da família', sucessao: 'Planejar sucessão com liquidez', quitar_dividas: 'Garantir a quitação de dívidas', aposentadoria: 'Complementar a aposentadoria' };
    var patrimonio = (diag.patrimonio_imobiliario || 0) + (diag.patrimonio_financeiro || 0);
    var diagHtml = '';
    diagHtml += rpItem('Objetivo', objLabels[diag.objetivo] || '—', true);
    diagHtml += rpItem('Perfil', (inp.sexo === 'F' ? 'Feminino' : 'Masculino') + ', ' + inp.idade + ' anos', true);
    diagHtml += rpItem('Dependentes / filhos', diag.dependentes + ' / ' + diag.filhos, true);
    diagHtml += rpItem('Renda mensal', brl(diag.renda_mensal));
    diagHtml += rpItem('Patrimônio', brl(patrimonio));
    diagHtml += rpItem('Estado (ITCMD)', diag.estado + ' · ' + pct(r.itcmd), true);
    document.getElementById('gx-srs-rp-diag').innerHTML = diagHtml;

    document.getElementById('gx-srs-rp-capital').textContent = brl(inp.capital_vida);
    document.getElementById('gx-srs-rp-reco-txt').innerHTML = 'Capital de proteção dimensionado para o seu objetivo — ' + rationaleSentence(r);

    var estrat = inp.estrategia === 'WL20' ? '20 anos' : '10 anos';
    var kpis = '';
    kpis += '<div class="gx-srs-rp-kpi"><div class="k">Aporte mensal</div><div class="v">' + brl(pr.mensal_bruto) + '</div></div>';
    kpis += '<div class="gx-srs-rp-kpi"><div class="k">Mensal c/ proteções</div><div class="v">' + brl(pr.total_mensal_com_riders) + '</div></div>';
    kpis += '<div class="gx-srs-rp-kpi"><div class="k">Plano quitado em</div><div class="v">' + estrat + '</div></div>';
    kpis += '<div class="gx-srs-rp-kpi"><div class="k">Reserva aos 65</div><div class="v">' + (ds.reserva_aos_65 ? brl(ds.reserva_aos_65) : '—') + '</div></div>';
    document.getElementById('gx-srs-rp-kpis').innerHTML = kpis;

    var cob = '';
    (pr.breakdown || []).forEach(function (c) {
      cob += '<tr><td>' + (COB_LABELS[c.tipo] || c.tipo) + '</td><td class="num">' + brl(c.capital) + '</td><td class="num">' + brl(c.mensal) + '</td></tr>';
    });
    document.getElementById('gx-srs-rp-cob').innerHTML = cob;

    // horizonte do gráfico limitado p/ não distorcer com a explosão exponencial no fim
    var MAX_IDADE = 90;
    var cut = s.labels.length;
    for (var k = 0; k < s.labels.length; k++) { if (s.labels[k] > MAX_IDADE) { cut = k; break; } }
    var L = s.labels.slice(0, cut), pagoC = s.pago.slice(0, cut), reservaC = s.reserva.slice(0, cut);

    // séries adicionais: patrimônio (evolução) e vida produtiva (renda futura protegida)
    var idade0 = L[0];
    var rendaMensal = diag.renda_mensal || 0;
    var patrSerie = L.map(function (ida) { return Math.round(patrimonio * Math.pow(1 + PATRIMONIO_CRESC, ida - idade0)); });
    var vidaSerie = L.map(function (ida) { return Math.round(rendaMensal * 12 * Math.max(0, IDADE_APOSENTADORIA - ida)); });
    var vp0 = rendaMensal * 12 * Math.max(0, IDADE_APOSENTADORIA - (parseInt(inp.idade, 10) || idade0));

    if (reportChart) { reportChart.destroy(); }
    reportChart = new Chart(document.getElementById('gx-srs-rp-chart').getContext('2d'), {
      type: 'line',
      data: { labels: L, datasets: [
        { label: 'Total aportado', data: pagoC, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.06)', borderWidth: 2, pointRadius: 0, tension: 0.15, fill: true },
        { label: 'Reserva resgatável', data: reservaC, borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.10)', borderWidth: 2, pointRadius: 0, tension: 0.15, fill: true },
        { label: 'Patrimônio', data: patrSerie, borderColor: '#1f5694', borderWidth: 2, pointRadius: 0, tension: 0.15, fill: false },
        { label: 'Vida produtiva', data: vidaSerie, borderColor: '#c9a96a', borderWidth: 2, borderDash: [6, 4], pointRadius: 0, tension: 0, fill: false }
      ] },
      options: { responsive: true, maintainAspectRatio: false, animation: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
          legend: { display: true, labels: { font: { family: "'JetBrains Mono', monospace", size: 10 }, boxWidth: 12, padding: 12 } },
          tooltip: { callbacks: { label: function (c) { return c.dataset.label + ': ' + brl(c.parsed.y); } } }
        },
        scales: {
          x: { title: { display: true, text: 'Idade', font: { family: "'JetBrains Mono', monospace", size: 10 } }, ticks: { maxTicksLimit: 10, font: { family: "'JetBrains Mono', monospace", size: 9 } }, grid: { color: 'rgba(12,49,99,0.06)' } },
          y: { ticks: { font: { family: "'JetBrains Mono', monospace", size: 9 }, callback: function (v) { return brlCompact(v); } }, grid: { color: 'rgba(12,49,99,0.06)' } }
        } }
    });

    var capTxt = 'As curvas mostram, ao longo da sua vida: a <b>vida produtiva</b> (a renda futura que sua família perderia num imprevisto, que decresce até a aposentadoria), a evolução do seu <b>patrimônio</b>, a <b>reserva resgatável</b> formada pelo seguro e o <b>total aportado</b>.';
    if (vp0 > 0) {
      capTxt += ' Hoje, sua vida produtiva vale <strong>' + brl(vp0) + '</strong> para a sua família — é exatamente o que o seguro de vida resgatável garante desde já, enquanto a reserva cresce para ocupar esse lugar.';
    }
    document.getElementById('gx-srs-rp-caption').innerHTML = capTxt;

    // Marcos: início, FIM DO PERÍODO DE APORTE (destaque), 65 anos e reserva vitalícia.
    var idadeContrat = parseInt(inp.idade, 10) || (s.labels[0] - 1);
    var fimAportes = idadeContrat + (ds.quitacao_ano || 0);   // idade ao terminar de aportar
    var marcos = [
      { idade: s.labels[0], rotulo: 'Início do plano' },
      { idade: fimAportes, rotulo: 'Fim dos aportes', nota: '(' + (ds.quitacao_ano || 0) + ' anos pagos)', hi: true },
      { idade: 65, rotulo: 'Aos 65 anos' },
      { idade: s.labels[s.labels.length - 1], rotulo: 'Reserva vitalícia' }
    ];
    var seen = {}, mrows = '';
    marcos.forEach(function (m) {
      var i = s.labels.indexOf(m.idade);
      if (i === -1 || seen[m.idade]) { return; }
      seen[m.idade] = 1;
      var label = m.rotulo + ' · ' + m.idade + ' anos' + (m.nota ? ' <span style="opacity:.6">' + m.nota + '</span>' : '');
      mrows += '<tr' + (m.hi ? ' class="hi"' : '') + '><td>' + label + '</td><td class="num">' + brl(s.pago[i]) + '</td><td class="num">' + brl(s.reserva[i]) + '</td></tr>';
    });
    document.getElementById('gx-srs-rp-marcos').innerHTML = mrows;

    var sucEl = document.getElementById('gx-srs-rp-suc');
    if (patrimonio > 0) {
      document.getElementById('gx-srs-rp-suc-txt').innerHTML = 'Na transmissão do seu patrimônio de <strong>' + brl(patrimonio)
        + '</strong>, a família enfrentaria cerca de <strong>' + brl(r.sucessao) + '</strong> em custos de sucessão (ITCMD ' + pct(r.itcmd) + ' em ' + r.uf
        + ' + inventário ' + pct(INVENTARIO_RATE) + '). Um seguro de vida resgatável entrega essa liquidez na hora e fora do inventário — a família honra os custos e mantém o patrimônio intacto, sem leilão de bens nem resgate de investimentos no pior momento.';
      sucEl.hidden = false;
    } else {
      sucEl.hidden = true;
    }
  }

  var printBtn = document.getElementById('gx-srs-print');
  if (printBtn) { printBtn.addEventListener('click', function () { window.print(); }); }
})();
</script>
