<?php $l = $lesson ?? []; $isEdit = !empty($lesson); ?>
<form class="gxc-inline-form" method="post" action="<?= adminUrl('cursos/aula/salvar'); ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="course_id" value="<?= (int) $courseId; ?>">
    <input type="hidden" name="section_id" value="<?= (int) $sectionId; ?>">
    <?php if ($isEdit): ?><input type="hidden" name="lesson_id" value="<?= (int) $l['id']; ?>"><?php endif; ?>
    <div class="gxc-grid">
        <div class="gxc-field gxc-field--full"><label class="gxc-label">Título da aula</label><input class="gxc-input" name="title" value="<?= esc($l['title'] ?? '', 'attr'); ?>" required></div>
        <div class="gxc-field"><label class="gxc-label">Tipo</label>
            <select class="gxc-select" name="content_type">
                <?php $ct = $l['content_type'] ?? 'video'; foreach (['video' => 'Vídeo', 'text' => 'Texto'] as $k => $lbl): ?>
                    <option value="<?= $k; ?>" <?= $ct === $k ? 'selected' : ''; ?>><?= $lbl; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="gxc-field"><label class="gxc-label">Provedor de vídeo</label>
            <select class="gxc-select" name="video_provider">
                <?php $vp = $l['video_provider'] ?? 'youtube'; foreach (['youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'bunny' => 'Bunny', 'mp4' => 'MP4 direto'] as $k => $lbl): ?>
                    <option value="<?= $k; ?>" <?= $vp === $k ? 'selected' : ''; ?>><?= $lbl; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="gxc-field gxc-field--full"><label class="gxc-label">URL do vídeo (embed)</label><input class="gxc-input" name="video_url" value="<?= esc($l['video_url'] ?? '', 'attr'); ?>" placeholder="https://www.youtube.com/embed/..."></div>
        <div class="gxc-field gxc-field--full"><label class="gxc-label">Conteúdo / descrição (HTML)</label><textarea class="gxc-textarea" name="body_html"><?= esc($l['body_html'] ?? ''); ?></textarea></div>
        <div class="gxc-field"><label class="gxc-label">Duração (segundos)</label><input class="gxc-input" type="number" name="duration_seconds" value="<?= (int) ($l['duration_seconds'] ?? 0); ?>"></div>
        <div class="gxc-field"><label class="gxc-label">XP da aula</label><input class="gxc-input" type="number" name="xp_reward" value="<?= (int) ($l['xp_reward'] ?? 10); ?>"></div>
        <div class="gxc-field"><label class="gxc-label">Nível exigido (override)</label>
            <select class="gxc-select" name="access_level_id">
                <option value="">Herda do curso</option>
                <?php foreach ($levels as $L): ?>
                    <option value="<?= (int) $L['id']; ?>" <?= (int) ($l['access_level_id'] ?? 0) === (int) $L['id'] ? 'selected' : ''; ?>><?= esc($L['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="gxc-field"><label class="gxc-label">Ordem</label><input class="gxc-input" type="number" name="lesson_order" value="<?= (int) ($l['lesson_order'] ?? 0); ?>"></div>
        <div class="gxc-field gxc-field--full"><label class="gxc-check"><input type="checkbox" name="is_free_preview" value="1" <?= !empty($l['is_free_preview']) ? 'checked' : ''; ?>> Aula-amostra (grátis, sem exigir acesso)</label></div>
    </div>
    <div style="margin-top:10px;"><button class="gxc-btn gxc-btn--sm" type="submit"><?= $isEdit ? 'Salvar aula' : 'Adicionar aula'; ?></button></div>
</form>
