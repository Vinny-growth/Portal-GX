<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <h1 class="page-title">Conversa com o Agente</h1>
            <div class="page-content">
                <div class="row">
                    <div class="col-md-8">
                <?php if (!empty($progress) && isset($progress['score'])): ?>
                    <div class="mb-3">
                        <div class="progress" style="height: 12px;">
                            <?php $pct = (int)round(($progress['score']/$progress['total'])*100); ?>
                            <div class="progress-bar" role="progressbar" style="width: <?= $pct; ?>%;" aria-valuenow="<?= $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Progresso: <?= (int)$progress['score']; ?>/<?= (int)$progress['total']; ?></small>
                    </div>
                <?php endif; ?>
                <?php if (!empty($noTokens) && $noTokens): ?>
                    <div class="alert alert-warning">
                        Você já usou sua sessão gratuita. Para continuar, agende uma reunião gratuita com um consultor.
                    </div>
                    <a class="btn btn-lg btn-custom" href="<?= base_url('wealth/agendar'); ?>">Agendar reunião gratuita com consultor</a>
                <?php else: ?>
                    <?php if (!empty($next_step)): ?>
                        <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                            <?php if ($next_step == 'consent'): ?>
                                <h4>Consentimento</h4>
                                <p>Autoriza o uso dos seus dados para planejamento financeiro e contato comercial?</p>
                                <button id="wm-accept-consent" class="btn btn-success">Aceito</button>
                            <?php elseif ($next_step == 'estado_civil' || $next_step == 'ano_nascimento'): ?>
                                <h4>Dados Básicos</h4>
                                <form id="wm-form-profile">
                                    <?= csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Estado civil</label>
                                            <?php $ec = $profile->estado_civil ?? ''; ?>
                                            <select name="estado_civil" class="form-control">
                                                <option value="">Selecione</option>
                                                <option value="solteiro" <?= $ec==='solteiro'?'selected':''; ?>>Solteiro(a)</option>
                                                <option value="casado" <?= $ec==='casado'?'selected':''; ?>>Casado(a)</option>
                                                <option value="divorciado" <?= $ec==='divorciado'?'selected':''; ?>>Divorciado(a)</option>
                                                <option value="viúvo" <?= ($ec==='viúvo' || $ec==='viuvo')?'selected':''; ?>>Viúvo(a)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Ano de nascimento</label>
                                            <input type="number" name="ano_nascimento" class="form-control" placeholder="1985" value="<?= esc($profile->ano_nascimento ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mt-2"><button class="btn btn-primary" id="wm-save-profile">Salvar</button></div>
                                </form>
                            <?php elseif ($next_step == 'income'): ?>
                                <h4>Rendas Mensais</h4>
                                <form id="wm-form-income">
                                    <?= csrf_field(); ?>
                                    <p>Selecione as fontes e informe os valores:</p>
                                    <?php $incomes = [
                                        'salario' => 'Salário',
                                        'pro_labore' => 'Pró‑labore',
                                        'dividendos' => 'Dividendos',
                                        'aluguéis' => 'Aluguéis',
                                        'pensoes' => 'Pensões',
                                        'outros' => 'Outros'
                                    ]; ?>
                                    <div class="row">
                                        <?php $ei = $existing_incomes ?? []; foreach ($incomes as $key => $label): $val = isset($ei[$key]) ? (float)$ei[$key] : null; ?>
                                            <div class="col-sm-4" style="margin-bottom:10px;">
                                                <label><input type="checkbox" name="income_items[]" value="<?= $key; ?>" <?= $val !== null ? 'checked' : ''; ?>> <?= esc($label); ?></label>
                                                <input type="number" step="0.01" min="0" name="income_val_<?= $key; ?>" class="form-control" placeholder="Valor mensal (R$)" value="<?= $val !== null ? esc(number_format($val, 2, '.', '')) : ''; ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="btn btn-primary" id="wm-save-income">Salvar rendas</button>
                                </form>
                            <?php elseif ($next_step == 'expenses'): ?>
                                <h4>Despesas Mensais</h4>
                                <form id="wm-form-expense">
                                    <?= csrf_field(); ?>
                                    <table class="table table-bordered" id="wm-expense-table">
                                        <thead><tr><th>Categoria</th><th>Valor (R$)</th><th></th></tr></thead>
                                        <tbody>
                                            <?php $ee = $existing_expenses ?? []; if (!empty($ee)): foreach ($ee as $ex): ?>
                                                <tr>
                                                    <td><input type="text" name="expense_cat[]" class="form-control" value="<?= esc($ex['categoria']); ?>" placeholder="Ex: moradia"></td>
                                                    <td><input type="number" step="0.01" min="0" name="expense_val[]" class="form-control" value="<?= esc(number_format($ex['valor'] ?? 0, 2, '.', '')); ?>" placeholder="0,00"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="expense_cat[]" class="form-control" placeholder="Ex: moradia"></td>
                                                    <td><input type="number" step="0.01" min="0" name="expense_val[]" class="form-control" placeholder="0,00"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-default" id="wm-expense-add">Adicionar linha</button>
                                    <button class="btn btn-primary" id="wm-save-expense">Salvar despesas</button>
                                </form>
                            <?php elseif ($next_step == 'assets_financial'): ?>
                                <h4>Alocação Financeira</h4>
                                <form id="wm-form-alloc">
                                    <?= csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Total patrimônio financeiro (R$)</label>
                                            <input type="number" step="0.01" min="0" name="total_financeiro" class="form-control" placeholder="Ex: 150000" value="<?= esc(number_format(($alloc_total_financeiro ?? 0), 2, '.', '')); ?>">
                                        </div>
                                    </div>
                                    <p class="mt-2">Informe % por classe (o total deve somar 100%):</p>
                                    <?php $classes = ['caixa','CDB','fundos','ações','previdência','ETFs','internacional']; ?>
                                    <div class="row">
                                        <?php $ap = $existing_alloc_pct ?? []; foreach ($classes as $c): $pct = isset($ap[$c]) ? (float)$ap[$c] : 0; ?>
                                            <div class="col-sm-3" style="margin-bottom:10px;">
                                                <label><?= ucfirst($c); ?> (%)</label>
                                                <input type="number" step="0.01" min="0" max="100" name="alloc_<?= $c; ?>" class="form-control" placeholder="0" value="<?= esc(number_format($pct, 2, '.', '')); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="btn btn-primary" id="wm-save-alloc">Salvar alocação</button>
                                </form>
                            <?php elseif ($next_step == 'realestate'): ?>
                                <h4>Imóveis</h4>
                                <form id="wm-form-realestate">
                                    <?= csrf_field(); ?>
                                    <table class="table table-bordered" id="wm-realestate-table">
                                        <thead><tr><th>Tipo</th><th>Valor Estimado (R$)</th><th>Aluguel (R$)</th><th>Dívida (R$)</th><th></th></tr></thead>
                                        <tbody>
                                            <?php $re = $existing_realestate ?? []; if (!empty($re)): foreach ($re as $it): ?>
                                                <tr>
                                                    <td><input type="text" name="re_tipo[]" class="form-control" value="<?= esc($it->tipo); ?>" placeholder="Ex: apartamento"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_valor[]" class="form-control" value="<?= esc(number_format((float)$it->valor_estimado, 2, '.', '')); ?>"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_aluguel[]" class="form-control" value="<?= esc(number_format((float)$it->renda_aluguel, 2, '.', '')); ?>"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_divida[]" class="form-control" value="<?= esc(number_format((float)$it->saldo_divida, 2, '.', '')); ?>"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="re_tipo[]" class="form-control" placeholder="Ex: apartamento"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_valor[]" class="form-control"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_aluguel[]" class="form-control"></td>
                                                    <td><input type="number" step="0.01" min="0" name="re_divida[]" class="form-control"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-default" id="wm-re-add">Adicionar imóvel</button>
                                    <button class="btn btn-primary" id="wm-save-re">Salvar imóveis</button>
                                </form>
                            <?php elseif ($next_step == 'dependentes'): ?>
                                <h4>Dependentes</h4>
                                <form id="wm-form-deps">
                                    <?= csrf_field(); ?>
                                    <label>Possui filhos/dependentes?</label>
                                    <?php $deps = $existing_dependentes ?? []; $hasDeps = !empty($deps); $depCount = $hasDeps ? count($deps) : 1; ?>
                                    <select name="has_children" class="form-control" id="wm-has-children">
                                        <option value="0" <?= !$hasDeps ? 'selected' : ''; ?>>Não</option>
                                        <option value="1" <?= $hasDeps ? 'selected' : ''; ?>>Sim</option>
                                    </select>
                                    <div id="wm-deps-area" style="display:<?= $hasDeps ? 'block' : 'none'; ?>; margin-top:10px;">
                                        <label>Quantos?</label>
                                        <input type="number" min="1" max="10" name="num_children" id="wm-num-children" class="form-control" value="<?= (int)$depCount; ?>">
                                        <div id="wm-deps-ages" class="row" style="margin-top:10px;">
                                            <?php if ($hasDeps): $i=0; foreach ($deps as $d): $i++; ?>
                                                <div class="col-sm-2">
                                                    <label>Idade <?= $i; ?></label>
                                                    <input type="number" min="0" max="120" name="child_age[]" class="form-control" value="<?= esc((int)($d['idade'] ?? 0)); ?>">
                                                </div>
                                            <?php endforeach; endif; ?>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" id="wm-save-deps" style="margin-top:10px;">Salvar dependentes</button>
                                </form>
                            <?php elseif ($next_step == 'liabilities'): ?>
                                <h4>Passivos</h4>
                                <form id="wm-form-liab">
                                    <?= csrf_field(); ?>
                                    <table class="table table-bordered" id="wm-liab-table">
                                        <thead><tr><th>Tipo</th><th>Saldo Atual (R$)</th><th>Taxa (%)</th><th>Prazo (meses)</th><th></th></tr></thead>
                                        <tbody>
                                            <?php $li = $existing_liabilities ?? []; if (!empty($li)): foreach ($li as $it): ?>
                                                <tr>
                                                    <td><input type="text" name="liab_tipo[]" class="form-control" value="<?= esc($it->tipo); ?>" placeholder="Ex: financiamento"></td>
                                                    <td><input type="number" step="0.01" min="0" name="liab_saldo[]" class="form-control" value="<?= esc(number_format((float)$it->saldo_atual, 2, '.', '')); ?>"></td>
                                                    <td><input type="number" step="0.01" min="0" name="liab_taxa[]" class="form-control" value="<?= esc(number_format((float)$it->taxa_aprox, 2, '.', '')); ?>"></td>
                                                    <td><input type="number" min="0" name="liab_prazo[]" class="form-control" value="<?= esc((int)$it->prazo_meses); ?>"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="liab_tipo[]" class="form-control" placeholder="Ex: financiamento"></td>
                                                    <td><input type="number" step="0.01" min="0" name="liab_saldo[]" class="form-control"></td>
                                                    <td><input type="number" step="0.01" min="0" name="liab_taxa[]" class="form-control"></td>
                                                    <td><input type="number" min="0" name="liab_prazo[]" class="form-control"></td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-default" id="wm-liab-add">Adicionar passivo</button>
                                    <button class="btn btn-primary" id="wm-save-liab">Salvar passivos</button>
                                </form>
                            <?php elseif ($next_step == 'goals'): ?>
                                <h4>Metas</h4>
                                <form id="wm-form-goals">
                                    <?= csrf_field(); ?>
                                    <table class="table table-bordered" id="wm-goals-table">
                                        <thead><tr><th>Meta</th><th>Valor Objetivo (R$)</th><th>Prazo (meses)</th><th>Prioridade</th><th></th></tr></thead>
                                        <tbody>
                                            <?php $go = $existing_goals ?? []; if (!empty($go)): foreach ($go as $g): ?>
                                                <tr>
                                                    <td><input type="text" name="goal_nome[]" class="form-control" value="<?= esc($g->nome_meta); ?>" placeholder="Ex: aposentadoria"></td>
                                                    <td><input type="number" step="0.01" min="0" name="goal_valor[]" class="form-control" value="<?= esc(number_format((float)$g->valor_objetivo, 2, '.', '')); ?>"></td>
                                                    <td><input type="number" min="0" name="goal_prazo[]" class="form-control" value="<?= esc((int)$g->prazo_meses); ?>"></td>
                                                    <td>
                                                        <?php $p = (string)($g->prioridade ?? 'media'); ?>
                                                        <select name="goal_prio[]" class="form-control">
                                                            <option value="media" <?= $p==='media'?'selected':''; ?>>Média</option>
                                                            <option value="alta" <?= $p==='alta'?'selected':''; ?>>Alta</option>
                                                            <option value="baixa" <?= $p==='baixa'?'selected':''; ?>>Baixa</option>
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="goal_nome[]" class="form-control" placeholder="Ex: aposentadoria"></td>
                                                    <td><input type="number" step="0.01" min="0" name="goal_valor[]" class="form-control"></td>
                                                    <td><input type="number" min="0" name="goal_prazo[]" class="form-control"></td>
                                                    <td>
                                                        <select name="goal_prio[]" class="form-control">
                                                            <option value="media">Média</option>
                                                            <option value="alta">Alta</option>
                                                            <option value="baixa">Baixa</option>
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-default" id="wm-goal-add">Adicionar meta</button>
                                    <button class="btn btn-primary" id="wm-save-goals">Salvar metas</button>
                                </form>
                            <?php elseif ($next_step == 'risk'): ?>
                                <h4>Perfil de Risco e Horizonte</h4>
                                <form id="wm-form-risk">
                                    <?= csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Perfil de risco</label>
                                            <?php $pr = $profile->perfil_risco ?? ''; ?>
                                            <select name="perfil_risco" class="form-control">
                                                <option value="">Selecione</option>
                                                <option value="conservador" <?= $pr==='conservador'?'selected':''; ?>>Conservador</option>
                                                <option value="moderado" <?= $pr==='moderado'?'selected':''; ?>>Moderado</option>
                                                <option value="arrojado" <?= $pr==='arrojado'?'selected':''; ?>>Arrojado</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Horizonte</label>
                                            <?php $hz = $profile->horizonte ?? ''; ?>
                                            <select name="horizonte" class="form-control">
                                                <option value="">Selecione</option>
                                                <option value="curto" <?= $hz==='curto'?'selected':''; ?>>Curto</option>
                                                <option value="médio" <?= ($hz==='médio'||$hz==='medio')?'selected':''; ?>>Médio</option>
                                                <option value="longo" <?= $hz==='longo'?'selected':''; ?>>Longo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" id="wm-save-risk" style="margin-top:10px;">Salvar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div id="wm-chat" class="mb-3" style="border:1px solid #eee; border-radius:6px; padding:15px; max-height:420px; overflow:auto;" aria-live="polite">
                        <?php if (!empty($messages)): foreach ($messages as $m): ?>
                            <div class="mb-2">
                                <strong><?= $m->role == 'user' ? 'Você' : 'Agente'; ?>:</strong>
                                <div><?= esc($m->content); ?></div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <form id="wm-chat-form" method="post" onsubmit="return false;">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="session_id" value="<?= esc($session->id ?? ''); ?>">
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Digite sua resposta..." autocomplete="off" aria-label="Mensagem">
                            <button class="btn btn-custom" id="wm-send" type="button">Enviar</button>
                        </div>
                    </form>
                    <div class="mt-3 text-end">
                        <a class="btn btn-lg btn-custom" href="<?= base_url('wealth/resultado'); ?>">Ver Resultado</a>
                    </div>
                <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default" style="border-radius:6px; padding:15px;">
                            <h4>Etapas</h4>
                            <ul class="list-unstyled">
                                <?php if (!empty($steps)): foreach ($steps as $st): ?>
                                    <li>
                                        <?php if ($st['status'] == 'done'): ?>
                                            <span style="color: #28a745;">&#10003;</span>
                                        <?php elseif ($st['status'] == 'current'): ?>
                                            <span style="color: #007bff;">&#9679;</span>
                                        <?php else: ?>
                                            <span style="color: #ccc;">&#9633;</span>
                                        <?php endif; ?>
                                        <?= esc($st['label']); ?>
                                    </li>
                                <?php endforeach; endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    (function(){
        var form = document.getElementById('wm-chat-form');
        if (!form) return;
        var input = form.querySelector('input[name="message"]');
        var chat = document.getElementById('wm-chat');
        var sendBtn = document.getElementById('wm-send');
        var sending = false;
        // Auto-scroll chat to latest on load
        if (chat) { chat.scrollTop = chat.scrollHeight; }
        function send(){
            var msg = (input.value || '').trim();
            if (!msg || sending) return;
            sending = true; sendBtn.setAttribute('disabled','disabled');
            var fd = new FormData(form);
            fetch('<?= base_url('WealthManager/sendMessage'); ?>', {method: 'POST', body: fd})
                .then(function(r){ return r.ok ? r.json() : Promise.reject(); })
                .then(function(res){
                    if (!res || !res.success) throw new Error('send_failed');
                    chat.innerHTML += '<div class="mb-2"><strong>Você:</strong><div>'+escapeHtml(msg)+'</div></div>';
                    chat.innerHTML += '<div class="mb-2"><strong>Agente:</strong><div>'+escapeHtml(res.reply)+'</div></div>';
                    try { var txt = (res.reply||'').toLowerCase(); if (txt.indexOf('sessão finalizada') !== -1 || txt.indexOf('sessao finalizada') !== -1) { chat.innerHTML += '<div class="mb-3"><a class="btn btn-lg btn-custom" href="<?= base_url('wealth/resultado'); ?>">Ver Resultado</a></div>'; } } catch(e){}
                    input.value='';
                    chat.scrollTop = chat.scrollHeight;
                })
                .catch(function(){ alert('Não foi possível enviar a mensagem. Tente novamente.'); })
                .finally(function(){ sending = false; sendBtn.removeAttribute('disabled'); input.focus(); });
        }
        sendBtn.addEventListener('click', send);
        input.addEventListener('keydown', function(e){ if (e.key === 'Enter') { e.preventDefault(); send(); } });
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    })();
</script>
<script>
    (function(){
        // Consent
        var btnConsent = document.getElementById('wm-accept-consent');
        if (btnConsent) btnConsent.addEventListener('click', function(){
            var fd = new FormData(); fd.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>');
            fetch('<?= base_url('WealthManager/acceptConsent'); ?>', {method:'POST', body:fd}).then(()=>location.reload());
        });
        // Profile basic
        var btnProf = document.getElementById('wm-save-profile');
        if (btnProf) btnProf.addEventListener('click', function(){
            var f = document.getElementById('wm-form-profile'); var fd = new FormData(f);
            fetch('<?= base_url('WealthManager/saveProfileBasic'); ?>', {method:'POST', body:fd}).then(()=>location.reload());
        });
        // Income
        var btnInc = document.getElementById('wm-save-income');
        if (btnInc) btnInc.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-income'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveIncomeForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Expense add/remove
        var addBtn = document.getElementById('wm-expense-add');
        if (addBtn) addBtn.addEventListener('click', function(){
            var tbody = document.querySelector('#wm-expense-table tbody');
            var tr = document.createElement('tr');
            tr.innerHTML = '<td><input type="text" name="expense_cat[]" class="form-control" placeholder="Ex: alimentação"></td>'+
                           '<td><input type="number" step="0.01" min="0" name="expense_val[]" class="form-control" placeholder="0,00"></td>'+
                           '<td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>';
            tbody.appendChild(tr);
        });
        document.addEventListener('click', function(e){ if (e.target && e.target.classList.contains('wm-row-del')) { e.target.closest('tr').remove(); }});
        var btnExp = document.getElementById('wm-save-expense');
        if (btnExp) btnExp.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-expense'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveExpenseForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Dependents dynamic
        var hasSel = document.getElementById('wm-has-children');
        var depArea = document.getElementById('wm-deps-area');
        var numInput = document.getElementById('wm-num-children');
        var agesWrap = document.getElementById('wm-deps-ages');
        function renderAges(){ if (!agesWrap || !numInput) return; var existing = agesWrap.querySelectorAll('input[name="child_age[]"]'); var existingVals = []; existing.forEach(function(el){ existingVals.push(el.value); }); agesWrap.innerHTML=''; var n = parseInt(numInput.value||'0'); for (var i=0;i<n;i++){ var col=document.createElement('div'); col.className='col-sm-2'; var val = typeof existingVals[i] !== 'undefined' ? existingVals[i] : ''; col.innerHTML='<label>Idade '+(i+1)+'</label><input type="number" min="0" max="120" name="child_age[]" class="form-control" value="'+val+'">'; agesWrap.appendChild(col);} }
        if (hasSel) { hasSel.addEventListener('change', function(){ depArea.style.display = (this.value==='1') ? 'block':'none'; }); }
        if (numInput) { numInput.addEventListener('change', renderAges); renderAges(); }
        var btnDeps = document.getElementById('wm-save-deps');
        if (btnDeps) btnDeps.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-deps'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveDependentsForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Allocation
        var btnAlloc = document.getElementById('wm-save-alloc');
        if (btnAlloc) btnAlloc.addEventListener('click', function(e){
            e.preventDefault();
            var f=document.getElementById('wm-form-alloc');
            // validate sum ~ 100%
            var fields=['caixa','CDB','fundos','ações','previdência','ETFs','internacional'];
            var sum=0; fields.forEach(function(k){ var el=f.querySelector('[name="alloc_'+k+'"]'); sum += parseFloat(el && el.value ? el.value : '0'); });
            if (sum > 0 && (sum < 99.5 || sum > 100.5)) { if (!confirm('Os percentuais somam '+sum.toFixed(2)+'%. Deseja continuar mesmo assim?')) return; }
            var fd=new FormData(f);
            fetch('<?= base_url('WealthManager/saveAllocationForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload());
        });
        // Risk
        var btnRisk = document.getElementById('wm-save-risk');
        if (btnRisk) btnRisk.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-risk'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveProfileBasic'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Real estate
        var reAdd = document.getElementById('wm-re-add'); if (reAdd) reAdd.addEventListener('click', function(){ var tbody=document.querySelector('#wm-realestate-table tbody'); var tr=document.createElement('tr'); tr.innerHTML='<td><input type="text" name="re_tipo[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="re_valor[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="re_aluguel[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="re_divida[]" class="form-control"></td><td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>'; tbody.appendChild(tr); });
        var reSave = document.getElementById('wm-save-re'); if (reSave) reSave.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-realestate'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveRealEstateForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Liabilities
        var liAdd = document.getElementById('wm-liab-add'); if (liAdd) liAdd.addEventListener('click', function(){ var tbody=document.querySelector('#wm-liab-table tbody'); var tr=document.createElement('tr'); tr.innerHTML='<td><input type="text" name="liab_tipo[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="liab_saldo[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="liab_taxa[]" class="form-control"></td><td><input type="number" min="0" name="liab_prazo[]" class="form-control"></td><td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>'; tbody.appendChild(tr); });
        var liSave = document.getElementById('wm-save-liab'); if (liSave) liSave.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-liab'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveLiabilitiesForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
        // Goals
        var goAdd = document.getElementById('wm-goal-add'); if (goAdd) goAdd.addEventListener('click', function(){ var tbody=document.querySelector('#wm-goals-table tbody'); var tr=document.createElement('tr'); tr.innerHTML='<td><input type="text" name="goal_nome[]" class="form-control"></td><td><input type="number" step="0.01" min="0" name="goal_valor[]" class="form-control"></td><td><input type="number" min="0" name="goal_prazo[]" class="form-control"></td><td><select name="goal_prio[]" class="form-control"><option value="media">Média</option><option value="alta">Alta</option><option value="baixa">Baixa</option></select></td><td><button type="button" class="btn btn-default btn-sm wm-row-del">x</button></td>'; tbody.appendChild(tr); });
        var goSave = document.getElementById('wm-save-goals'); if (goSave) goSave.addEventListener('click', function(e){ e.preventDefault(); var f=document.getElementById('wm-form-goals'); var fd=new FormData(f); fetch('<?= base_url('WealthManager/saveGoalsForm'); ?>',{method:'POST',body:fd}).then(()=>location.reload()); });
    })();
</script>
