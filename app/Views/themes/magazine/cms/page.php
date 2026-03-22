<?php /* Theme override for CMS page, based on app/Views/cms/page.php */ ?>
<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <h1 class="page-title"><?= esc($page->title); ?></h1>
            <div class="page-content">
                <?php 
                if (!function_exists('cms_render_block')) {
                    function cms_block_style($b){
                        $s = '';
                        $style = $b['style'] ?? [];
                        if (!empty($style['bg'])) $s .= 'background:'.html_escape($style['bg']).';';
                        if (!empty($style['color'])) $s .= 'color:'.html_escape($style['color']).';';
                        if (!empty($style['padding'])) $s .= 'padding:'.html_escape($style['padding']).';';
                        $cls = '';
                        $al = $style['align'] ?? '';
                        if ($al==='center') $cls .= ' text-center';
                        elseif ($al==='right') $cls .= ' text-end';
                        $hm = !empty($b['hide_mobile']);
                        if ($hm) $cls .= ' d-none d-md-block';
                        return [$cls, $s];
                    }
                    function cms_render_block($b){
                        $t = $b['type'] ?? 'paragraph';
                        [$cls, $styleStr] = cms_block_style($b);
                        if ($t=='heading') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><h2>'.esc($b['content'] ?? '').'</h2></div>';
                        } elseif ($t=='paragraph') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><p>'.esc($b['content'] ?? '').'</p></div>';
                        } elseif ($t=='image') {
                            if (!empty($b['url'])) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'"><figure><img src="'.esc($b['url']).'" alt="'.esc($b['text'] ?? '').'" style="max-width:100%; height:auto;">'.(!empty($b['text'])?'<figcaption>'.esc($b['text']).'</figcaption>':'').'</figure></div>';
                            }
                        } elseif ($t=='video') {
                            $u = $b['url'] ?? '';
                            $embed = '';
                            if (strpos($u,'youtube.com')!==false || strpos($u,'youtu.be')!==false) {
                                if (preg_match('~(?:v=|youtu.be/)([\w-]{6,})~', $u, $m)) { $embed = 'https://www.youtube.com/embed/'.html_escape($m[1]); }
                            } elseif (strpos($u,'vimeo.com')!==false) {
                                if (preg_match('~vimeo.com/(\d+)~', $u, $m)) { $embed = 'https://player.vimeo.com/video/'.html_escape($m[1]); }
                            }
                            if ($embed) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'"><div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"><iframe src="'.$embed.'" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div></div>';
                            }
                        } elseif ($t=='card') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><div class="cms-card">';
                            if (!empty($b['image'])) echo '<img src="'.esc($b['image']).'" alt="">';
                            if (!empty($b['title'])) echo '<h3>'.esc($b['title']).'</h3>';
                            if (!empty($b['content'])) echo '<p>'.esc($b['content']).'</p>';
                            $btnText = $b['btn_text'] ?? ''; $btnUrl = $b['btn_url'] ?? '';
                            if ($btnText && $btnUrl) echo '<p><a class="btn btn-custom" href="'.esc($btnUrl).'">'.esc($btnText).'</a></p>';
                            echo '</div></div>';
                        } elseif ($t=='button') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><p><a class="btn btn-custom" href="'.esc($b['url'] ?? '#').'">'.esc($b['text'] ?? 'Saiba mais').'</a></p></div>';
                        } elseif ($t=='banner') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><div style="border:1px solid #eee; border-radius:10px; padding:16px; background:linear-gradient(135deg,#f8f9ff 0%,#eef5ff 100%); margin:18px 0;"><h3>'.esc($b['content'] ?? '').'</h3></div></div>';
                        } elseif ($t=='form') {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'">'.view('cms/_contact_form').'</div>';
                        } elseif ($t=='register') {
                            if (!function_exists('authCheck') || !authCheck()) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'">'.view('cms/_register_form').'</div>';
                            }
                        } elseif ($t=='carousel') {
                            $items = $b['items'] ?? [];
                            $autoplay = !empty($b['autoplay']); $dots = !empty($b['dots']); $pause = !empty($b['pause']); $interval = (int)($b['interval'] ?? 4000);
                            if (is_array($items) && count($items)) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'">';
                                echo '<div class="wm-carousel" data-index="0" data-autoplay="'.($autoplay?1:0).'" data-interval="'.$interval.'" data-dots="'.($dots?1:0).'" data-pause="'.($pause?1:0).'">';
                                foreach ($items as $ix => $it) {
                                    $active = $ix === 0 ? 'style="opacity:1;"' : 'style="opacity:0;"';
                                    $url = esc($it['url'] ?? ''); $txt = esc($it['text'] ?? '');
                                    echo '<figure class="wm-slide" '.$active.'><img src="'.$url.'" alt="'.$txt.'" style="max-width:100%; height:auto;">'.($txt?'<figcaption>'.$txt.'</figcaption>':'').'</figure>';
                                }
                                echo '<div class="wm-nav" style="margin-top:6px; text-align:center;"><button class="btn btn-xs btn-default wm-prev">◀</button> <button class="btn btn-xs btn-default wm-next">▶</button>'.($dots?'<span class="wm-dots"></span>':'').'</div>';
                                echo '</div></div>';
                            }
                        } elseif ($t=='accordion') {
                            $items = $b['items'] ?? []; $single = !empty($b['single_open']);
                            if (is_array($items) && count($items)) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'"><div class="wm-accordion'.($single?' wm-acc-single':'').'">';
                                foreach ($items as $ix=>$it) {
                                    $ti = esc($it['title'] ?? ('Item '.($ix+1))); $co = esc($it['content'] ?? '');
                                    echo '<div class="wm-acc-item" style="border:1px solid #eee; border-radius:6px; margin-bottom:6px;">'
                                        .'<div class="wm-acc-head" style="padding:10px; cursor:pointer; font-weight:600;">'.$ti.'</div>'
                                        .'<div class="wm-acc-body" style="display:none; padding:10px;">'.$co.'</div>'
                                        .'</div>';
                                }
                                echo '</div></div>';
                            }
                        } elseif ($t=='cards-grid' || $t=='cards_grid') {
                            $cols = max(2, min(4, (int)($b['grid_cols'] ?? 3)));
                            $w = (int)floor(12 / $cols);
                            $items = $b['items'] ?? [];
                            if (is_array($items) && count($items)) {
                                echo '<div class="'.$cls.'" style="'.$styleStr.'"><div class="row">';
                                foreach ($items as $it) {
                                    $img = esc($it['image'] ?? ''); $ti = esc($it['title'] ?? ''); $co = esc($it['content'] ?? ''); $bt = esc($it['btn_text'] ?? ''); $bu = esc($it['btn_url'] ?? '');
                                    echo '<div class="col-md-'.$w.'" style="margin-bottom:12px;">'
                                        .'<div class="cms-card">'
                                        .($img?'<img src="'.$img.'" alt="">':'')
                                        .($ti?'<h3>'.$ti.'</h3>':'')
                                        .($co?'<p>'.$co.'</p>':'')
                                        .(($bt&&$bu)?'<p><a class="btn btn-custom" href="'.$bu.'">'.$bt.'</a></p>':'')
                                        .'</div>'
                                        .'</div>';
                                }
                                echo '</div></div>';
                            }
                        } elseif ($t=='section') {
                            $cols = $b['cols'] ?? [[],[]]; $layout = $b['layout'] ?? []; $count = is_array($cols) ? count($cols) : 2;
                            $widths = [];
                            if (is_array($layout) && count($layout) == $count) {
                                foreach ($layout as $w) { $widths[] = max(1, min(12, (int)$w)); }
                            } else {
                                $base = floor(12 / max(1,$count));
                                for ($i=0;$i<$count;$i++) { $widths[] = $base; }
                            }
                            echo '<div class="row'.$cls.'" style="margin-bottom:10px;'.$styleStr.'">';
                            for ($i=0;$i<$count;$i++) {
                                $inner = $cols[$i] ?? []; $w = $widths[$i] ?? 6;
                                echo '<div class="col-md-'.html_escape($w).'">';
                                foreach ($inner as $ib) { cms_render_block($ib); }
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="'.$cls.'" style="'.$styleStr.'"><p>'.esc($b['content'] ?? '').'</p></div>';
                        }
                    }
                }
                $blocks = $layout['blocks'] ?? []; if (!empty($blocks)): foreach ($blocks as $b): ?>
                    <?php $t = $b['type'] ?? 'paragraph'; ?>
                    <?php if ($t=='heading'): ?>
                        <h2><?= esc($b['content'] ?? ''); ?></h2>
                    <?php elseif ($t=='paragraph'): ?>
                        <p><?= esc($b['content'] ?? ''); ?></p>
                    <?php elseif ($t=='section'): ?>
                        <?php $cols = $b['cols'] ?? [[],[]]; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php foreach (($cols[0] ?? []) as $ib): cms_render_block($ib); endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <?php foreach (($cols[1] ?? []) as $ib): cms_render_block($ib); endforeach; ?>
                            </div>
                        </div>
                    <?php elseif ($t=='image'): ?>
                        <?php if (!empty($b['url'])): ?>
                            <figure><img src="<?= esc($b['url']); ?>" alt="<?= esc($b['text'] ?? ''); ?>" style="max-width:100%; height:auto;"><?php if (!empty($b['text'])): ?><figcaption><?= esc($b['text']); ?></figcaption><?php endif; ?></figure>
                        <?php endif; ?>
                    <?php elseif ($t=='button'): ?>
                        <p><a class="btn btn-custom" href="<?= esc($b['url'] ?? '#'); ?>"><?= esc($b['text'] ?? 'Saiba mais'); ?></a></p>
                    <?php elseif ($t=='banner'): ?>
                        <div style="border:1px solid #eee; border-radius:10px; padding:16px; background:linear-gradient(135deg,#f8f9ff 0%,#eef5ff 100%); margin:18px 0;">
                            <h3><?= esc($b['content'] ?? ''); ?></h3>
                        </div>
                    <?php elseif ($t=='form'): ?>
                        <?= view('cms/_contact_form'); ?>
                    <?php elseif (in_array($t, ['cards-grid','carousel','accordion','card','video','register'])): ?>
                        <?php cms_render_block($b); ?>
                    <?php endif; ?>
                <?php endforeach; else: ?>
                    <p>Conteúdo em breve.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    // Carousel
    document.querySelectorAll('.wm-carousel').forEach(function(car){
        var slides = car.querySelectorAll('.wm-slide');
        slides.forEach(function(s){ s.style.position='absolute'; s.style.top='0'; s.style.left='0'; s.style.right='0'; s.style.transition='opacity .4s ease'; });
        car.style.position='relative';
        var dotsWrap = car.querySelector('.wm-dots');
        var autoplay = (car.getAttribute('data-autoplay')==='1');
        var pause = (car.getAttribute('data-pause')==='1');
        var interval = parseInt(car.getAttribute('data-interval')||'4000',10);
        var timer = null;
        var idx = 0; function show(i){ idx=i; slides.forEach(function(s, k){ s.style.opacity = (k===i? '1':'0'); }); if (dotsWrap){ var dots = dotsWrap.querySelectorAll('button'); dots.forEach(function(d, k){ d.classList.toggle('active', k===i); }); } }
        var prev = car.querySelector('.wm-prev'); var next = car.querySelector('.wm-next');
        if (prev) prev.addEventListener('click', function(){ idx = (idx-1+slides.length)%slides.length; show(idx); });
        if (next) next.addEventListener('click', function(){ idx = (idx+1)%slides.length; show(idx); });
        if (dotsWrap){ dotsWrap.innerHTML = ''; for (var i=0;i<slides.length;i++){ var b=document.createElement('button'); b.className='btn btn-xs '+(i===0?'btn-primary':'btn-default'); b.style.margin='0 2px'; (function(n){ b.addEventListener('click', function(){ show(n); updateDots(); }); })(i); dotsWrap.appendChild(b);} function updateDots(){ var buttons=dotsWrap.querySelectorAll('button'); buttons.forEach(function(d,k){ d.className = 'btn btn-xs ' + (k===idx?'btn-primary':'btn-default'); }); } }
        function start(){ if (autoplay){ stop(); timer = setInterval(function(){ idx = (idx+1)%slides.length; show(idx); }, interval); } }
        function stop(){ if (timer){ clearInterval(timer); timer=null; } }
        if (pause){ car.addEventListener('mouseenter', stop); car.addEventListener('mouseleave', start); }
        show(0); start();
    });
    // Accordion
    document.querySelectorAll('.wm-accordion .wm-acc-head').forEach(function(h){
        h.addEventListener('click', function(){
            var b = this.nextElementSibling; if (!b) return; var wrap = this.closest('.wm-accordion');
            var single = wrap && wrap.classList.contains('wm-acc-single');
            if (single){ wrap.querySelectorAll('.wm-acc-body').forEach(function(x){ if (x!==b) x.style.display='none'; }); }
            b.style.display = (b.style.display==='none'||!b.style.display)?'block':'none';
        });
    });
})();
</script>

