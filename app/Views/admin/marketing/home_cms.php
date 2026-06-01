<?php
$checked = static function ($value) {
    return !empty($value) ? 'checked' : '';
};

$navQuickLinks = $homeConfig['nav']['quick_links'] ?? [];
$heroProofItems = $homeConfig['hero']['proof_items'] ?? [];
$trustItems = $homeConfig['trust_strip']['items'] ?? [];
$verticalItems = $homeConfig['verticals']['items'] ?? [];
$processItems = $homeConfig['process']['items'] ?? [];
$clippingItems = $homeConfig['clippings']['items'] ?? [];
$partnerItems = $homeConfig['partners']['items'] ?? [];

if (empty($navQuickLinks)) {
    $navQuickLinks = [['enabled' => 1, 'label' => '', 'href' => '']];
}
if (empty($heroProofItems)) {
    $heroProofItems = [['enabled' => 1, 'title' => '', 'text' => '']];
}
if (empty($trustItems)) {
    $trustItems = [['enabled' => 1, 'label' => '']];
}
if (empty($verticalItems)) {
    $verticalItems = [['enabled' => 1, 'eyebrow' => '', 'title' => '', 'description' => '', 'link_label' => '', 'link_url' => '', 'accent' => '#C7A053']];
}
if (empty($processItems)) {
    $processItems = [['enabled' => 1, 'title' => '', 'desc' => '']];
}
if (empty($clippingItems)) {
    $clippingItems = [['enabled' => 1, 'portal' => '', 'published_at' => '', 'title' => '', 'excerpt' => '', 'image_url' => '', 'article_url' => '']];
}
if (empty($partnerItems)) {
    $partnerItems = [['enabled' => 1, 'name' => '', 'logo_url' => '', 'website_url' => '']];
}
?>
<style>
    .mk-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
    }
    .mk-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .mk-note {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
    }
    .mk-grid {
        display: grid;
        gap: 16px;
    }
    .mk-grid-2 {
        grid-template-columns: 1fr;
    }
    .mk-row {
        margin-bottom: 12px;
        padding: 14px;
        border: 1px dashed #d2d9e1;
        background: #fafbfd;
    }
    .mk-row-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .mk-row-title {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #374151;
    }
    .mk-input-group {
        display: flex;
        gap: 8px;
    }
    .mk-input-group .form-control {
        min-width: 0;
    }
    .mk-section-toggle {
        margin-bottom: 16px;
    }
    .mk-section-toggle label {
        font-weight: 600;
    }
    .mk-box-help {
        margin-top: 12px;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
    }
    @media (min-width: 992px) {
        .mk-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Home Institucional - CMS</h1>
            <p class="mk-note">Edite a home institucional por seção. Clipping, parceiros, verticais e etapas podem ser adicionados, removidos, ativados e desativados sem mexer manualmente na view.</p>
        </section>
    </div>
</div>

<form action="<?= adminUrl('marketing/home-cms'); ?>" method="post">
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
                            <a href="<?= langBaseUrl(); ?>" target="_blank" class="btn btn-default"><i class="fa fa-eye"></i> Ver home</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salvar alterações</button>
                        </div>
                    </div>
                    <p class="mk-box-help">Os cards de simuladores continuam vindo das páginas públicas dos simuladores, e os cards do blog continuam vindo dos posts já publicados. Nesta tela você controla a apresentação da home e as áreas manuais de autoridade.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Navegação e métricas</h3>
                </div>
                <div class="box-body">
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>CTA do menu</label>
                            <input type="text" class="form-control" name="home[nav][primary_cta_label]" value="<?= esc($homeConfig['nav']['primary_cta_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Link do CTA do menu</label>
                            <input type="text" class="form-control" name="home[nav][primary_cta_url]" value="<?= esc($homeConfig['nav']['primary_cta_url'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Label da métrica 1</label>
                            <input type="text" class="form-control" name="home[hero_stats_labels][simulators]" value="<?= esc($homeConfig['hero_stats_labels']['simulators'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Label da métrica 2</label>
                            <input type="text" class="form-control" name="home[hero_stats_labels][verticals]" value="<?= esc($homeConfig['hero_stats_labels']['verticals'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Label da métrica 3</label>
                            <input type="text" class="form-control" name="home[hero_stats_labels][insights]" value="<?= esc($homeConfig['hero_stats_labels']['insights'] ?? ''); ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Links rápidos do menu</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-quick-links"><i class="fa fa-plus"></i> Adicionar link</button>
                    </div>
                    <div id="mk-quick-links" class="mk-repeater" data-template="quick-link" data-name="home[nav][quick_links]" data-next-index="<?= count($navQuickLinks); ?>">
                        <?php foreach ($navQuickLinks as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Link rápido</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[nav][quick_links][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[nav][quick_links][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="home[nav][quick_links][<?= $index; ?>][label]" value="<?= esc($item['label'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="home[nav][quick_links][<?= $index; ?>][href]" value="<?= esc($item['href'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Hero</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[hero][enabled]" value="0">
                        <label><input type="checkbox" name="home[hero][enabled]" value="1" <?= $checked($homeConfig['hero']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>

                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Badge</label>
                            <input type="text" class="form-control" name="home[hero][badge]" value="<?= esc($homeConfig['hero']['badge'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título principal</label>
                            <input type="text" class="form-control" name="home[hero][title]" value="<?= esc($homeConfig['hero']['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>CTA principal</label>
                            <input type="text" class="form-control" name="home[hero][primary_cta_label]" value="<?= esc($homeConfig['hero']['primary_cta_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Link do CTA principal</label>
                            <input type="text" class="form-control" name="home[hero][primary_cta_url]" value="<?= esc($homeConfig['hero']['primary_cta_url'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>CTA secundário</label>
                            <input type="text" class="form-control" name="home[hero][secondary_cta_label]" value="<?= esc($homeConfig['hero']['secondary_cta_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Link do CTA secundário</label>
                            <input type="text" class="form-control" name="home[hero][secondary_cta_url]" value="<?= esc($homeConfig['hero']['secondary_cta_url'] ?? ''); ?>">
                        </div>
                    </div>
                    <label style="margin-top:12px;">Subtítulo</label>
                    <textarea class="form-control" name="home[hero][subtitle]" rows="3"><?= esc($homeConfig['hero']['subtitle'] ?? ''); ?></textarea>

                    <hr>

                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Provas rápidas do hero</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-hero-proof"><i class="fa fa-plus"></i> Adicionar item</button>
                    </div>
                    <div id="mk-hero-proof" class="mk-repeater" data-template="proof-item" data-name="home[hero][proof_items]" data-next-index="<?= count($heroProofItems); ?>">
                        <?php foreach ($heroProofItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Item do hero</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[hero][proof_items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[hero][proof_items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="home[hero][proof_items][<?= $index; ?>][title]" value="<?= esc($item['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Texto</label>
                                        <input type="text" class="form-control" name="home[hero][proof_items][<?= $index; ?>][text]" value="<?= esc($item['text'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Faixa de autoridade</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[trust_strip][enabled]" value="0">
                        <label><input type="checkbox" name="home[trust_strip][enabled]" value="1" <?= $checked($homeConfig['trust_strip']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Texto de abertura</label>
                            <input type="text" class="form-control" name="home[trust_strip][lead]" value="<?= esc($homeConfig['trust_strip']['lead'] ?? ''); ?>">
                        </div>
                    </div>
                    <hr>
                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Itens da faixa</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-trust-items"><i class="fa fa-plus"></i> Adicionar item</button>
                    </div>
                    <div id="mk-trust-items" class="mk-repeater" data-template="trust-item" data-name="home[trust_strip][items]" data-next-index="<?= count($trustItems); ?>">
                        <?php foreach ($trustItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Item da faixa</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[trust_strip][items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[trust_strip][items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="home[trust_strip][items][<?= $index; ?>][label]" value="<?= esc($item['label'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Verticais</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[verticals][enabled]" value="0">
                        <label><input type="checkbox" name="home[verticals][enabled]" value="1" <?= $checked($homeConfig['verticals']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="home[verticals][label]" value="<?= esc($homeConfig['verticals']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="home[verticals][title]" value="<?= esc($homeConfig['verticals']['title'] ?? ''); ?>">
                        </div>
                    </div>
                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="home[verticals][description]" rows="3"><?= esc($homeConfig['verticals']['description'] ?? ''); ?></textarea>
                    <hr>
                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Cards de verticais</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-vertical-items"><i class="fa fa-plus"></i> Adicionar vertical</button>
                    </div>
                    <div id="mk-vertical-items" class="mk-repeater" data-template="vertical-item" data-name="home[verticals][items]" data-next-index="<?= count($verticalItems); ?>">
                        <?php foreach ($verticalItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Card de vertical</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[verticals][items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[verticals][items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Eyebrow</label>
                                        <input type="text" class="form-control" name="home[verticals][items][<?= $index; ?>][eyebrow]" value="<?= esc($item['eyebrow'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-5">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="home[verticals][items][<?= $index; ?>][title]" value="<?= esc($item['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Cor</label>
                                        <input type="text" class="form-control" name="home[verticals][items][<?= $index; ?>][accent]" value="<?= esc($item['accent'] ?? '#C7A053'); ?>">
                                    </div>
                                    <div class="col-sm-12" style="margin-top:12px;">
                                        <label>Descrição</label>
                                        <textarea class="form-control" name="home[verticals][items][<?= $index; ?>][description]" rows="2"><?= esc($item['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-sm-4" style="margin-top:12px;">
                                        <label>Texto do link</label>
                                        <input type="text" class="form-control" name="home[verticals][items][<?= $index; ?>][link_label]" value="<?= esc($item['link_label'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-8" style="margin-top:12px;">
                                        <label>URL do link</label>
                                        <input type="text" class="form-control" name="home[verticals][items][<?= $index; ?>][link_url]" value="<?= esc($item['link_url'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Processo</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[process][enabled]" value="0">
                        <label><input type="checkbox" name="home[process][enabled]" value="1" <?= $checked($homeConfig['process']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="home[process][label]" value="<?= esc($homeConfig['process']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="home[process][title]" value="<?= esc($homeConfig['process']['title'] ?? ''); ?>">
                        </div>
                    </div>
                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="home[process][description]" rows="3"><?= esc($homeConfig['process']['description'] ?? ''); ?></textarea>
                    <hr>
                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Etapas</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-process-items"><i class="fa fa-plus"></i> Adicionar etapa</button>
                    </div>
                    <div id="mk-process-items" class="mk-repeater" data-template="process-item" data-name="home[process][items]" data-next-index="<?= count($processItems); ?>">
                        <?php foreach ($processItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Etapa</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[process][items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[process][items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="home[process][items][<?= $index; ?>][title]" value="<?= esc($item['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Descrição</label>
                                        <input type="text" class="form-control" name="home[process][items][<?= $index; ?>][desc]" value="<?= esc($item['desc'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Simuladores</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[simulators][enabled]" value="0">
                        <label><input type="checkbox" name="home[simulators][enabled]" value="1" <?= $checked($homeConfig['simulators']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <label>Label da seção</label>
                    <input type="text" class="form-control" name="home[simulators][label]" value="<?= esc($homeConfig['simulators']['label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Título</label>
                    <input type="text" class="form-control" name="home[simulators][title]" value="<?= esc($homeConfig['simulators']['title'] ?? ''); ?>">
                    <label style="margin-top:12px;">Descrição</label>
                    <textarea class="form-control" name="home[simulators][description]" rows="3"><?= esc($homeConfig['simulators']['description'] ?? ''); ?></textarea>
                    <label style="margin-top:12px;">Texto do CTA</label>
                    <input type="text" class="form-control" name="home[simulators][cta_label]" value="<?= esc($homeConfig['simulators']['cta_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">URL do CTA</label>
                    <input type="text" class="form-control" name="home[simulators][cta_url]" value="<?= esc($homeConfig['simulators']['cta_url'] ?? ''); ?>">
                    <p class="mk-box-help">Os cards desta área continuam vindo das páginas públicas dos simuladores. Aqui você edita a moldura da seção.</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Blog</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[blog][enabled]" value="0">
                        <label><input type="checkbox" name="home[blog][enabled]" value="1" <?= $checked($homeConfig['blog']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <label>Label da seção</label>
                    <input type="text" class="form-control" name="home[blog][label]" value="<?= esc($homeConfig['blog']['label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Título</label>
                    <input type="text" class="form-control" name="home[blog][title]" value="<?= esc($homeConfig['blog']['title'] ?? ''); ?>">
                    <label style="margin-top:12px;">Descrição</label>
                    <textarea class="form-control" name="home[blog][description]" rows="3"><?= esc($homeConfig['blog']['description'] ?? ''); ?></textarea>
                    <label style="margin-top:12px;">Texto do link dos cards</label>
                    <input type="text" class="form-control" name="home[blog][featured_cta_label]" value="<?= esc($homeConfig['blog']['featured_cta_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Texto do CTA da seção</label>
                    <input type="text" class="form-control" name="home[blog][cta_label]" value="<?= esc($homeConfig['blog']['cta_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">URL do CTA da seção</label>
                    <input type="text" class="form-control" name="home[blog][cta_url]" value="<?= esc($homeConfig['blog']['cta_url'] ?? ''); ?>">
                    <p class="mk-box-help">Os cards desta área continuam vindo dos posts publicados. Aqui você controla apenas a apresentação da seção.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Clipping de notícias</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[clippings][enabled]" value="0">
                        <label><input type="checkbox" name="home[clippings][enabled]" value="1" <?= $checked($homeConfig['clippings']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="home[clippings][label]" value="<?= esc($homeConfig['clippings']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="home[clippings][title]" value="<?= esc($homeConfig['clippings']['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Texto do CTA dos cards</label>
                            <input type="text" class="form-control" name="home[clippings][item_cta_label]" value="<?= esc($homeConfig['clippings']['item_cta_label'] ?? ''); ?>">
                        </div>
                    </div>
                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="home[clippings][description]" rows="3"><?= esc($homeConfig['clippings']['description'] ?? ''); ?></textarea>
                    <p class="mk-box-help">Para prints de matérias, prefira imagens com proporção mais alta, como 4:3, e resolução mínima próxima de 1600x1200 para melhor leitura no card.</p>
                    <hr>
                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Matérias</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-clipping-items"><i class="fa fa-plus"></i> Adicionar matéria</button>
                    </div>
                    <div id="mk-clipping-items" class="mk-repeater" data-template="clipping-item" data-name="home[clippings][items]" data-next-index="<?= count($clippingItems); ?>">
                        <?php foreach ($clippingItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Matéria</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[clippings][items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[clippings][items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Portal</label>
                                        <input type="text" class="form-control" name="home[clippings][items][<?= $index; ?>][portal]" value="<?= esc($item['portal'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Data de publicação</label>
                                        <input type="text" class="form-control" name="home[clippings][items][<?= $index; ?>][published_at]" value="<?= esc($item['published_at'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-12" style="margin-top:12px;">
                                        <label>Título da matéria</label>
                                        <input type="text" class="form-control" name="home[clippings][items][<?= $index; ?>][title]" value="<?= esc($item['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-12" style="margin-top:12px;">
                                        <label>Resumo</label>
                                        <textarea class="form-control" name="home[clippings][items][<?= $index; ?>][excerpt]" rows="2"><?= esc($item['excerpt'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-sm-6" style="margin-top:12px;">
                                        <label>Imagem da matéria</label>
                                        <div class="mk-input-group mk-image-group">
                                            <input type="text" class="form-control" name="home[clippings][items][<?= $index; ?>][image_url]" value="<?= esc($item['image_url'] ?? ''); ?>">
                                            <button type="button" class="btn btn-default mk-pick-image" data-toggle="modal" data-target="#file_manager_image">Selecionar</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="margin-top:12px;">
                                        <label>Link da matéria</label>
                                        <input type="text" class="form-control" name="home[clippings][items][<?= $index; ?>][article_url]" value="<?= esc($item['article_url'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Parceiros de qualidade</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[partners][enabled]" value="0">
                        <label><input type="checkbox" name="home[partners][enabled]" value="1" <?= $checked($homeConfig['partners']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="home[partners][label]" value="<?= esc($homeConfig['partners']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="home[partners][title]" value="<?= esc($homeConfig['partners']['title'] ?? ''); ?>">
                        </div>
                    </div>
                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="home[partners][description]" rows="3"><?= esc($homeConfig['partners']['description'] ?? ''); ?></textarea>
                    <hr>
                    <div class="mk-row-head">
                        <h4 class="mk-row-title">Parceiros</h4>
                        <button type="button" class="btn btn-default btn-sm mk-add-row" data-target="#mk-partner-items"><i class="fa fa-plus"></i> Adicionar parceiro</button>
                    </div>
                    <div id="mk-partner-items" class="mk-repeater" data-template="partner-item" data-name="home[partners][items]" data-next-index="<?= count($partnerItems); ?>">
                        <?php foreach ($partnerItems as $index => $item): ?>
                            <div class="mk-row">
                                <div class="mk-row-head">
                                    <h4 class="mk-row-title">Parceiro</h4>
                                    <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="hidden" name="home[partners][items][<?= $index; ?>][enabled]" value="0">
                                        <label class="checkbox-inline"><input type="checkbox" name="home[partners][items][<?= $index; ?>][enabled]" value="1" <?= $checked($item['enabled'] ?? 0); ?>> Ativo</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="home[partners][items][<?= $index; ?>][name]" value="<?= esc($item['name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Site do parceiro</label>
                                        <input type="text" class="form-control" name="home[partners][items][<?= $index; ?>][website_url]" value="<?= esc($item['website_url'] ?? ''); ?>">
                                    </div>
                                    <div class="col-sm-12" style="margin-top:12px;">
                                        <label>Logo</label>
                                        <div class="mk-input-group mk-image-group">
                                            <input type="text" class="form-control" name="home[partners][items][<?= $index; ?>][logo_url]" value="<?= esc($item['logo_url'] ?? ''); ?>">
                                            <button type="button" class="btn btn-default mk-pick-image" data-toggle="modal" data-target="#file_manager_image">Selecionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">CTA de fechamento</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[cta][enabled]" value="0">
                        <label><input type="checkbox" name="home[cta][enabled]" value="1" <?= $checked($homeConfig['cta']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <label>Label da seção</label>
                    <input type="text" class="form-control" name="home[cta][label]" value="<?= esc($homeConfig['cta']['label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Título</label>
                    <input type="text" class="form-control" name="home[cta][title]" value="<?= esc($homeConfig['cta']['title'] ?? ''); ?>">
                    <label style="margin-top:12px;">Descrição</label>
                    <textarea class="form-control" name="home[cta][description]" rows="3"><?= esc($homeConfig['cta']['description'] ?? ''); ?></textarea>
                    <label style="margin-top:12px;">CTA principal</label>
                    <input type="text" class="form-control" name="home[cta][primary_cta_label]" value="<?= esc($homeConfig['cta']['primary_cta_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Link do CTA principal</label>
                    <input type="text" class="form-control" name="home[cta][primary_cta_url]" value="<?= esc($homeConfig['cta']['primary_cta_url'] ?? ''); ?>">
                    <label style="margin-top:12px;">CTA secundário</label>
                    <input type="text" class="form-control" name="home[cta][secondary_cta_label]" value="<?= esc($homeConfig['cta']['secondary_cta_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Link do CTA secundário</label>
                    <input type="text" class="form-control" name="home[cta][secondary_cta_url]" value="<?= esc($homeConfig['cta']['secondary_cta_url'] ?? ''); ?>">
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Bloco de contato</h3>
                </div>
                <div class="box-body">
                    <div class="mk-section-toggle">
                        <input type="hidden" name="home[lead][enabled]" value="0">
                        <label><input type="checkbox" name="home[lead][enabled]" value="1" <?= $checked($homeConfig['lead']['enabled'] ?? 0); ?>> Ativar seção</label>
                    </div>
                    <label>Label da seção</label>
                    <input type="text" class="form-control" name="home[lead][label]" value="<?= esc($homeConfig['lead']['label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Título</label>
                    <input type="text" class="form-control" name="home[lead][title]" value="<?= esc($homeConfig['lead']['title'] ?? ''); ?>">
                    <label style="margin-top:12px;">Descrição</label>
                    <textarea class="form-control" name="home[lead][description]" rows="3"><?= esc($homeConfig['lead']['description'] ?? ''); ?></textarea>
                    <label style="margin-top:12px;">Título do formulário</label>
                    <input type="text" class="form-control" name="home[lead][form_heading]" value="<?= esc($homeConfig['lead']['form_heading'] ?? ''); ?>">
                    <label style="margin-top:12px;">Descrição do formulário</label>
                    <textarea class="form-control" name="home[lead][form_description]" rows="2"><?= esc($homeConfig['lead']['form_description'] ?? ''); ?></textarea>
                    <label style="margin-top:12px;">Texto do botão</label>
                    <input type="text" class="form-control" name="home[lead][form_button_label]" value="<?= esc($homeConfig['lead']['form_button_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Placeholder da mensagem</label>
                    <textarea class="form-control" name="home[lead][message_placeholder]" rows="2"><?= esc($homeConfig['lead']['message_placeholder'] ?? ''); ?></textarea>

                    <div class="row" style="margin-top:15px;">
                        <div class="col-sm-6">
                            <input type="hidden" name="home[lead][show_phone]" value="0">
                            <label class="checkbox-inline"><input type="checkbox" name="home[lead][show_phone]" value="1" <?= $checked($homeConfig['lead']['show_phone'] ?? 0); ?>> Exibir telefone</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="hidden" name="home[lead][show_email]" value="0">
                            <label class="checkbox-inline"><input type="checkbox" name="home[lead][show_email]" value="1" <?= $checked($homeConfig['lead']['show_email'] ?? 0); ?>> Exibir e-mail</label>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-sm-6">
                            <input type="hidden" name="home[lead][show_simulators_chip]" value="0">
                            <label class="checkbox-inline"><input type="checkbox" name="home[lead][show_simulators_chip]" value="1" <?= $checked($homeConfig['lead']['show_simulators_chip'] ?? 0); ?>> Exibir chip simuladores</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="hidden" name="home[lead][show_blog_chip]" value="0">
                            <label class="checkbox-inline"><input type="checkbox" name="home[lead][show_blog_chip]" value="1" <?= $checked($homeConfig['lead']['show_blog_chip'] ?? 0); ?>> Exibir chip blog</label>
                        </div>
                    </div>
                    <label style="margin-top:12px;">Texto do chip simuladores</label>
                    <input type="text" class="form-control" name="home[lead][simulators_chip_label]" value="<?= esc($homeConfig['lead']['simulators_chip_label'] ?? ''); ?>">
                    <label style="margin-top:12px;">Texto do chip blog</label>
                    <input type="text" class="form-control" name="home[lead][blog_chip_label]" value="<?= esc($homeConfig['lead']['blog_chip_label'] ?? ''); ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="mk-toolbar">
                        <p class="mk-note">As seções de clipping e parceiros só aparecem na home pública quando estiverem ativas e com itens preenchidos.</p>
                        <div class="mk-toolbar-actions">
                            <a href="<?= langBaseUrl(); ?>" target="_blank" class="btn btn-default"><i class="fa fa-eye"></i> Ver home</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salvar alterações</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/template" id="tmpl-quick-link">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Link rápido</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-4">
                <label>Label</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][label]" value="">
            </div>
            <div class="col-sm-6">
                <label>Link</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][href]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-proof-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Item do hero</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-4">
                <label>Título</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][title]" value="">
            </div>
            <div class="col-sm-6">
                <label>Texto</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][text]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-trust-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Item da faixa</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-10">
                <label>Label</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][label]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-vertical-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Card de vertical</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-3">
                <label>Eyebrow</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][eyebrow]" value="">
            </div>
            <div class="col-sm-5">
                <label>Título</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][title]" value="">
            </div>
            <div class="col-sm-2">
                <label>Cor</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][accent]" value="#C7A053">
            </div>
            <div class="col-sm-12" style="margin-top:12px;">
                <label>Descrição</label>
                <textarea class="form-control" name="__NAME__[__INDEX__][description]" rows="2"></textarea>
            </div>
            <div class="col-sm-4" style="margin-top:12px;">
                <label>Texto do link</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][link_label]" value="">
            </div>
            <div class="col-sm-8" style="margin-top:12px;">
                <label>URL do link</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][link_url]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-process-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Etapa</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-4">
                <label>Título</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][title]" value="">
            </div>
            <div class="col-sm-6">
                <label>Descrição</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][desc]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-clipping-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Matéria</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-4">
                <label>Portal</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][portal]" value="">
            </div>
            <div class="col-sm-6">
                <label>Data de publicação</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][published_at]" value="">
            </div>
            <div class="col-sm-12" style="margin-top:12px;">
                <label>Título da matéria</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][title]" value="">
            </div>
            <div class="col-sm-12" style="margin-top:12px;">
                <label>Resumo</label>
                <textarea class="form-control" name="__NAME__[__INDEX__][excerpt]" rows="2"></textarea>
            </div>
            <div class="col-sm-6" style="margin-top:12px;">
                <label>Imagem da matéria</label>
                <div class="mk-input-group mk-image-group">
                    <input type="text" class="form-control" name="__NAME__[__INDEX__][image_url]" value="">
                    <button type="button" class="btn btn-default mk-pick-image" data-toggle="modal" data-target="#file_manager_image">Selecionar</button>
                </div>
            </div>
            <div class="col-sm-6" style="margin-top:12px;">
                <label>Link da matéria</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][article_url]" value="">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-partner-item">
    <div class="mk-row">
        <div class="mk-row-head">
            <h4 class="mk-row-title">Parceiro</h4>
            <button type="button" class="btn btn-danger btn-xs mk-remove-row">Remover</button>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" name="__NAME__[__INDEX__][enabled]" value="0">
                <label class="checkbox-inline"><input type="checkbox" name="__NAME__[__INDEX__][enabled]" value="1" checked> Ativo</label>
            </div>
            <div class="col-sm-4">
                <label>Nome</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][name]" value="">
            </div>
            <div class="col-sm-6">
                <label>Site do parceiro</label>
                <input type="text" class="form-control" name="__NAME__[__INDEX__][website_url]" value="">
            </div>
            <div class="col-sm-12" style="margin-top:12px;">
                <label>Logo</label>
                <div class="mk-input-group mk-image-group">
                    <input type="text" class="form-control" name="__NAME__[__INDEX__][logo_url]" value="">
                    <button type="button" class="btn btn-default mk-pick-image" data-toggle="modal" data-target="#file_manager_image">Selecionar</button>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
(function() {
    var currentImageInput = null;

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('mk-add-row')) {
            var targetSelector = e.target.getAttribute('data-target');
            var target = document.querySelector(targetSelector);
            if (!target) return;
            var templateId = target.getAttribute('data-template');
            var baseName = target.getAttribute('data-name');
            var nextIndex = parseInt(target.getAttribute('data-next-index') || '0', 10);
            var template = document.getElementById('tmpl-' + templateId);
            if (!template) return;
            var html = template.innerHTML
                .replace(/__NAME__/g, baseName)
                .replace(/__INDEX__/g, String(nextIndex));
            target.insertAdjacentHTML('beforeend', html);
            target.setAttribute('data-next-index', String(nextIndex + 1));
        }

        if (e.target && e.target.classList.contains('mk-remove-row')) {
            var row = e.target.closest('.mk-row');
            if (row) {
                row.remove();
            }
        }

        if (e.target && e.target.classList.contains('mk-pick-image')) {
            var group = e.target.closest('.mk-image-group');
            currentImageInput = group ? group.querySelector('input.form-control') : null;
        }
    });

    $(document).on('click', '#file_manager_image #btn_img_select', function() {
        try {
            var base = $('#selected_img_base_url').val() || '';
            var path = $('#selected_img_default_file_path').val() || '';
            if (currentImageInput) {
                currentImageInput.value = base + path;
            }
        } catch (err) {}
    });
})();
</script>

<?php
echo view('admin/file-manager/_load_file_manager', ['loadImages' => true]);
?>
