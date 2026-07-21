<?php
$isEdit = !empty($course);
$v = fn($k, $d = '') => esc($course[$k] ?? $d, 'attr');
$action = $isEdit ? adminUrl('cursos/' . $course['id'] . '/editar') : adminUrl('cursos/salvar');
?>
<?= view('admin/courses/_styles'); ?>
<div class="gxc-wrap">
    <?php if (session()->getFlashdata('success')): ?><div class="gxc-flash"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="gxc-flash gxc-flash--err"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="gxc-head">
        <div>
            <div class="gxc-eyebrow">Módulo · Cursos</div>
            <h1 class="gxc-title"><?= $isEdit ? 'Editar curso' : 'Novo curso'; ?></h1>
        </div>
        <div class="gxc-actions">
            <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos'); ?>">← Voltar</a>
            <?php if ($isEdit): ?><a class="gxc-btn gxc-btn--gold" target="_blank" href="<?= site_url('curso/' . $course['slug']); ?>">Ver no site ↗</a><?php endif; ?>
        </div>
    </div>

    <!-- Dados do curso -->
    <form class="gxc-card" method="post" action="<?= $action; ?>">
        <?= csrf_field(); ?>
        <div class="gxc-card__eyebrow">Dados do curso</div>
        <div class="gxc-grid">
            <div class="gxc-field gxc-field--full"><label class="gxc-label">Título</label><input class="gxc-input" name="title" value="<?= $v('title'); ?>" required></div>
            <div class="gxc-field"><label class="gxc-label">Slug (URL)</label><input class="gxc-input" name="slug" value="<?= $v('slug'); ?>" placeholder="gerado do título"></div>
            <div class="gxc-field"><label class="gxc-label">Categoria (carrossel)</label><input class="gxc-input" name="category" value="<?= $v('category'); ?>" placeholder="Ex.: Trilhas em destaque"></div>
            <div class="gxc-field gxc-field--full"><label class="gxc-label">Subtítulo</label><input class="gxc-input" name="subtitle" value="<?= $v('subtitle'); ?>"></div>
            <div class="gxc-field gxc-field--full"><label class="gxc-label">Descrição</label><textarea class="gxc-textarea" name="description"><?= esc($course['description'] ?? ''); ?></textarea></div>
            <div class="gxc-field"><label class="gxc-label">Nível</label>
                <select class="gxc-select" name="level">
                    <?php $lvl = $course['level'] ?? ''; foreach (['' => '—', 'iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'] as $k => $lbl): ?>
                        <option value="<?= $k; ?>" <?= $lvl === $k ? 'selected' : ''; ?>><?= $lbl; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="gxc-field"><label class="gxc-label">Instrutor</label><input class="gxc-input" name="instructor" value="<?= $v('instructor'); ?>"></div>
            <div class="gxc-field gxc-field--full"><label class="gxc-label">Capa (URL da imagem)</label>
                <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap">
                    <input class="gxc-input" name="cover_image" value="<?= $v('cover_image'); ?>" style="flex:1;min-width:240px">
                    <button type="button" class="gxc-btn gxc-btn--gold gxc-btn--sm gxc-genimg" data-type="course" data-id="<?= $isEdit ? (int) $course['id'] : ''; ?>">🎨 Gerar com IA</button>
                </div>
                <img class="gxc-genimg-preview" src="<?= $v('cover_image'); ?>" alt="" style="margin-top:8px;max-width:280px;border:1px solid var(--gx-border);<?= empty($course['cover_image']) ? 'display:none' : ''; ?>">
                <span class="gxc-muted" style="display:block;margin-top:4px">Gera uma capa com IA seguindo o design system da marca (paisagem 3:2, sem texto). ~20-40s.</span>
            </div>
            <div class="gxc-field"><label class="gxc-label">Trailer (URL embed)</label><input class="gxc-input" name="trailer_url" value="<?= $v('trailer_url'); ?>"></div>
            <div class="gxc-field"><label class="gxc-label">Nível de acesso exigido</label>
                <select class="gxc-select" name="access_level_id">
                    <option value="">Livre (público)</option>
                    <?php foreach ($levels as $L): ?>
                        <option value="<?= (int) $L['id']; ?>" <?= (int) ($course['access_level_id'] ?? 0) === (int) $L['id'] ? 'selected' : ''; ?>><?= esc($L['name']); ?> (rank <?= (int) $L['rank']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="gxc-field"><label class="gxc-label">XP ao concluir o curso</label><input class="gxc-input" type="number" name="xp_reward" value="<?= (int) ($course['xp_reward'] ?? 100); ?>"></div>
            <div class="gxc-field"><label class="gxc-label">Duração estimada (min)</label><input class="gxc-input" type="number" name="estimated_minutes" value="<?= (int) ($course['estimated_minutes'] ?? 0); ?>"></div>
            <div class="gxc-field"><label class="gxc-label">Ordem no catálogo</label><input class="gxc-input" type="number" name="course_order" value="<?= (int) ($course['course_order'] ?? 0); ?>"></div>
            <div class="gxc-field gxc-field--full">
                <div style="display:flex;gap:var(--space-6);flex-wrap:wrap;">
                    <label class="gxc-check"><input type="checkbox" name="is_published" value="1" <?= !empty($course['is_published']) ? 'checked' : ''; ?>> Publicado</label>
                    <label class="gxc-check"><input type="checkbox" name="is_featured" value="1" <?= !empty($course['is_featured']) ? 'checked' : ''; ?>> Destaque (hero)</label>
                    <label class="gxc-check"><input type="checkbox" name="drip_enabled" value="1" <?= !empty($course['drip_enabled']) ? 'checked' : ''; ?>> Desbloqueio sequencial (drip)</label>
                </div>
            </div>
        </div>
        <div style="margin-top:var(--space-5);"><button class="gxc-btn" type="submit"><?= $isEdit ? 'Salvar alterações' : 'Criar curso'; ?></button></div>
    </form>

    <?php if ($isEdit): ?>
    <!-- Builder de seções + aulas -->
    <div class="gxc-card">
        <div class="gxc-card__eyebrow">Conteúdo · Seções & aulas</div>

        <?php foreach ($sections as $s): ?>
            <div class="gxc-section">
                <div class="gxc-section__head">
                    <strong><?= esc($s['title']); ?></strong>
                    <div class="gxc-actions">
                        <span class="gxc-num" style="color:var(--gx-secondary-light);"><?= count($lessonsBySection[(int) $s['id']] ?? []); ?> aulas</span>
                        <form method="post" action="<?= adminUrl('cursos/secao/' . $s['id'] . '/excluir'); ?>" onsubmit="return confirm('Remover a seção e suas aulas?');"><?= csrf_field(); ?>
                            <button class="gxc-btn gxc-btn--danger gxc-btn--sm" type="submit">Remover seção</button>
                        </form>
                    </div>
                </div>

                <?php foreach (($lessonsBySection[(int) $s['id']] ?? []) as $l): ?>
                    <div class="gxc-lesson">
                        <div>
                            <strong><?= esc($l['title']); ?></strong>
                            <span class="gxc-muted">· <?= esc($l['content_type']); ?> · <span class="gxc-num"><?= (int) $l['xp_reward']; ?></span> XP<?= !empty($l['is_free_preview']) ? ' · <span class="gxc-badge gxc-badge--gold">amostra</span>' : ''; ?></span>
                        </div>
                        <div class="gxc-actions">
                            <details class="gxc-det">
                                <summary>Editar</summary>
                                <?= view('admin/courses/_lesson_form', ['courseId' => $course['id'], 'sectionId' => $s['id'], 'lesson' => $l, 'levels' => $levels]); ?>
                            </details>
                            <form method="post" action="<?= adminUrl('cursos/aula/' . $l['id'] . '/excluir'); ?>" onsubmit="return confirm('Remover aula?');"><?= csrf_field(); ?>
                                <button class="gxc-btn gxc-btn--danger gxc-btn--sm" type="submit">×</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <details class="gxc-det"><summary>+ Adicionar aula nesta seção</summary>
                    <?= view('admin/courses/_lesson_form', ['courseId' => $course['id'], 'sectionId' => $s['id'], 'lesson' => null, 'levels' => $levels]); ?>
                </details>

                <details class="gxc-det"><summary>Renomear seção</summary>
                    <form class="gxc-inline-form" method="post" action="<?= adminUrl('cursos/secao/salvar'); ?>"><?= csrf_field(); ?>
                        <input type="hidden" name="course_id" value="<?= (int) $course['id']; ?>">
                        <input type="hidden" name="section_id" value="<?= (int) $s['id']; ?>">
                        <div class="gxc-grid">
                            <div class="gxc-field"><label class="gxc-label">Título</label><input class="gxc-input" name="title" value="<?= esc($s['title'], 'attr'); ?>"></div>
                            <div class="gxc-field"><label class="gxc-label">Ordem</label><input class="gxc-input" type="number" name="section_order" value="<?= (int) $s['section_order']; ?>"></div>
                            <div class="gxc-field gxc-field--full"><label class="gxc-label">Descrição</label><input class="gxc-input" name="description" value="<?= esc($s['description'] ?? '', 'attr'); ?>"></div>
                        </div>
                        <div style="margin-top:10px;"><button class="gxc-btn gxc-btn--sm" type="submit">Salvar seção</button></div>
                    </form>
                </details>
            </div>
        <?php endforeach; ?>

        <!-- nova seção -->
        <details class="gxc-det" style="border:1px dashed var(--gx-border);">
            <summary>+ Adicionar nova seção</summary>
            <form class="gxc-inline-form" method="post" action="<?= adminUrl('cursos/secao/salvar'); ?>"><?= csrf_field(); ?>
                <input type="hidden" name="course_id" value="<?= (int) $course['id']; ?>">
                <div class="gxc-grid">
                    <div class="gxc-field"><label class="gxc-label">Título da seção</label><input class="gxc-input" name="title" placeholder="Ex.: Módulo 1 — Fundamentos"></div>
                    <div class="gxc-field"><label class="gxc-label">Ordem</label><input class="gxc-input" type="number" name="section_order" value="<?= count($sections) + 1; ?>"></div>
                </div>
                <div style="margin-top:10px;"><button class="gxc-btn gxc-btn--sm" type="submit">Adicionar seção</button></div>
            </form>
        </details>
    </div>
    <?php endif; ?>
</div>

<?= view('admin/courses/_genimg_js'); ?>
