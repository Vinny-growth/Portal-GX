
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('dashboard'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Widgets Disponíveis</h3>
                <div class="box-tools">
                    <button class="btn btn-success btn-sm" id="save-config">
                        <i class="fa fa-save"></i> Salvar Configuração
                    </button>
                </div>
            </div>
            <div class="box-body">
                <p class="text-muted">Arraste para reordenar. Ative só o que precisa e reflita a operação real do time no dashboard principal.</p>
                
                <div id="widget-list" class="sortable-widgets">
                    <?php
                    $currentConfig = is_array($widgetConfig) ? $widgetConfig : [];
                    $sortedWidgets = [];
                    $defaultPositions = [];
                    $defaultPosition = 1;

                    foreach ($availableWidgets as $key => $widget) {
                        $defaultPositions[$key] = $defaultPosition++;
                        $position = isset($currentConfig[$key]['position']) ? $currentConfig[$key]['position'] : 999;
                        $enabled = isset($currentConfig[$key]['enabled']) ? $currentConfig[$key]['enabled'] : true;
                        $sortedWidgets[] = [
                            'key' => $key,
                            'widget' => $widget,
                            'position' => $position,
                            'enabled' => $enabled
                        ];
                    }
                    
                    usort($sortedWidgets, function($a, $b) {
                        return $a['position'] - $b['position'];
                    });
                    ?>
                    
                    <?php foreach ($sortedWidgets as $item): ?>
                        <div class="widget-item" data-widget="<?= $item['key']; ?>" data-default-position="<?= esc($defaultPositions[$item['key']] ?? 0); ?>">
                            <div class="widget-header">
                                <div class="widget-drag-handle">
                                    <i class="fa fa-bars"></i>
                                </div>
                                <div class="widget-title">
                                    <?php if (!empty($item['widget']['icon'])): ?>
                                        <i class="fa <?= esc($item['widget']['icon']); ?>"></i>
                                    <?php endif; ?>
                                    <?= esc($item['widget']['name']); ?>
                                </div>
                                <div class="widget-controls">
                                    <label class="switch">
                                        <input type="checkbox" class="widget-toggle" <?= $item['enabled'] ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="widget-description">
                                <?= esc($item['widget']['description']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Configurações</h3>
            </div>
            <div class="box-body">
                <h5>Como usar:</h5>
                <ul class="text-sm">
                    <li>Use o interruptor para ativar/desativar widgets</li>
                    <li>Arraste os widgets para reordená-los</li>
                    <li>Clique em "Salvar Configuração" para aplicar as mudanças</li>
                    <li>Os widgets desativados não aparecerão no dashboard</li>
                </ul>
                
                <hr>
                
                <h5>Ações Rápidas:</h5>
                <div class="btn-group-vertical btn-group-full">
                    <button class="btn btn-default btn-sm" id="enable-all">
                        <i class="fa fa-check"></i> Ativar Todos
                    </button>
                    <button class="btn btn-default btn-sm" id="disable-all">
                        <i class="fa fa-times"></i> Desativar Todos
                    </button>
                    <button class="btn btn-default btn-sm" id="reset-config">
                        <i class="fa fa-refresh"></i> Restaurar Padrão
                    </button>
                </div>
                
                <hr>
                
                <h5>Estatísticas:</h5>
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Widgets Ativos</span>
                        <span class="info-box-number" id="active-count">-</span>
                    </div>
                </div>
                
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Widgets</span>
                        <span class="info-box-number"><?= count($availableWidgets); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Preview</h3>
            </div>
            <div class="box-body">
                <p class="text-muted text-sm">
                    A ordem abaixo será aplicada no dashboard principal após salvar a configuração.
                </p>
                
                <div id="widget-preview">
                    <!-- Preview will be generated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/plugins/sortable/Sortable.min.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let sortable;
    
    // Initialize sortable
    initializeSortable();
    
    // Update statistics
    updateStatistics();
    
    // Generate preview
    generatePreview();
    
    // Event listeners
    document.getElementById('save-config').addEventListener('click', saveConfiguration);
    document.getElementById('enable-all').addEventListener('click', enableAllWidgets);
    document.getElementById('disable-all').addEventListener('click', disableAllWidgets);
    document.getElementById('reset-config').addEventListener('click', resetConfiguration);
    
    // Listen for toggle changes
    document.querySelectorAll('.widget-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            updateStatistics();
            generatePreview();
        });
    });
    
    function initializeSortable() {
        const el = document.getElementById('widget-list');
        sortable = Sortable.create(el, {
            handle: '.widget-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function() {
                generatePreview();
            }
        });
    }
    
    function saveConfiguration() {
        const config = getCurrentConfiguration();
        const saveBtn = document.getElementById('save-config');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Salvando...';
        saveBtn.disabled = true;
        const payload = new URLSearchParams();
        const requestData = setAjaxData({
            config: JSON.stringify(config)
        });
        Object.keys(requestData).forEach(key => {
            payload.append(key, requestData[key]);
        });

        fetch('<?= adminUrl('dashboard/save-widget-config'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: payload.toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(data => {
            if (data.success) {
                showNotification('Configuração salva com sucesso!', 'success');
            } else {
                showNotification('Erro ao salvar configuração: ' + (data.error || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            showNotification('Erro de rede: ' + error.message, 'error');
        })
        .finally(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
    
    function getCurrentConfiguration() {
        const widgets = {};
        const widgetItems = document.querySelectorAll('.widget-item');
        
        widgetItems.forEach((item, index) => {
            const widgetKey = item.dataset.widget;
            const toggle = item.querySelector('.widget-toggle');
            
            widgets[widgetKey] = {
                enabled: toggle.checked,
                position: index + 1
            };
        });
        
        return widgets;
    }
    
    function enableAllWidgets() {
        document.querySelectorAll('.widget-toggle').forEach(toggle => {
            toggle.checked = true;
        });
        updateStatistics();
        generatePreview();
    }
    
    function disableAllWidgets() {
        document.querySelectorAll('.widget-toggle').forEach(toggle => {
            toggle.checked = false;
        });
        updateStatistics();
        generatePreview();
    }
    
    function resetConfiguration() {
        if (confirm('Tem certeza que deseja restaurar a configuração padrão?')) {
            const list = document.getElementById('widget-list');
            const items = Array.from(list.querySelectorAll('.widget-item'));

            items.sort((a, b) => {
                return Number(a.dataset.defaultPosition) - Number(b.dataset.defaultPosition);
            });

            items.forEach(item => {
                const toggle = item.querySelector('.widget-toggle');
                if (toggle) {
                    toggle.checked = true;
                }
                list.appendChild(item);
            });

            updateStatistics();
            generatePreview();
        }
    }
    
    function updateStatistics() {
        const activeCount = document.querySelectorAll('.widget-toggle:checked').length;
        document.getElementById('active-count').textContent = activeCount;
    }
    
    function generatePreview() {
        const config = getCurrentConfiguration();
        const activeWidgets = [];
        
        Object.keys(config).forEach(key => {
            if (config[key].enabled) {
                activeWidgets.push({
                    key: key,
                    position: config[key].position,
                    name: getWidgetName(key)
                });
            }
        });
        
        activeWidgets.sort((a, b) => a.position - b.position);
        
        let previewHtml = '';
        if (activeWidgets.length > 0) {
            previewHtml = '<small class="text-muted">Ordem aplicada no dashboard:</small><ol class="preview-list">';
            activeWidgets.forEach(widget => {
                previewHtml += `<li>${widget.name}</li>`;
            });
            previewHtml += '</ol>';
        } else {
            previewHtml = '<p class="text-muted text-sm">Nenhum widget ativo</p>';
        }
        
        document.getElementById('widget-preview').innerHTML = previewHtml;
    }
    
    function getWidgetName(key) {
        const widgetItem = document.querySelector(`[data-widget="${key}"]`);
        return widgetItem ? widgetItem.querySelector('.widget-title').textContent.trim() : key;
    }
    
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible notification-popup`;
        notification.innerHTML = `
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ${message}
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
});
</script>

<style>
.widget-item {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.widget-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.widget-header {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.widget-drag-handle {
    color: #999;
    cursor: grab;
    margin-right: 10px;
    padding: 5px;
}

.widget-drag-handle:active {
    cursor: grabbing;
}

.widget-title {
    flex: 1;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.widget-controls {
    margin-left: auto;
}

.widget-description {
    padding: 10px 15px 15px;
    color: #666;
    font-size: 13px;
    line-height: 1.4;
}

/* Toggle Switch Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.slider.round {
    border-radius: 24px;
}

.slider.round:before {
    border-radius: 50%;
}

/* Sortable Styles */
.sortable-ghost {
    opacity: 0.4;
}

.sortable-chosen {
    background: #f9f9f9;
}

.sortable-drag {
    transform: rotate(5deg);
}

/* Button Group Styles */
.btn-group-full {
    width: 100%;
}

.btn-group-vertical .btn {
    width: 100%;
    margin-bottom: 5px;
}

/* Preview Styles */
.preview-list {
    font-size: 13px;
    margin: 10px 0;
    padding-left: 20px;
}

.preview-list li {
    margin-bottom: 5px;
    color: #555;
}

/* Info Box Styles */
.info-box {
    display: block;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
}

.info-box-content {
    padding: 5px 10px;
}

.info-box-text {
    display: block;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
}

.info-box-number {
    display: block;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

/* Notification Styles */
.notification-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .widget-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .widget-controls {
        margin-left: 0;
        margin-top: 10px;
    }
    
    .notification-popup {
        left: 20px;
        right: 20px;
        min-width: auto;
    }
}
</style>
