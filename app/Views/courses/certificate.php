<?php
$brandName = function_exists('brand') ? brand('display_name', 'GX Capital') : 'GX Capital';
$title = $cert['course_title'] ?: ($course['title'] ?? 'Curso');
$name = $cert['user_name'] ?: 'Aluno';
$date = !empty($cert['issued_at']) ? date('d/m/Y', strtotime($cert['issued_at'])) : '';
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Certificado · <?= esc($title) ?> · <?= esc($brandName) ?></title>
<link rel="stylesheet" href="/colors_and_type.css">
<style>
    body{margin:0;font-family:var(--font-sans);background:var(--gx-primary-dark);color:var(--fg1);padding:40px 16px;}
    .cert{max-width:900px;margin:0 auto;background:#fff;border:2px solid var(--gx-primary);padding:64px 56px;position:relative;box-shadow:12px 12px 0 0 rgba(201,169,106,.4);}
    .cert::before{content:'';position:absolute;inset:14px;border:1px solid var(--gx-gold);pointer-events:none;}
    .cert__wm{position:absolute;right:20px;bottom:-10px;font-size:200px;font-weight:900;color:var(--gx-primary);opacity:.04;letter-spacing:-.05em;line-height:.8;pointer-events:none;}
    .cert__brand{text-transform:uppercase;letter-spacing:var(--ls-widest);font-weight:900;color:var(--gx-primary);font-size:16px}
    .cert__brand span{color:var(--gx-gold)}
    .cert__kick{margin-top:36px;text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:13px;font-weight:800;color:var(--gx-secondary-dark)}
    .cert__name{font-size:clamp(32px,6vw,54px);font-weight:900;letter-spacing:var(--ls-tight);color:var(--gx-primary);margin:10px 0 4px;line-height:1}
    .cert__line{width:280px;height:2px;background:var(--gx-gold);margin:14px 0 22px}
    .cert__txt{font-size:17px;color:var(--fg2);line-height:1.6;max-width:60ch}
    .cert__course{font-size:24px;font-weight:800;color:var(--gx-primary);margin:8px 0 0}
    .cert__foot{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-top:48px;position:relative;z-index:1}
    .cert__meta{font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:var(--fg2)}
    .cert__code{font-family:var(--font-mono);font-weight:700;color:var(--gx-primary)}
    .cert__actions{max-width:900px;margin:24px auto 0;display:flex;gap:12px;justify-content:center}
    .cert__btn{padding:12px 24px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);font-size:12px;border:1px solid var(--gx-gold);background:var(--gx-gold);color:var(--gx-primary-dark);cursor:pointer;text-decoration:none}
    .cert__btn--ghost{background:transparent;color:var(--gx-secondary-light);border-color:rgba(219,199,162,.5)}
    @media print{body{background:#fff;padding:0}.cert{box-shadow:none;border-color:var(--gx-primary)}.cert__actions{display:none}}
</style>
</head>
<body>
<div class="cert">
    <div class="cert__wm">GXC</div>
    <div class="cert__brand"><?= esc($brandName) ?> <span>Academy</span></div>
    <div class="cert__kick">Certificado de conclusão</div>
    <div class="cert__name"><?= esc($name) ?></div>
    <div class="cert__line"></div>
    <div class="cert__txt">concluiu com êxito toda a trilha de aprendizado</div>
    <div class="cert__course"><?= esc($title) ?></div>
    <div class="cert__foot">
        <div class="cert__meta">Emitido em<br><strong style="font-size:15px;color:var(--fg1)"><?= esc($date) ?></strong></div>
        <div class="cert__meta" style="text-align:right">Código de verificação<br><span class="cert__code"><?= esc($cert['code']) ?></span></div>
    </div>
</div>
<div class="cert__actions">
    <a class="cert__btn" href="javascript:window.print()">Imprimir / PDF</a>
    <a class="cert__btn cert__btn--ghost" href="<?= site_url('cursos') ?>">← Voltar aos cursos</a>
</div>
</body>
</html>
