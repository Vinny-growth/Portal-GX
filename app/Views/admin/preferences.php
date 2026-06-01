<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('preferences'); ?></h3>
    </div>
</div>

<script>
    $(document).on('click', '#btn-validate-openai-key', function () {
        var key = $('#openai_api_key_input').val();
        var $status = $('#openai_key_status');
        $status.text('Validando...').css('color', '#666');
        var data = setAjaxData({openai_api_key: key});
        $.ajax({
            url: VrConfig.baseURL + 'Admin/validateOpenAIKeyPost',
            type: 'POST',
            data: data,
            success: function (resp) {
                try { if (typeof resp === 'string') { resp = JSON.parse(resp); } } catch (e) {}
                if (resp && resp.success) {
                    $status.text(resp.message || 'Chave válida').css('color', '#2e7d32');
                } else {
                    $status.text((resp && resp.message) ? resp.message : 'Falha ao validar').css('color', '#c62828');
                }
            },
            error: function () {
                $status.text('Erro na validação').css('color', '#c62828');
            }
        });
    });
    // Validate AI Writer key
    $(document).on('click', '#btn-validate-aiwriter-key', function () {
        var key = $('#aiwriter_api_key_input').val();
        var $status = $('#aiwriter_key_status');
        $status.text('Validando...').css('color', '#666');
        var data = setAjaxData({api_key: key});
        $.ajax({
            url: VrConfig.baseURL + 'Admin/validateAIWriterKeyPost',
            type: 'POST',
            data: data,
            success: function (resp) {
                try { if (typeof resp === 'string') { resp = JSON.parse(resp); } } catch (e) {}
                if (resp && resp.success) {
                    $status.text(resp.message || 'Chave válida').css('color', '#2e7d32');
                } else {
                    $status.text((resp && resp.message) ? resp.message : 'Falha ao validar').css('color', '#c62828');
                }
            },
            error: function () {
                $status.text('Erro na validação').css('color', '#c62828');
            }
        });
    });

    // Dynamic selects for model/size/quality
    function rebuildOpenAIOptions() {
        var model = $('#openai_model_select').val() || 'gpt-image-1-mini';
        var sizesByModel = {
            'gpt-image-1-mini': ['1024x1024','1536x1024','1024x1536'],
            'gpt-image-1': ['1024x1024','1536x1024','1024x1536'],
            'dall-e-3': ['1024x1024','1792x1024','1024x1792'],
            'dall-e-2': ['256x256','512x512','1024x1024']
        };
        var qualitiesByModel = {
            'gpt-image-1-mini': ['low','medium','high'],
            'gpt-image-1': ['low','medium','high'],
            'dall-e-3': ['standard','hd'],
            'dall-e-2': []
        };

        var defSize = '<?= esc(getenv('OPENAI_DEFAULT_SIZE') ?: ''); ?>';
        var defQuality = '<?= esc(getenv('OPENAI_DEFAULT_QUALITY') ?: ''); ?>';

        var $size = $('#openai_size_select');
        var $quality = $('#openai_quality_select');
        $size.empty();
        (sizesByModel[model] || []).forEach(function (sz) {
            var sel = (defSize && defSize === sz) ? 'selected' : '';
            $size.append('<option value="'+sz+'" '+sel+'>'+sz+'</option>');
        });
        if (!$size.val()) { $size.prop('selectedIndex', ($size.children().length>0 ? $size.children().length-1 : 0)); }

        $quality.empty();
        var qList = qualitiesByModel[model] || [];
        if (qList.length === 0) {
            $quality.append('<option value="">(não aplicável)</option>');
        } else {
            qList.forEach(function (q) {
                var sel = (defQuality && defQuality === q) ? 'selected' : '';
                $quality.append('<option value="'+q+'" '+sel+'>'+q.toUpperCase()+'</option>');
            });
            if (!$quality.val()) { $quality.prop('selectedIndex', qList.length-1); }
        }
    }

    $('#openai_model_select').on('change', function () {
        // Reset defaults on change to ensure valid combo
        rebuildOpenAIOptions();
    });

    // initial build
    rebuildOpenAIOptions();
