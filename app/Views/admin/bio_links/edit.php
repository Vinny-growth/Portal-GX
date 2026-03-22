<?= view('admin/includes/_header'); ?>
<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title">Editar Bio Link</h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('bio-links'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Voltar
                    </a>
                </div>
            </div>

            <form action="<?= adminUrl('bio-links/edit/' . $link['id']); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label for="title">Título do Link *</label>
                        <input type="text" name="title" id="title" class="form-control" required 
                               placeholder="Ex: Instagram, YouTube, Site Oficial..." 
                               value="<?= old('title', $link['title']); ?>">
                        <small class="text-muted">Título que aparecerá no botão</small>
                    </div>

                    <div class="form-group">
                        <label for="url">URL *</label>
                        <input type="url" name="url" id="url" class="form-control" required 
                               placeholder="https://..." 
                               value="<?= old('url', $link['url']); ?>">
                        <small class="text-muted">Link completo incluindo https://</small>
                    </div>

                    <div class="form-group">
                        <label for="icon">Ícone (opcional)</label>
                        <input type="text" name="icon" id="icon" class="form-control" 
                               placeholder="Ex: fa fa-instagram, fa fa-youtube, fa fa-globe..." 
                               value="<?= old('icon', $link['icon']); ?>">
                        <small class="text-muted">
                            Classe CSS do ícone (Font Awesome). 
                            <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Ver ícones disponíveis</a>
                        </small>
                        <div class="icon-preview" style="margin-top: 10px; <?= empty($link['icon']) ? 'display: none;' : ''; ?>">
                            <i id="icon-display" class="<?= esc($link['icon']); ?>"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_color">Cor do Botão</label>
                                <div class="input-group colorpicker-component">
                                    <input type="text" name="button_color" id="button_color" class="form-control" 
                                           value="<?= old('button_color', $link['button_color']); ?>">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="text_color">Cor do Texto</label>
                                <div class="input-group colorpicker-component">
                                    <input type="text" name="text_color" id="text_color" class="form-control" 
                                           value="<?= old('text_color', $link['text_color']); ?>">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" value="1" 
                                       <?= old('is_active', $link['is_active']) ? 'checked' : ''; ?>>
                                Link ativo (visível na página pública)
                            </label>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="form-group">
                        <label>Preview do Botão</label>
                        <div class="bio-link-preview">
                            <a href="#" id="preview-button" class="btn" style="display: block; margin: 10px 0; text-decoration: none; border-radius: 25px; padding: 12px 20px; text-align: center; font-weight: 600;">
                                <i id="preview-icon"></i>
                                <span id="preview-title"></span>
                            </a>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="form-group">
                        <label>Estatísticas</label>
                        <div class="well">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Ordem de Exibição:</strong> <?= $link['display_order']; ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total de Cliques:</strong> <?= $link['click_count']; ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($link['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>&nbsp;&nbsp;Salvar Alterações
                    </button>
                    <a href="<?= adminUrl('bio-links'); ?>" class="btn btn-default">
                        <i class="fa fa-times"></i>&nbsp;&nbsp;Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Color pickers
    $('.colorpicker-component').colorpicker();
    
    // Icon preview
    $('#icon').on('input', function() {
        var iconClass = $(this).val();
        if (iconClass) {
            $('#icon-display').attr('class', iconClass);
            $('.icon-preview').show();
        } else {
            $('.icon-preview').hide();
        }
        updatePreview();
    });
    
    // Live preview update
    $('#title, #button_color, #text_color').on('input', updatePreview);
    $('.colorpicker-component').on('changeColor', updatePreview);
    
    function updatePreview() {
        var title = $('#title').val() || 'Título do Link';
        var buttonColor = $('#button_color').val() || '#007bff';
        var textColor = $('#text_color').val() || '#ffffff';
        var icon = $('#icon').val();
        
        $('#preview-title').text(title);
        $('#preview-button').css({
            'background-color': buttonColor,
            'color': textColor,
            'border': '2px solid ' + buttonColor
        });
        
        if (icon) {
            $('#preview-icon').attr('class', icon).show();
        } else {
            $('#preview-icon').hide();
        }
    }
    
    // Initial preview
    updatePreview();
});
</script>

<?= view('admin/includes/_footer'); ?>