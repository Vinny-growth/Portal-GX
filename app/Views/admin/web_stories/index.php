<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('web_stories'); ?></h3>
                </div>
                <div class="right">
                    <div class="btn-group">
                        <a href="<?= adminUrl('web-stories/add'); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;Editor Simples
                        </a>
                        <a href="<?= adminUrl('web-stories/add?advanced=1'); ?>" class="btn btn-primary">
                            <i class="fa fa-magic"></i>&nbsp;&nbsp;Editor Avançado
                        </a>
                        <button type="button" id="btn-generate-all" class="btn btn-info">
                            <i class="fa fa-cogs"></i>&nbsp;&nbsp;Gerar Imagens (IA) para todos
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-12">
                        <div class="btn-group">
                            <button type="button" id="btn-bulk-activate" class="btn btn-default btn-sm" disabled><i class="fa fa-eye"></i> Ativar Selecionados</button>
                            <button type="button" id="btn-bulk-deactivate" class="btn btn-default btn-sm" disabled><i class="fa fa-eye-slash"></i> Desativar Selecionados</button>
                            <button type="button" id="btn-bulk-delete" class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i> Excluir Selecionados</button>
                        </div>
                        <span id="bulk-selected-count" class="text-muted" style="margin-left:10px; vertical-align: middle;">0 selecionados</span>
                    </div>
                </div>
                <div id="bulk-gen-progress" class="alert alert-info" style="display:none;">
                    <i class="fa fa-spinner fa-spin"></i>
                    <span class="text">Iniciando geração em lote...</span>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th width="20"><input type="checkbox" id="chk-all"></th>
                                        <th width="20"><?= trans('id'); ?></th>
                                        <th width="60"><?= trans('image'); ?></th>
                                        <th><?= trans('title'); ?></th>
                                        <th width="120"><?= trans('category'); ?></th>
                                        <th width="120"><?= trans('language'); ?></th>
                                        <th width="80"><?= trans('views'); ?></th>
                                        <th width="80"><?= trans('clicks'); ?></th>
                                        <th width="80"><?= trans('status'); ?></th>
                                        <th width="80"><?= trans('order'); ?></th>
                                        <th width="120"><?= trans('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-stories">
                                    <?php if (!empty($webStories)): ?>
                                        <?php foreach ($webStories as $story): ?>
                                            <tr data-story-id="<?= $story->id; ?>">
                                                <td><input type="checkbox" class="chk-story" value="<?= $story->id; ?>"></td>
                                                <td><?= $story->id; ?></td>
                                                <td>
                                                    <?php if (!empty($story->image_path)): ?>
                                                        <img src="<?= base_url($story->image_path); ?>" alt="<?= esc($story->title); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <?php elseif (!empty($story->image_url)): ?>
                                                        <img src="<?= $story->image_url; ?>" alt="<?= esc($story->title); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <?php else: ?>
                                                        <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fa fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= esc($story->title); ?></strong>
                                                    <?php if (!empty($story->description)): ?>
                                                        <br><small class="text-muted"><?= esc(character_limiter($story->description, 60)); ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($story->is_generated == 1): ?>
                                                        <br><span class="label label-info">AI Generated</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= !empty($story->category_name) ? esc($story->category_name) : '-'; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $lang = getLanguage($story->lang_id);
                                                    echo !empty($lang) ? esc($lang->name) : '-';
                                                    ?>
                                                </td>
                                                <td><?= number_format($story->view_count); ?></td>
                                                <td><?= number_format($story->click_count); ?></td>
                                                <td class="status-cell">
                                                    <?php if ($story->is_active == 1): ?>
                                                        <span class="label label-success"><?= trans('active'); ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-default"><?= trans('inactive'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="drag-handle" style="cursor: move;">
                                                        <i class="fa fa-arrows-v"></i>
                                                    </span>
                                                    <span class="display-order"><?= $story->display_order; ?></span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn bg-purple btn-sm dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu options-dropdown">
                                                            <li>
                                                                <a href="<?= adminUrl('web-stories/edit/' . $story->id); ?>">
                                                                    <i class="fa fa-edit option-icon"></i><?= trans('edit'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="generateImagesFromList(<?= $story->id; ?>)">
                                                                    <i class="fa fa-magic option-icon"></i> Gerar Imagens (IA)
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= adminUrl('web-stories/toggle/' . $story->id); ?>">
                                                                    <i class="fa fa-eye option-icon"></i>
                                                                    <?= $story->is_active == 1 ? trans('deactivate') : trans('activate'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="deleteStory(<?= $story->id; ?>);">
                                                                    <i class="fa fa-trash option-icon"></i><?= trans('delete'); ?>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center"><?= trans('no_records_found'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form action="" method="post" id="form-delete">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
</form>

<script>
$(document).ready(function() {
    // Make stories sortable
    $("#sortable-stories").sortable({
        handle: ".drag-handle",
        update: function(event, ui) {
            var storyIds = [];
            $("#sortable-stories tr").each(function(index) {
                var storyId = $(this).data('story-id');
                if (storyId) {
                    storyIds.push(storyId);
                }
            });
            
            // Update display order numbers
            $("#sortable-stories tr").each(function(index) {
                $(this).find('.display-order').text(index + 1);
            });
            
            // Send AJAX request to update order
            $.ajax({
                url: '<?= base_url('WebStories/adminUpdateOrder'); ?>',
                method: 'POST',
                data: {
                    stories: storyIds,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        showNotification('success', result.message);
                    } else {
                        showNotification('error', result.message);
                    }
                },
                error: function() {
                    showNotification('error', 'Error updating order');
                }
            });
        }
    });
});

// Checkbox master
document.getElementById('chk-all')?.addEventListener('change', function(){
    var checked = this.checked;
    document.querySelectorAll('.chk-story').forEach(function(el){ el.checked = checked; });
    updateBulkUI();
});

function getSelectedIds() {
    var ids = [];
    document.querySelectorAll('.chk-story:checked').forEach(function(el){ ids.push(el.value); });
    return ids;
}

function updateBulkUI() {
    var ids = getSelectedIds();
    var count = ids.length;
    var label = document.getElementById('bulk-selected-count');
    if (label) { label.textContent = count + ' selecionado' + (count === 1 ? '' : 's'); }
    var can = count > 0;
    ['btn-bulk-activate','btn-bulk-deactivate','btn-bulk-delete'].forEach(function(id){
        var b = document.getElementById(id);
        if (b) { b.disabled = !can; }
    });
}

// Track individual checkbox changes
document.addEventListener('change', function(e){
    if (e.target && e.target.classList && e.target.classList.contains('chk-story')) {
        updateBulkUI();
    }
});

// Initialize UI on load
updateBulkUI();

function updateRowStatus(id, active) {
    var row = document.querySelector('tr[data-story-id="' + id + '"]');
    if (!row) return;
    var cell = row.querySelector('td.status-cell');
    if (!cell) {
        // fallback: 9th cell (with selection column added)
        cell = row.querySelector('td:nth-child(9)');
    }
    if (cell) {
        cell.innerHTML = active ? '<span class="label label-success"><?= trans('active'); ?></span>' : '<span class="label label-default"><?= trans('inactive'); ?></span>';
    }
}

// Bulk visibility
document.getElementById('btn-bulk-activate')?.addEventListener('click', function(){ bulkVisibility(1); });
document.getElementById('btn-bulk-deactivate')?.addEventListener('click', function(){ bulkVisibility(0); });

function bulkVisibility(val) {
    var ids = getSelectedIds();
    if (!ids.length) { toastr.warning('Selecione ao menos uma story'); return; }
    $.ajax({
        url: '<?= base_url('WebStories/bulkVisibility'); ?>',
        method: 'POST',
        data: { ids: ids, visibility: val, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        success: function(resp){
            try { if (typeof resp === 'string') resp = JSON.parse(resp); } catch(e) {}
            if (resp && resp.success) {
                ids.forEach(function(id){ updateRowStatus(id, val == 1); });
                toastr.success('Visibilidade atualizada (' + ids.length + ')');
            } else {
                toastr.error((resp && resp.message) ? resp.message : 'Falha ao atualizar');
            }
        },
        error: function(){ toastr.error('Erro na requisição'); }
    });
}

// Bulk delete
document.getElementById('btn-bulk-delete')?.addEventListener('click', function(){
    var ids = getSelectedIds();
    if (!ids.length) { toastr.warning('Selecione ao menos uma story'); return; }
    if (!confirm('Tem certeza que deseja excluir as selecionadas?')) return;
    $.ajax({
        url: '<?= base_url('WebStories/bulkDelete'); ?>',
        method: 'POST',
        data: { ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        success: function(resp){
            try { if (typeof resp === 'string') resp = JSON.parse(resp); } catch(e) {}
            if (resp && resp.success) {
                ids.forEach(function(id){
                    var row = document.querySelector('tr[data-story-id="' + id + '"]');
                    if (row) row.parentNode.removeChild(row);
                });
                toastr.success('Excluídas: ' + (resp.deleted || ids.length));
            } else {
                toastr.error((resp && resp.message) ? resp.message : 'Falha ao excluir');
            }
        },
        error: function(){ toastr.error('Erro na requisição'); }
    });
});

function deleteStory(id) {
    console.log('deleteStory called with ID:', id);
    
    if (confirm('<?= trans('confirm_delete'); ?>')) {
        console.log('User confirmed deletion');
        
        var form = document.getElementById('form-delete');
        if (!form) {
            console.error('Form with id="form-delete" not found!');
            alert('Error: Delete form not found on page');
            return;
        }
        
        var deleteUrl = '<?= adminUrl('web-stories/delete'); ?>/' + id;
        console.log('Setting form action to:', deleteUrl);
        
        form.action = deleteUrl;
        
        console.log('Submitting form...');
        form.submit();
    } else {
        console.log('User cancelled deletion');
    }
}

function showNotification(type, message) {
    // Add your notification display logic here
    if (type === 'success') {
        toastr.success(message);
    } else {
        toastr.error(message);
    }
}

function ensureProgressBadge(row) {
    var badge = row.querySelector('.img-gen-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'label label-info img-gen-badge';
        badge.style.marginLeft = '8px';
        // append next to title strong element if available
        var titleCell = row.children[2];
        if (titleCell) {
            titleCell.appendChild(document.createElement('br'));
            titleCell.appendChild(badge);
        } else {
            row.appendChild(badge);
        }
    }
    return badge;
}

function generateImagesFromList(storyId) {
    var row = document.querySelector('tr[data-story-id="' + storyId + '"]');
    if (!row) { return; }
    var badge = ensureProgressBadge(row);
    badge.textContent = 'Iniciando geração de imagens...';

    var running = true;
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    function step() {
        if (!running) return;
        fetch('<?= base_url('WebStories/generateImagesStep'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'web_story_id': storyId,
                [csrfName]: csrfHash
            })
        }).then(function(r){ return r.json(); }).then(function(d){
            if (d && d.success) {
                if (d.progress) {
                    badge.textContent = 'Imagens: ' + d.progress.with + '/' + d.progress.total + ' (' + d.progress.progress + '%)';
                }
                if (d.cover && d.cover.url) {
                    // Atualiza thumb da lista se ainda não existe ou para refletir a capa mais recente
                    var imgCell = row.querySelector('td:nth-child(2)');
                    if (imgCell) {
                        var img = imgCell.querySelector('img');
                        if (!img) {
                            img = document.createElement('img');
                            img.style.width = '50px';
                            img.style.height = '50px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '4px';
                            imgCell.innerHTML = '';
                            imgCell.appendChild(img);
                        }
                        img.src = d.cover.url + '?t=' + Date.now();
                        img.alt = 'cover';
                    }
                }
                if (d.done) {
                    badge.className = 'label label-success img-gen-badge';
                    badge.textContent = 'Imagens concluídas';
                    if (typeof toastr !== 'undefined') { toastr.success('🎉 Imagens geradas para a story #' + storyId); }
                    running = false;
                    return;
                }
                setTimeout(step, 1500);
            } else {
                badge.className = 'label label-danger img-gen-badge';
                badge.textContent = 'Falha na geração';
                if (typeof toastr !== 'undefined') { toastr.error('Falha ao gerar imagens para a story #' + storyId); }
                running = false;
            }
        }).catch(function(e){
            badge.className = 'label label-danger img-gen-badge';
            badge.textContent = 'Erro: ' + e.message;
            running = false;
        });
    }
    step();
}

// Promise-based generator for one story (used by bulk)
function generateImagesForStory(storyId) {
    return new Promise(function(resolve) {
        var row = document.querySelector('tr[data-story-id="' + storyId + '"]');
        if (!row) { return resolve(); }
        var badge = ensureProgressBadge(row);
        badge.className = 'label label-info img-gen-badge';
        badge.textContent = 'Iniciando geração de imagens...';

        var csrfName = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';
        var running = true;
        (function step(){
            if (!running) return resolve();
            fetch('<?= base_url('WebStories/generateImagesStep'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ 'web_story_id': storyId, [csrfName]: csrfHash })
            }).then(function(r){ return r.json(); }).then(function(d){
                if (d && d.success) {
                    if (d.progress) {
                        badge.textContent = 'Imagens: ' + d.progress.with + '/' + d.progress.total + ' (' + d.progress.progress + '%)';
                    }
                    if (d.cover && d.cover.url) {
                        var imgCell = row.querySelector('td:nth-child(2)');
                        if (imgCell) {
                            var img = imgCell.querySelector('img');
                            if (!img) {
                                img = document.createElement('img');
                                img.style.width = '50px';
                                img.style.height = '50px';
                                img.style.objectFit = 'cover';
                                img.style.borderRadius = '4px';
                                imgCell.innerHTML = '';
                                imgCell.appendChild(img);
                            }
                            img.src = d.cover.url + '?t=' + Date.now();
                            img.alt = 'cover';
                        }
                    }
                    if (d.done) {
                        badge.className = 'label label-success img-gen-badge';
                        badge.textContent = 'Imagens concluídas';
                        if (typeof toastr !== 'undefined') { toastr.success('🎉 Imagens geradas para a story #' + storyId); }
                        running = false; return resolve();
                    }
                    setTimeout(step, 1500);
                } else {
                    badge.className = 'label label-danger img-gen-badge';
                    badge.textContent = 'Falha na geração';
                    if (typeof toastr !== 'undefined') { toastr.error('Falha ao gerar imagens para a story #' + storyId); }
                    running = false; return resolve();
                }
            }).catch(function(){ running = false; return resolve(); });
        })();
    });
}

