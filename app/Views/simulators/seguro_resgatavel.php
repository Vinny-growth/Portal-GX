<?php
$whatsAppBaseUrl = $whatsAppBaseUrl ?? '';
$simulatorsHubUrl = $simulatorsHubUrl ?? base_url('simuladores');
$termsUrl = $termsUrl ?? '#';
$previewUrl = base_url('api/quotation/preview');
$unlockUrl  = base_url('api/quotation/unlock');
$heroImgRel = 'uploads/marketing/srs_hero.webp';
$heroImgUrl = is_file(FCPATH . $heroImgRel) ? base_url($heroImgRel) : '';
$ufList = \App\Libraries\QuotationGate::ufList();
$itcmdMap = \App\Libraries\QuotationGate::itcmdMap();
$inventarioRate = \App\Libraries\QuotationGate::INVENTARIO_RATE;
$ufPadrao = \App\Libraries\QuotationGate::UF_PADRAO;
?>
<div class="gx-srs">

  <!-- topbar -->
  <header class="gx-srs-topbar">
    <div class="gx-srs-wrap">
      <a href="<?= esc(base_url()); ?>" class="gx-srs-brand" aria-label="<?= esc(brand('display_name'), 'attr'); ?>">
        <img src="<?= getLogoFooter(); ?>" alt="<?= esc(brand('display_name'), 'attr'); ?>">
      </a>
      <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-srs-back">&larr; <?= lang('Simuladores.srs_back'); ?></a>
    </div>
  </header>

  <!-- hero -->
  <section class="gx-srs-hero<?= $heroImgUrl ? ' has-img' : ''; ?>"<?= $heroImgUrl ? ' style="background-image: linear-gradient(90deg, var(--gx-primary-dark) 0%, rgba(0,13,35,0.95) 40%, rgba(0,13,35,0.55) 70%, rgba(12,49,99,0.30) 100%), url(\'' . esc($heroImgUrl, 'attr') . '\');"' : ''; ?>>
    <div class="gx-srs-watermark" aria-hidden="true">GXC</div>
    <div class="gx-srs-wrap">
      <div class="gx-srs-eyebrow"><?= lang('Simuladores.srs_hero_eyebrow'); ?></div>
      <h1 class="gx-srs-headline"><?= lang('Simuladores.srs_hero_headline'); ?></h1>
      <p class="gx-srs-sub"><?= lang('Simuladores.srs_hero_sub'); ?></p>
      <div class="gx-srs-hero-signals">
        <span class="gx-srs-chip"><?= lang('Simuladores.srs_chip_pagamento'); ?></span>
        <span class="gx-srs-chip"><?= lang('Simuladores.srs_chip_correcao'); ?></span>
        <span class="gx-srs-chip"><?= lang('Simuladores.srs_reserva_resgatavel'); ?></span>
        <span class="gx-srs-chip"><?= lang('Simuladores.srs_chip_protecoes'); ?></span>
      </div>
    </div>
  </section>

  <!-- main -->
  <main class="gx-srs-main">
    <div class="gx-srs-wrap">
      <div class="gx-srs-shell">

        <!-- form -->
        <form class="gx-srs-card" id="gx-srs-form" novalidate>
          <div class="gx-srs-card-title"><span><?= lang('Simuladores.srs_form_title'); ?></span></div>
          <p class="gx-srs-intro"><?= lang('Simuladores.srs_form_intro'); ?></p>

          <!-- 1. Sobre você -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label"><?= lang('Simuladores.srs_sec1'); ?></div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-idade"><?= lang('Simuladores.srs_lbl_idade'); ?></label>
                <input class="gx-srs-input" type="number" id="gx-srs-idade" name="idade" min="14" max="65" value="35" inputmode="numeric" required>
              </div>
              <div class="gx-srs-field">
                <label><?= lang('Simuladores.srs_lbl_sexo'); ?></label>
                <div class="gx-srs-seg" role="group" aria-label="<?= lang('Simuladores.srs_lbl_sexo'); ?>">
                  <button type="button" data-seg="sexo" data-value="M" aria-pressed="true"><?= lang('Simuladores.srs_masculino'); ?></button>
                  <button type="button" data-seg="sexo" data-value="F" aria-pressed="false"><?= lang('Simuladores.srs_feminino'); ?></button>
                </div>
                <input type="hidden" name="sexo" id="gx-srs-sexo" value="M">
              </div>
            </div>
          </div>

          <!-- 2. Família -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label"><?= lang('Simuladores.srs_sec2'); ?></div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-dependentes"><?= lang('Simuladores.srs_lbl_dependentes'); ?></label>
                <select class="gx-srs-input" id="gx-srs-dependentes" name="dependentes">
                  <option value="0"><?= lang('Simuladores.srs_dep_0'); ?></option>
                  <option value="1"><?= lang('Simuladores.srs_dep_1'); ?></option>
                  <option value="2" selected><?= lang('Simuladores.srs_dep_2'); ?></option>
                  <option value="3"><?= lang('Simuladores.srs_dep_3'); ?></option>
                  <option value="4"><?= lang('Simuladores.srs_4mais'); ?></option>
                </select>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-filhos"><?= lang('Simuladores.srs_lbl_filhos'); ?></label>
                <select class="gx-srs-input" id="gx-srs-filhos" name="filhos">
                  <option value="0"><?= lang('Simuladores.srs_filho_nenhum'); ?></option>
                  <option value="1" selected>1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4"><?= lang('Simuladores.srs_4mais'); ?></option>
                </select>
              </div>
            </div>
          </div>

          <!-- 3. Vida financeira e patrimônio -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label"><?= lang('Simuladores.srs_sec3'); ?></div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-renda"><?= lang('Simuladores.srs_lbl_renda'); ?></label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-renda" name="renda_mensal" inputmode="numeric" placeholder="<?= lang('Simuladores.srs_ph_renda'); ?>" value="30.000">
                <div class="gx-srs-hint"><?= lang('Simuladores.srs_hint_renda'); ?></div>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-uf"><?= lang('Simuladores.srs_lbl_uf'); ?></label>
                <select class="gx-srs-input" id="gx-srs-uf" name="estado">
                  <?php foreach ($ufList as $u): ?>
                    <option value="<?= esc($u['uf'], 'attr'); ?>"<?= $u['uf'] === $ufPadrao ? ' selected' : ''; ?>><?= esc($u['nome']); ?> (<?= esc($u['uf']); ?>)</option>
                  <?php endforeach; ?>
                </select>
                <div class="gx-srs-hint"><?= lang('Simuladores.srs_hint_uf'); ?></div>
              </div>
            </div>
            <div class="gx-srs-grid2">
              <div class="gx-srs-field">
                <label for="gx-srs-patr-imob"><?= lang('Simuladores.srs_lbl_patr_imob'); ?></label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-patr-imob" name="patrimonio_imobiliario" inputmode="numeric" placeholder="<?= lang('Simuladores.srs_ph_patr_imob'); ?>" value="0">
                <div class="gx-srs-hint"><?= lang('Simuladores.srs_hint_patr_imob'); ?></div>
              </div>
              <div class="gx-srs-field">
                <label for="gx-srs-patr-fin"><?= lang('Simuladores.srs_lbl_patr_fin'); ?></label>
                <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-patr-fin" name="patrimonio_financeiro" inputmode="numeric" placeholder="<?= lang('Simuladores.srs_ph_patr_fin'); ?>" value="0">
                <div class="gx-srs-hint"><?= lang('Simuladores.srs_hint_patr_fin'); ?></div>
              </div>
            </div>
            <div class="gx-srs-field">
              <label for="gx-srs-dividas"><?= lang('Simuladores.srs_lbl_dividas'); ?></label>
              <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-dividas" name="dividas" inputmode="numeric" placeholder="<?= lang('Simuladores.srs_ph_dividas'); ?>" value="0">
              <div class="gx-srs-hint"><?= lang('Simuladores.srs_hint_dividas'); ?></div>
            </div>
          </div>

          <!-- 4. Objetivo -->
          <div class="gx-srs-section">
            <div class="gx-srs-section-label"><?= lang('Simuladores.srs_sec4'); ?></div>
            <div class="gx-srs-field">
              <label for="gx-srs-objetivo"><?= lang('Simuladores.srs_lbl_objetivo'); ?></label>
              <select class="gx-srs-input" id="gx-srs-objetivo" name="objetivo">
                <option value="protecao_familiar" selected><?= lang('Simuladores.srs_obj_protecao'); ?></option>
                <option value="sucessao"><?= lang('Simuladores.srs_obj_sucessao'); ?></option>
                <option value="quitar_dividas"><?= lang('Simuladores.srs_obj_quitar'); ?></option>
                <option value="aposentadoria"><?= lang('Simuladores.srs_obj_aposentadoria'); ?></option>
              </select>
            </div>
            <div class="gx-srs-field">
              <label><?= lang('Simuladores.srs_lbl_estrategia'); ?></label>
              <div class="gx-srs-seg" role="group" aria-label="<?= lang('Simuladores.srs_aria_estrategia'); ?>">
                <button type="button" data-seg="estrategia" data-value="WL10" aria-pressed="true"><?= lang('Simuladores.srs_10anos'); ?></button>
                <button type="button" data-seg="estrategia" data-value="WL20" aria-pressed="false"><?= lang('Simuladores.srs_20anos'); ?></button>
              </div>
              <input type="hidden" name="estrategia" id="gx-srs-estrategia" value="WL10">
            </div>
          </div>

          <!-- Proteção recomendada (calculada) -->
          <div class="gx-srs-reco">
            <div class="gx-srs-reco-label"><?= lang('Simuladores.srs_reco_label'); ?></div>
            <div class="gx-srs-reco-insight" id="gx-srs-reco-insight"><?= lang('Simuladores.srs_reco_insight_default'); ?></div>
            <div class="gx-srs-field" style="margin-top:var(--space-4); margin-bottom:var(--space-3)">
              <label for="gx-srs-cap-vida"><?= lang('Simuladores.srs_capital_segurado'); ?> <span class="gx-srs-reco-edit"><?= lang('Simuladores.srs_reco_edit'); ?></span></label>
              <input class="gx-srs-input gx-srs-money" type="text" id="gx-srs-cap-vida" name="capital_vida" inputmode="numeric" value="150.000" required>
            </div>
            <label class="gx-srs-check">
              <input type="checkbox" id="gx-srs-dg-toggle" checked>
              <span><?= lang('Simuladores.srs_dg_toggle'); ?></span>
            </label>
            <input type="hidden" id="gx-srs-cap-dg" name="capital_dg_plus" value="200000">
          </div>

          <button type="submit" class="gx-srs-btn gx-srs-btn-primary" id="gx-srs-calc"><?= lang('Simuladores.srs_calc_btn'); ?></button>
          <div class="gx-srs-status" id="gx-srs-form-status"></div>
        </form>

        <!-- result -->
        <section class="gx-srs-card">
          <div class="gx-srs-card-title"><span><?= lang('Simuladores.srs_result_title'); ?></span></div>

          <div class="gx-srs-placeholder" id="gx-srs-placeholder">
            <?= lang('Simuladores.srs_placeholder'); ?>
          </div>

          <div class="gx-srs-result" id="gx-srs-result">
            <div class="gx-srs-chartwrap">
              <canvas class="gx-srs-chart-canvas" id="gx-srs-chart" height="340"></canvas>
              <div class="gx-srs-lock">
                <div class="gx-srs-lock-badge" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="0"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 id="gx-srs-lock-title"><?= lang('Simuladores.srs_lock_title'); ?></h3>
                <p id="gx-srs-lock-sub"><?= lang('Simuladores.srs_lock_sub'); ?></p>
                <button type="button" class="gx-srs-btn gx-srs-btn-gold" id="gx-srs-unlock-cta"><?= lang('Simuladores.srs_desbloquear'); ?></button>
              </div>
            </div>

            <div class="gx-srs-legend">
              <span><i class="gx-srs-dot red"></i> <?= lang('Simuladores.srs_legend_red'); ?></span>
              <span><i class="gx-srs-dot green"></i> <?= lang('Simuladores.srs_legend_green'); ?></span>
            </div>

            <div class="gx-srs-kpis">
              <div class="gx-srs-kpi is-navy">
                <div class="gx-srs-kpi-label"><?= lang('Simuladores.srs_kpi_breakeven'); ?></div>
                <div class="gx-srs-kpi-value" id="gx-srs-kpi-breakeven">—</div>
                <div class="gx-srs-kpi-sub" id="gx-srs-kpi-breakeven-sub"><?= lang('Simuladores.srs_kpi_breakeven_sub'); ?></div>
              </div>
              <div class="gx-srs-kpi is-dark">
                <div class="gx-srs-kpi-label"><?= lang('Simuladores.srs_kpi_quitacao'); ?></div>
                <div class="gx-srs-kpi-value" id="gx-srs-kpi-quitacao">—</div>
                <div class="gx-srs-kpi-sub"><?= lang('Simuladores.srs_kpi_quitacao_sub'); ?></div>
              </div>
              <div class="gx-srs-kpi">
                <div class="gx-srs-kpi-label"><?= lang('Simuladores.srs_kpi_protecao'); ?></div>
                <div class="gx-srs-kpi-value" id="gx-srs-kpi-protecao" style="color:var(--gx-primary)">—</div>
                <div class="gx-srs-kpi-sub"><?= lang('Simuladores.srs_kpi_protecao_sub'); ?></div>
              </div>
            </div>

            <div class="gx-srs-celebrate" id="gx-srs-celebrate">
              <div class="gx-srs-eyebrow"><?= lang('Simuladores.srs_celebrate_eyebrow'); ?></div>
              <p class="gx-srs-kpi-sub" id="gx-srs-celebrate-text" style="color:#fff;opacity:.9;font-size:var(--fs-md)"></p>
              <?php if (!empty($whatsAppBaseUrl)): ?>
                <a class="gx-srs-btn gx-srs-btn-gold" id="gx-srs-wa" href="<?= esc($whatsAppBaseUrl); ?>" target="_blank" rel="noopener" style="width:auto;padding:0 var(--space-8);margin-top:var(--space-4)"><?= lang('Simuladores.srs_wa_btn'); ?></a>
              <?php endif; ?>
            </div>

            <div class="gx-srs-disclaimer">
              <?= lang('Simuladores.srs_disclaimer'); ?>
            </div>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- modal de lead -->
  <div class="gx-srs-modal" id="gx-srs-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="gx-srs-modal-title">
    <div class="gx-srs-dialog">
      <button type="button" class="gx-srs-close" id="gx-srs-modal-close" aria-label="<?= esc(lang('Simuladores.srs_modal_close'), 'attr'); ?>">&times;</button>
      <h2 id="gx-srs-modal-title"><?= lang('Simuladores.srs_modal_title'); ?></h2>
      <p><?= lang('Simuladores.srs_modal_sub'); ?></p>
      <form id="gx-srs-lead-form" novalidate>
        <div class="gx-srs-field">
          <label for="gx-srs-name"><?= lang('Simuladores.srs_lbl_nome'); ?></label>
          <input class="gx-srs-input" type="text" id="gx-srs-name" name="name" autocomplete="name" required>
        </div>
        <div class="gx-srs-field">
          <label for="gx-srs-email"><?= lang('Simuladores.srs_lbl_email'); ?></label>
          <input class="gx-srs-input" type="email" id="gx-srs-email" name="email" autocomplete="email" required>
        </div>
        <?= view('partials/_lead_phone_field', [
            'fieldIdPrefix' => 'gx-srs-phone',
            'label' => lang('Simuladores.srs_lbl_whatsapp'),
            'hint' => '',
        ]); ?>
        <label class="gx-srs-consent">
          <input type="checkbox" id="gx-srs-consent" name="consent" value="1" required>
          <span><?= lang('Simuladores.srs_consent'); ?></span>
        </label>
        <button type="submit" class="gx-srs-btn gx-srs-btn-primary" id="gx-srs-lead-submit"><?= lang('Simuladores.srs_desbloquear'); ?></button>
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

  // Justificativa da recomendação (usada na caixa de proteção recomendada).
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

  // ---- Chart (sempre indexado 0–100; NUNCA exibe R$ ao lead) ----
  function drawChart(labels, redData, greenData) {
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
          tooltip: { enabled: false }
        },
        scales: {
          x: { title: { display: true, text: 'Idade', font: { family: "'JetBrains Mono', monospace", size: 10 } },
               ticks: { maxTicksLimit: 10, font: { family: "'JetBrains Mono', monospace", size: 10 } },
               grid: { color: 'rgba(12,49,99,0.06)' } },
          y: { ticks: { display: false }, grid: { color: 'rgba(12,49,99,0.06)' } }
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
        // KPIs do teaser — todos NÃO-monetários e CRÍVEIS (nenhum R$ nem múltiplo inflado antes do especialista)
        document.getElementById('gx-srs-kpi-breakeven').textContent = p.breakeven_idade ? ('idade ' + p.breakeven_idade) : '—';
        document.getElementById('gx-srs-kpi-breakeven-sub').textContent = p.breakeven_ano ? ('no ano ' + p.breakeven_ano + ' da apólice') : 'reserva ultrapassa o total pago';
        document.getElementById('gx-srs-kpi-quitacao').textContent = p.quitacao_ano ? (p.quitacao_ano + ' anos') : '—';
        document.getElementById('gx-srs-kpi-protecao').textContent = p.idade_final ? ('até os ' + p.idade_final) : 'vitalícia';
        // teaser sem R$: só o ponto de virada (break-even), que é característica real do produto
        if (p.breakeven_idade) {
          document.getElementById('gx-srs-lock-title').textContent = 'Aos ' + p.breakeven_idade + ' anos sua reserva ultrapassa tudo que você pagou.';
        }
        // gráfico com a curva indexada (0–100), eixo R$ oculto + blur
        drawChart(p.labels, p.pago_idx, p.reserva_idx);
        result.scrollIntoView({ behavior: 'smooth', block: 'center' });
      })
      .catch(function (err) { setStatus(formStatus, err.message); })
      .finally(function () { calcBtn.disabled = false; calcBtn.textContent = 'Ver meu diagnóstico e projeção'; });
  });

  // ---- abrir modal ----
  function openModal() { modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); }
  function closeModal() { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); }
  document.getElementById('gx-srs-unlock-cta').addEventListener('click', openModal);
  document.getElementById('gx-srs-modal-close').addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });

  // ---- Unlock (grava o lead; NÃO recebe nem exibe R$ — o relatório é entregue pelo especialista) ----
  leadForm.addEventListener('submit', function (e) {
    e.preventDefault();
    setStatus(leadStatus, '');
    if (!lastInput) { setStatus(leadStatus, 'Refaça a projeção antes de continuar.'); return; }
    var consentEl = document.getElementById('gx-srs-consent');
    if (consentEl && !consentEl.checked) { setStatus(leadStatus, 'Marque a autorização de contato para receber seu relatório.'); return; }
    if (!leadForm.reportValidity()) { return; }

    var phoneCountry = (leadForm.querySelector('[name="phone_country"]') || {}).value || 'BR';
    var phoneNumber  = (leadForm.querySelector('[name="phone"]') || {}).value || '';

    var payload = Object.assign({}, lastInput, readDiagnostic(), {
      name: document.getElementById('gx-srs-name').value,
      email: document.getElementById('gx-srs-email').value,
      phone: phoneNumber,
      phone_country: phoneCountry,
      consent: consentEl && consentEl.checked ? '1' : '',
      landing_page: window.location.href
    });
    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (k) {
      var v = new URLSearchParams(window.location.search).get(k);
      if (v) payload[k] = v;
    });

    leadSubmit.disabled = true; leadSubmit.textContent = 'Enviando...';

    fetch(UNLOCK_URL, { method: 'POST', body: toBody(payload), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok || res.d.status !== 'success') { throw new Error(res.d.message || 'Não foi possível registrar agora.'); }
        onLeadWon();
        closeModal();
      })
      .catch(function (err) { setStatus(leadStatus, err.message); })
      .finally(function () { leadSubmit.disabled = false; leadSubmit.textContent = 'Quero meu Relatório 360'; });
  });

  // ---- Lead capturado: mensagem de conquista + handoff pro WhatsApp (sem R$) ----
  function onLeadWon() {
    // desborra o gráfico indexado (só a FORMA da projeção — nunca há R$ nesta tela)
    result.classList.remove('gx-srs-locked');

    var nome = (document.getElementById('gx-srs-name').value || '').trim();
    var primeiro = nome ? nome.split(' ')[0] : '';
    var saud = primeiro ? ('Parabéns, ' + primeiro + '! ') : 'Parabéns! ';
    var celebrate = document.getElementById('gx-srs-celebrate');
    document.getElementById('gx-srs-celebrate-text').textContent =
      saud + 'Você ganhou um Relatório Patrimonial 360 completo — o planejamento da sua vida financeira, com proteção, reserva e sucessão desenhados para o seu objetivo. Um especialista GX já está preparando o seu e vai te entregar agora pelo WhatsApp. Fale com ele para receber. 👇';

    // WhatsApp com mensagem pré-preenchida — cai no agente que qualifica o lead na entrada.
    var wa = document.getElementById('gx-srs-wa');
    if (wa) {
      var base = (wa.getAttribute('href') || '').split('?')[0];
      if (base) {
        var objLabels = {
          protecao_familiar: 'proteger a renda da minha família',
          sucessao: 'planejar a sucessão com liquidez',
          quitar_dividas: 'garantir a quitação de dívidas num imprevisto',
          aposentadoria: 'complementar a aposentadoria'
        };
        var diag = readDiagnostic();
        var objTxt = objLabels[diag.objetivo] || 'proteger meu patrimônio';
        var msg = 'Olá! ' + (primeiro ? ('Sou ' + primeiro + '. ') : '')
          + 'Fiz meu diagnóstico no simulador de seguro de vida resgatável da GX e quero receber meu Relatório Patrimonial 360 completo. Meu objetivo é ' + objTxt + '.';
        wa.href = base + '?text=' + encodeURIComponent(msg);
      }
    }

    celebrate.classList.add('is-on');
    celebrate.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
})();
</script>
