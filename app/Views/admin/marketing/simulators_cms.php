<?php
$heroProof = $simulatorsConfig['hero_proof'] ?? [];
$technologySignals = $simulatorsConfig['technology']['signals'] ?? [];

while (count($heroProof) < 3) {
    $heroProof[] = ['text' => ''];
}

while (count($technologySignals) < 3) {
    $technologySignals[] = ['text' => ''];
}
?>
<style>
    .mk-note {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
    }
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
    .mk-grid {
        display: grid;
        gap: 16px;
    }
    .mk-grid-2 {
        grid-template-columns: 1fr;
    }
    .mk-grid-3 {
        grid-template-columns: 1fr;
    }
    .mk-box-help {
        margin-top: 12px;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
    }
    .mk-list-grid {
        display: grid;
        gap: 12px;
    }
    .mk-list-item {
        padding: 14px;
        border: 1px dashed #d2d9e1;
        background: #fafbfd;
    }
    @media (min-width: 992px) {
        .mk-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .mk-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Simuladores de Câmbio - CMS</h1>
            <p class="mk-note">Esta tela controla o hub temático de <strong>/simuladores/cambio</strong>, mantendo as rotas legadas de câmbio apontando para ele e permitindo atualizar os parâmetros econômicos que alimentam os cálculos.</p>
        </section>
    </div>
</div>

<form action="<?= adminUrl('marketing/simulators-cms'); ?>" method="post">
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
                            <a href="<?= langBaseUrl('simuladores/cambio'); ?>" target="_blank" class="btn btn-default"><i class="fa fa-eye"></i> Ver página</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salvar alterações</button>
                        </div>
                    </div>
                    <p class="mk-box-help">Os simuladores continuam sendo indicativos. O objetivo deste hub é gerar leads qualificados e entregar uma leitura inicial para importadores e exportadores antes do contato com a mesa.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Hero e prova rápida</h3>
                </div>
                <div class="box-body">
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Badge</label>
                            <input type="text" class="form-control" name="simulators[hero][badge]" value="<?= esc($simulatorsConfig['hero']['badge'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título principal</label>
                            <input type="text" class="form-control" name="simulators[hero][title]" value="<?= esc($simulatorsConfig['hero']['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>CTA principal</label>
                            <input type="text" class="form-control" name="simulators[hero][primary_cta_label]" value="<?= esc($simulatorsConfig['hero']['primary_cta_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Link do CTA principal</label>
                            <input type="text" class="form-control" name="simulators[hero][primary_cta_url]" value="<?= esc($simulatorsConfig['hero']['primary_cta_url'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>CTA secundário</label>
                            <input type="text" class="form-control" name="simulators[hero][secondary_cta_label]" value="<?= esc($simulatorsConfig['hero']['secondary_cta_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Link do CTA secundário</label>
                            <input type="text" class="form-control" name="simulators[hero][secondary_cta_url]" value="<?= esc($simulatorsConfig['hero']['secondary_cta_url'] ?? ''); ?>">
                        </div>
                    </div>

                    <label style="margin-top:12px;">Subtítulo</label>
                    <textarea class="form-control" name="simulators[hero][subtitle]" rows="3"><?= esc($simulatorsConfig['hero']['subtitle'] ?? ''); ?></textarea>

                    <hr>

                    <div class="mk-list-grid">
                        <?php foreach ($heroProof as $index => $item): ?>
                            <div class="mk-list-item">
                                <label>Prova rápida <?= $index + 1; ?></label>
                                <input type="text" class="form-control" name="simulators[hero_proof][<?= $index; ?>][text]" value="<?= esc($item['text'] ?? ''); ?>">
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
                    <h3 class="box-title">Mesa, tecnologia e autoridade</h3>
                </div>
                <div class="box-body">
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="simulators[technology][label]" value="<?= esc($simulatorsConfig['technology']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="simulators[technology][title]" value="<?= esc($simulatorsConfig['technology']['title'] ?? ''); ?>">
                        </div>
                    </div>

                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="simulators[technology][description]" rows="3"><?= esc($simulatorsConfig['technology']['description'] ?? ''); ?></textarea>

                    <div class="mk-grid mk-grid-3" style="margin-top:16px;">
                        <div>
                            <label>Estatística 1 - valor</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_primary_value]" value="<?= esc($simulatorsConfig['technology']['stat_primary_value'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Estatística 2 - valor</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_secondary_value]" value="<?= esc($simulatorsConfig['technology']['stat_secondary_value'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Estatística 3 - valor</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_tertiary_value]" value="<?= esc($simulatorsConfig['technology']['stat_tertiary_value'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Estatística 1 - label</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_primary_label]" value="<?= esc($simulatorsConfig['technology']['stat_primary_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Estatística 2 - label</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_secondary_label]" value="<?= esc($simulatorsConfig['technology']['stat_secondary_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Estatística 3 - label</label>
                            <input type="text" class="form-control" name="simulators[technology][stat_tertiary_label]" value="<?= esc($simulatorsConfig['technology']['stat_tertiary_label'] ?? ''); ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="mk-list-grid">
                        <?php foreach ($technologySignals as $index => $item): ?>
                            <div class="mk-list-item">
                                <label>Sinal de autoridade <?= $index + 1; ?></label>
                                <input type="text" class="form-control" name="simulators[technology][signals][<?= $index; ?>][text]" value="<?= esc($item['text'] ?? ''); ?>">
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
                    <h3 class="box-title">Indicadores econômicos usados nos simuladores</h3>
                </div>
                <div class="box-body">
                    <p class="mk-note">Preencha com percentuais em formato simples, por exemplo <strong>14.25</strong> para 14,25%. Estes valores alimentam os defaults dos cálculos públicos.</p>

                    <div class="mk-grid mk-grid-3" style="margin-top:16px;">
                        <div>
                            <label>Label de referência</label>
                            <input type="text" class="form-control" name="simulators[indicators][reference_label]" value="<?= esc($simulatorsConfig['indicators']['reference_label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Competência / data-base</label>
                            <input type="text" class="form-control" name="simulators[indicators][reference_date]" value="<?= esc($simulatorsConfig['indicators']['reference_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>USD/BRL de referência</label>
                            <input type="text" class="form-control" name="simulators[indicators][usd_brl]" value="<?= esc($simulatorsConfig['indicators']['usd_brl'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Spread comercial (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][commercial_spread]" value="<?= esc($simulatorsConfig['indicators']['commercial_spread'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>IOF (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][iof]" value="<?= esc($simulatorsConfig['indicators']['iof'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>SELIC (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][selic]" value="<?= esc($simulatorsConfig['indicators']['selic'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>CDI (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][cdi]" value="<?= esc($simulatorsConfig['indicators']['cdi'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>IPCA 12m (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][ipca_12m]" value="<?= esc($simulatorsConfig['indicators']['ipca_12m'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>SOFR (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][sofr]" value="<?= esc($simulatorsConfig['indicators']['sofr'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Custo mensal de hedge (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][hedge_cost_monthly]" value="<?= esc($simulatorsConfig['indicators']['hedge_cost_monthly'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Spread onshore adicional (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][onshore_spread]" value="<?= esc($simulatorsConfig['indicators']['onshore_spread'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Spread offshore adicional (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][offshore_spread]" value="<?= esc($simulatorsConfig['indicators']['offshore_spread'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Fee média de trade finance (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][trade_finance_fee]" value="<?= esc($simulatorsConfig['indicators']['trade_finance_fee'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Cenário de estresse cambial (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][stress_scenario]" value="<?= esc($simulatorsConfig['indicators']['stress_scenario'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Margem-alvo do importador (%)</label>
                            <input type="text" class="form-control" name="simulators[indicators][importer_target_margin]" value="<?= esc($simulatorsConfig['indicators']['importer_target_margin'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Câmbio piso do exportador</label>
                            <input type="text" class="form-control" name="simulators[indicators][exporter_floor_rate]" value="<?= esc($simulatorsConfig['indicators']['exporter_floor_rate'] ?? ''); ?>">
                        </div>
                    </div>

                    <label style="margin-top:12px;">Nota operacional</label>
                    <textarea class="form-control" name="simulators[indicators][note]" rows="3"><?= esc($simulatorsConfig['indicators']['note'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Captação de lead</h3>
                </div>
                <div class="box-body">
                    <div class="mk-grid mk-grid-2">
                        <div>
                            <label>Label da seção</label>
                            <input type="text" class="form-control" name="simulators[lead][label]" value="<?= esc($simulatorsConfig['lead']['label'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título da seção</label>
                            <input type="text" class="form-control" name="simulators[lead][title]" value="<?= esc($simulatorsConfig['lead']['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Título do formulário</label>
                            <input type="text" class="form-control" name="simulators[lead][form_title]" value="<?= esc($simulatorsConfig['lead']['form_title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Texto do botão</label>
                            <input type="text" class="form-control" name="simulators[lead][button_label]" value="<?= esc($simulatorsConfig['lead']['button_label'] ?? ''); ?>">
                        </div>
                    </div>

                    <label style="margin-top:12px;">Descrição da seção</label>
                    <textarea class="form-control" name="simulators[lead][description]" rows="2"><?= esc($simulatorsConfig['lead']['description'] ?? ''); ?></textarea>

                    <label style="margin-top:12px;">Descrição do formulário</label>
                    <textarea class="form-control" name="simulators[lead][form_description]" rows="2"><?= esc($simulatorsConfig['lead']['form_description'] ?? ''); ?></textarea>

                    <label style="margin-top:12px;">Mensagem de sucesso</label>
                    <textarea class="form-control" name="simulators[lead][success_message]" rows="2"><?= esc($simulatorsConfig['lead']['success_message'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
