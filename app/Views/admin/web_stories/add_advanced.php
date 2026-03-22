<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_web_story'); ?> - Editor Avançado</h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('web-stories'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;<?= trans('back'); ?>
                    </a>
                </div>
            </div>

            <form id="web-story-form" action="<?= adminUrl('web-stories/add'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <input type="hidden" name="is_advanced_editor" value="1" />
                
                <div class="box-body">
                    <!-- Story Basic Info -->
                    <div class="story-basic-info">
                        <h4><i class="fa fa-info-circle"></i> Informações Básicas</h4>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= trans('title'); ?> *</label>
                                    <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" 
                                           value="<?= old('title'); ?>" required maxlength="255">
                                </div>
                            </div>
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
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= trans('category'); ?></label>
                                    <select class="form-control" name="category_id">
                                        <option value=""><?= trans('select_category'); ?></option>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category->id; ?>" <?= old('category_id') == $category->id ? 'selected' : ''; ?>>
                                                    <?= esc($category->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="is_active">
                                        <option value="1" <?= old('is_active', '1') == '1' ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?= old('is_active') == '0' ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descrição Geral</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Descrição geral da web story"><?= old('description'); ?></textarea>
                        </div>
                    </div>

                    <hr>

                    <!-- Story Pages Editor -->
                    <div class="story-pages-editor">
                        <div class="pages-header">
                            <h4><i class="fa fa-file-text"></i> Editor de Páginas</h4>
                            <div class="pages-controls">
                                <button type="button" class="btn btn-success btn-add-page">
                                    <i class="fa fa-plus"></i> Adicionar Página
                                </button>
                                <button type="button" class="btn btn-info btn-preview-story">
                                    <i class="fa fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>

                        <div id="pages-container" class="pages-container">
                            <!-- Pages will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fa fa-save"></i>&nbsp;&nbsp;<?= trans('save'); ?>
                    </button>
                    <a href="<?= adminUrl('web-stories'); ?>" class="btn btn-default">
                        <?= trans('cancel'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Page Template -->
<template id="page-template">
    <div class="page-item" data-page-index="">
        <div class="page-header">
            <div class="page-title">
                <i class="fa fa-file-o"></i>
                <span class="page-name">Página #<span class="page-number"></span></span>
                <span class="page-type-badge"></span>
            </div>
            <div class="page-controls">
                <button type="button" class="btn btn-xs btn-info btn-duplicate-page" title="Duplicar">
                    <i class="fa fa-copy"></i>
                </button>
                <button type="button" class="btn btn-xs btn-primary btn-toggle-page" title="Expandir/Recolher">
                    <i class="fa fa-chevron-up"></i>
                </button>
                <button type="button" class="btn btn-xs btn-danger btn-delete-page" title="Excluir">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Tipo de Página</label>
                        <select class="form-control page-type" name="pages[0][page_type]">
                            <option value="cover">Capa</option>
                            <option value="content">Conteúdo</option>
                            <option value="image">Imagem</option>
                            <option value="video">Vídeo</option>
                            <option value="cta">Call to Action</option>
                            <option value="custom">Personalizada</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Posição do Texto</label>
                        <select class="form-control" name="pages[][text_position]">
                            <option value="top">Topo</option>
                            <option value="center" selected>Centro</option>
                            <option value="bottom">Inferior</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Tamanho da Fonte</label>
                        <select class="form-control" name="pages[][font_size]">
                            <option value="small">Pequena</option>
                            <option value="medium" selected>Média</option>
                            <option value="large">Grande</option>
                            <option value="xlarge">Extra Grande</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Título da Página</label>
                        <input type="text" class="form-control page-title-input" name="pages[][title]" 
                               placeholder="Título desta página">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Cor do Texto</label>
                        <input type="color" class="form-control" name="pages[][text_color]" value="#FFFFFF">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Conteúdo</label>
                <textarea class="form-control" name="pages[][content]" rows="4" 
                          placeholder="Conteúdo desta página"></textarea>
            </div>

            <!-- Background Settings -->
            <div class="background-settings">
                <h5><i class="fa fa-paint-brush"></i> Configurações de Fundo</h5>
                
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Tipo de Fundo</label>
                            <select class="form-control background-type" name="pages[][background_type]">
                                <option value="gradient" selected>Gradiente</option>
                                <option value="color">Cor Sólida</option>
                                <option value="image">Imagem</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="background-options">
                            <div class="gradient-option">
                                <label>Gradiente</label>
                                <select class="form-control gradient-select" name="pages[][background_value]">
                                    <option value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">Azul/Roxo</option>
                                    <option value="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">Rosa/Vermelho</option>
                                    <option value="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">Azul Claro</option>
                                    <option value="linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)">Verde/Ciano</option>
                                    <option value="linear-gradient(135deg, #fa709a 0%, #fee140 100%)">Rosa/Amarelo</option>
                                    <option value="linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)">Pastel</option>
                                </select>
                            </div>
                            <div class="color-option" style="display: none;">
                                <label>Cor</label>
                                <input type="color" class="form-control color-picker" value="#667eea">
                            </div>
                            <div class="image-option" style="display: none;">
                                <label>Imagem de Fundo</label>
                                <input type="file" class="form-control" name="pages[][background_image]" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Settings -->
            <div class="media-settings">
                <h5><i class="fa fa-picture-o"></i> Mídia</h5>
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Imagem</label>
                            <input type="file" class="form-control" name="pages[][page_image]" accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>URL da Imagem (alternativa)</label>
                            <input type="url" class="form-control" name="pages[][image_url]" 
                                   placeholder="https://...">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>URL do Vídeo (YouTube, Vimeo, etc.)</label>
                    <input type="url" class="form-control" name="pages[][video_url]" 
                           placeholder="https://www.youtube.com/watch?v=...">
                </div>
            </div>

            <!-- CTA Settings -->
            <div class="cta-settings">
                <h5><i class="fa fa-hand-pointer-o"></i> Call to Action</h5>
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Texto do Botão</label>
                            <input type="text" class="form-control" name="pages[][cta_text]" 
                                   placeholder="Clique aqui">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>URL do Link</label>
                            <input type="url" class="form-control" name="pages[][cta_url]" 
                                   placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" class="page-order" name="pages[][page_order]" value="">
        </div>
    </div>
</template>

<style>
.story-basic-info {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.pages-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.pages-controls {
    display: flex;
    gap: 10px;
}

.pages-container {
    min-height: 200px;
}

.page-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    background: #fff;
}

.page-header {
    background: #f5f5f5;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
}

.page-type-badge {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.page-controls {
    display: flex;
    gap: 5px;
}

.page-content {
    padding: 20px;
    display: none;
}

.page-content.active {
    display: block;
}

.background-settings,
.media-settings,
.cta-settings {
    border: 1px solid #eee;
    padding: 15px;
    border-radius: 5px;
    margin-top: 15px;
}

.background-settings h5,
.media-settings h5,
.cta-settings h5 {
    margin-top: 0;
    color: #333;
}

.page-item.sortable-placeholder {
    background: #f0f0f0;
    border: 2px dashed #ccc;
}

.page-item.ui-sortable-helper {
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .pages-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .pages-controls {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let pageCounter = 0;
    
    // Add first page automatically
    addPage('cover');
    
    // Add page button
    document.querySelector('.btn-add-page').addEventListener('click', function() {
        addPage('content');
    });
    
    // Preview button
    document.querySelector('.btn-preview-story').addEventListener('click', function() {
        alert('Preview: Funcionalidade de preview simplificada. Verifique o console para detalhes das páginas.');
        console.log('Web Story Pages:', collectPageData());
    });
    
    // Prevent multiple form submissions
    const form = document.getElementById('web-story-form');
    const submitBtn = document.getElementById('submit-btn');
    let isSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        // Validate that at least one page exists
        const pages = document.querySelectorAll('.page-item');
        if (pages.length === 0) {
            e.preventDefault();
            alert('Por favor, adicione pelo menos uma página à web story.');
            return false;
        }
        
        // Update page order before submission
        updatePageNumbers();
        
        // Mark as submitting and disable button
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Salvando...';
    });
    
    function updateFormFieldNames(pageElement, index) {
        // Update all form field names to use the correct array index
        const fields = pageElement.querySelectorAll('[name^="pages["]');
        fields.forEach(field => {
            const name = field.getAttribute('name');
            const fieldName = name.substring(name.indexOf(']') + 1);
            field.setAttribute('name', `pages[${index}]${fieldName}`);
        });
    }
    
    function addPage(type = 'content') {
        pageCounter++;
        const template = document.getElementById('page-template');
        const clone = template.content.cloneNode(true);
        
        // Update page data
        const pageItem = clone.querySelector('.page-item');
        pageItem.dataset.pageIndex = pageCounter;
        
        // Update page number
        clone.querySelector('.page-number').textContent = pageCounter;
        
        // Set page type
        const pageTypeSelect = clone.querySelector('.page-type');
        pageTypeSelect.value = type;
        updatePageTypeBadge(clone, type);
        
        // Set page order
        clone.querySelector('.page-order').value = pageCounter;
        
        // Update all form field names with correct array index
        updateFormFieldNames(clone, pageCounter - 1);
        
        // Add event listeners
        addPageEventListeners(clone);
        
        // Add to container
        document.getElementById('pages-container').appendChild(clone);
        
        // Make sortable
        updateSortable();
        
        // Open the page for editing
        const newPageItem = document.querySelector(`[data-page-index="${pageCounter}"]`);
        const content = newPageItem.querySelector('.page-content');
        content.classList.add('active');
        const toggleBtn = newPageItem.querySelector('.btn-toggle-page i');
        toggleBtn.className = 'fa fa-chevron-down';
    }
    
    function addPageEventListeners(pageElement) {
        // Toggle page content
        const header = pageElement.querySelector('.page-header');
        const toggleBtn = pageElement.querySelector('.btn-toggle-page');
        const content = pageElement.querySelector('.page-content');
        
        header.addEventListener('click', function(e) {
            if (e.target.closest('.page-controls')) return;
            content.classList.toggle('active');
            const icon = toggleBtn.querySelector('i');
            icon.className = content.classList.contains('active') ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
        });
        
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            content.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.className = content.classList.contains('active') ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
        });
        
        // Delete page
        const deleteBtn = pageElement.querySelector('.btn-delete-page');
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('Tem certeza que deseja excluir esta página?')) {
                pageElement.closest('.page-item').remove();
                updatePageNumbers();
            }
        });
        
        // Duplicate page
        const duplicateBtn = pageElement.querySelector('.btn-duplicate-page');
        duplicateBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            duplicatePage(pageElement.closest('.page-item'));
        });
        
        // Page type change
        const pageTypeSelect = pageElement.querySelector('.page-type');
        pageTypeSelect.addEventListener('change', function() {
            updatePageTypeBadge(pageElement, this.value);
        });
        
        // Background type change
        const backgroundType = pageElement.querySelector('.background-type');
        backgroundType.addEventListener('change', function() {
            updateBackgroundOptions(pageElement, this.value);
        });
        
        // Page title change
        const titleInput = pageElement.querySelector('.page-title-input');
        titleInput.addEventListener('input', function() {
            updatePageTitle(pageElement, this.value);
        });
    }
    
    function updatePageTypeBadge(pageElement, type) {
        const badge = pageElement.querySelector('.page-type-badge');
        const typeNames = {
            'cover': 'Capa',
            'content': 'Conteúdo', 
            'image': 'Imagem',
            'video': 'Vídeo',
            'cta': 'CTA',
            'custom': 'Personalizada'
        };
        badge.textContent = typeNames[type] || type;
        
        const colors = {
            'cover': '#28a745',
            'content': '#007bff',
            'image': '#ffc107',
            'video': '#dc3545',
            'cta': '#fd7e14',
            'custom': '#6f42c1'
        };
        badge.style.backgroundColor = colors[type] || '#007bff';
    }
    
    function updateBackgroundOptions(pageElement, type) {
        const options = pageElement.querySelectorAll('.background-options > div');
        options.forEach(opt => opt.style.display = 'none');
        
        const targetOption = pageElement.querySelector(`.${type}-option`);
        if (targetOption) {
            targetOption.style.display = 'block';
        }
    }
    
    function updatePageTitle(pageElement, title) {
        const pageName = pageElement.querySelector('.page-name');
        const pageNumber = pageName.querySelector('.page-number').textContent;
        if (title.trim()) {
            pageName.innerHTML = `<span class="page-number">${pageNumber}</span> - ${title}`;
        } else {
            pageName.innerHTML = `Página #<span class="page-number">${pageNumber}</span>`;
        }
    }
    
    function duplicatePage(pageItem) {
        const clone = pageItem.cloneNode(true);
        pageCounter++;
        
        clone.dataset.pageIndex = pageCounter;
        clone.querySelector('.page-number').textContent = pageCounter;
        clone.querySelector('.page-order').value = pageCounter;
        
        // Update title
        const titleInput = clone.querySelector('.page-title-input');
        if (titleInput.value) {
            titleInput.value += ' (Cópia)';
            updatePageTitle(clone, titleInput.value);
        }
        
        addPageEventListeners(clone);
        pageItem.parentNode.insertBefore(clone, pageItem.nextSibling);
        updateSortable();
    }
    
    function updatePageNumbers() {
        const pages = document.querySelectorAll('.page-item');
        pages.forEach((page, index) => {
            const number = index + 1;
            page.querySelector('.page-number').textContent = number;
            page.querySelector('.page-order').value = number;
            page.dataset.pageIndex = number;
            
            // Update form field names with correct array index
            updateFormFieldNames(page, index);
        });
        pageCounter = pages.length;
    }
    
    function updateSortable() {
        $('#pages-container').sortable({
            handle: '.page-header',
            placeholder: 'sortable-placeholder',
            helper: 'clone',
            update: function() {
                updatePageNumbers();
            }
        });
    }
    
    function collectPageData() {
        const pages = [];
        const pageElements = document.querySelectorAll('.page-item');
        
        pageElements.forEach((pageElement, index) => {
            const pageData = {
                page_order: index + 1,
                page_type: pageElement.querySelector('[name$="[page_type]"]').value,
                title: pageElement.querySelector('[name$="[title]"]').value,
                content: pageElement.querySelector('[name$="[content]"]').value
            };
            pages.push(pageData);
        });
        
        return pages;
    }
});
</script>