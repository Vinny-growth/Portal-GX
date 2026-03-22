<style>
    .cms-builder { display:flex; gap:12px; }
    .cms-left { width: 260px; }
    .cms-canvas { flex:1; min-height: 400px; border:1px dashed #bbb; border-radius:8px; padding:12px; background:#fff; }
    .cms-right { width: 300px; }
    .cms-block { border:1px solid #eee; border-radius:6px; padding:8px; margin-bottom:8px; background:#fafafa; cursor:grab; }
    .cms-item { border:1px solid #e9ecef; background:#fff; border-radius:6px; padding:12px; margin-bottom:10px; cursor:move; }
    .cms-item .actions { text-align:right; }
    .cms-item .label { font-weight:600; margin-bottom:6px; }
    .cms-empty { color:#999; text-align:center; padding:20px; }
</style>
<div class="row"><div class="col-sm-12"><section class="content-header"><h1>Builder - <?= esc($page->title); ?></h1></section></div></div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="cms-builder">
                    <div class="cms-left">
                        <h4>Blocos</h4>
                        <div class="cms-block" data-type="heading">Título</div>
                        <div class="cms-block" data-type="paragraph">Parágrafo</div>
                        <div class="cms-block" data-type="image">Imagem</div>
                        <div class="cms-block" data-type="button">Botão</div>
                        <div class="cms-block" data-type="banner">Banner</div>
                        <div class="cms-block" data-type="form">Formulário</div>
                        <div class="cms-block" data-type="register">Formulário de Registro</div>
                        <hr>
                        <div class="cms-block" data-type="section2">Seção (2 colunas)</div>
                        <div class="cms-block" data-type="section3">Seção (3 colunas)</div>
                        <div class="cms-block" data-type="video">Vídeo</div>
                        <div class="cms-block" data-type="card">Card</div>
                        <div class="cms-block" data-type="carousel">Carrossel</div>
                        <div class="cms-block" data-type="accordion">Acordeão</div>
                        <div class="cms-block" data-type="cards-grid">Cards (Grid)</div>
                        <hr>
                        <h4>Templates</h4>
                        <div id="cms-templates">
                            <?php if (!empty($templates)): foreach ($templates as $t): ?>
                                <div class="cms-block">
                                    <div><?= esc($t->title); ?></div>
                                    <div class="text-right" style="margin-top:6px;">
                                        <button class="btn btn-xs btn-default cms-insert-template" data-json='<?= esc($t->json); ?>'>Inserir</button>
                                        <a class="btn btn-xs btn-danger" onclick="return confirm('Remover template?');" href="<?= adminUrl('cms-pages/delete-template/'.(int)$t->id); ?>">Remover</a>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="text-muted">Nenhum template salvo</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="cms-canvas">
                        <div id="cms-canvas-list">
                            <?php $blocks = $layout['blocks'] ?? []; ?>
                            <?php if (empty($blocks)) { ?>
                                <div class="cms-empty">Arraste blocos para cá</div>
                            <?php } else { ?>
                            <?php foreach ($blocks as $i=>$b) { ?>
                                <div class="cms-item" data-type="<?= esc($b['type'] ?? 'paragraph'); ?>">
                                    <div class="label">Bloco: <?= esc($b['type']); ?></div>
                                    <div class="fields">
                                        <?php if (($b['type'] ?? '')=='section' || ($b['type'] ?? '')=='section2') { ?>
                                            <?php $cols = $b['cols'] ?? [[],[]]; $colCount = is_array($cols)?count($cols):2; $layout = $b['layout'] ?? null; $layoutStr = is_array($layout)? implode('-', $layout) : ($colCount==3?'4-4-4':'6-6'); ?>
                                            <div class="form-group" style="margin-bottom:8px;">
                                                <label>Layout</label>
                                                <select class="form-control" name="layout">
                                                    <?php if ($colCount==3) { ?>
                                                        <?php $opts=['4-4-4','3-6-3','3-3-6','6-3-3']; foreach($opts as $opt) { ?>
                                                            <option value="<?= $opt; ?>" <?= $layoutStr==$opt?'selected':''; ?>><?= $opt; ?></option>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php $opts=['6-6','4-8','8-4','3-9','9-3']; foreach($opts as $opt) { ?>
                                                            <option value="<?= $opt; ?>" <?= $layoutStr==$opt?'selected':''; ?>><?= $opt; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="row" style="gap:10px;">
                                                <?php for ($ci=0;$ci<2;$ci++) { $colBlocks = $cols[$ci] ?? []; ?>
                                                    <div class="col-md-6">
                                                        <div class="cms-col" style="border:1px dashed #ddd; border-radius:6px; padding:8px;">
                                                            <div class="cms-col-toolbar" style="text-align:right; margin-bottom:6px;">
                                                                <div class="btn-group btn-group-xs" role="group">
                                                                    <button type="button" class="btn btn-default cms-add-inner" data-type="heading">Título</button>
                                                                    <button type="button" class="btn btn-default cms-add-inner" data-type="paragraph">Parágrafo</button>
                                                                    <button type="button" class="btn btn-default cms-add-inner" data-type="image">Imagem</button>
                                                                    <button type="button" class="btn btn-default cms-add-inner" data-type="button">Botão</button>
                                                                    <button type="button" class="btn btn-default cms-add-inner" data-type="register">Registro</button>
                                                                </div>
                                                            </div>
                                                            <div class="cms-col-list">
                                                                <?php if (!empty($colBlocks)): ?>
                                                                <?php foreach ($colBlocks as $ib): ?>
                                                                    <div class="cms-item" data-type="<?= esc($ib['type'] ?? 'paragraph'); ?>">
                                                                        <div class="label">Bloco: <?= esc($ib['type']); ?></div>
                                                                        <div class="fields">
                                                                            <?php $content = $ib['content'] ?? ''; $url = $ib['url'] ?? ''; $text = $ib['text'] ?? ''; ?>
                                                                            <?php if (($ib['type'] ?? '')=='heading'): ?>
                                                                                <input class="form-control" name="content" placeholder="Título" value="<?= esc($content); ?>">
                                                                            <?php elseif (($ib['type'] ?? '')=='paragraph'): ?>
                                                                                <textarea class="form-control" name="content" placeholder="Parágrafo" rows="3"><?= esc($content); ?></textarea>
                                                                            <?php elseif (($ib['type'] ?? '')=='image'): ?>
                                                                                <div class="input-group" style="margin-bottom:6px;">
                                                                                    <input class="form-control" name="url" placeholder="URL da imagem" value="<?= esc($url); ?>">
                                                                                    <button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button>
                                                                                </div>
                                                                                <input class="form-control" name="text" placeholder="Legenda (opcional)" value="<?= esc($text); ?>" style="margin-top:6px;">
                                                                            <?php elseif (($ib['type'] ?? '')=='button'): ?>
                                                                                <input class="form-control" name="text" placeholder="Texto do botão" value="<?= esc($ib['text'] ?? ''); ?>">
                                                                                <input class="form-control" name="url" placeholder="Link" value="<?= esc($ib['url'] ?? ''); ?>" style="margin-top:6px;">
                                                                            <?php elseif (($ib['type'] ?? '')=='register'): ?>
                                                                                <small>Formulário de registro embutido.</small>
                                                                            <?php else: ?>
                                                                                <input class="form-control" name="content" placeholder="Conteúdo" value="<?= esc($content); ?>">
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="actions" style="margin-top:6px;">
                                                                            <button type="button" class="btn btn-xs btn-default cms-del">remover</button>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <div class="cms-empty">Adicione blocos</div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } else { ?>
                                            <?php $content = $b['content'] ?? ''; $url = $b['url'] ?? ''; $text = $b['text'] ?? ''; ?>
                                            <?php if (($b['type'] ?? '')=='heading'): ?>
                                                <input class="form-control" name="content" placeholder="Título" value="<?= esc($content); ?>">
                                            <?php elseif (($b['type'] ?? '')=='paragraph'): ?>
                                                <textarea class="form-control" name="content" placeholder="Parágrafo" rows="3"><?= esc($content); ?></textarea>
                                            <?php elseif (($b['type'] ?? '')=='image'): ?>
                                                <div class="input-group" style="margin-bottom:6px;">
                                                    <input class="form-control" name="url" placeholder="URL da imagem" value="<?= esc($url); ?>">
                                                    <button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button>
                                                </div>
                                                <input class="form-control" name="text" placeholder="Legenda (opcional)" value="<?= esc($text); ?>" style="margin-top:6px;">
                                            <?php elseif (($b['type'] ?? '')=='button'): ?>
                                            <input class="form-control" name="text" placeholder="Texto do botão" value="<?= esc($text); ?>">
                                            <input class="form-control" name="url" placeholder="Link" value="<?= esc($url); ?>" style="margin-top:6px;">
                                        <?php elseif (($b['type'] ?? '')=='video'): ?>
                                            <input class="form-control" name="url" placeholder="URL do vídeo (YouTube/Vimeo)" value="<?= esc($url); ?>">
                                        <?php elseif (($b['type'] ?? '')=='card'): ?>
                                            <div class="input-group" style="margin-bottom:6px;">
                                                <input class="form-control" name="image" placeholder="URL da imagem" value="<?= esc($b['image'] ?? ''); ?>">
                                                <button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button>
                                            </div>
                                            <input class="form-control" name="title" placeholder="Título" value="<?= esc($b['title'] ?? ''); ?>" style="margin-top:6px;">
                                            <textarea class="form-control" name="content" placeholder="Texto" rows="3" style="margin-top:6px;"><?= esc($content); ?></textarea>
                                            <input class="form-control" name="btn_text" placeholder="Texto do botão" value="<?= esc($b['btn_text'] ?? ''); ?>" style="margin-top:6px;">
                                            <input class="form-control" name="btn_url" placeholder="Link" value="<?= esc($b['btn_url'] ?? ''); ?>" style="margin-top:6px;">
                                        <?php elseif (($b['type'] ?? '')=='banner'): ?>
                                            <input class="form-control" name="content" placeholder="Título do banner" value="<?= esc($content); ?>">
                                        <?php elseif (($b['type'] ?? '')=='form'): ?>
                                            <small>Formulário de contato (padrão). Campos fixos.</small>
                                        <?php endif; ?>
                                    </div>
                                    <?php } // end if type section/section2 ?>
                                    <div class="style" style="margin-top:8px; border-top:1px dashed #eee; padding-top:8px;">
                                        <div class="row">
                                            <div class="col-sm-6"><input class="form-control" name="style_bg" placeholder="Fundo (ex: #f8f9ff)" value="<?= esc(($b['style']['bg'] ?? '')); ?>"></div>
                                            <div class="col-sm-6"><input class="form-control" name="style_color" placeholder="Cor do texto" value="<?= esc(($b['style']['color'] ?? '')); ?>"></div>
                                        </div>
                                        <div class="row" style="margin-top:6px;">
                                            <div class="col-sm-6"><input class="form-control" name="style_padding" placeholder="Padding (ex: 16px)" value="<?= esc(($b['style']['padding'] ?? '')); ?>"></div>
                                            <div class="col-sm-6">
                                                <select class="form-control" name="style_align">
                                                    <?php $al = $b['style']['align'] ?? ''; ?>
                                                    <option value="">Alinhamento</option>
                                                    <option value="left" <?= $al=='left'?'selected':''; ?>>Esquerda</option>
                                                    <option value="center" <?= $al=='center'?'selected':''; ?>>Centro</option>
                                                    <option value="right" <?= $al=='right'?'selected':''; ?>>Direita</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:6px;">
                                            <div class="col-sm-6">
                                                <?php $hm = !empty($b['hide_mobile']) ? 'checked' : ''; ?>
                                                <label style="font-weight:400;"><input type="checkbox" name="hide_mobile" value="1" <?= $hm; ?>> Ocultar no mobile</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="actions" style="margin-top:6px;">
                                        <button type="button" class="btn btn-xs btn-default cms-del">remover</button>
                                        <button type="button" class="btn btn-xs btn-default cms-dup" style="margin-left:6px;">duplicar</button>
                                        <?php $bt = $b['type'] ?? ''; if ($bt=='section' || $bt=='section2'): ?>
                                            <button type="button" class="btn btn-xs btn-info cms-save-template" style="margin-left:6px;">Salvar como template</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php } // end foreach blocks ?>
                            <?php } // end if blocks ?>
                        </div>
                    </div>
                    <div class="cms-right">
                        <h4>Ações</h4>
                        <div class="btn-group btn-group-justified" style="width:100%; margin-bottom:8px;">
                            <a id="cms-undo" class="btn btn-default" title="Desfazer (Ctrl+Z)"><i class="fa fa-undo"></i></a>
                            <a id="cms-redo" class="btn btn-default" title="Refazer (Ctrl+Y)"><i class="fa fa-repeat"></i></a>
                        </div>
                        <button id="cms-save" class="btn btn-primary btn-block">Salvar</button>
                        <a class="btn btn-success btn-block" href="<?= adminUrl('cms-pages/publish/'.(int)$page->id); ?>">Publicar</a>
                        <a class="btn btn-default btn-block" href="<?= adminUrl('cms-pages'); ?>">Voltar</a>
                        <hr>
                        <small>Dica: arraste para reordenar os blocos. Clique em remover para excluir.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
<script>
(function(){
    var palette = document.querySelectorAll('.cms-block');
    var list = document.getElementById('cms-canvas-list');
    palette.forEach(function(p){
        p.addEventListener('click', function(){ addBlock(p.getAttribute('data-type')); pushHistory(); });
    });
    function addBlock(type){
        var div = document.createElement('div');
        div.className = 'cms-item';
        div.setAttribute('data-type', type);
        div.innerHTML = renderFields(type) + '<div class="actions" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-del">remover</button><button type="button" class="btn btn-xs btn-default cms-dup" style="margin-left:6px;">duplicar</button></div>';
        if (list.querySelector('.cms-empty')) list.querySelector('.cms-empty').remove();
        list.appendChild(div);
        attachFieldChangeHandlers(div);
    }
    function renderFields(type){
        var html = '<div class="label">Bloco: '+type+'</div><div class="fields">';
        if (type==='heading') html += '<input class="form-control" name="content" placeholder="Título">';
        else if (type==='paragraph') html += '<textarea class="form-control" name="content" placeholder="Parágrafo" rows="3"></textarea>';
        else if (type==='image') html += '<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="url" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div><input class="form-control" name="text" placeholder="Legenda (opcional)" style="margin-top:6px;">';
        else if (type==='button') html += '<input class="form-control" name="text" placeholder="Texto do botão"><input class="form-control" name="url" placeholder="Link" style="margin-top:6px;">';
        else if (type==='banner') html += '<input class="form-control" name="content" placeholder="Título do banner">';
        else if (type==='form') html += '<small>Formulário de contato (padrão). Campos fixos.</small>';
        else if (type==='video') html += '<input class="form-control" name="url" placeholder="URL do vídeo (YouTube/Vimeo)">';
        else if (type==='register') html += '<small>Exibe o formulário de registro (apenas visitantes).</small>';
        else if (type==='card') html += '<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="image" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div><input class="form-control" name="title" placeholder="Título" style="margin-top:6px;"><textarea class="form-control" name="content" placeholder="Texto" rows="3" style="margin-top:6px;"></textarea><input class="form-control" name="btn_text" placeholder="Texto do botão" style="margin-top:6px;"><input class="form-control" name="btn_url" placeholder="Link" style="margin-top:6px;">';
        else if (type==='carousel') html += '<div class="cms-carousel-items">\
<div class="cms-carousel-item" style="border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;">\
<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="c_url[]" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div>\
<input class="form-control" name="c_text[]" placeholder="Legenda (opcional)">\
<div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>\
</div></div><button type="button" class="btn btn-default btn-sm cms-carousel-add">Adicionar imagem</button>';
        else if (type==='accordion') html += '<div class="cms-acc-items">\
<div class="cms-acc-item" style="border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;">\
<input class="form-control" name="a_title[]" placeholder="Título">\
<textarea class="form-control" name="a_content[]" placeholder="Conteúdo" rows="3" style="margin-top:6px;"></textarea>\
<div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>\
</div></div><button type="button" class="btn btn-default btn-sm cms-acc-add">Adicionar item</button><div class="checkbox" style="margin-top:6px;"><label><input type="checkbox" name="a_single" value="1"> Apenas um aberto por vez</label></div>';
        else if (type==='cards-grid') html += '<div class="form-group"><label>Colunas Desktop</label><select class="form-control" name="grid_cols_md"><option value="2">2</option><option value="3" selected>3</option><option value="4">4</option></select></div><div class="form-group"><label>Colunas Tablet</label><select class="form-control" name="grid_cols_sm"><option value="1">1</option><option value="2" selected>2</option><option value="3">3</option></select></div><div class="form-group"><label>Colunas Large</label><select class="form-control" name="grid_cols_lg"><option value="3">3</option><option value="4" selected>4</option><option value="6">6</option></select></div><div class="form-group"><label>Gutter</label><select class="form-control" name="grid_gutter"><option value="8">Pequeno</option><option value="16" selected>Médio</option><option value="24">Grande</option></select></div><div class="form-group"><label>Hover</label><select class="form-control" name="grid_hover"><option value="none" selected>Nenhum</option><option value="raise">Elevar</option><option value="shadow">Sombra</option></select></div>\
<div class="cms-cgrid-items">\
<div class="cms-cgrid-item" style="border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;">\
<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="g_image[]" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div>\
<input class="form-control" name="g_title[]" placeholder="Título" style="margin-top:6px;">\
<textarea class="form-control" name="g_content[]" placeholder="Texto" rows="2" style="margin-top:6px;"></textarea>\
<div class="row" style="margin-top:6px;"><div class="col-sm-6"><input class="form-control" name="g_btn_text[]" placeholder="Texto do botão"></div><div class="col-sm-6"><input class="form-control" name="g_btn_url[]" placeholder="Link"></div></div>\
<div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>\
</div></div><button type="button" class="btn btn-default btn-sm cms-cgrid-add">Adicionar card</button>';
        else if (type==='section2') html += renderSection2();
        else if (type==='section3') html += renderSection3();
        html += '</div>';
        html += '<div class="style" style="margin-top:8px; border-top:1px dashed #eee; padding-top:8px;">'
              +   '<div class="row">'
              +     '<div class="col-sm-6"><input class="form-control" name="style_bg" placeholder="Fundo (ex: #f8f9ff)"></div>'
              +     '<div class="col-sm-6"><input class="form-control" name="style_color" placeholder="Cor do texto"></div>'
              +   '</div>'
              +   '<div class="row" style="margin-top:6px;">'
              +     '<div class="col-sm-6"><input class="form-control" name="style_padding" placeholder="Padding (ex: 16px)"></div>'
              +     '<div class="col-sm-6"><select class="form-control" name="style_align"><option value="">Alinhamento</option><option value="left">Esquerda</option><option value="center">Centro</option><option value="right">Direita</option></select></div>'
              +   '</div>'
              +   '<div class="row" style="margin-top:6px;"><div class="col-sm-6"><label style="font-weight:400;"><input type="checkbox" name="hide_mobile" value="1"> Ocultar no mobile</label></div></div>'
              + '</div>';
        return html;
    }
    function renderSection2(){
        var s = '';
        s += '<div class="form-group" style="margin-bottom:8px;"><label>Layout</label><select class="form-control" name="layout">'
           +   '<option value="6-6" selected>6-6</option>'
           +   '<option value="4-8">4-8</option><option value="8-4">8-4</option>'
           +   '<option value="3-9">3-9</option><option value="9-3">9-3</option>'
           + '</select></div>';
        s += '<div class="row" style="gap:10px;">';
        for (var i=0;i<2;i++){
            s += '<div class="col-md-6">'
               +   '<div class="cms-col" style="border:1px dashed #ddd; border-radius:6px; padding:8px;">'
               +     '<div class="cms-col-toolbar" style="text-align:right; margin-bottom:6px;">'
               +       '<div class="btn-group btn-group-xs" role="group">'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="heading">Título</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="paragraph">Parágrafo</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="image">Imagem</button>'
               +       '</div>'
               +     '</div>'
               +     '<div class="cms-col-list"><div class="cms-empty">Adicione blocos</div></div>'
               +   '</div>'
               + '</div>';
        }
        s += '</div>';
        return s;
    }
    function renderSection3(){
        var s = '';
        s += '<div class="form-group" style="margin-bottom:8px;"><label>Layout</label><select class="form-control" name="layout">'
           +   '<option value="4-4-4" selected>4-4-4</option>'
           +   '<option value="3-6-3">3-6-3</option><option value="3-3-6">3-3-6</option><option value="6-3-3">6-3-3</option>'
           + '</select></div>';
        s += '<div class="row" style="gap:10px;">';
        for (var i=0;i<3;i++){
            s += '<div class="col-md-4">'
               +   '<div class="cms-col" style="border:1px dashed #ddd; border-radius:6px; padding:8px;">'
               +     '<div class="cms-col-toolbar" style="text-align:right; margin-bottom:6px;">'
               +       '<div class="btn-group btn-group-xs" role="group">'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="heading">Título</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="paragraph">Parágrafo</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="image">Imagem</button>'
               +       '</div>'
               +     '</div>'
               +     '<div class="cms-col-list"><div class="cms-empty">Adicione blocos</div></div>'
               +   '</div>'
               + '</div>';
        }
        s += '</div>';
        return s;
    }
    function renderSection3(){
        var s = '';
        s += '<div class="row" style="gap:10px;">';
        for (var i=0;i<3;i++){
            s += '<div class="col-md-4">'
               +   '<div class="cms-col" style="border:1px dashed #ddd; border-radius:6px; padding:8px;">'
               +     '<div class="cms-col-toolbar" style="text-align:right; margin-bottom:6px;">'
               +       '<div class="btn-group btn-group-xs" role="group">'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="heading">Título</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="paragraph">Parágrafo</button>'
               +         '<button type="button" class="btn btn-default cms-add-inner" data-type="image">Imagem</button>'
               +       '</div>'
               +     '</div>'
               +     '<div class="cms-col-list"><div class="cms-empty">Adicione blocos</div></div>'
               +   '</div>'
               + '</div>';
        }
        s += '</div>';
        return s;
    }
    document.addEventListener('click', function(e){ 
        if (e.target && e.target.classList.contains('cms-del')) { 
            e.target.closest('.cms-item').remove(); 
            if (!list.children.length) { var emp=document.createElement('div'); emp.className='cms-empty'; emp.textContent='Arraste blocos para cá'; list.appendChild(emp);} 
            pushHistory();
        }
        if (e.target && e.target.classList.contains('cms-dup')){
            var item = e.target.closest('.cms-item'); if (!item) return;
            var clone = item.cloneNode(true);
            item.parentNode.insertBefore(clone, item.nextSibling);
            refreshSortables(); attachFieldChangeHandlers(clone); pushHistory();
        }
        if (e.target && e.target.classList.contains('cms-row-del')){
            var row = e.target.closest('.cms-carousel-item, .cms-acc-item, .cms-cgrid-item'); if (row) { row.remove(); pushHistory(); }
        }
        if (e.target && e.target.classList.contains('cms-carousel-add')){
            var wrap = e.target.closest('.cms-item'); if (!wrap) return;
            var cont = wrap.querySelector('.cms-carousel-items'); if (!cont) return;
            var div = document.createElement('div');
            div.className = 'cms-carousel-item';
            div.style.cssText = 'border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;';
            div.innerHTML = '<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="c_url[]" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div><input class="form-control" name="c_text[]" placeholder="Legenda (opcional)"><div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>';
            cont.appendChild(div); attachFieldChangeHandlers(div); pushHistory();
        }
        if (e.target && e.target.classList.contains('cms-acc-add')){
            var wrap = e.target.closest('.cms-item'); if (!wrap) return;
            var cont = wrap.querySelector('.cms-acc-items'); if (!cont) return;
            var div = document.createElement('div');
            div.className = 'cms-acc-item';
            div.style.cssText = 'border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;';
            div.innerHTML = '<input class="form-control" name="a_title[]" placeholder="Título"><textarea class="form-control" name="a_content[]" placeholder="Conteúdo" rows="3" style="margin-top:6px;"></textarea><div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>';
            cont.appendChild(div); attachFieldChangeHandlers(div); pushHistory();
        }
        if (e.target && e.target.classList.contains('cms-cgrid-add')){
            var wrap = e.target.closest('.cms-item'); if (!wrap) return;
            var cont = wrap.querySelector('.cms-cgrid-items'); if (!cont) return;
            var div = document.createElement('div');
            div.className = 'cms-cgrid-item';
            div.style.cssText = 'border:1px dashed #ddd; border-radius:6px; padding:8px; margin-bottom:6px;';
            div.innerHTML = '<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="g_image[]" placeholder="URL da imagem"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div><input class="form-control" name="g_title[]" placeholder="Título" style="margin-top:6px;"><textarea class="form-control" name="g_content[]" placeholder="Texto" rows="2" style="margin-top:6px;"></textarea><div class="row" style="margin-top:6px;"><div class="col-sm-6"><input class="form-control" name="g_btn_text[]" placeholder="Texto do botão"></div><div class="col-sm-6"><input class="form-control" name="g_btn_url[]" placeholder="Link"></div></div><div class="text-right" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-row-del">remover</button></div>';
            cont.appendChild(div); attachFieldChangeHandlers(div); pushHistory();
        }
    });
    $('#cms-canvas-list').sortable({placeholder: 'cms-item', stop: function(){ pushHistory(); }});

    function captureJson(){
        var blocks = [];
        // Captura somente filhos diretos .cms-item do canvas
        Array.prototype.slice.call(list.children).forEach(function(item){
            if (!item.classList || !item.classList.contains('cms-item')) return;
            var type = item.getAttribute('data-type');
            var obj = {type: type};
            if (type === 'section' || type === 'section2' || type==='section3') {
                obj.type = 'section';
                obj.cols = [];
                var laySel = item.querySelector('select[name="layout"]');
                if (laySel && laySel.value) { obj.layout = laySel.value.split('-').map(function(x){ return parseInt(x,10)||0; }); }
                item.querySelectorAll('.cms-col-list').forEach(function(col){
                    var inner = [];
                    Array.prototype.slice.call(col.children).forEach(function(inItem){
                        if (!inItem.classList || !inItem.classList.contains('cms-item')) return;
                        var t = inItem.getAttribute('data-type');
                        var o = {type: t};
                        inItem.querySelectorAll('.fields [name]').forEach(function(inp){
                            var name = inp.name;
                            if (name==='hide_mobile') { if (inp.checked) o['hide_mobile'] = 1; }
                            else if (name.startsWith('style_')) { o['style'] = o['style']||{}; o['style'][name.replace('style_','')] = inp.value; }
                            else { o[name] = inp.value; }
                        });
                        inner.push(o);
                    });
                    obj.cols.push(inner);
                });
            } else if (type === 'carousel') {
                obj.items = [];
                item.querySelectorAll('.cms-carousel-item').forEach(function(row){
                    var u = (row.querySelector('input[name="c_url[]"]').value || '').trim();
                    var t = (row.querySelector('input[name="c_text[]"]').value || '').trim();
                    if (u) obj.items.push({url:u, text:t});
                });
            } else if (type === 'accordion') {
                obj.items = [];
                item.querySelectorAll('.cms-acc-item').forEach(function(row){
                    var ti = (row.querySelector('input[name="a_title[]"]').value || '').trim();
                    var co = (row.querySelector('textarea[name="a_content[]"]').value || '').trim();
                    if (ti || co) obj.items.push({title:ti, content:co});
                });
            } else if (type === 'cards-grid') {
                obj.type = 'cards-grid';
                var colsSel = item.querySelector('select[name="grid_cols"]'); obj.grid_cols = colsSel ? parseInt(colsSel.value,10) || 3 : 3;
                obj.items = [];
                item.querySelectorAll('.cms-cgrid-item').forEach(function(row){
                    var img = (row.querySelector('input[name="g_image[]"]').value || '').trim();
                    var ti = (row.querySelector('input[name="g_title[]"]').value || '').trim();
                    var co = (row.querySelector('textarea[name="g_content[]"]').value || '').trim();
                    var bt = (row.querySelector('input[name="g_btn_text[]"]').value || '').trim();
                    var bu = (row.querySelector('input[name="g_btn_url[]"]').value || '').trim();
                    if (ti || img || co) obj.items.push({image:img, title:ti, content:co, btn_text:bt, btn_url:bu});
                });
            } else {
                item.querySelectorAll('.fields [name]').forEach(function(inp){
                    var name = inp.name;
                    if (name==='hide_mobile') { if (inp.checked) obj['hide_mobile'] = 1; }
                    else if (name.startsWith('style_')) { obj['style'] = obj['style']||{}; obj['style'][name.replace('style_','')] = inp.value; }
                    else { obj[name] = inp.value; }
                });
            }
            blocks.push(obj);
        });
        return JSON.stringify({blocks: blocks});
    }
    document.getElementById('cms-save').addEventListener('click', function(){
        var json = captureJson();
        var fd = new FormData();
        fd.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>');
        fd.append('data_json', json);
        fetch('<?= adminUrl('cms-pages/save-builder/'.(int)$page->id); ?>', {method:'POST', body:fd})
            .then(r=>r.json()).then(function(res){ if (res && res.success){ alert('Salvo!'); } else { alert('Falha ao salvar'); } });
    });

    // Image picker integration
    var currentImgInput = null;
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('cms-pick-image')) {
            var item = e.target.closest('.cms-item');
            if (item) {
                currentImgInput = item.querySelector('input[name="url"]');
                if (!currentImgInput) currentImgInput = item.querySelector('input[name="image"]');
            } else { currentImgInput = null; }
        }
    });
    $(document).on('click', '#file_manager_image #btn_img_select', function(){
        try {
            var base = $('#selected_img_base_url').val() || '';
            var defp = $('#selected_img_default_file_path').val() || '';
            var full = base + defp;
            if (currentImgInput) { currentImgInput.value = full; }
        } catch(e) {}
    });
    // duplicate column content
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('cms-dup-col')){
            var col = e.target.closest('.cms-col'); if (!col) return;
            var listCol = col.querySelector('.cms-col-list');
            var items = listCol ? listCol.querySelectorAll(':scope > .cms-item') : [];
            items.forEach(function(it){ var clone = it.cloneNode(true); listCol.appendChild(clone); attachFieldChangeHandlers(clone); });
            refreshSortables(); pushHistory();
        }
    });
    // Make inner columns sortable and allow adding inner items
    function refreshSortables(){
        $('#cms-canvas-list').sortable({placeholder: 'cms-item', stop: function(){ pushHistory(); }});
        $('.cms-col-list').sortable({placeholder: 'cms-item', connectWith: '.cms-col-list', stop: function(){ pushHistory(); }});
    }
    refreshSortables();
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('cms-add-inner')){
            var type = e.target.getAttribute('data-type');
            var col = e.target.closest('.cms-col').querySelector('.cms-col-list');
            var div = document.createElement('div');
            div.className = 'cms-item';
            div.setAttribute('data-type', type);
            div.innerHTML = '<div class="label">Bloco: '+type+'</div>'
                + '<div class="fields">' + (function(){
                    if (type==='heading') return '<input class="form-control" name="content" placeholder="Título">';
                    if (type==='paragraph') return '<textarea class="form-control" name="content" placeholder="Parágrafo" rows="3"></textarea>';
                    if (type==='image') return '<div class="input-group" style="margin-bottom:6px;">'
                        +'<input class="form-control" name="url" placeholder="URL da imagem">'
                        +'<button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button>'
                        +'</div><input class="form-control" name="text" placeholder="Legenda (opcional)" style="margin-top:6px;">';
                    if (type==='button') return '<input class="form-control" name="text" placeholder="Texto do botão"><input class="form-control" name="url" placeholder="Link" style="margin-top:6px;">';
                    if (type==='register') return '<small>Formulário de registro embutido.</small>';
                    return '<input class="form-control" name="content" placeholder="Conteúdo">';
                })() + '</div>'
                + '<div class="actions" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-del">remover</button></div>';
            if (col.querySelector('.cms-empty')) col.querySelector('.cms-empty').remove();
            col.appendChild(div);
            refreshSortables(); attachFieldChangeHandlers(div); pushHistory();
        }
    });

    // Templates: insert into canvas
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('cms-insert-template')){
            var json = e.target.getAttribute('data-json') || '{}';
            var block = null; try { block = JSON.parse(json); } catch(err){}
            if (!block || block.type!=='section') return;
            var div = document.createElement('div');
            var colsCount = (block.cols && block.cols.length) || 2;
            var t = (colsCount===3) ? 'section3' : 'section2';
            div.className = 'cms-item';
            div.setAttribute('data-type', t);
            div.innerHTML = renderFields(t) + '<div class="actions" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-del">remover</button><button type="button" class="btn btn-xs btn-info cms-save-template" style="margin-left:6px;">Salvar como template</button></div>';
            if (list.querySelector('.cms-empty')) list.querySelector('.cms-empty').remove();
            list.appendChild(div);
            refreshSortables();
            // set layout from template (if provided)
            try {
                var laySel = div.querySelector('select[name="layout"]');
                if (laySel && Array.isArray(block.layout) && block.layout.length) {
                    laySel.value = block.layout.join('-');
                }
            } catch(err){}
            // populate inner blocks
            var colLists = div.querySelectorAll('.cms-col-list');
            (block.cols||[]).forEach(function(col, idx){
                var colList = colLists[idx]; if (!colList) return;
                colList.innerHTML = '';
                col.forEach(function(inItem){
                    var it = document.createElement('div');
                    var tt = inItem.type || 'paragraph';
                    it.className = 'cms-item'; it.setAttribute('data-type', tt);
                    var fields = '';
                    if (tt==='heading') fields = '<input class="form-control" name="content" placeholder="Título" value="'+(inItem.content||'')+'">';
                    else if (tt==='paragraph') fields = '<textarea class="form-control" name="content" placeholder="Parágrafo" rows="3">'+(inItem.content||'')+'</textarea>';
                    else if (tt==='image') fields = '<div class="input-group" style="margin-bottom:6px;"><input class="form-control" name="url" placeholder="URL da imagem" value="'+(inItem.url||'')+'"><button type="button" class="btn btn-default btn-sm cms-pick-image" data-toggle="modal" data-target="#file_manager_image" style="margin-left:6px;">Selecionar</button></div><input class="form-control" name="text" placeholder="Legenda (opcional)" value="'+(inItem.text||'')+'" style="margin-top:6px;">';
                    else if (tt==='button') fields = '<input class="form-control" name="text" placeholder="Texto do botão" value="'+(inItem.text||'')+'"><input class="form-control" name="url" placeholder="Link" value="'+(inItem.url||'')+'" style="margin-top:6px;">';
                    else if (tt==='register') fields = '<small>Formulário de registro embutido.</small>';
                    else fields = '<input class="form-control" name="content" placeholder="Conteúdo" value="'+(inItem.content||'')+'">';
                    it.innerHTML = '<div class="label">Bloco: '+tt+'</div><div class="fields">'+fields+'</div><div class="actions" style="margin-top:6px;"><button type="button" class="btn btn-xs btn-default cms-del">remover</button></div>';
                    colList.appendChild(it);
                });
            });
        }
    });

    // Templates: save current section cms-item as template
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('cms-save-template')){
            var item = e.target.closest('.cms-item'); if (!item) return;
            var type = item.getAttribute('data-type');
            if (type!=='section2' && type!=='section3' && type!=='section') return;
            var obj = {type:'section', cols:[]};
            // include layout
            var laySel = item.querySelector('select[name="layout"]');
            if (laySel && laySel.value) { obj.layout = laySel.value.split('-').map(function(x){ return parseInt(x,10)||0; }); }
            item.querySelectorAll('.cms-col-list').forEach(function(col){
                var inner=[];
                col.querySelectorAll(':scope > .cms-item').forEach(function(inItem){
                    var t=inItem.getAttribute('data-type'); var o={type:t};
                    inItem.querySelectorAll('.fields [name]').forEach(function(inp){
                        var name=inp.name; if (name==='hide_mobile'){ if (inp.checked) o['hide_mobile']=1; }
                        else if (name.startsWith('style_')){ o['style']=o['style']||{}; o['style'][name.replace('style_','')]=inp.value; }
                        else { o[name]=inp.value; }
                    });
                    inner.push(o);
                });
                obj.cols.push(inner);
            });
            var title = prompt('Nome do template:', 'Seção '+(new Date()).toLocaleString()); if (!title) return;
            var fd = new FormData(); fd.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>'); fd.append('title', title); fd.append('json', JSON.stringify(obj));
            fetch('<?= adminUrl('cms-pages/save-template'); ?>',{method:'POST', body:fd}).then(r=>r.json()).then(function(res){ if (res && res.success){ location.reload(); } else { alert('Falha ao salvar template'); } });
        }
    });

    // ==== Histórico (Desfazer/Refazer) baseado em snapshot do DOM ====
    var history = [];
    var histIndex = -1;
    function updateUndoRedo(){
        var u = document.getElementById('cms-undo');
        var r = document.getElementById('cms-redo');
        if (u) u.classList.toggle('disabled', histIndex <= 0);
        if (r) r.classList.toggle('disabled', histIndex >= history.length - 1);
    }
    function pushHistory(){
        var state = list.innerHTML;
        if (history[histIndex] === state) return;
        history = history.slice(0, histIndex + 1);
        history.push(state);
        if (history.length > 50) { history.shift(); } else { histIndex++; }
        updateUndoRedo();
    }
    function applyHistory(index){
        if (index < 0 || index >= history.length) return;
        list.innerHTML = history[index];
        refreshSortables();
        attachFieldChangeHandlers(document);
        updateUndoRedo();
    }
    function attachFieldChangeHandlers(scope){
        (scope || document).querySelectorAll('.fields [name], select[name="layout"]').forEach(function(el){
            el.addEventListener('change', function(){ pushHistory(); });
            el.addEventListener('input', function(){ /* opcional: debounce */ });
        });
    }
    // inicializar
    attachFieldChangeHandlers(document);
    pushHistory();
    updateUndoRedo();
    var btnUndo = document.getElementById('cms-undo'); if (btnUndo) btnUndo.addEventListener('click', function(){ if (histIndex > 0) { histIndex--; applyHistory(histIndex); } });
    var btnRedo = document.getElementById('cms-redo'); if (btnRedo) btnRedo.addEventListener('click', function(){ if (histIndex < history.length - 1) { histIndex++; applyHistory(histIndex); } });
    document.addEventListener('keydown', function(e){
        var k = e.key.toLowerCase();
        if ((e.ctrlKey||e.metaKey) && k==='z') { e.preventDefault(); if (histIndex > 0) { histIndex--; applyHistory(histIndex); } }
        if ((e.ctrlKey||e.metaKey) && (k==='y' || (e.shiftKey && k==='z'))) { e.preventDefault(); if (histIndex < history.length - 1) { histIndex++; applyHistory(histIndex); } }
    });
})();
</script>
<style>
/* Card block basic styles */
.cms-card { border:1px solid #eee; border-radius:10px; padding:16px; background:#fff; }
.cms-card img { max-width:100%; border-radius:8px; margin-bottom:10px; }
</style>
<?php // include file manager modals (images only)
echo view('admin/file-manager/_load_file_manager', ['loadImages' => true]);
?>
