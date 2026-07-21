<?php
$brandName = function_exists('brand') ? brand('display_name', 'GX Capital') : 'GX Capital';
$u = function_exists('user') ? user() : null;
$xp = $totalXp ?? null;
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle ?? 'Academy') ?> · <?= esc($brandName) ?></title>
<link rel="stylesheet" href="/colors_and_type.css">
<style>
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--font-sans);background:var(--gx-primary-dark);color:#eef2f7;-webkit-font-smoothing:antialiased;}
    a{color:inherit;text-decoration:none}
    .ac-mono{font-family:var(--font-mono);font-variant-numeric:tabular-nums}
    /* nav */
    .ac-nav{position:sticky;top:0;z-index:50;display:flex;align-items:center;justify-content:space-between;gap:var(--space-4);
        padding:14px 5vw;background:linear-gradient(180deg,rgba(0,13,35,.8),rgba(0,13,35,0));border-bottom:1px solid transparent;transition:background .3s,border-color .3s;}
    .ac-nav.solid{background:rgba(0,13,35,.94);backdrop-filter:blur(10px);border-bottom-color:rgba(219,199,162,.15)}
    .ac-brand{display:flex;align-items:center;gap:10px;font-weight:900;letter-spacing:var(--ls-tight);font-size:18px;text-transform:uppercase;}
    .ac-brand span{color:var(--gx-gold)}
    .ac-navlinks{display:flex;align-items:center;gap:var(--space-5);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:var(--ls-wide);}
    .ac-navlinks a{color:#c7d2e0;transition:color .2s}.ac-navlinks a:hover{color:var(--gx-gold)}
    .ac-xp{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border:1px solid rgba(219,199,162,.35);color:var(--gx-secondary-light);font-weight:700;font-size:12px;}
    .ac-xp b{font-family:var(--font-mono);color:var(--gx-gold)}
    /* layout */
    .ac-main{max-width:1400px;margin:0 auto;padding:0 5vw var(--space-20);}
    .ac-eyebrow{display:flex;align-items:center;gap:12px;text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:12px;font-weight:800;color:var(--gx-gold);margin:var(--space-10) 0 var(--space-5);}
    .ac-eyebrow::before{content:'';width:34px;height:2px;background:var(--gx-gold);display:inline-block}
    /* hero */
    .ac-hero{position:relative;min-height:64vh;display:flex;align-items:flex-end;padding:5vw;margin:0 -5vw;overflow:hidden;
        background:linear-gradient(180deg,rgba(0,13,35,.2),rgba(0,13,35,.75) 60%,var(--gx-primary-dark)),var(--gx-primary);background-size:cover;background-position:center;}
    .ac-hero__wm{position:absolute;right:-2vw;bottom:-6vw;font-weight:900;font-size:26vw;line-height:.8;color:#fff;opacity:.04;letter-spacing:-.05em;pointer-events:none;user-select:none;}
    .ac-hero__in{position:relative;max-width:680px}
    .ac-hero__kick{text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:12px;font-weight:800;color:var(--gx-gold)}
    .ac-hero h1{font-size:clamp(38px,6vw,80px);font-weight:900;line-height:.95;letter-spacing:var(--ls-tighter);text-transform:uppercase;margin:14px 0}
    .ac-hero p{font-size:18px;color:#cdd7e4;max-width:56ch;line-height:1.5}
    .ac-hero__meta{display:flex;gap:18px;flex-wrap:wrap;margin:18px 0 24px;font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#aebbcc}
    /* buttons */
    .ac-btn{display:inline-flex;align-items:center;gap:8px;padding:13px 26px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);
        font-size:13px;border:1px solid var(--gx-gold);background:var(--gx-gold);color:var(--gx-primary-dark);cursor:pointer;transition:transform .18s,box-shadow .18s;}
    .ac-btn:hover{transform:translate(-1px,-1px);box-shadow:5px 5px 0 0 rgba(0,0,0,.35);color:var(--gx-primary-dark)}
    .ac-btn--ghost{background:transparent;color:var(--gx-secondary-light);border-color:rgba(219,199,162,.5)}
    .ac-btn--ghost:hover{color:#fff}
    .ac-btn--nav{background:#fff;border-color:#fff;color:var(--gx-primary-dark)}
    /* carousel (estilo Netflix) */
    .ac-row{position:relative;margin-top:var(--space-4)}
    .ac-track{display:flex;gap:16px;overflow-x:auto;padding:28px 2px 36px;scroll-snap-type:x proximity;scrollbar-width:none;scroll-behavior:smooth}
    .ac-track::-webkit-scrollbar{display:none}
    .ac-arrow{position:absolute;top:28px;bottom:36px;width:52px;z-index:30;border:0;cursor:pointer;color:#fff;font-size:28px;font-weight:900;background:linear-gradient(90deg,rgba(0,13,35,.9),rgba(0,13,35,0));opacity:0;transition:opacity .2s;display:grid;place-items:center}
    .ac-arrow--l{left:-5vw}.ac-arrow--r{right:-5vw;transform:scaleX(-1)}
    .ac-row:hover .ac-arrow{opacity:.95}.ac-arrow:hover{color:var(--gx-gold)}
    .ac-card{flex:0 0 300px;scroll-snap-align:start;background:#0a2547;border:1px solid rgba(219,199,162,.15);position:relative;transition:transform .28s var(--transition-smooth,ease),border-color .28s,box-shadow .28s}
    .ac-card:hover{transform:translateY(-6px) scale(1.045);border-color:var(--gx-gold);box-shadow:0 18px 40px -12px rgba(0,0,0,.7),0 0 0 1px var(--gx-gold);z-index:20}
    .ac-card__cover{height:170px;position:relative;overflow:hidden;background:var(--gradient-primary);display:block}
    .ac-card__img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .6s var(--transition-smooth,ease)}
    .ac-card:hover .ac-card__img{transform:scale(1.14)}
    .ac-card__grad{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,13,35,0) 45%,rgba(10,37,71,.8));pointer-events:none}
    .ac-card__play{position:absolute;inset:0;display:grid;place-items:center;opacity:0;transition:opacity .25s;z-index:2}
    .ac-card:hover .ac-card__play{opacity:1}
    .ac-card__play span{width:54px;height:54px;border-radius:var(--radius-pill);background:rgba(201,169,106,.95);color:var(--gx-primary-dark);display:grid;place-items:center;font-size:19px;padding-left:3px;box-shadow:0 6px 18px rgba(0,0,0,.55);transform:scale(.7);transition:transform .25s}
    .ac-card:hover .ac-card__play span{transform:scale(1)}
    .ac-card__badge{position:absolute;top:10px;left:10px;z-index:2;padding:3px 9px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);background:var(--gx-primary-dark);color:var(--gx-gold);border:1px solid rgba(219,199,162,.4)}
    .ac-card__body{padding:14px 16px 16px}
    .ac-card__cat{font-size:10px;text-transform:uppercase;letter-spacing:var(--ls-widest);color:var(--gx-gold);font-weight:800}
    .ac-card__title{font-size:17px;font-weight:800;margin:6px 0 8px;line-height:1.15}
    .ac-card__sub{font-size:13px;color:#aebbcc;line-height:1.4;min-height:36px}
    .ac-card__foot{display:flex;align-items:center;justify-content:space-between;margin-top:14px;font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#8ea0b6}
    /* progress bar */
    .ac-prog{height:6px;background:rgba(255,255,255,.12);overflow:hidden}
    .ac-prog>i{display:block;height:100%;background:var(--gx-gold)}
    .ac-card__cover .ac-prog{position:absolute;left:0;right:0;bottom:0;z-index:3;height:5px;background:rgba(0,0,0,.5)}
    /* panels */
    .ac-panel{background:#0a2547;border:1px solid rgba(219,199,162,.15);padding:var(--space-8);}
    .ac-flash{padding:12px 16px;margin:var(--space-4) 0;background:rgba(220,38,38,.15);border-left:4px solid var(--gx-danger);color:#ffd7d7;font-weight:600}
    .ac-empty{padding:var(--space-16);text-align:center;color:#8ea0b6;border:1px dashed rgba(219,199,162,.25)}
    @media(max-width:640px){.ac-card{flex-basis:78vw}.ac-navlinks .ac-hide{display:none}}
</style>
</head>
<body>
<nav class="ac-nav" id="acNav">
    <a class="ac-brand" href="<?= site_url('cursos') ?>"><?= esc($brandName) ?> <span>Academy</span></a>
    <div class="ac-navlinks">
        <a href="<?= site_url('cursos') ?>">Cursos</a>
        <?php if ($u): ?><a class="ac-hide" href="<?= site_url('meus-cursos') ?>">Meus cursos</a><?php endif; ?>
        <?php if ($u): ?><a href="<?= site_url('comunidade') ?>">Comunidade</a><?php endif; ?>
        <?php if ($u && !empty($unread)): ?><a class="ac-xp" href="<?= site_url('comunidade/notificacoes') ?>" title="Notificações">🔔 <b class="ac-mono"><?= (int) $unread ?></b></a><?php endif; ?>
        <?php if ($u && $xp !== null): ?><span class="ac-xp">XP <b class="ac-mono"><?= (int) $xp ?></b></span><?php endif; ?>
        <?php if ($u): ?><a class="ac-hide" href="<?= site_url('cursos') ?>"><?= esc($u->username ?? 'Aluno') ?></a>
        <?php else: ?><a class="ac-btn ac-btn--nav" href="<?= site_url('login') ?>">Entrar</a><?php endif; ?>
    </div>
</nav>
<main class="ac-main">
<script>(function(){var n=document.getElementById('acNav');if(!n)return;var f=function(){n.classList.toggle('solid',window.scrollY>40);};f();window.addEventListener('scroll',f,{passive:true});})();</script>
<?php if (session()->getFlashdata('error')): ?><div class="ac-flash"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
