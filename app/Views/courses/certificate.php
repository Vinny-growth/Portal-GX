<?php
$brandName  = function_exists('brand') ? brand('display_name', 'GX Capital') : 'GX Capital';
$title      = $cert['course_title'] ?: ($course['title'] ?? 'Curso');
$name       = $cert['user_name'] ?: 'Aluno';
$date       = !empty($cert['issued_at']) ? date('d/m/Y', strtotime($cert['issued_at'])) : '';
$year       = !empty($cert['issued_at']) ? date('Y', strtotime($cert['issued_at'])) : date('Y');
$founder    = function_exists('brand') ? brand('founder_name', '') : '';
$founderTit = function_exists('brand') ? brand('founder_title', 'Direção') : 'Direção';
$verifyUrl  = site_url('certificado/' . $cert['code']);
$seal       = strtoupper(mb_substr(preg_replace('/[^A-Za-z]/', '', $brandName) ?: 'GXC', 0, 3));
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Certificado · <?= esc($title) ?> · <?= esc($brandName) ?></title>
<link rel="stylesheet" href="/colors_and_type.css">
<style>
    @page { size: A4 landscape; margin: 0; }
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--font-sans);background:radial-gradient(circle at 50% 0%, #123a70, var(--gx-primary-dark) 70%);color:var(--fg1);padding:40px 16px;min-height:100vh}

    .cert-wrap{max-width:1040px;margin:0 auto}
    .cert{position:relative;aspect-ratio:297/210;background:#fdfbf7;color:var(--gx-primary);overflow:hidden;
        box-shadow:0 30px 80px -20px rgba(0,0,0,.65);border:10px solid var(--gx-primary)}
    /* textura sutil + moldura dourada interna */
    .cert::before{content:'';position:absolute;inset:16px;border:2px solid var(--gx-gold);pointer-events:none;z-index:1}
    .cert::after{content:'';position:absolute;inset:0;pointer-events:none;opacity:.5;
        background:repeating-linear-gradient(45deg, rgba(12,49,99,.02) 0 2px, transparent 2px 9px)}
    /* cantos ornamentais */
    .corner{position:absolute;width:46px;height:46px;border:3px solid var(--gx-secondary-dark);z-index:2}
    .corner.tl{top:24px;left:24px;border-right:0;border-bottom:0}
    .corner.tr{top:24px;right:24px;border-left:0;border-bottom:0}
    .corner.bl{bottom:24px;left:24px;border-right:0;border-top:0}
    .corner.br{bottom:24px;right:24px;border-left:0;border-top:0}
    /* watermark */
    .cert__wm{position:absolute;right:-2%;bottom:-8%;font-size:34vh;font-weight:900;color:var(--gx-primary);opacity:.035;letter-spacing:-.05em;line-height:.7;pointer-events:none;z-index:0}

    .cert__in{position:relative;z-index:3;height:100%;padding:6% 8% 5%;display:flex;flex-direction:column}
    .cert__top{display:flex;justify-content:space-between;align-items:flex-start;gap:20px}
    .cert__brand{text-transform:uppercase;letter-spacing:var(--ls-widest);font-weight:900;color:var(--gx-primary);font-size:clamp(14px,1.7vw,20px)}
    .cert__brand span{color:var(--gx-secondary-dark)}
    .cert__brand small{display:block;font-size:10px;letter-spacing:.35em;color:var(--fg2);font-weight:700;margin-top:4px}

    /* selo / medalhão */
    .seal{position:relative;width:clamp(84px,11vw,120px);height:clamp(84px,11vw,120px);border-radius:50%;
        background:radial-gradient(circle at 34% 28%, #f0dfbe, var(--gx-gold) 52%, var(--gx-secondary-dark));
        border:3px solid #fff;box-shadow:0 0 0 2px var(--gx-gold),0 10px 22px rgba(0,0,0,.28);display:grid;place-items:center;color:var(--gx-primary-dark);flex:0 0 auto}
    .seal::before{content:'';position:absolute;inset:9px;border:1px dashed rgba(0,13,35,.4);border-radius:50%}
    .seal__t{font-family:var(--font-mono);font-weight:900;font-size:clamp(20px,2.6vw,30px);letter-spacing:-.03em;line-height:1}
    .seal__s{position:absolute;bottom:14%;font-size:8px;font-weight:800;letter-spacing:.22em;text-transform:uppercase}

    .cert__body{flex:1;display:flex;flex-direction:column;justify-content:center;margin:2% 0}
    .cert__kick{text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:clamp(11px,1.3vw,14px);font-weight:800;color:var(--gx-secondary-dark)}
    .cert__pre{margin-top:14px;font-size:clamp(13px,1.3vw,16px);color:var(--fg2);letter-spacing:.02em}
    .cert__name{font-size:clamp(34px,6.4vw,68px);font-weight:900;letter-spacing:-.02em;color:var(--gx-primary);line-height:1;margin:6px 0 0}
    .cert__rule{display:flex;align-items:center;gap:14px;margin:16px 0 18px;max-width:70%}
    .cert__rule i{height:2px;background:linear-gradient(90deg,var(--gx-gold),rgba(201,169,106,0));flex:1}
    .cert__rule b{width:7px;height:7px;background:var(--gx-gold);transform:rotate(45deg);flex:0 0 auto}
    .cert__txt{font-size:clamp(13px,1.35vw,17px);color:var(--fg2);line-height:1.5;max-width:62ch}
    .cert__course{font-size:clamp(20px,2.6vw,30px);font-weight:800;color:var(--gx-primary);margin-top:6px;line-height:1.1}

    .cert__foot{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap}
    .sig{min-width:190px}
    .sig__line{height:1.5px;background:var(--gx-primary);opacity:.55;margin-bottom:7px}
    .sig__name{font-weight:800;font-size:clamp(13px,1.3vw,16px);color:var(--gx-primary)}
    .sig__role{font-size:10px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:var(--fg2);margin-top:2px}
    .cert__meta{text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:var(--fg2);line-height:1.7}
    .cert__meta strong{color:var(--gx-primary);font-size:13px}
    .cert__code{font-family:var(--font-mono);font-weight:700;color:var(--gx-primary);font-size:12px}
    .cert__verify{font-size:9px;color:var(--fg3);letter-spacing:.04em;text-transform:none}

    .cert__actions{max-width:1040px;margin:22px auto 0;display:flex;gap:12px;justify-content:center}
    .cert__btn{padding:12px 26px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);font-size:12px;border:1px solid var(--gx-gold);background:var(--gx-gold);color:var(--gx-primary-dark);cursor:pointer;text-decoration:none}
    .cert__btn--ghost{background:transparent;color:var(--gx-secondary-light);border-color:rgba(219,199,162,.5)}

    @media(max-width:720px){
        .cert{aspect-ratio:auto}
        .cert__name{margin-top:12px}
    }
    @media print{
        body{background:#fff;padding:0;min-height:auto}
        .cert-wrap{max-width:none}
        .cert{aspect-ratio:auto;height:100vh;box-shadow:none;border-width:8px}
        .cert__actions{display:none}
    }
</style>
</head>
<body>
<div class="cert-wrap">
    <div class="cert">
        <span class="corner tl"></span><span class="corner tr"></span><span class="corner bl"></span><span class="corner br"></span>
        <div class="cert__wm"><?= esc($seal) ?></div>
        <div class="cert__in">
            <div class="cert__top">
                <div class="cert__brand"><?= esc($brandName) ?> <span>Academy</span><small>Certificado oficial</small></div>
                <div class="seal">
                    <div class="seal__t"><?= esc($seal) ?></div>
                    <div class="seal__s"><?= esc($year) ?></div>
                </div>
            </div>

            <div class="cert__body">
                <div class="cert__kick">Certificado de Conclusão</div>
                <div class="cert__pre">Certificamos que</div>
                <div class="cert__name"><?= esc($name) ?></div>
                <div class="cert__rule"><b></b><i></i></div>
                <div class="cert__txt">concluiu com êxito, dedicação e aproveitamento toda a trilha de aprendizado</div>
                <div class="cert__course"><?= esc($title) ?></div>
            </div>

            <div class="cert__foot">
                <?php if (!empty($founder)): ?>
                <div class="sig">
                    <div class="sig__line"></div>
                    <div class="sig__name"><?= esc($founder) ?></div>
                    <div class="sig__role"><?= esc($founderTit) ?> · <?= esc($brandName) ?></div>
                </div>
                <?php else: ?>
                <div class="sig">
                    <div class="sig__line"></div>
                    <div class="sig__name"><?= esc($brandName) ?> Academy</div>
                    <div class="sig__role">Coordenação de Ensino</div>
                </div>
                <?php endif; ?>
                <div class="cert__meta">
                    Emitido em<br><strong><?= esc($date) ?></strong><br>
                    <span style="display:inline-block;margin-top:6px">Verificação: <span class="cert__code"><?= esc($cert['code']) ?></span></span><br>
                    <span class="cert__verify"><?= esc(preg_replace('#^https?://#', '', $verifyUrl)) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cert__actions">
    <a class="cert__btn" href="javascript:window.print()">Imprimir / Salvar PDF</a>
    <a class="cert__btn cert__btn--ghost" href="<?= site_url('cursos') ?>">← Voltar aos cursos</a>
</div>
</body>
</html>
