<?php
$footerMenuLinks = array_values(array_filter($baseMenuLinks ?? [], static function ($item) {
    return $item->item_visibility == 1 && $item->item_location == 'footer';
}));
$socialLinks = array_values(array_filter(getSocialLinksArray($baseSettings, false), static function ($item) {
    return !empty($item['value']);
}));
$contactPhoneHref = !empty($baseSettings->contact_phone) ? preg_replace('/[^0-9+]/', '', (string)$baseSettings->contact_phone) : '';
?>
<?= view('common/_json_ld'); ?>
<footer class="gx-site-footer">
    <div class="gx-wrap">
        <div class="gx-footer-grid">
            <section class="gx-footer-column gx-footer-brand">
                <a href="<?= langBaseUrl(); ?>" class="gx-footer-logo" aria-label="GX Capital">
                    <img src="<?= getLogoFooter(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
                </a>
                <?php if (!empty($baseSettings->about_footer)): ?>
                    <p class="gx-footer-copy"><?= esc($baseSettings->about_footer); ?></p>
                <?php endif; ?>
                <div class="gx-footer-contact">
                    <?php if (!empty($baseSettings->contact_phone)): ?>
                        <a href="<?= !empty($contactPhoneHref) ? 'tel:' . esc($contactPhoneHref) : '#'; ?>" class="gx-footer-chip"><?= esc($baseSettings->contact_phone); ?></a>
                    <?php endif;
                    if (!empty($baseSettings->contact_email)): ?>
                        <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="gx-footer-chip"><?= esc($baseSettings->contact_email); ?></a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="gx-footer-column">
                <p class="gx-footer-title">Navegação</p>
                <div class="gx-footer-links">
                    <?php if (!empty($footerMenuLinks)):
                        foreach ($footerMenuLinks as $item): ?>
                            <a href="<?= generateMenuItemURL($item, $baseCategories); ?>" class="gx-footer-link"><?= esc($item->item_name); ?></a>
                        <?php endforeach;
                    else: ?>
                        <a href="<?= esc($blogUrl ?? langBaseUrl('blog')); ?>" class="gx-footer-link">Blog</a>
<?php if (service('moduleRegistry')->enabled('simulators')): ?>
                        <a href="<?= esc($simulatorsHubUrl ?? langBaseUrl('simuladores')); ?>" class="gx-footer-link">Simuladores</a>
<?php endif; ?>
<?php if (service('moduleRegistry')->enabled('wealth')): ?>
                        <a href="<?= esc($wealthUrl ?? base_url('wealth')); ?>" class="gx-footer-link">Wealth</a>
<?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="gx-footer-column">
                <p class="gx-footer-title">Canais</p>
                <div class="gx-social-links">
                    <?php if (!empty($socialLinks)):
                        foreach ($socialLinks as $item): ?>
                            <a href="<?= esc($item['value']); ?>" class="gx-social-link" target="_blank" rel="noopener">
                                <?= esc(ucwords(str_replace(['-', '_'], ' ', $item['name']))); ?>
                            </a>
                        <?php endforeach;
                    else: ?>
                        <a href="<?= esc($blogUrl ?? langBaseUrl('blog')); ?>" class="gx-social-link">Blog GX Capital</a>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="gx-footer-bottom">
            <p><?= esc($baseSettings->copyright); ?></p>
            <a href="#gx-nav" class="gx-scrollup">Voltar ao topo</a>
        </div>
    </div>
</footer>

<?php if (empty(helperGetCookie('cks_warning')) && $baseSettings->cookies_warning): ?>
    <div class="gx-cookie-banner" id="gx-cookie-banner">
        <div class="gx-cookie-copy"><?= $baseSettings->cookies_warning_text; ?></div>
        <button type="button" class="gx-btn gx-btn-primary" id="gx-cookie-accept"><?= trans("accept_cookies"); ?></button>
    </div>
<?php endif; ?>

