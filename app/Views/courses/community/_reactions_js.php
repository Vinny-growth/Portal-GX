<script>
(function(){
    var url = <?= json_encode(site_url('comunidade/reagir')) ?>;
    var cn = <?= json_encode(csrf_token()) ?>, ch = <?= json_encode(csrf_hash()) ?>;
    document.querySelectorAll('[data-react]').forEach(function(btn){
        btn.addEventListener('click', function(){
            btn.disabled = true;
            var body = new URLSearchParams();
            body.append('target_type', btn.dataset.type);
            body.append('target_id', btn.dataset.id);
            body.append(cn, ch);
            fetch(url, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:body})
            .then(function(r){return r.json();})
            .then(function(d){
                btn.disabled=false;
                if(!d.ok) return;
                btn.classList.toggle('on', d.reacted);
                var b = btn.querySelector('b'); if(b){ b.textContent = d.count; }
            }).catch(function(){ btn.disabled=false; });
        });
    });
})();
</script>
