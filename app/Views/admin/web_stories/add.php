<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_web_story'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('web-stories'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;<?= trans('back'); ?>
                    </a>
                </div>
            </div>

            <form action="<?= adminUrl('web-stories/add'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?= trans('title'); ?> *</label>
                                <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" 
                                       value="<?= old('title'); ?>" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?= trans('description'); ?></label>
                                <textarea class="form-control" name="description" rows="3" 
                                          placeholder="<?= trans('description'); ?>"><?= old('description'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?= trans('language'); ?></label>
                                <select class="form-control" name="lang_id">
                                    <?php foreach ($activeLanguages as $language): ?>
                                        <option value="<?= $language->id; ?>" 
                                                <?= (old('lang_id') == $language->id || (!old('lang_id') && $language->id == $activeLang->id)) ? 'selected' : ''; ?>>
                                            <?= esc($language->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?= trans('category'); ?></label>
                                <select class="form-control" name="category_id">
                                    <option value=""><?= trans('select_category'); ?></option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category->id; ?>" 
                                                    <?= old('category_id') == $category->id ? 'selected' : ''; ?>>
                                                <?= esc($category->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?= trans('link_url'); ?></label>
                                <input type="url" class="form-control" name="link_url" 
                                       placeholder="https://example.com" value="<?= old('link_url'); ?>">
                                <small class="text-muted"><?= trans('optional_external_link'); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload/Generation Section -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label><?= trans('image'); ?></label>
                                
                                <!-- Tabs for upload/generate -->
                                <ul class="nav nav-tabs" id="image-tabs">
                                    <li class="active">
                                        <a href="#upload-tab" data-toggle="tab">
                                            <i class="fa fa-upload"></i> <?= trans('upload_image'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#generate-tab" data-toggle="tab">
                                            <i class="fa fa-magic"></i> <?= trans('generate_with_ai'); ?>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" style="border: 1px solid #ddd; border-top: none; padding: 15px;">
                                    <!-- Upload Tab -->
                                    <div class="tab-pane active" id="upload-tab">
                                        <div class="upload-area" style="border: 2px dashed #ddd; padding: 30px; text-align: center; margin-bottom: 15px;">
                                            <input type="file" name="image" id="image-upload" accept="image/*" style="display: none;">
                                            <div id="upload-placeholder">
                                                <i class="fa fa-cloud-upload fa-3x text-muted"></i>
                                                <p class="text-muted"><?= trans('drag_drop_or_click_to_upload'); ?></p>
                                                <button type="button" class="btn btn-primary" onclick="$('#image-upload').click();">
                                                    <?= trans('choose_file'); ?>
                                                </button>
                                            </div>
                                            <div id="upload-preview" style="display: none;">
                                                <img id="preview-image" src="" alt="Preview" style="max-width: 300px; max-height: 200px;">
                                                <br><br>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="clearUpload();">
                                                    <?= trans('remove'); ?>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= trans('supported_formats'); ?>: JPG, PNG, GIF, WEBP (<?= trans('max_size'); ?>: 10MB)
                                        </small>
                                    </div>

                                    <!-- Generate Tab -->
                                    <div class="tab-pane" id="generate-tab">
                                        <?php if ($openaiApiKey !== 'Not configured'): ?>
                                            <div class="form-group">
                                                <label><?= trans('image_prompt'); ?></label>
                                                <textarea class="form-control" id="ai-prompt" rows="3" 
                                                          placeholder="<?= trans('describe_image_to_generate'); ?>"></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label><?= trans('style'); ?> (<?= trans('optional'); ?>)</label>
                                                <select class="form-control" id="ai-style">
                                                    <option value=""><?= trans('default'); ?></option>
                                                    <option value="photorealistic">Photorealistic</option>
                                                    <option value="digital art">Digital Art</option>
                                                    <option value="illustration">Illustration</option>
                                                    <option value="cartoon">Cartoon</option>
                                                    <option value="abstract">Abstract</option>
                                                    <option value="minimalist">Minimalist</option>
                                                </select>
                                            </div>

                                            <button type="button" class="btn btn-success" id="generate-btn">
                                                <i class="fa fa-magic"></i> <?= trans('generate_image'); ?>
                                            </button>

                                            <div id="generation-result" style="display: none; margin-top: 15px;">
                                                <img id="generated-image" src="" alt="Generated" style="max-width: 300px; max-height: 200px;">
                                                <br><br>
                                                <button type="button" class="btn btn-success btn-sm" onclick="useGeneratedImage();">
                                                    <?= trans('use_this_image'); ?>
                                                </button>
                                                <button type="button" class="btn btn-default btn-sm" onclick="clearGenerated();">
                                                    <?= trans('generate_new'); ?>
                                                </button>
                                            </div>

                                            <div id="generation-loading" style="display: none; margin-top: 15px; text-align: center;">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p><?= trans('generating_image'); ?>...</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-warning"></i>
                                                <?= trans('openai_api_key_not_configured'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?= trans('display_order'); ?></label>
                                <input type="number" class="form-control" name="display_order" 
                                       value="<?= old('display_order') ?: '1'; ?>" min="1">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?= trans('status'); ?></label>
                                <select class="form-control" name="is_active">
                                    <option value="1" <?= old('is_active') == '1' ? 'selected' : ''; ?>><?= trans('active'); ?></option>
                                    <option value="0" <?= old('is_active') == '0' ? 'selected' : ''; ?>><?= trans('inactive'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for AI generation -->
                    <input type="hidden" name="is_generated" id="is_generated" value="0">
                    <input type="hidden" name="generation_prompt" id="generation_prompt" value="">
                    <input type="hidden" name="generated_image_url" id="generated_image_url" value="">
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fa fa-check"></i>&nbsp;&nbsp;<?= trans('save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle file upload preview
    $('#image-upload').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#upload-placeholder').hide();
                $('#upload-preview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle drag and drop
    $('.upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    $('.upload-area').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
    });

    $('.upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            var file = files[0];
            if (file.type.match('image.*')) {
                $('#image-upload')[0].files = files;
                $('#image-upload').trigger('change');
            }
        }
    });

    // Prevent multiple form submissions
    let isSubmitting = false;
    $('form').on('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
        $('#submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Salvando...');
    });

    // AI Image Generation
    $('#generate-btn').click(function() {
        var prompt = $('#ai-prompt').val().trim();
        if (!prompt) {
            alert('<?= trans('please_enter_prompt'); ?>');
            return;
        }

        var style = $('#ai-style').val();
        
        $('#generation-loading').show();
        $('#generate-btn').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('WebStories/generateImage'); ?>',
            method: 'POST',
            data: {
                prompt: prompt,
                style: style,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                $('#generation-loading').hide();
                $('#generate-btn').prop('disabled', false);
                
                if (response.success) {
                    $('#generated-image').attr('src', response.image_url);
                    $('#generation-result').show();
                    
                    // Store generation data
                    window.generatedImageData = {
                        url: response.image_url,
                        prompt: response.prompt
                    };
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $('#generation-loading').hide();
                $('#generate-btn').prop('disabled', false);
                alert('<?= trans('error_generating_image'); ?>');
            }
        });
    });
});

function clearUpload() {
    $('#image-upload').val('');
    $('#upload-preview').hide();
    $('#upload-placeholder').show();
}

function useGeneratedImage() {
    if (window.generatedImageData) {
        $('#is_generated').val('1');
        $('#generation_prompt').val(window.generatedImageData.prompt);
        $('#generated_image_url').val(window.generatedImageData.url);
        
        // Clear file upload
        clearUpload();
        
        alert('<?= trans('generated_image_will_be_used'); ?>');
    }
}

function clearGenerated() {
    $('#generation-result').hide();
    $('#is_generated').val('0');
    $('#generation_prompt').val('');
    $('#generated_image_url').val('');
    window.generatedImageData = null;
}
</script>

<style>
.upload-area.drag-over {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.nav-tabs > li > a {
    border-radius: 4px 4px 0 0;
}

#image-tabs {
    margin-bottom: 0;
}

.tab-content {
    background: #fff;
    border-radius: 0 0 4px 4px;
}

#generation-loading i {
    color: #007bff;
}
</style>