<?php if (!authCheck() && $generalSettings->registration_system == 1): ?>
    <div class="gx-modal-backdrop" id="modalLogin" hidden>
        <div class="gx-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="gx-login-title">
            <button type="button" class="gx-auth-close" data-gx-modal-close aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="gx-modal-header">
                <p class="gx-label">Área logada</p>
                <h2 class="gx-form-title" id="gx-login-title"><?= trans("login"); ?></h2>
                <p class="gx-form-copy">Acesse sua conta para continuar sua jornada na GX Capital.</p>
            </div>
            <div id="result-login"></div>
            <form id="form-login" class="gx-auth-form" action="<?= base_url('Auth/loginPost'); ?>" method="post">
                <div class="gx-form-field">
                    <label for="gx-login-email"><?= trans("email"); ?></label>
                    <input id="gx-login-email" type="email" name="email" maxlength="255" autocomplete="email" required>
                </div>
                <div class="gx-form-field">
                    <label for="gx-login-password"><?= trans("password"); ?></label>
                    <input id="gx-login-password" type="password" name="password" maxlength="255" autocomplete="current-password" required>
                </div>
                <div class="gx-auth-links">
                    <a href="<?= generateURL('forgot_password'); ?>"><?= trans("forgot_password"); ?>?</a>
                    <a href="<?= generateURL('register'); ?>"><?= trans("register"); ?></a>
                </div>
                <button type="submit" class="gx-btn gx-btn-primary gx-form-submit"><?= trans("login"); ?></button>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
