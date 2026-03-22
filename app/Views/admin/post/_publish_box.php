<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('publish'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <?php if (!empty($post)): ?>
            <?php if ($post->status == 0): ?>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="scheduled_post" value="1" id="cb_scheduled" class="custom-control-input" <?= $post->is_scheduled == 1 ? 'checked' : ''; ?>>
                        <label for="cb_scheduled" class="custom-control-label"><?= trans("scheduled_post"); ?></label>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="scheduled_post" value="<?= $post->is_scheduled; ?>">
            <?php endif; ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <label><?= trans('date_publish'); ?></label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type='text' class="form-control" name="date_published" placeholder="<?= trans("date_publish"); ?>" value="<?= $post->created_at; ?>">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php if ($post->status == 0): ?>
                    <button type="submit" name="publish" value="1" class="btn btn-warning pull-right m-l-10" onclick="allowSubmitForm = true;"><?= trans('publish'); ?></button>
                <?php endif; ?>
                <button type="submit" name="publish" value="0" class="btn btn-primary pull-right" onclick="allowSubmitForm = true;"><?= trans('save_changes'); ?></button>
            </div>
            
            <!-- Web Stories Generator Button (only for saved articles) -->
            <?php if (isset($post) && !empty($post->id) && isset($postType) && $postType === 'article'): ?>
                <div class="form-group" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <h5><i class="fa fa-magic"></i> Web Stories</h5>
                    <p class="text-muted small">Gere automaticamente Web Stories a partir deste artigo usando IA</p>
                    <button type="button" class="btn btn-info btn-block" onclick="testConnection()" style="margin-bottom: 10px;">
                        <i class="fa fa-plug"></i> Testar Conexão
                    </button>
                    <button type="button" class="btn btn-success btn-block" id="generate-web-stories-btn" onclick="generateWebStories(<?= $post->id; ?>)">
                        <i class="fa fa-magic"></i> Criar Web Stories
                    </button>
                    <div id="web-stories-loading" style="display: none; text-align: center; margin-top: 10px;">
                        <i class="fa fa-spinner fa-spin"></i> Gerando Web Stories...
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="scheduled_post" value="1" id="cb_scheduled" class="custom-control-input">
                    <label for="cb_scheduled" class="custom-control-label"><?= trans("scheduled_post"); ?></label>
                </div>
            </div>
            <div id="date_published_content" class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <label><?= trans('date_publish'); ?></label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type='text' class="form-control" name="date_published" id="input_date_published" placeholder="<?= trans("date_publish"); ?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="status" value="1" class="btn btn-primary pull-right" onclick="allowSubmitForm = true;"><?= trans('btn_submit'); ?></button>
                <button type="submit" name="status" value="0" class="btn btn-warning btn-draft pull-right" onclick="allowSubmitForm = true;"><?= trans('save_draft'); ?></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function generateWebStories(postId) {
    console.log('generateWebStories called with postId:', postId);
    
    // Prevent multiple clicks
    const btn = document.getElementById('generate-web-stories-btn');
    const loading = document.getElementById('web-stories-loading');
    
    if (btn.disabled) return;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Gerando...';
    loading.style.display = 'block';
    
    // Make AJAX call with timeout
    const url = '<?= base_url('WebStories/generateFromArticle'); ?>';
    console.log('Calling URL:', url);
    
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 120000); // 2 minutes timeout
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            'post_id': postId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }),
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        loading.style.display = 'none';
        
        if (data.success) {
            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message);
            } else {
                alert(data.message);
            }
            
            // Start image generation step-by-step for reliability
            if (data.web_story_id) {
                startImageGeneration(data.web_story_id, postId);
            }

            // Redirect to edit web story (open in nova aba)
            if (data.redirect_url) {
                setTimeout(() => {
                    window.open(data.redirect_url, '_blank');
                }, 1000);
            }
            
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-magic"></i> Criar Web Stories';
        } else {
            // Show error message
            if (typeof toastr !== 'undefined') {
                toastr.error(data.message || 'Erro ao gerar Web Stories');
            } else {
                alert(data.message || 'Erro ao gerar Web Stories');
            }
            
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-magic"></i> Criar Web Stories';
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Error details:', error);
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        loading.style.display = 'none';
        
        let errorMessage = 'Erro de conexão';
        
        if (error.name === 'AbortError') {
            errorMessage = 'Timeout: A operação demorou mais que 2 minutos';
        } else if (error.message.includes('HTTP')) {
            errorMessage = `Erro do servidor: ${error.message}`;
        } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
            errorMessage = 'Erro de rede: Verifique sua conexão';
        }
        
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
        } else {
            alert(errorMessage);
        }
        
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-magic"></i> Criar Web Stories';
    });
}

function startImageGeneration(webStoryId, postId) {
    console.log('Starting step-by-step image generation for Web Story:', webStoryId);
    const doStep = () => {
        fetch('<?= base_url('WebStories/generateImagesStep'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'web_story_id': webStoryId,
                'post_id': postId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        }).then(r => r.json()).then(d => {
            if (d && d.success) {
                if (d.progress) {
                    console.log('Progress', d.progress);
                    if (typeof toastr !== 'undefined') {
                        toastr.info('📸 Imagens: ' + d.progress.with + '/' + d.progress.total + ' (' + d.progress.progress + '%)');
                    }
                }
                if (d.done) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('🎉 Todas as imagens foram geradas!');
                    }
                    return;
                }
                // Agenda próxima etapa
                setTimeout(doStep, 1500);
            } else {
                console.error('Image gen step failed', d);
            }
        }).catch(e => console.error('Image gen error', e));
    };
    doStep();
}

function testConnection() {
    console.log('Testing connection...');
    
    const url = '<?= base_url('WebStories/testConnection'); ?>';
    console.log('Testing URL:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(response => {
        console.log('Test response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Test response data:', data);
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success('Conexão OK: ' + data.message + ' (' + data.timestamp + ')');
            } else {
                alert('Conexão OK: ' + data.message);
            }
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.error('Teste falhou: ' + data.message);
            } else {
                alert('Teste falhou: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Test error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Erro no teste de conexão: ' + error.message);
        } else {
            alert('Erro no teste de conexão: ' + error.message);
        }
    });
}

function monitorImageProgress(webStoryId) {
    console.log('Starting image progress monitoring for Web Story:', webStoryId);
    
    let checkCount = 0;
    const maxChecks = 20; // Maximum 20 checks (10 minutes at 30s intervals)
    
    const progressInterval = setInterval(() => {
        checkCount++;
        
        fetch('<?= base_url('WebStories/checkImageStatus'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'web_story_id': webStoryId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Image progress:', data);
            
            if (data.success) {
                if (data.complete) {
                    clearInterval(progressInterval);
                    if (typeof toastr !== 'undefined') {
                        toastr.success('🎉 ' + data.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.info('📸 ' + data.message + ' (' + data.progress + '%)');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Progress check error:', error);
        });
        
        // Stop checking after maximum attempts
        if (checkCount >= maxChecks) {
            clearInterval(progressInterval);
            console.log('Stopped monitoring after', maxChecks, 'checks');
        }
    }, 30000); // Check every 30 seconds
}
</script>
