<script>
/* Gerador de imagem por IA (capas de curso/aula/espaço). Botões .gxc-genimg:
   - dentro de um form: lê title/subtitle/category/level/name do form + fill do input+preview;
   - fora de form (linha de tabela): usa data-title/data-id/data-type + data-preview (seletor do <img>). */
(function(){
    if (window.__gxGenImgBound) return; window.__gxGenImgBound = true;
    var endpoint = <?= json_encode(adminUrl('cursos/gerar-imagem')) ?>;
    var cn = <?= json_encode(csrf_token()) ?>, ch = <?= json_encode(csrf_hash()) ?>;
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.gxc-genimg');
        if (!btn) return;
        e.preventDefault();
        var form = btn.closest('form');
        var read = function(n){
            if (btn.dataset[n]) return btn.dataset[n];
            if (form){ var el = form.querySelector('[name="'+n+'"]'); if (el) return el.value; }
            return '';
        };
        var title = btn.dataset.title || read('title') || (form && form.querySelector('[name="name"]') ? form.querySelector('[name="name"]').value : '');
        if (!title.trim()) { alert('Preencha o título/nome antes de gerar a imagem.'); return; }
        var body = new URLSearchParams();
        body.append('type', btn.dataset.type || 'course');
        body.append('title', title);
        body.append('subtitle', read('subtitle') || read('description'));
        body.append('category', read('category'));
        body.append('level', read('level'));
        if (btn.dataset.format) body.append('format', btn.dataset.format);
        var id = btn.dataset.id || read('lesson_id') || '';
        if (id) body.append('id', id);
        body.append(cn, ch);
        var orig = btn.textContent; btn.disabled = true; btn.textContent = '🎨 Gerando… (~30s)';
        fetch(endpoint, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: body})
        .then(function(r){ return r.json(); })
        .then(function(d){
            btn.disabled = false; btn.textContent = orig;
            if (!d.ok) { alert(d.error || 'Falha ao gerar a imagem.'); return; }
            if (form){
                var input = form.querySelector('[name="cover_image"]');
                if (input) input.value = d.url;
                var prev = form.querySelector('.gxc-genimg-preview');
                if (prev) { prev.src = d.url; prev.style.display = 'block'; }
            }
            if (btn.dataset.preview){
                var img = document.querySelector(btn.dataset.preview);
                if (img){ img.src = d.url; img.style.display = 'inline-block'; }
            }
        })
        .catch(function(){ btn.disabled = false; btn.textContent = orig; alert('Erro de rede ao gerar a imagem.'); });
    });
})();
</script>