(function() {
    var csrfName = <?= json_encode(csrf_token()); ?>;
    var sysLangId = <?= json_encode((string)$activeLang->id); ?>;
    var metaToken = document.querySelector('meta[name="X-CSRF-TOKEN"]');
    var captchaNodes = Array.prototype.slice.call(document.querySelectorAll('[data-gx-recaptcha]'));
    var captchaScriptLoading = false;
    var captchaScriptLoaded = false;

    function csrfValue() {
        return metaToken ? metaToken.getAttribute('content') : '';
    }

    function addSecurityFields(payload) {
        if (sysLangId) {
            payload.append('sys_lang_id', sysLangId);
            payload.append('sysLangId', sysLangId);
        }
        if (csrfName && csrfValue()) {
            payload.append(csrfName, csrfValue());
        }
        return payload;
    }

    document.querySelectorAll("form[method='post']").forEach(function(form) {
        if (!form.querySelector("input[name='sys_lang_id']")) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'sys_lang_id';
            input.value = sysLangId;
            form.appendChild(input);
        }
    });

    function setCaptchaMessage(node, message) {
        var placeholder = node ? node.querySelector('.gx-captcha-placeholder') : null;
        if (placeholder) {
            placeholder.textContent = message;
        }
    }

    function renderCaptchas() {
        if (!captchaNodes.length || typeof window.grecaptcha === 'undefined') {
            return;
        }

        captchaNodes.forEach(function(node) {
            if (node.getAttribute('data-gx-captcha-ready') === '1') {
                return;
            }

            try {
                node.innerHTML = '';
                window.grecaptcha.render(node, {
                    sitekey: node.getAttribute('data-sitekey') || '',
                    theme: node.getAttribute('data-theme') || 'light'
                });
                node.setAttribute('data-gx-captcha-ready', '1');
                node.classList.remove('is-loading');
                node.classList.add('is-loaded');
            } catch (error) {
                node.classList.remove('is-loading');
                node.innerHTML = '<div class="gx-captcha-placeholder">Não foi possível carregar a verificação de segurança agora.</div>';
            }
        });
    }

    window.gxHomeCaptchaReady = function() {
        captchaScriptLoaded = true;
        captchaScriptLoading = false;
        if (window.requestAnimationFrame) {
            window.requestAnimationFrame(renderCaptchas);
        } else {
            setTimeout(renderCaptchas, 16);
        }
    };

    function loadCaptchaScript() {
        if (!captchaNodes.length || captchaScriptLoading || captchaScriptLoaded) {
            return;
        }

        captchaScriptLoading = true;
        captchaNodes.forEach(function(node) {
            node.classList.add('is-loading');
            setCaptchaMessage(node, 'Carregando verificação de segurança...');
        });

        var script = document.createElement('script');
        var lang = captchaNodes[0].getAttribute('data-lang') || '';
        script.src = 'https://www.google.com/recaptcha/api.js?render=explicit&onload=gxHomeCaptchaReady' + (lang ? '&hl=' + encodeURIComponent(lang) : '');
        script.async = true;
        script.defer = true;
        script.onerror = function() {
            captchaScriptLoading = false;
            captchaNodes.forEach(function(node) {
                node.classList.remove('is-loading');
                setCaptchaMessage(node, 'Falha ao carregar a verificação de segurança.');
            });
        };
        document.head.appendChild(script);
    }

    if (captchaNodes.length) {
        captchaNodes.forEach(function(node) {
            var form = node.closest('form');
            if (!form) {
                return;
            }

            form.addEventListener('focusin', loadCaptchaScript, {passive: true});
            form.addEventListener('pointerdown', loadCaptchaScript, {passive: true});
            form.addEventListener('submit', function(event) {
                if (node.getAttribute('data-gx-captcha-ready') === '1') {
                    return;
                }
                event.preventDefault();
                loadCaptchaScript();
                setCaptchaMessage(node, 'A verificação está sendo carregada. Aguarde um instante e tente novamente.');
            }, true);
        });

        if ('IntersectionObserver' in window) {
            var captchaObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    loadCaptchaScript();
                    captchaObserver.unobserve(entry.target);
                });
            }, {rootMargin: '240px 0px'});
            captchaNodes.forEach(function(node) {
                captchaObserver.observe(node);
            });
        } else {
            loadCaptchaScript();
        }
    }

    var cookieButton = document.getElementById('gx-cookie-accept');
    var cookieBanner = document.getElementById('gx-cookie-banner');
    if (cookieButton && cookieBanner) {
        cookieButton.addEventListener('click', function() {
            cookieBanner.classList.add('is-hidden');
            /* Update Google Consent Mode on cookie acceptance */
            if (typeof gtag === 'function') {
                gtag('consent', 'update', {
                    'ad_storage': 'granted',
                    'ad_user_data': 'granted',
                    'ad_personalization': 'granted',
                    'analytics_storage': 'granted'
                });
            }
            document.cookie = 'gx_cookie_consent=accepted;path=/;max-age=31536000;SameSite=Lax';
            var payload = addSecurityFields(new FormData());
            fetch(<?= json_encode(base_url('close-cookies-warning-post')); ?>, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin'
            }).catch(function() {});
        });
    }

    var modal = document.getElementById('modalLogin');
    var modalOpeners = document.querySelectorAll('[data-gx-modal-open="modalLogin"], [data-bs-target="#modalLogin"]');
    var modalClosers = document.querySelectorAll('[data-gx-modal-close]');

    function openModal() {
        if (!modal) {
            return;
        }
        modal.hidden = false;
        document.body.classList.add('gx-modal-open');
    }

    function closeModal() {
        if (!modal) {
            return;
        }
        modal.hidden = true;
        document.body.classList.remove('gx-modal-open');
    }

    modalOpeners.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            openModal();
        });
    });

    modalClosers.forEach(function(button) {
        button.addEventListener('click', function() {
            closeModal();
        });
    });

    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    var loginForm = document.getElementById('form-login');
    var loginResult = document.getElementById('result-login');
    if (loginForm && loginResult) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            loginResult.innerHTML = '';
            var payload = addSecurityFields(new FormData(loginForm));
            fetch(loginForm.getAttribute('action'), {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then(function(response) {
                return response.text();
            }).then(function(text) {
                try {
                    var data = JSON.parse(text);
                    if (data.result === 1) {
                        window.location.reload();
                        return;
                    }
                    if (data.result === 0 && data.error_message) {
                        loginResult.innerHTML = data.error_message;
                        return;
                    }
                } catch (error) {}
                loginResult.innerHTML = text;
            }).catch(function() {
                loginResult.innerHTML = '<div class="m-b-15"><div class="alert alert-danger">Não foi possível concluir o login agora.</div></div>';
            });
        });
    }
})();
</script>
<?php if ($generalSettings->pwa_status == 1): ?>
<script>if ('serviceWorker' in navigator) {window.addEventListener('load', function () {navigator.serviceWorker.register('<?= base_url('pwa-sw.js');?>').catch(function () {});});}</script>
<?php endif; ?>
<?= $generalSettings->adsense_activation_code; ?>
<?= $generalSettings->google_analytics; ?>
<?= $generalSettings->custom_footer_codes; ?>
<script>
(function() {
    /* ── GX Tracking Toolkit (global) ── */
    window.dataLayer = window.dataLayer || [];

    /* Safe wrappers — never break the page if pixel/gtag is absent */
    window.gxFbq = function(action, event, params) {
        if (typeof fbq !== 'function') return;
        if (action === 'trackCustom') {
            fbq('trackCustom', event, params || {});
        } else {
            fbq('track', event, params || {});
        }
    };

    window.gxGtag = function() {
        if (typeof gtag === 'function') {
            gtag.apply(null, arguments);
        }
    };

    /* ── UTM persistence via sessionStorage ── */
    var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    var params = new URLSearchParams(window.location.search);
    var stored = {};
    try { stored = JSON.parse(sessionStorage.getItem('gx_utm') || '{}'); } catch (e) {}

    var hasNew = false;
    utmKeys.forEach(function(key) {
        if (params.has(key)) {
            stored[key] = params.get(key);
            hasNew = true;
        }
    });
    if (hasNew) {
        try { sessionStorage.setItem('gx_utm', JSON.stringify(stored)); } catch (e) {}
    }

    /* Inject stored UTMs into any FormData */
    window.gxAppendUtm = function(payload) {
        var utm = {};
        try { utm = JSON.parse(sessionStorage.getItem('gx_utm') || '{}'); } catch (e) {}
        var p = new URLSearchParams(window.location.search);
        utmKeys.forEach(function(key) {
            var val = p.get(key) || utm[key] || '';
            if (val && !payload.get(key)) {
                payload.append(key, val);
            }
        });
    };

    /* Inject UTMs into hidden fields of a <form> element */
    window.gxHydrateUtmFields = function(form) {
        if (!form) return;
        var utm = {};
        try { utm = JSON.parse(sessionStorage.getItem('gx_utm') || '{}'); } catch (e) {}
        var p = new URLSearchParams(window.location.search);
        utmKeys.forEach(function(key) {
            var val = p.get(key) || utm[key] || '';
            var field = form.querySelector('input[name="' + key + '"]');
            if (field && val) field.value = val;
        });
    };

    /* ── Event ID generator for Meta deduplication ── */
    window.gxEventId = function() {
        var ts = Date.now().toString(36);
        var rand = Math.random().toString(36).substring(2, 10);
        return 'gx_' + ts + '_' + rand;
    };

    /* ── Enhanced Conversions: send hashed user_data to Google ── */
    window.gxSetUserData = function(email, phone) {
        if (typeof gtag !== 'function') return;
        var userData = {};
        if (email) userData.email = email.toLowerCase().trim();
        if (phone) userData.phone_number = phone.replace(/[^+\d]/g, '');
        if (Object.keys(userData).length > 0) {
            gtag('set', 'user_data', userData);
        }
    };

    /* ── Meta Pixel with event_id for deduplication ── */
    window.gxFbqDedup = function(action, event, params) {
        if (typeof fbq !== 'function') return '';
        var eventId = gxEventId();
        var p = Object.assign({}, params || {});
        p.eventID = eventId;
        if (action === 'trackCustom') {
            fbq('trackCustom', event, p, { eventID: eventId });
        } else {
            fbq('track', event, p, { eventID: eventId });
        }
        return eventId;
    };

    /* ── Global WhatsApp click tracking ── */
    document.addEventListener('click', function(e) {
        var link = e.target.closest ? e.target.closest('[data-gx-whatsapp-link]') : null;
        if (!link) return;
        gxFbq('track', 'Contact', { content_name: 'WhatsApp Click' });
        gxGtag('event', 'contact', {
            method: 'whatsapp',
            event_category: 'engagement',
            event_label: link.getAttribute('href') || ''
        });
    }, { passive: true });

    /* ── dataLayer page context ── */
    var pagePath = window.location.pathname;
    var pageTitle = document.title;
    window.dataLayer.push({
        event: 'gx_page_view',
        page_path: pagePath,
        page_title: pageTitle,
        utm_source: stored.utm_source || '',
        utm_medium: stored.utm_medium || '',
        utm_campaign: stored.utm_campaign || ''
    });
})();
</script>
</body>
</html>
