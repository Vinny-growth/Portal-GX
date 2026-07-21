<script>
/* Setas dos carrosséis (.ac-row › .ac-track). Rolam ~1 tela e se escondem no início/fim. */
(function(){
    document.querySelectorAll('.ac-row').forEach(function(row){
        var track = row.querySelector('.ac-track'); if (!track) return;
        var step = function(){ return Math.max(280, track.clientWidth * 0.82); };
        var l = row.querySelector('.ac-arrow--l'), r = row.querySelector('.ac-arrow--r');
        if (l) l.addEventListener('click', function(){ track.scrollBy({left:-step(), behavior:'smooth'}); });
        if (r) r.addEventListener('click', function(){ track.scrollBy({left:step(), behavior:'smooth'}); });
        var upd = function(){
            if (l) l.style.visibility = track.scrollLeft > 8 ? 'visible' : 'hidden';
            if (r) r.style.visibility = (track.scrollLeft + track.clientWidth) < (track.scrollWidth - 8) ? 'visible' : 'hidden';
        };
        upd(); track.addEventListener('scroll', upd, {passive:true}); window.addEventListener('resize', upd);
    });
})();
</script>
