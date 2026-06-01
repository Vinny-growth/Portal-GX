<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Configurações Gerais</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Configurações Gerais</h3>
            </div>
            <form action="<?= base_url('Admin/generalSettingsPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label">Nome da Aplicação</label>
                        <input type="text" class="form-control" name="application_name" placeholder="Nome da Aplicação" value="<?= esc($generalSettings->application_name); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Fuso Horário</label>
                        <select name="timezone" class="form-control max-600">
                            <?php $timezones = timezone_identifiers_list();
                            if (!empty($timezones)):
                                foreach ($timezones as $timezone):?>
                                    <option value="<?= $timezone; ?>" <?= $timezone == $generalSettings->timezone ? 'selected' : ''; ?>><?= $timezone; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Logo da Aplicação - <small>Formatos: PNG, JPG, SVG, GIF</small></label>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= $getLogo(); ?>" alt="logo" style="max-width: 250px; max-height: 250px;">
                        </div>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                Selecionar Logo
                                <input type="file" name="logo" size="40" accept=".png, .jpg, .jpeg, .svg, .gif">
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Favicon - <small>16x16 ou 32x32 pixels</small></label>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= $getFavicon(); ?>" alt="favicon" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                Selecionar Favicon
                                <input type="file" name="favicon" size="40" accept=".png, .jpg, .jpeg, .svg, .gif">
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Copyright</label>
                        <input type="text" class="form-control" name="copyright" placeholder="Copyright © 2025 GX Capital - Todos os direitos reservados"
                               value="<?= esc($generalSettings->copyright); ?>">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Configurações de Contato</h3>
            </div>
            <form action="<?= base_url('Admin/contactSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <strong>WhatsApp dos botões comerciais</strong><br>
                        O número registrado abaixo será usado nos botões de WhatsApp das páginas de simuladores e demais páginas comerciais.
                    </div>

                    <div class="form-group">
                        <label class="control-label">WhatsApp Comercial</label>
                        <input type="text" class="form-control" name="contact_whatsapp" placeholder="+55 11 99999-9999"
                               value="<?= esc($generalSettings->contact_whatsapp ?? ''); ?>">
                        <p class="help-block" style="margin-bottom: 0;">Informe o número com DDD e, de preferência, com código do país. Exemplo: +55 11 99999-9999.</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Endereço</label>
                        <input type="text" class="form-control" name="contact_address" placeholder="Rua, número, bairro, cidade"
                               value="<?= esc($generalSettings->contact_address); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">E-mail</label>
                        <input type="text" class="form-control" name="contact_email" placeholder="contato@gxcapital.com"
                               value="<?= esc($generalSettings->contact_email); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Telefone</label>
                        <input type="text" class="form-control" name="contact_phone" placeholder="(11) 99999-9999"
                               value="<?= esc($generalSettings->contact_phone); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Texto de Contato</label>
                        <textarea class="form-control text-area" name="contact_text"
                                  placeholder="Texto descritivo sobre o contato da empresa"><?= esc($generalSettings->contact_text); ?></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar alterações</button>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Configurações de Mídia Social</h3>
            </div>
            <form action="<?= base_url('Admin/socialMediaSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">

                    <div class="form-group">
                        <label class="control-label">URL do Facebook</label>
                        <input type="text" class="form-control" name="facebook_url" placeholder="https://www.facebook.com/seuPerfil" value="<?= esc($generalSettings->facebook_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do Twitter/X</label>
                        <input type="text" class="form-control" name="twitter_url" placeholder="https://twitter.com/seuPerfil" value="<?= esc($generalSettings->twitter_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do Instagram</label>
                        <input type="text" class="form-control" name="instagram_url" placeholder="https://www.instagram.com/seuPerfil" value="<?= esc($generalSettings->instagram_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do Pinterest</label>
                        <input type="text" class="form-control" name="pinterest_url" placeholder="https://br.pinterest.com/seuPerfil" value="<?= esc($generalSettings->pinterest_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do LinkedIn</label>
                        <input type="text" class="form-control" name="linkedin_url" placeholder="https://www.linkedin.com/company/seuPerfil" value="<?= esc($generalSettings->linkedin_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do VKontakte</label>
                        <input type="text" class="form-control" name="vk_url" placeholder="https://vk.com/seuPerfil" value="<?= esc($generalSettings->vk_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do Telegram</label>
                        <input type="text" class="form-control" name="telegram_url" placeholder="https://t.me/seuCanal" value="<?= esc($generalSettings->telegram_url); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">URL do YouTube</label>
                        <input type="text" class="form-control" name="youtube_url" placeholder="https://www.youtube.com/c/seuCanal" value="<?= esc($generalSettings->youtube_url); ?>">
                    </div>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Aviso de Cookies</h3>
            </div>
            <form action="<?= base_url('Admin/cookiesWarningPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label>Exibir aviso de cookies</label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="cookies_warning" value="1" id="cookies_warning_1" class="square-purple" <?= $generalSettings->cookies_warning == 1 ? 'checked' : ''; ?>>
                                <label for="cookies_warning_1" class="option-label">Sim</label>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="cookies_warning" value="0" id="cookies_warning_2" class="square-purple" <?= $generalSettings->cookies_warning != 1 ? 'checked' : ''; ?>>
                                <label for="cookies_warning_2" class="option-label">Não</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Texto do aviso de cookies</label>
                        <textarea class="form-control text-area" name="cookies_warning_text" placeholder="Este site utiliza cookies para melhorar sua experiência..." style="min-height: 100px;"><?= $generalSettings->cookies_warning_text; ?></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Códigos Personalizados</h3>
            </div>
            <form action="<?= base_url('Admin/customHeaderCodesPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label">Códigos do Cabeçalho (Google Tag Manager, Analytics, etc.)</label>
                        <textarea class="form-control text-area" name="custom_header_codes" placeholder="<!-- Insira aqui códigos do Google Tag Manager, Analytics, etc. -->" style="min-height: 200px;"><?= $generalSettings->custom_header_codes; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Códigos do Rodapé (Scripts de conversão, etc.)</label>
                        <textarea class="form-control text-area" name="custom_footer_codes" placeholder="<!-- Insira aqui scripts de conversão, tracking, etc. -->" style="min-height: 200px;"><?= $generalSettings->custom_footer_codes; ?></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Meta Conversions API (Facebook)</h3>
                <div class="box-tools pull-right">
                    <small class="text-muted">Configure os eventos de conversão para tracking no Facebook/Meta</small>
                </div>
            </div>
            <form action="<?= base_url('Admin/metaConversionsApiPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Pixel ID do Facebook</label>
                                <input type="text" class="form-control" name="pixel_id" placeholder="123456789012345" 
                                       value="<?= esc($generalSettings->meta_pixel_id ?? ''); ?>">
                                <small class="text-muted">Encontre seu Pixel ID no Gerenciador de Eventos do Facebook</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Access Token</label>
                                <input type="text" class="form-control" name="access_token" placeholder="EAAxxxxxxxxxxxxx" 
                                       value="<?= esc($generalSettings->meta_access_token ?? ''); ?>">
                                <small class="text-muted">Token de acesso para a API do Facebook</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Test Event Code (Opcional)</label>
                                <input type="text" class="form-control" name="test_event_code" placeholder="TEST12345" 
                                       value="<?= esc($generalSettings->meta_test_event_code ?? ''); ?>">
                                <small class="text-muted">Código para testar eventos (deixe vazio em produção)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ativar Meta Conversions API</label>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 col-option">
                                        <input type="radio" name="meta_api_enabled" value="1" id="meta_api_enabled_1" class="square-purple" 
                                               <?= ($generalSettings->meta_api_enabled ?? 0) == 1 ? 'checked' : ''; ?>>
                                        <label for="meta_api_enabled_1" class="option-label">Ativado</label>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 col-option">
                                        <input type="radio" name="meta_api_enabled" value="0" id="meta_api_enabled_0" class="square-purple" 
                                               <?= ($generalSettings->meta_api_enabled ?? 0) != 1 ? 'checked' : ''; ?>>
                                        <label for="meta_api_enabled_0" class="option-label">Desativado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Modo de Teste</label>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 col-option">
                                        <input type="radio" name="meta_test_mode" value="1" id="meta_test_mode_1" class="square-purple" 
                                               <?= ($generalSettings->meta_test_mode ?? 0) == 1 ? 'checked' : ''; ?>>
                                        <label for="meta_test_mode_1" class="option-label">Teste</label>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 col-option">
                                        <input type="radio" name="meta_test_mode" value="0" id="meta_test_mode_0" class="square-purple" 
                                               <?= ($generalSettings->meta_test_mode ?? 0) != 1 ? 'checked' : ''; ?>>
                                        <label for="meta_test_mode_0" class="option-label">Produção</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Eventos para Tracking</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="track_events[]" value="PageView" 
                                               <?= in_array('PageView', $generalSettings->meta_track_events ?? []) ? 'checked' : ''; ?>>
                                        Page View
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="track_events[]" value="Lead" 
                                               <?= in_array('Lead', $generalSettings->meta_track_events ?? []) ? 'checked' : ''; ?>>
                                        Lead (Formulário)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="track_events[]" value="CompleteRegistration" 
                                               <?= in_array('CompleteRegistration', $generalSettings->meta_track_events ?? []) ? 'checked' : ''; ?>>
                                        Cadastro Completo
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="track_events[]" value="Contact" 
                                               <?= in_array('Contact', $generalSettings->meta_track_events ?? []) ? 'checked' : ''; ?>>
                                        Contato
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Como configurar:</h5>
                        <ol>
                            <li>Acesse o <strong>Gerenciador de Eventos</strong> do Facebook</li>
                            <li>Selecione seu Pixel e copie o <strong>Pixel ID</strong> (encontre no topo da página)</li>
                            <li>Vá em <strong>Configurações</strong> → <strong>Conversions API</strong></li>
                            <li>Clique em <strong>Gerar Access Token</strong> e copie o token gerado</li>
                            <li>Para testes, ative o <strong>Modo de Teste</strong> e adicione um <strong>Test Event Code</strong></li>
                            <li><strong>Importante:</strong> Use apenas o Pixel ID, não é necessário Dataset ID</li>
                        </ol>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Salvar configurações Meta API</button>
                </div>
            </form>
        </div>
    </div>
</div>
