<?php if (!empty($postJsonLD) && isset($postTimeSpent)): ?>
    <script>let mouseMoveCount = 0, scrollCount = 0, debounceTimer, mouseMoveDebounceTimer, debounceDelay = 8;function incrementMouseMove() {clearTimeout(mouseMoveDebounceTimer);mouseMoveDebounceTimer = setTimeout(function () {mouseMoveCount++;}, debounceDelay);}document.addEventListener('mousemove', incrementMouseMove);document.addEventListener('touchmove', incrementMouseMove);window.addEventListener('scroll', function () {clearTimeout(debounceTimer);debounceTimer = setTimeout(function () {scrollCount++;}, debounceDelay);});setTimeout(function () {let userBehaviorData = {postId: '<?= $postJsonLD->id; ?>', mouseMoveCount: mouseMoveCount, scrollCount: scrollCount};$.ajax({type: 'POST', url: VrConfig.baseURL + '/Ajax/incrementPostViews', data: setAjaxData(userBehaviorData)});}, <?= $postTimeSpent; ?>);</script>
<?php endif; ?>

<?php if (!empty($jsPage) && $jsPage == 'gallery'): ?>
    <script src="<?= base_url('assets/vendor/masonry-filter/imagesloaded.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/masonry-filter/masonry-3.1.4.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/masonry-filter/masonry.filter.js'); ?>"></script>
    <link href="<?= base_url('assets/vendor/glightbox/css/glightbox.min.css'); ?>" rel="stylesheet">
    <script src="<?= base_url('assets/vendor/glightbox/js/glightbox.min.js'); ?>"></script>
    <script>
        var startFromLeft = true;
        if (VrConfig.rtl == true) {
            startFromLeft = false;
        }
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: false
        });
        $(document).ready(function () {
            $(document).on("click touchstart", ".filters .btn", function () {
                $(".filters .btn").removeClass("active"), $(this).addClass("active")
            }), $(function () {
                var i = $("#masonry");
                i.imagesLoaded(function () {
                    i.masonry({gutterWidth: 0, isAnimated: !0, itemSelector: ".gallery-item", isOriginLeft: startFromLeft})
                }), $(".filters .btn").click(function (t) {
                    t.preventDefault();
                    var e = $(this).attr("data-filter");
                    i.masonryFilter({
                        filter: function () {
                            return !e || $(this).attr("data-filter") == e
                        }
                    })
                })
            })
        });
    </script>
<?php endif;
if (!empty($post) && ($post->post_type == 'trivia_quiz' || $post->post_type == 'personality_quiz' || $post->post_type == 'poll')): ?>
    <script src="<?= base_url('assets/vendor/quiz/quiz-2.2.js'); ?>"></script>
    <script>
        $(document).ready(function () {
            getQuizAnswers('<?= $post->id; ?>');
            getQuizResults('<?= $post->id; ?>');
        });
    </script>
<?php endif;
if (!empty($post) && $post->post_type == "video"): ?>
    <script src="<?= base_url('assets/vendor/plyr/plyr.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/plyr/plyr.polyfilled.min.js'); ?>"></script>
    <script>
        const player = new Plyr('#player');
        $(document).ajaxStop(function () {
            const player = new Plyr('#player');
        });
    </script>
<?php endif;
if (checkNewsletterModal()):?>
    <script>$(window).on('load', function () {
            $('#modalNewsletter').modal('show');
        });</script>
<?php endif; ?>