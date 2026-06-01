<script>
(function() {
    var nav = document.getElementById('gx-nav');
    var toggle = document.getElementById('gx-nav-toggle');
    var links = document.getElementById('gx-nav-links');
    var csrfName = '<?= csrf_token(); ?>';
    var csrfHash = '<?= csrf_hash(); ?>';
    var diagnosticTracked = false;

    function postTrack(name) {
        try {
            var fd = new FormData();
            fd.append('name', name);
            fd.append(csrfName, csrfHash);
            fetch('<?= base_url('WealthManager/trackEvent'); ?>', {method: 'POST', body: fd});
        } catch (e) {}
    }

    if (nav) {
        var check = function() {
            nav.classList.toggle('is-scrolled', window.scrollY > 20);
        };
        check();
        window.addEventListener('scroll', check, {passive: true});
    }

    if (toggle && links && nav) {
        toggle.addEventListener('click', function() {
            var open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        links.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    document.querySelectorAll('[data-gx-reveal]').forEach(function(node) {
        node.classList.remove('is-visible');
    });
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                var delay = entry.target.getAttribute('data-gx-delay');
                if (delay) {
                    entry.target.style.transitionDelay = delay + 'ms';
                }
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, {threshold: 0.1, rootMargin: '0px 0px -40px 0px'});
        document.querySelectorAll('[data-gx-reveal]').forEach(function(node) {
            observer.observe(node);
        });
    } else {
        document.querySelectorAll('[data-gx-reveal]').forEach(function(node) {
            node.classList.add('is-visible');
        });
    }

    document.querySelectorAll('[data-wealth-track]').forEach(function(node) {
        node.addEventListener('click', function() {
            postTrack(node.getAttribute('data-wealth-track'));
        });
    });

    var forms = Array.prototype.slice.call(document.querySelectorAll('.js-wealth-lead-form'));
    var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    function setFieldOnForms(name, value) {
        forms.forEach(function(form) {
            var field = form.querySelector('[name="' + name + '"]');
            if (field) {
                field.value = value || '';
            }
        });
    }

    try {
        var params = new URLSearchParams(window.location.search);
        utmKeys.forEach(function(key) {
            var storageKey = 'gx_wealth_' + key;
            var value = params.get(key) || window.localStorage.getItem(storageKey) || '';
            if (params.get(key)) {
                window.localStorage.setItem(storageKey, params.get(key));
            }
            setFieldOnForms(key, value);
        });
    } catch (e) {}

    var objectiveButtons = Array.prototype.slice.call(document.querySelectorAll('[data-wealth-objective]'));
    var investedInput = document.getElementById('gx-wealth-invested');
    var contributionInput = document.getElementById('gx-wealth-monthly-invest');
    var costInput = document.getElementById('gx-wealth-monthly-cost');
    var targetCapitalNode = document.getElementById('gx-wealth-target-capital');
    var projectionNode = document.getElementById('gx-wealth-projection-10y');
    var coverageNode = document.getElementById('gx-wealth-coverage');
    var gapNode = document.getElementById('gx-wealth-gap');
    var insightNode = document.getElementById('gx-wealth-insight-text');
    var selectedObjective = objectiveButtons.length ? objectiveButtons[0].getAttribute('data-wealth-objective') : '';

    function formatCurrency(value) {
        return value.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            maximumFractionDigits: 0
        });
    }

    function calculateFutureValue(presentValue, contribution, monthlyRate, months) {
        var total = presentValue;
        for (var i = 0; i < months; i += 1) {
            total = (total * (1 + monthlyRate)) + contribution;
        }
        return total;
    }

    function trackDiagnosticOnce() {
        if (diagnosticTracked) {
            return;
        }
        diagnosticTracked = true;
        postTrack('wealth_diagnostic_interaction');
    }

    function updateDiagnostic() {
        if (!investedInput || !contributionInput || !costInput) {
            return;
        }

        var invested = Math.max(parseFloat(investedInput.value || '0'), 0);
        var monthlyInvest = Math.max(parseFloat(contributionInput.value || '0'), 0);
        var monthlyCost = Math.max(parseFloat(costInput.value || '0'), 0);
        var targetCapital = monthlyCost > 0 ? (monthlyCost * 12) / 0.04 : 0;
        var projection10y = calculateFutureValue(invested, monthlyInvest, 0.0045, 120);
        var coverage = targetCapital > 0 ? (projection10y / targetCapital) * 100 : 0;
        var gap = targetCapital > 0 ? Math.max(targetCapital - projection10y, 0) : 0;

        if (targetCapitalNode) {
            targetCapitalNode.textContent = formatCurrency(targetCapital);
        }
        if (projectionNode) {
            projectionNode.textContent = formatCurrency(projection10y);
        }
        if (coverageNode) {
            coverageNode.textContent = targetCapital > 0 ? coverage.toFixed(1).replace('.', ',') + '%' : '0,0%';
        }
        if (gapNode) {
            gapNode.textContent = formatCurrency(gap);
        }
        if (insightNode) {
            if (!monthlyCost) {
                insightNode.textContent = 'Informe o custo de vida mensal para estimar o capital-alvo patrimonial.';
            } else if (projection10y >= targetCapital) {
                insightNode.textContent = 'Mantido o ritmo atual, a projeção em 10 anos ultrapassa o capital estimado para sustentar o padrão de vida informado.';
            } else {
                insightNode.textContent = 'No ritmo atual, ainda existe um gap relevante entre a projeção patrimonial e o capital estimado para sustentar o padrão de vida desejado.';
            }
        }

        setFieldOnForms('diagnosis_invested', invested.toFixed(2));
        setFieldOnForms('diagnosis_monthly_invest', monthlyInvest.toFixed(2));
        setFieldOnForms('diagnosis_monthly_cost', monthlyCost.toFixed(2));
        setFieldOnForms('diagnosis_target_capital', targetCapital.toFixed(2));
        setFieldOnForms('diagnosis_projection_10y', projection10y.toFixed(2));
        setFieldOnForms('diagnosis_gap', gap.toFixed(2));
        setFieldOnForms('diagnosis_coverage_pct', coverage.toFixed(2));
        setFieldOnForms('diagnosis_objective', selectedObjective);
    }

    if (objectiveButtons.length) {
        objectiveButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                selectedObjective = button.getAttribute('data-wealth-objective') || '';
                objectiveButtons.forEach(function(item) {
                    item.classList.toggle('is-active', item === button);
                });
                setFieldOnForms('diagnosis_objective', selectedObjective);
                setFieldOnForms('goal', selectedObjective);
                trackDiagnosticOnce();
                updateDiagnostic();
            });
        });
    }

    [investedInput, contributionInput, costInput].forEach(function(input) {
        if (!input) {
            return;
        }
        input.addEventListener('input', function() {
            trackDiagnosticOnce();
            updateDiagnostic();
        });
    });
    updateDiagnostic();

    forms.forEach(function(form) {
        var submitButton = form.querySelector('button[type="submit"]');
        var feedback = form.querySelector('.gx-wealth-form-feedback');
        var shell = form.closest('.gx-wealth-form-shell');
        var successBox = shell ? shell.querySelector('.gx-wealth-form-success') : null;

        form.addEventListener('submit', function(event) {
            if (!window.fetch || !window.FormData) {
                return;
            }

            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            var ajaxUrl = form.getAttribute('data-ajax-url') || form.action;
            var formData = new FormData(form);
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
            }
            if (feedback) {
                feedback.textContent = '';
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
                .then(function(response) {
                    return response.json().catch(function() {
                        return {};
                    }).then(function(data) {
                        return {ok: response.ok, data: data};
                    });
                })
                .then(function(result) {
                    if (!result.ok || !result.data.success) {
                        throw new Error(result.data.message || 'Não foi possível enviar seus dados.');
                    }
                    if (shell) {
                        shell.classList.add('is-success');
                    }
                    if (successBox) {
                        successBox.hidden = false;
                    }
                    postTrack('wealth_lead_submit');
                })
                .catch(function(error) {
                    if (feedback) {
                        feedback.textContent = error.message || 'Não foi possível enviar seus dados.';
                    }
                })
                .finally(function() {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = submitButton.getAttribute('data-default-label') || 'Enviar';
                    }
                });
        });
    });
})();
</script>
