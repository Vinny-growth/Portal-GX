<?php $ga4Connection = $ga4Connection ?? []; ?>
<?php $ga4Properties = $ga4Properties ?? []; ?>

<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Google Analytics 4</h1>
            <p class="text-muted">Conecte uma conta Google, autorize leitura do GA4 e escolha a propriedade que alimentará o dashboard.</p>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<?php if (!empty($ga4Connection['last_error'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger">
                <strong>Último erro do Google Analytics:</strong>
                <?= esc($ga4Connection['last_error']); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Credenciais OAuth</h3>
            </div>
            <form action="<?= adminUrl('dashboard/google-analytics/credentials'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label>Client ID</label>
                        <input type="text" name="ga4_client_id" class="form-control" value="<?= esc($ga4Connection['client_id'] ?? ''); ?>" placeholder="Seu OAuth Client ID do Google Cloud">
                    </div>
                    <div class="form-group">
                        <label>Client Secret</label>
                        <input type="password" name="ga4_client_secret" class="form-control" placeholder="<?= !empty($ga4Connection['has_client_secret']) ? 'Deixe em branco para manter o secret atual' : 'Seu OAuth Client Secret'; ?>">
                        <?php if (!empty($ga4Connection['has_client_secret'])): ?>
                            <p class="help-block">Já existe um client secret salvo. Preencha somente se quiser substituí-lo.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Redirect URI autorizada</label>
                        <input type="text" class="form-control" value="<?= esc($ga4RedirectUri ?? ''); ?>" readonly>
                        <p class="help-block">Cadastre exatamente esta URL no cliente OAuth do Google Cloud.</p>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary">Salvar credenciais</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Status da conexão</h3>
            </div>
            <div class="box-body">
                <p><strong>Credenciais:</strong> <?= !empty($ga4Connection['credentials_configured']) ? 'Configuradas' : 'Pendentes'; ?></p>
                <p><strong>Conta Google:</strong> <?= !empty($ga4Connection['is_connected']) ? 'Conectada' : 'Não conectada'; ?></p>
                <p><strong>Property GA4:</strong> <?= !empty($ga4Connection['property_selected']) ? 'Selecionada' : 'Não selecionada'; ?></p>

                <?php if (!empty($ga4Connection['client_id_masked'])): ?>
                    <p><strong>Client ID:</strong> <?= esc($ga4Connection['client_id_masked']); ?></p>
                <?php endif; ?>

                <?php if (!empty($ga4Connection['connected_email'])): ?>
                    <p><strong>E-mail autorizado:</strong> <?= esc($ga4Connection['connected_email']); ?></p>
                <?php endif; ?>

                <?php if (!empty($ga4Connection['account_name']) || !empty($ga4Connection['property_name'])): ?>
                    <p><strong>Conta:</strong> <?= esc($ga4Connection['account_name'] ?: 'N/D'); ?></p>
                    <p><strong>Property atual:</strong> <?= esc($ga4Connection['property_name'] ?: 'N/D'); ?></p>
                <?php endif; ?>

                <?php if (!empty($ga4Connection['connected_at'])): ?>
                    <p><strong>Conectado em:</strong> <?= esc(formatDate($ga4Connection['connected_at'])); ?></p>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <?php if (!empty($ga4Connection['credentials_configured'])): ?>
                    <a href="<?= adminUrl('dashboard/google-analytics/connect'); ?>" class="btn btn-success">
                        <i class="fa fa-google"></i> <?= !empty($ga4Connection['is_connected']) ? 'Reconectar conta Google' : 'Conectar conta Google'; ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($ga4Connection['is_connected'])): ?>
                    <form action="<?= adminUrl('dashboard/google-analytics/disconnect'); ?>" method="post" style="margin-top:10px;">
                        <?= csrf_field(); ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="clear_credentials" value="1"> Remover também as credenciais OAuth salvas
                            </label>
                        </div>
                        <button class="btn btn-default">
                            <i class="fa fa-unlink"></i> Desconectar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($ga4Connection['is_connected'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Selecionar propriedade do GA4</h3>
                </div>
                <form action="<?= adminUrl('dashboard/google-analytics/property'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="box-body">
                        <?php if (!empty($ga4Properties)): ?>
                            <div class="form-group">
                                <label>Propriedades acessíveis com a conta conectada</label>
                                <select name="property_id" class="form-control">
                                    <option value="">Selecione uma propriedade</option>
                                    <?php foreach ($ga4Properties as $property): ?>
                                        <option value="<?= esc($property['property_id']); ?>" <?= ($ga4Connection['property_id'] ?? '') === ($property['property_id'] ?? '') ? 'selected' : ''; ?>>
                                            <?= esc(($property['account_name'] ?? 'Conta') . ' / ' . ($property['property_name'] ?? 'Property') . ' (' . ($property['property_id'] ?? '') . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="help-block">O dashboard passará a consumir a propriedade selecionada.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Nenhuma propriedade do GA4 foi encontrada para a conta autorizada.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($ga4Properties)): ?>
                        <div class="box-footer">
                            <button class="btn btn-primary">Salvar propriedade</button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