</script>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('preferences'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/preferencesPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('pagination_number_posts'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="number" class="form-control" name="pagination_per_page" value="<?= $generalSettings->pagination_per_page; ?>" min="1" max="3000" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('file_manager_show_files'); ?></label>
                            </div>
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="number" class="form-control" name="file_manager_show_files" value="<?= $generalSettings->file_manager_show_files; ?>" min="1" max="3000" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('date_format'); ?></label>
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 col-option">
                                <input type="text" class="form-control" name="date_format" placeholder="<?= trans('date_format'); ?>" value="<?= $generalSettings->date_format; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('maintenance_mode'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="maintenance_mode_status" value="1" id="maintenance_mode_status_1" class="square-purple" <?= $generalSettings->maintenance_mode_status == 1 ? 'checked' : ''; ?>>
                                <label for="maintenance_mode_status_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="maintenance_mode_status" value="0" id="maintenance_mode_status_2" class="square-purple" <?= $generalSettings->maintenance_mode_status != 1 ? 'checked' : ''; ?>>
                                <label for="maintenance_mode_status_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="maintenance_mode_title" placeholder="<?= trans('title'); ?>" value="<?= $generalSettings->maintenance_mode_title; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?></label>
                        <textarea class="form-control text-area" name="maintenance_mode_description" placeholder="<?= trans('description'); ?>" style="min-height: 100px;"><?= $generalSettings->maintenance_mode_description; ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('approve_added_user_posts'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="approve_added_user_posts" value="1" id="approve_added_user_posts_1" class="square-purple" <?= $generalSettings->approve_added_user_posts == 1 ? 'checked' : ''; ?>>
                                <label for="approve_added_user_posts_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="approve_added_user_posts" value="0" id="approve_added_user_posts_2" class="square-purple" <?= $generalSettings->approve_added_user_posts != 1 ? 'checked' : ''; ?>>
                                <label for="approve_added_user_posts_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('comment_system'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="comment_system" value="1" id="comment_system_1" class="square-purple" <?= $generalSettings->comment_system == 1 ? 'checked' : ''; ?>>
                                <label for="comment_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="comment_system" value="0" id="comment_system_2" class="square-purple" <?= $generalSettings->comment_system != 1 ? 'checked' : ''; ?>>
                                <label for="comment_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('comment_approval_system'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="comment_approval_system" value="1" id="comment_approval_system_1" class="square-purple" <?= $generalSettings->comment_approval_system == 1 ? 'checked' : ''; ?>>
                                <label for="comment_approval_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="comment_approval_system" value="0" id="comment_approval_system_2" class="square-purple" <?= $generalSettings->comment_approval_system != 1 ? 'checked' : ''; ?>>
                                <label for="comment_approval_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('emoji_reactions'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="emoji_reactions" value="1" id="emoji_reactions_1" class="square-purple" <?= $generalSettings->emoji_reactions == 1 ? 'checked' : ''; ?>>
                                <label for="emoji_reactions_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="emoji_reactions" value="0" id="emoji_reactions_2" class="square-purple" <?= $generalSettings->emoji_reactions != 1 ? 'checked' : ''; ?>>
                                <label for="emoji_reactions_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('show_hits'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="show_hits" value="1" id="show_hits_1" class="square-purple" <?= $generalSettings->show_hits == 1 ? 'checked' : ''; ?>>
                                <label for="show_hits_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="show_hits" value="0" id="show_hits_2" class="square-purple" <?= $generalSettings->show_hits != 1 ? 'checked' : ''; ?>>
                                <label for="show_hits_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('reward_system'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="reward_system" value="1" id="reward_system_1" class="square-purple" <?= $generalSettings->reward_system == 1 ? 'checked' : ''; ?>>
                                <label for="reward_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="reward_system" value="0" id="reward_system_2" class="square-purple" <?= $generalSettings->reward_system != 1 ? 'checked' : ''; ?>>
                                <label for="reward_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('registration_system'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="registration_system" value="1" id="registration_system_1" class="square-purple" <?= $generalSettings->registration_system == 1 ? 'checked' : ''; ?>>
                                <label for="registration_system_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="registration_system" value="0" id="registration_system_2" class="square-purple" <?= $generalSettings->registration_system != 1 ? 'checked' : ''; ?>>
                                <label for="registration_system_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('email_verification'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="email_verification" value="1" id="email_verification_1" class="square-purple" <?= $generalSettings->email_verification == 1 ? 'checked' : ''; ?>>
                                <label for="email_verification_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="email_verification" value="0" id="email_verification_2" class="square-purple" <?= $generalSettings->email_verification != 1 ? 'checked' : ''; ?>>
                                <label for="email_verification_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('audio_enabled'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="audio_enabled" value="1" id="audio_enabled_1" class="square-purple" <?= $generalSettings->audio_enabled == 1 ? 'checked' : ''; ?>>
                                <label for="audio_enabled_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="audio_enabled" value="0" id="audio_enabled_2" class="square-purple" <?= $generalSettings->audio_enabled != 1 ? 'checked' : ''; ?>>
                                <label for="audio_enabled_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <label><?= trans('rss_content_page'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="rss_content_type" value="content" id="rss_content_type_1" class="square-purple" <?= $generalSettings->rss_content_type == 'content' ? 'checked' : ''; ?>>
                                <label for="rss_content_type_1" class="option-label"><?= trans('show_full_content'); ?></label>
                            </div>
                            <div class="col-sm-3 col-xs-12 col-option">
                                <input type="radio" name="rss_content_type" value="summary" id="rss_content_type_2" class="square-purple" <?= $generalSettings->rss_content_type != 'content' ? 'checked' : ''; ?>>
                                <label for="rss_content_type_2" class="option-label"><?= trans('show_summary'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">OpenAI (Imagens)</h3>
            </div>
            <form action="<?= base_url('Admin/preferencesPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="form" value="openai_env">
                <div class="box-body">
                    <div class="form-group">
                        <label>OPENAI_API_KEY</label>
                        <input id="openai_api_key_input" type="text" class="form-control" name="openai_api_key" placeholder="sk-..." value="<?= esc(getenv('OPENAI_API_KEY') ?: ''); ?>">
                        <small class="text-muted">Usado para gerar imagens (Web Stories). Salvo no arquivo .env.</small>
                        <div style="margin-top:8px;">
                            <button type="button" id="btn-validate-openai-key" class="btn btn-default btn-sm">
                                <i class="fa fa-check"></i> Validar chave
                            </button>
                            <span id="openai_key_status" style="margin-left:10px;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Modelo padrão</label>
                        <?php $defModel = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini'; ?>
                        <select id="openai_model_select" class="form-control" name="openai_model">
                            <option value="gpt-image-1-mini" <?= ($defModel=='gpt-image-1-mini'?'selected':''); ?>>GPT-Image-1 Mini (cost-efficient)</option>
                            <option value="gpt-image-1" <?= ($defModel=='gpt-image-1'?'selected':''); ?>>GPT-Image-1</option>
                            <option value="dall-e-3" <?= ($defModel=='dall-e-3'?'selected':''); ?>>DALL-E 3</option>
                            <option value="dall-e-2" <?= ($defModel=='dall-e-2'?'selected':''); ?>>DALL-E 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tamanho padrão</label>
                        <?php $defSize = getenv('OPENAI_DEFAULT_SIZE') ?: ''; ?>
                        <select id="openai_size_select" class="form-control" name="openai_size"></select>
                    </div>
                    <div class="form-group">
                        <label>Qualidade padrão</label>
                        <?php $defQuality = getenv('OPENAI_DEFAULT_QUALITY') ?: ''; ?>
                        <select id="openai_quality_select" class="form-control" name="openai_quality"></select>
                    </div>
                    <div class="form-group">
                        <label>Estilo de Marca (para imagens)</label>
                        <?php $defBrandStyle = getenv('OPENAI_BRAND_STYLE') ?: ''; ?>
                        <textarea class="form-control" name="openai_brand_style" rows="2" placeholder="Ex.: fotografia editorial premium, minimalista, paleta fria, alto contraste, luz natural"><?= esc($defBrandStyle); ?></textarea>
                        <div style="margin-top:8px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                            <small class="text-muted" style="margin-right:10px;">Opcional. Complementa o prompt das imagens (Web Stories e capas) com um estilo visual da sua marca.</small>
                            <button type="button" id="btn-fill-brand-style" class="btn btn-default btn-sm">
                                <i class="fa fa-magic"></i> Sugerir com cores do tema
                            </button>
                            <button type="button" id="btn-clear-brand-style" class="btn btn-default btn-sm">
                                <i class="fa fa-eraser"></i> Limpar
                            </button>
                        </div>
                    </div>
                    <?php
                        $chipColors = [];
                        if (!empty($activeTheme->theme_color)) { $chipColors[] = $activeTheme->theme_color; }
                        if (!empty($activeTheme->block_color)) { $chipColors[] = $activeTheme->block_color; }
                        if (!empty($activeTheme->mega_menu_color) && $activeTheme->theme != 'classic') { $chipColors[] = $activeTheme->mega_menu_color; }
                    ?>
                    <?php if (!empty($chipColors)): ?>
                        <div class="form-group">
                            <label>Cores do tema consideradas</label>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <?php foreach ($chipColors as $cc): ?>
                                    <div title="<?= esc($cc); ?>" style="display:flex;align-items:center;gap:6px;border:1px solid #ddd;border-radius:14px;padding:4px 8px;">
                                        <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background: <?= esc($cc); ?>;border:1px solid rgba(0,0,0,.15);"></span>
                                        <span style="font-size:12px;color:#555;">
                                            <?= esc($cc); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Essas cores da paleta do tema são referenciadas automaticamente nos prompts de imagem.</small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Pré-visualização do prompt base</label>
                        <div id="brand-prompt-preview" style="background:#f7f7f7;border:1px solid #e0e0e0;border-radius:4px;padding:10px;color:#444;font-size:13px;line-height:1.4;white-space:pre-wrap;"></div>
                        <small class="text-muted">Prévia gerada a partir do Estilo de Marca e das cores do tema. O tópico específico (título/assunto) é adicionado dinamicamente ao gerar cada imagem.</small>
                    </div>

                    <div class="form-group">
                        <label>Pré-visualização do prompt — Capa de Artigo (3:2, paisagem)</label>
                        <div id="brand-prompt-preview-cover" style="background:#f7f7f7;border:1px solid #e0e0e0;border-radius:4px;padding:10px;color:#444;font-size:13px;line-height:1.4;white-space:pre-wrap;"></div>
                        <small class="text-muted">Esse é o formato usado pelo botão “Gerar imagem de capa com IA”.</small>
                    </div>

                    <div class="form-group">
                        <label>Pré-visualização do prompt — Web Stories (Vertical)</label>
                        <div id="brand-prompt-preview-webstory" style="background:#f7f7f7;border:1px solid #e0e0e0;border-radius:4px;padding:10px;color:#444;font-size:13px;line-height:1.4;white-space:pre-wrap;"></div>
                        <small class="text-muted">Esse é o formato usado para gerar imagens das páginas de Web Stories.</small>
                    </div>

                    <script>
                        (function(){
                            function dedupColors(arr){
                                var out = [];
                                arr.forEach(function(c){ if (c && out.indexOf(c) === -1) out.push(c); });
                                return out;
                            }
                            function composeBasePrompt(style, colors){
                                var parts = [];
                                style = (style || '').trim();
                                colors = colors || [];
                                if (style) {
                                    parts.push(style + ' style');
                                }
                                if (colors.length){
                                    parts.push('harmonize with brand color palette (' + colors.join(' ') + ')');
                                }
                                parts.push('photorealistic, realistic textures, natural lighting, shallow depth of field');
                                parts.push('engaging yet clean composition, professional look');
                                parts.push('no text, no watermark, leave clear negative space in the center for text overlay');
                                parts.push('safe margins around central area, avoid cluttered background');
                                return parts.join(', ');
                            }
                            function composeCoverPrompt(style, colors){
                                style = (style || '').trim();
                                colors = colors || [];
                                var brandColors = colors.length ? '(' + colors.join(' ') + ')' : '';
                                var parts = [];
                                parts.push('Gerar uma imagem de capa editorial, realista e sem texto para um artigo.');
                                parts.push('Tema do artigo: "[TÍTULO DO ARTIGO]".');
                                if (style) {
                                    parts.push('Estilo: ' + style + '; composição limpa, fotográfica, iluminação natural, aspecto moderno e profissional.');
                                } else {
                                    parts.push('Estilo: composição limpa, fotográfica, iluminação natural, aspecto moderno e profissional.');
                                }
                                if (brandColors) {
                                    parts.push('Cores: utilizar e harmonizar com as cores da marca ' + brandColors + '.');
                                }
                                parts.push('Requisitos: formato paisagem 3:2, foco centralizado, deixar margens de segurança nas bordas, sem textos, sem logos, sem marcas d’água, alta qualidade.');
                                return parts.join(' ');
                            }
                            function composeWebStoryPrompt(style, colors){
                                style = (style || '').trim();
                                colors = colors || [];
                                var brandColors = colors.length ? '(' + colors.join(' ') + ')' : '';
                                var parts = [];
                                parts.push('Imagem para Web Story (vertical) para a página: "[TÍTULO DA PÁGINA]".');
                                if (style) {
                                    parts.push(style + ' style.');
                                }
                                if (brandColors) {
                                    parts.push('Harmonize com a paleta da marca ' + brandColors + '.');
                                }
                                parts.push('Fotorealista, texturas realistas, luz natural, profundidade de campo suave.');
                                parts.push('Composição limpa e envolvente, formato vertical amigável ao mobile, aspecto profissional.');
                                parts.push('Sem texto e sem marca d’água, manter margens de segurança nas bordas e área central limpa para sobreposição de texto.');
                                return parts.join(' ');
                            }
                            function rebuildBrandPromptPreview(){
                                try {
                                    var style = ($('textarea[name="openai_brand_style"]').val() || '').trim();
                                    var colors = [];
                                    if (VrConfig && VrConfig.themeColor) colors.push(VrConfig.themeColor);
                                    if (VrConfig && VrConfig.blockColor) colors.push(VrConfig.blockColor);
                                    if (VrConfig && VrConfig.megaColor) colors.push(VrConfig.megaColor);
                                    colors = dedupColors(colors);
                                    var preview = composeBasePrompt(style, colors);
                                    $('#brand-prompt-preview').text(preview);
                                    $('#brand-prompt-preview-cover').text(composeCoverPrompt(style, colors));
                                    $('#brand-prompt-preview-webstory').text(composeWebStoryPrompt(style, colors));
                                } catch(e) {}
                            }
                            // events
                            $(document).on('input', 'textarea[name="openai_brand_style"]', rebuildBrandPromptPreview);
                            $(document).ready(rebuildBrandPromptPreview);
                            $(document).on('click', '#btn-fill-brand-style', function(){
                                try {
                                    var colors = [];
                                    if (VrConfig && VrConfig.themeColor) { colors.push(VrConfig.themeColor); }
                                    if (VrConfig && VrConfig.blockColor) { colors.push(VrConfig.blockColor); }
                                    if (VrConfig && VrConfig.megaColor) { colors.push(VrConfig.megaColor); }
                                    // Dedup
                                    colors = colors.filter(function(c, i){ return c && colors.indexOf(c) === i; });

                                    var primary = colors.length ? colors[0] : '';
                                    var accents = colors.slice(1);

                                    var base = 'fotografia editorial premium, minimalista, luz natural, paleta da marca';
                                    if (primary) {
                                        base += ', predominância de ' + primary;
                                    }
                                    if (accents.length) {
                                        base += ', acentos ' + accents.join(' ');
                                    }
                                    base += ', alto contraste';

                                    var $ta = $('textarea[name="openai_brand_style"]');
                                    var current = ($ta.val() || '').trim();
                                    // Replace or append smartly
                                    if (!current) {
                                        $ta.val(base);
                                    } else {
                                        $ta.val(base);
                                    }
                                    try { showToast('success', 'Sugerido', 'Estilo preenchido com as cores do tema.'); } catch(e) {}
                                    rebuildBrandPromptPreview();
                                } catch(e) {
                                    alert('Não foi possível sugerir o estilo agora.');
                                }
                            });
                            $(document).on('click', '#btn-clear-brand-style', function(){
                                try {
                                    var $ta = $('textarea[name="openai_brand_style"]');
                                    $ta.val('');
                                    try { showToast('info', 'Limpo', 'Estilo de Marca limpo.'); } catch(e) {}
                                    rebuildBrandPromptPreview();
                                } catch(e) {
                                    alert('Não foi possível limpar agora.');
                                }
                            });
                        })();
                    </script>
                    <div class="form-group">
                        <label>Modelo de texto (Chat)</label>
                        <?php $defTextModel = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini'; ?>
                        <select id="openai_text_model_select" class="form-control" name="openai_text_model">
                            <option value="gpt-5.4-mini" <?= ($defTextModel=='gpt-5.4-mini'?'selected':''); ?>>gpt-5.4-mini</option>
                            <option value="gpt-5" <?= ($defTextModel=='gpt-5'?'selected':''); ?>>gpt-5</option>
                            <option value="gpt-4.1-mini" <?= ($defTextModel=='gpt-4.1-mini'?'selected':''); ?>>gpt-4.1-mini</option>
                            <option value="gpt-4o-mini" <?= ($defTextModel=='gpt-4o-mini'?'selected':''); ?>>gpt-4o-mini</option>
                        </select>
                        <small class="text-muted">Usado para gerar o texto das Web Stories.</small>
                    </div>
                    <div class="form-group">
                        <label>Modelo de texto (fallback)</label>
                        <?php $defTextFallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini'; ?>
                        <select id="openai_text_fallback_model_select" class="form-control" name="openai_text_fallback_model">
                            <option value="gpt-4.1-mini" <?= ($defTextFallback=='gpt-4.1-mini'?'selected':''); ?>>gpt-4.1-mini</option>
                            <option value="gpt-5.4-mini" <?= ($defTextFallback=='gpt-5.4-mini'?'selected':''); ?>>gpt-5.4-mini</option>
                            <option value="gpt-5" <?= ($defTextFallback=='gpt-5'?'selected':''); ?>>gpt-5</option>
                            <option value="gpt-4o-mini" <?= ($defTextFallback=='gpt-4o-mini'?'selected':''); ?>>gpt-4o-mini</option>
                        </select>
                        <small class="text-muted">Usado automaticamente se o modelo principal falhar/estourar timeout.</small>
                    </div>
                    <div class="form-group">
                        <label>Timeout do texto (segundos)</label>
                        <?php $defTextTimeout = getenv('OPENAI_TEXT_TIMEOUT') ?: '45'; ?>
                        <input type="number" class="form-control" name="openai_text_timeout" min="10" max="120" value="<?= esc($defTextTimeout); ?>">
                        <small class="text-muted">Tempo máximo de espera em cada tentativa (padrão 45s).</small>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('ai_content_creator'); ?>&nbsp;(<?= trans("ai_writer"); ?>)</h3>
            </div>
            <form action="<?= base_url('Admin/aiWriterPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <?php $aiWriter = aiWriter(); ?>
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('status', 1, 0, trans("enable"), trans("disable"), $aiWriter->status); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans('api_key'); ?></label>
                        <input id="aiwriter_api_key_input" type="text" class="form-control" name="api_key" placeholder="<?= trans('api_key'); ?>" value="<?= esc($aiWriter->apiKey); ?>" required>
                        <div style="margin-top:8px;">
                            <button type="button" id="btn-validate-aiwriter-key" class="btn btn-default btn-sm">
                                <i class="fa fa-check"></i> Validar chave
                            </button>
                            <span id="aiwriter_key_status" style="margin-left:10px;"></span>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
