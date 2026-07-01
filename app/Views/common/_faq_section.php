<?php
/**
 * Componente reutilizável de FAQ (acordeão acessível) — GX Capital / Nexus.
 *
 * Renderiza $faqItems como <details> nativos: acessíveis por teclado e leitor de
 * tela, sem JavaScript. O MESMO $faqItems deve alimentar o schema FAQPage
 * (helper jsonldFaqPage) no controller — nunca emitir FAQPage sem estes itens
 * visíveis no HTML (diretriz do Google).
 *
 * CSS escopado com fallback de tokens (var(--gx-*, #hex)) porque a folha
 * colors_and_type.css não é carregada no tema magazine — assim o componente é
 * portável para qualquer página (categorias-pilar, simuladores).
 *
 * Params:
 *   $faqItems   array   OBRIGATÓRIO  [['q'=>string,'a'=>string], ...]
 *   $faqTitle   string  opcional     título da seção
 *   $faqEyebrow string  opcional     eyebrow curto acima do título
 */
if (empty($faqItems) || !is_array($faqItems)) {
    return;
}
$faqTitle   = $faqTitle   ?? 'Perguntas frequentes';
$faqEyebrow = $faqEyebrow ?? 'FAQ';
?>
<section class="gx-faq" aria-label="<?= esc($faqTitle) ?>">
  <style>
    .gx-faq{--gxf-navy:var(--gx-primary,#0c3163);--gxf-gold:var(--gx-gold,#c9a96a);--gxf-champ:var(--gx-secondary-dark,#87704a);--gxf-border:var(--gx-border,#d3d9e0);--gxf-muted:#5a6a80;margin:3rem 0;}
    .gx-faq__head{display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.5rem;}
    .gx-faq__eyebrow{display:inline-flex;align-items:center;gap:.6rem;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.16em;color:var(--gxf-champ);}
    .gx-faq__eyebrow::before{content:"";width:32px;height:2px;background:var(--gxf-navy);display:inline-block;}
    .gx-faq__title{font-size:clamp(1.5rem,3vw,2rem);font-weight:900;letter-spacing:-.02em;color:var(--gxf-navy);margin:0;text-transform:uppercase;line-height:1.05;}
    .gx-faq__item{border:1px solid var(--gxf-border);border-left:3px solid var(--gxf-navy);border-radius:0;background:#fff;margin-bottom:.75rem;transition:box-shadow .2s cubic-bezier(.16,1,.3,1),border-color .2s;}
    .gx-faq__item[open]{border-left-color:var(--gxf-gold);box-shadow:var(--shadow-card-hover,6px 6px 0 0 rgba(12,49,99,.15));}
    .gx-faq__q{list-style:none;cursor:pointer;padding:1.1rem 3rem 1.1rem 1.25rem;font-size:1.02rem;font-weight:700;color:var(--gxf-navy);position:relative;display:block;}
    .gx-faq__q::-webkit-details-marker{display:none;}
    .gx-faq__q::after{content:"+";position:absolute;right:1.25rem;top:50%;transform:translateY(-50%);font-size:1.5rem;font-weight:400;color:var(--gxf-champ);line-height:1;}
    .gx-faq__item[open] .gx-faq__q::after{content:"\2013";}
    .gx-faq__q:focus-visible{outline:2px solid var(--gxf-gold);outline-offset:2px;}
    .gx-faq__a{padding:0 1.25rem 1.25rem;color:var(--gxf-muted);font-size:.97rem;line-height:1.65;}
  </style>
  <div class="gx-faq__head">
    <span class="gx-faq__eyebrow"><?= esc($faqEyebrow) ?></span>
    <h2 class="gx-faq__title"><?= esc($faqTitle) ?></h2>
  </div>
  <?php foreach ($faqItems as $item):
      $q = trim((string) ($item['q'] ?? ''));
      $a = trim((string) ($item['a'] ?? ''));
      if ($q === '' || $a === '') {
          continue;
      } ?>
    <details class="gx-faq__item">
      <summary class="gx-faq__q"><?= esc($q) ?></summary>
      <div class="gx-faq__a"><?= esc($a) ?></div>
    </details>
  <?php endforeach; ?>
</section>
