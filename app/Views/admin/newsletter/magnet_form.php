<?php
$magnet = $magnet ?? null;
$isEdit = !empty($magnet);
?>
<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= $isEdit ? 'Editar' : 'Novo'; ?> Lead Magnet</h3>
        <a href="<?= adminUrl('newsletter/magnets'); ?>" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form action="<?= adminUrl('newsletter/magnets/save'); ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $isEdit ? (int) $magnet->id : ''; ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="title" class="form-control" required value="<?= esc($isEdit ? $magnet->title : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= esc($isEdit ? $magnet->slug : ''); ?>" placeholder="(gerado automaticamente a partir do título)">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="description" class="form-control" rows="3"><?= esc($isEdit ? $magnet->description : ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Texto do botão de download</label>
                        <input type="text" name="cta_text" class="form-control" value="<?= esc($isEdit && $magnet->cta_text ? $magnet->cta_text : 'Baixar material'); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Arquivo + Cover</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Arquivo (PDF)</label>
                        <?php if ($isEdit && !empty($magnet->file_path)): ?>
                            <p><i class="fa fa-file-pdf-o"></i> <?= esc(basename($magnet->file_path)); ?>
                                <?php if (!empty($magnet->file_size)): ?>
                                    <small>(<?= number_format($magnet->file_size / 1024, 0); ?> KB)</small>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <input type="file" name="file" accept=".pdf,.epub,application/pdf">
                        <small class="text-muted">Apenas PDF ou EPUB.</small>
                    </div>
                    <div class="form-group">
                        <label>Cover (imagem)</label>
                        <?php if ($isEdit && !empty($magnet->cover_image)): ?>
                            <div style="margin-bottom:8px;">
                                <img src="<?= esc($magnet->cover_image); ?>" style="max-width:100%;border:1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="cover_image" accept="image/*">
                        <small class="text-muted">JPG/PNG/WEBP. Recomendado 600x800.</small>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="active" value="1" <?= (!$isEdit || (int) $magnet->active === 1) ? 'checked' : ''; ?>>
                            Ativo
                        </label>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </div>
        </div>
    </div>
</form>
