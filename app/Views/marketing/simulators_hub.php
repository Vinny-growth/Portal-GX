<?php $phoneHref = !empty($baseSettings->contact_phone) ? preg_replace('/[^0-9+]/', '', (string)$baseSettings->contact_phone) : ''; ?>
<main class="gx-marketing gx-simulators-hub">
    <div class="gx-shell">
        <section class="gx-hero">
            <div class="container-xl">
                <div class="gx-hero-grid">
                    <div class="gx-hero-copy">
                        <p class="gx-eyebrow">Hub de simuladores</p>
                        <h1 class="gx-hero-title">Catalogo central das ferramentas da GX Capital.</h1>
                        <p class="gx-hero-text">
                            Esta pagina organiza o acesso aos simuladores publicos sem mudar nenhuma rota antiga. O hub funciona como camada
                            de descoberta, nao como substituicao das URLs ja distribuidas pelo portal.
                        </p>
                        <div class="gx-actions">
                            <a href="<?= esc($homeUrl); ?>" class="gx-btn gx-btn-primary">Voltar para a home</a>
                            <a href="<?= esc($blogUrl); ?>" class="gx-btn gx-btn-secondary">Ir para o blog</a>
                        </div>
                        <nav class="gx-chip-list" aria-label="Acesso rapido do hub">
                            <a href="#catalogo" class="gx-chip">Catalogo</a>
                            <a href="#regra-de-rota" class="gx-chip">Regra de roteamento</a>
                            <a href="#fale-especialista" class="gx-chip">Falar com especialista</a>
                        </nav>
                    </div>

                    <aside class="gx-hero-panel">
                        <div class="gx-note-card" id="regra-de-rota">
                            <p class="gx-eyebrow">Regra inegociavel</p>
                            <p class="gx-card-text">
                                As URLs abaixo continuam identicas. O hub apenas referencia essas rotas publicas para preservar SEO, links
                                internos, backlinks e qualquer citacao ja publicada no blog.
                            </p>
                        </div>
                        <div class="gx-stat-grid">
                            <div class="gx-stat-card">
                                <span class="gx-stat-value"><?= esc((string)count($simulators)); ?></span>
                                <span class="gx-stat-label">rotas catalogadas</span>
                            </div>
                            <div class="gx-stat-card">
                                <span class="gx-stat-value">301</span>
                                <span class="gx-stat-label">apenas para legados opcionais</span>
                            </div>
                            <div class="gx-stat-card">
                                <span class="gx-stat-value">SEO</span>
                                <span class="gx-stat-label">canonica e sitemap preservados</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="gx-section" id="catalogo" aria-labelledby="gx-catalogo-title">
            <div class="container-xl">
                <div class="gx-section-head">
                    <p class="gx-eyebrow">Catalogo visual</p>
                    <h2 id="gx-catalogo-title" class="gx-section-title">Cada card aponta para a mesma URL publica que ja existe hoje.</h2>
                    <p class="gx-section-text">
                        Isso permite reorganizar a descoberta das ferramentas sem risco de quebrar links embutidos em artigos, menus, CTAs ou
                        compartilhamentos externos.
                    </p>
                </div>

                <?= view('marketing/_simulator_grid', ['simulators' => $simulators, 'showPath' => true]); ?>
            </div>
        </section>

        <section class="gx-section" aria-labelledby="gx-processo-title">
            <div class="container-xl">
                <div class="gx-cta-band">
                    <div>
                        <p class="gx-eyebrow">Fluxo recomendado</p>
                        <h2 id="gx-processo-title" class="gx-section-title">Escolha, simule, valide com o time e avance para execucao.</h2>
                        <p class="gx-section-text">
                            O hub foi desenhado para reduzir atrito na descoberta. Quando a simulacao nao fecha a decisao sozinha, o proximo
                            passo ja fica claro com o bloco de atendimento consultivo.
                        </p>
                    </div>
                    <div class="gx-inline-links">
                        <a href="<?= esc($homeUrl); ?>">Voltar para a nova home</a>
                        <a href="<?= esc($blogUrl); ?>">Ler conteudo tecnico antes</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="gx-section gx-lead-section" id="fale-especialista" aria-labelledby="gx-hub-lead-title">
            <div class="container-xl">
                <div class="gx-lead-grid">
                    <aside class="gx-lead-aside">
                        <p class="gx-eyebrow">Apoio consultivo</p>
                        <h2 id="gx-hub-lead-title" class="gx-section-title">Precisa interpretar o resultado do simulador?</h2>
                        <p class="gx-section-text">
                            Envie o contexto da operacao e direcionamos a conversa para a vertical correta, sem pedir que o usuario abandone
                            a rota ou recomece a jornada em outra pagina.
                        </p>
                        <div class="gx-contact-list">
                            <?php if (!empty($baseSettings->contact_phone)): ?>
                                <a href="<?= !empty($phoneHref) ? 'tel:' . esc($phoneHref) : '#'; ?>" class="gx-contact-chip"><?= esc($baseSettings->contact_phone); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($baseSettings->contact_email)): ?>
                                <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="gx-contact-chip"><?= esc($baseSettings->contact_email); ?></a>
                            <?php endif; ?>
                            <a href="<?= esc($blogUrl); ?>" class="gx-contact-chip">Abrir /blog</a>
                        </div>
                    </aside>

                    <div class="gx-lead-card">
                        <?= view('marketing/_specialist_form', [
                            'formId' => 'gx-hub-specialist-form',
                            'heading' => 'Leve a simulacao para uma conversa de structuring',
                            'description' => 'Descreva o instrumento, prazo, risco ou objetivo. O retorno parte do tipo de estrutura mais aderente.',
                            'buttonLabel' => 'Receber retorno consultivo',
                            'messagePlaceholder' => 'Ex.: comparei FIDC x desconto bancario, rodei risco cambial ou custo de capital e preciso validar a melhor estrutura.'
                        ]); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
