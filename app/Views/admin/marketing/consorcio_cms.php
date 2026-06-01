<?php
$testimonials = $consorcioConfig['testimonials']['items'] ?? [];

while (count($testimonials) < 3) {
    $testimonials[] = ['enabled' => 1, 'name' => '', 'city' => '', 'text' => '', 'photo_url' => ''];
}

$ogImage = $consorcioConfig['seo']['og_image'] ?? '';
?>
<style>
    .mk-note { margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6; }
    .mk-toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; }
    .mk-toolbar-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .mk-grid { display: grid; gap: 16px; }
    .mk-grid-2 { grid-template-columns: 1fr; }
    .mk-list-grid { display: grid; gap: 14px; }
    .mk-list-item { padding: 16px; border: 1px dashed #d2d9e1; background: #fafbfd; }
    .mk-list-item label { font-weight: 600; font-size: 13px; }
    .mk-image-preview { max-width: 120px; max-height: 120px; border-radius: 6px; border: 1px solid #e5e7eb; margin-top: 8px; display: block; }
    .mk-image-preview-lg { max-width: 240px; max-height: 140px; }
    .mk-remove-check { display: inline-flex; align-items: center; gap: 6px; margin-top: 6px; font-size: 12px; color: #dc2626; }
    @media (min-width: 992px) {
        .mk-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Simulador de Consórcio - CMS</h1>
            <p class="mk-note">Controla a imagem OG (compartilhamento social) e os depoimentos da página <strong>/simulador-consorcio</strong>.</p>
        </section>
    </div>
</div>

<form action="<?= adminUrl('marketing/consorcio-cms'); ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field(); ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="mk-toolbar">
                        <div>
                            <strong>Idioma atual:</strong> <?= esc($activeLang->name); ?>
                        </div>
                        <div class="mk-toolbar-actions">
                            <a href="<?= langBaseUrl('simulador-consorcio'); ?>" target="_blank" class="btn btn-default"><i class="fa fa-eye"></i> Ver página</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salvar alterações</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">SEO / Compartilhamento social (OG Image)</h3>
                </div>
                <div class="box-body">
                    <p class="mk-note">A imagem OG é exibida quando a página é compartilhada no Facebook, Instagram, WhatsApp, LinkedIn etc. Tamanho recomendado: <strong>1200x630px</strong> (JPG, PNG ou WEBP).</p>

                    <label style="margin-top:12px;">Imagem OG</label>
                    <input type="file" name="seo_og_image" accept="image/jpeg,image/png,image/webp">

                    <?php if (!empty($ogImage)): ?>
                        <img src="<?= base_url($ogImage); ?>" alt="OG Image" class="mk-image-preview mk-image-preview-lg">
                        <label class="mk-remove-check">
                            <input type="checkbox" name="consorcio[seo][og_image_remove]" value="1"> Remover imagem atual
                        </label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Depoimentos</h3>
                </div>
                <div class="box-body">
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="consorcio[testimonials][label]" value="<?= esc($consorcioConfig['testimonials']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="consorcio[testimonials][title]" value="<?= esc($consorcioConfig['testimonials']['title'] ?? ''); ?>">
                        </div>
                    </div>

                    <label style="margin-top:12px;">
                        <input type="checkbox" name="consorcio[testimonials][enabled]" value="1" <?= !empty($consorcioConfig['testimonials']['enabled']) ? 'checked' : ''; ?>>
                        Exibir seção de depoimentos na página
                    </label>

                    <hr>

                    <div class="mk-list-grid">
                        <?php foreach ($testimonials as $index => $item): ?>
                            <div class="mk-list-item">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                    <strong>Depoimento <?= $index + 1; ?></strong>
                                    <label style="font-weight:400; font-size:12px;">
                                        <input type="checkbox" name="consorcio[testimonials][items][<?= $index; ?>][enabled]" value="1" <?= !empty($item['enabled']) ? 'checked' : ''; ?>>
                                        Ativo
                                    </label>
                                </div>

                                <div class="mk-grid mk-grid-2">
                                    <div>
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="consorcio[testimonials][items][<?= $index; ?>][name]" value="<?= esc($item['name'] ?? ''); ?>" placeholder="Ex.: Maria S.">
                                    </div>
                                    <div>
                                        <label>Cidade</label>
                                        <input type="text" class="form-control" name="consorcio[testimonials][items][<?= $index; ?>][city]" value="<?= esc($item['city'] ?? ''); ?>" placeholder="Ex.: São Paulo">
                                    </div>
                                </div>

                                <label style="margin-top:10px;">Texto do depoimento</label>
                                <textarea class="form-control" name="consorcio[testimonials][items][<?= $index; ?>][text]" rows="3" placeholder="Depoimento do cliente..."><?= esc($item['text'] ?? ''); ?></textarea>

                                <label style="margin-top:10px;">Foto (opcional)</label>
                                <input type="file" name="testimonial_photo_<?= $index; ?>" accept="image/jpeg,image/png,image/webp">

                                <?php if (!empty($item['photo_url'])): ?>
                                    <img src="<?= base_url($item['photo_url']); ?>" alt="Foto" class="mk-image-preview">
                                    <label class="mk-remove-check">
                                        <input type="checkbox" name="consorcio[testimonials][items][<?= $index; ?>][photo_remove]" value="1"> Remover foto atual
                                    </label>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <p class="mk-note" style="margin-top:12px;">São exibidos sempre os 3 primeiros depoimentos ativos. Deixe os campos vazios para ocultar um slot.</p>
                </div>
            </div>
        </div>
    </div>
</form>