// Bulk generation for all rows on current page
document.getElementById('btn-generate-all')?.addEventListener('click', function(){
    // Seleciona apenas stories que aparentam não ter imagem de capa (sem <img> na coluna de imagem)
    var rows = Array.from(document.querySelectorAll('#sortable-stories tr[data-story-id]'))
        .filter(function(row){ return !row.querySelector('td:nth-child(2) img'); });
    if (!rows.length) { return; }
    var total = rows.length, idx = 0;
    var panel = document.getElementById('bulk-gen-progress');
    var text = panel.querySelector('.text');
    panel.style.display = 'block';
    text.textContent = 'Gerando imagens em lote (0/' + total + ')...';

    (function next(){
        if (idx >= total) {
            text.textContent = 'Lote concluído (' + total + '/' + total + ')';
            panel.className = 'alert alert-success';
            if (typeof toastr !== 'undefined') { toastr.success('🎉 Lote de geração de imagens concluído'); }
            return;
        }
        var id = rows[idx].getAttribute('data-story-id');
        generateImagesForStory(id).then(function(){
            idx++;
            text.textContent = 'Gerando imagens em lote (' + idx + '/' + total + ')...';
            setTimeout(next, 500);
        });
    })();
});
</script>

<style>
.drag-handle {
    cursor: move;
    margin-right: 5px;
}

#sortable-stories .ui-sortable-helper {
    background: #f9f9f9;
    border: 1px dashed #ccc;
}

.display-order {
    font-weight: bold;
    color: #666;
}
</style>
