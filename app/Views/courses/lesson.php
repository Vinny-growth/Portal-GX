<?php
$pageTitle = $lesson['title'];
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
$nextUrl = $next ? site_url('curso/' . $course['slug'] . '/aula/' . $next['slug']) : null;
$prevUrl = $prev ? site_url('curso/' . $course['slug'] . '/aula/' . $prev['slug']) : null;
$courseUrl = site_url('curso/' . $course['slug']);
?>
<style>
    .pl-crumb{margin-top:var(--space-6);font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#8ea0b6}
    .pl-crumb a{color:var(--gx-gold)}
    .pl-grid{display:grid;grid-template-columns:1fr 340px;gap:var(--space-6);margin-top:var(--space-4);align-items:start}
    .pl-video{position:relative;width:100%;aspect-ratio:16/9;background:#000;border:1px solid rgba(219,199,162,.2)}
    .pl-video iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
    .pl-text{padding:var(--space-8);background:#0a2547;border:1px solid rgba(219,199,162,.15);line-height:1.7;color:#dbe3ec;font-size:16px}
    .pl-title{font-size:28px;font-weight:900;letter-spacing:var(--ls-tight);margin:var(--space-6) 0 6px}
    .pl-meta{font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#8ea0b6}
    .pl-body{margin:var(--space-5) 0;color:#cdd7e4;line-height:1.7}
    .pl-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:var(--space-6);padding-top:var(--space-6);border-top:1px solid rgba(219,199,162,.15)}
    .pl-aside{background:#0a2547;border:1px solid rgba(219,199,162,.15);position:sticky;top:88px}
    .pl-aside__h{padding:16px 18px;border-bottom:1px solid rgba(219,199,162,.15);font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);font-weight:800;color:var(--gx-gold)}
    .pl-out{max-height:64vh;overflow:auto}
    .pl-out a,.pl-out span{display:flex;gap:12px;align-items:center;padding:12px 18px;border-bottom:1px solid rgba(219,199,162,.08);font-size:14px;color:#c7d2e0}
    .pl-out a:hover{background:rgba(219,199,162,.06)}
    .pl-out .cur{background:rgba(201,169,106,.14);color:#fff;border-left:3px solid var(--gx-gold)}
    .pl-out .n{flex:0 0 26px;height:26px;display:grid;place-items:center;border:1px solid rgba(219,199,162,.35);border-radius:var(--radius-pill);font-family:var(--font-mono);font-size:12px}
    .pl-out .n.done{background:var(--gx-gold);border-color:var(--gx-gold);color:var(--gx-primary-dark)}
    /* toast */
    .pl-toast{position:fixed;right:24px;bottom:24px;z-index:100;max-width:340px;background:var(--gx-primary-dark);border:1px solid var(--gx-gold);padding:18px 20px;transform:translateY(140%);transition:transform .4s var(--transition-smooth,ease);box-shadow:8px 8px 0 0 rgba(0,0,0,.4)}
    .pl-toast.show{transform:translateY(0)}
    .pl-toast b{color:var(--gx-gold);font-size:15px}
    .pl-toast .xp{font-family:var(--font-mono);font-size:32px;font-weight:900;color:#fff;margin:6px 0}
    .pl-toast small{color:#aebbcc;text-transform:uppercase;letter-spacing:var(--ls-wide);font-size:11px}
    .pl-ach{margin-top:10px;padding-top:10px;border-top:1px solid rgba(219,199,162,.2);font-size:13px;color:#eef2f7}
    @media(max-width:900px){.pl-grid{grid-template-columns:1fr}.pl-aside{position:static}}
</style>

<div class="pl-crumb"><a href="<?= $courseUrl ?>">← <?= esc($course['title']) ?></a></div>

<div class="pl-grid">
    <div>
        <?php if ($lesson['content_type'] === 'text' || empty($lesson['video_url'])): ?>
            <div class="pl-text"><?= $lesson['body_html'] ?: '<p style="color:#8ea0b6">Conteúdo desta aula em preparação.</p>' ?></div>
        <?php else: ?>
            <div class="pl-video"><iframe src="<?= esc($lesson['video_url'], 'attr') ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        <?php endif; ?>

        <h1 class="pl-title"><?= esc($lesson['title']) ?></h1>
        <div class="pl-meta"><?= esc($lesson['content_type'] === 'text' ? 'Leitura' : 'Vídeo-aula') ?> · <span class="ac-mono"><?= (int) $lesson['xp_reward'] ?></span> XP<?= $isCompleted ? ' · <span style="color:var(--gx-gold)">Concluída ✓</span>' : '' ?></div>

        <?php if ($lesson['content_type'] !== 'text' && !empty($lesson['body_html'])): ?>
            <div class="pl-body"><?= $lesson['body_html'] ?></div>
        <?php endif; ?>

        <div class="pl-actions">
            <div><?php if ($prevUrl): ?><a class="ac-btn ac-btn--ghost" href="<?= $prevUrl ?>">← Anterior</a><?php endif; ?></div>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
                <button class="ac-btn" id="btnComplete" data-completed="<?= $isCompleted ? '1' : '0' ?>"><?= $isCompleted ? '✓ Concluída' : 'Concluir aula' ?></button>
                <?php if ($nextUrl): ?><a class="ac-btn ac-btn--ghost" id="btnNext" href="<?= $nextUrl ?>">Próxima →</a><?php endif; ?>
            </div>
        </div>
    </div>

    <aside class="pl-aside">
        <div class="pl-aside__h"><?= esc($course['title']) ?></div>
        <div class="pl-out">
            <?php foreach ($allLessons as $i => $l):
                $done = ($progressMap[(int) $l['id']]['status'] ?? '') === 'completed';
                $cur = (int) $l['id'] === (int) $lesson['id'];
                $href = site_url('curso/' . $course['slug'] . '/aula/' . $l['slug']);
            ?>
                <a class="<?= $cur ? 'cur' : '' ?>" href="<?= $href ?>">
                    <span class="n <?= $done ? 'done' : '' ?>"><?= $done ? '✓' : ($i + 1) ?></span>
                    <span style="flex:1;padding:0;border:0;background:none;display:block"><?= esc($l['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<div class="pl-toast" id="toast">
    <b>+XP conquistado!</b>
    <div class="xp" id="toastXp">+0</div>
    <small id="toastMsg">Aula concluída</small>
    <div class="pl-ach" id="toastAch" style="display:none"></div>
</div>

<script>
(function(){
    var btn = document.getElementById('btnComplete');
    var toast = document.getElementById('toast');
    var completeUrl = <?= json_encode(site_url('curso/aula/completar')) ?>;
    var csrfName = <?= json_encode(csrf_token()) ?>;
    var csrfHash = <?= json_encode(csrf_hash()) ?>;
    var lessonId = <?= (int) $lesson['id'] ?>;

    function showToast(xp, msg, achievements){
        document.getElementById('toastXp').textContent = '+' + xp;
        document.getElementById('toastMsg').textContent = msg;
        var ach = document.getElementById('toastAch');
        if (achievements && achievements.length){
            ach.style.display = 'block';
            ach.innerHTML = '🏅 ' + achievements.map(function(a){return a.name;}).join(' · ');
        } else { ach.style.display = 'none'; }
        toast.classList.add('show');
        setTimeout(function(){ toast.classList.remove('show'); }, 5200);
    }

    if (btn && btn.dataset.completed !== '1'){
        btn.addEventListener('click', function(){
            btn.disabled = true; btn.textContent = 'Salvando…';
            var body = new URLSearchParams();
            body.append('lesson_id', lessonId);
            body.append(csrfName, csrfHash);
            fetch(completeUrl, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: body})
            .then(function(r){return r.json();})
            .then(function(d){
                if(!d.ok){ btn.disabled=false; btn.textContent='Concluir aula'; return; }
                btn.textContent = '✓ Concluída'; btn.dataset.completed='1';
                var msg = d.course_completed ? 'Trilha concluída! 🏆' : 'Aula concluída';
                showToast(d.xp_awarded, msg, d.new_achievements);
                if (typeof d.total_xp !== 'undefined'){
                    var xpEl = document.querySelector('.ac-xp b'); if(xpEl){ xpEl.textContent = d.total_xp; }
                }
                if (d.course_completed && d.certificate_url){
                    setTimeout(function(){ window.location = d.certificate_url; }, 2600);
                } else if (d.next_url){
                    var nx = document.getElementById('btnNext');
                    if (nx){ nx.classList.remove('ac-btn--ghost'); nx.textContent = 'Próxima →'; }
                }
            })
            .catch(function(){ btn.disabled=false; btn.textContent='Concluir aula'; });
        });
    }
})();
</script>

<?php echo view('courses/_foot'); ?>
