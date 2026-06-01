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
                    <h3 class="box-title">Bio Links</h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('bio-links/analytics'); ?>" class="btn btn-info">
                        <i class="fa fa-bar-chart"></i>&nbsp;&nbsp;Analytics
                    </a>
                    <a href="<?= adminUrl('bio-links/add'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;Adicionar Link
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- Bio Settings Form -->
                <form action="<?= adminUrl('bio-links/update-settings'); ?>" method="post" class="m-b-20">
                    <?= csrf_field(); ?>
                    <div class="well">
                        <h4><i class="fa fa-edit"></i> Configurações da Bio</h4>
                        <div class="form-group">
                            <label for="bio_description">Texto de Descrição</label>
                            <textarea name="bio_description" id="bio_description" class="form-control" rows="3" 
                                      placeholder="Digite o texto que aparecerá abaixo do logo na página bio..."><?= esc($bioSettings->bio_description ?? ''); ?></textarea>
                            <small class="text-muted">Este texto aparecerá abaixo do logo na página pública /bio</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Salvar Configurações
                        </button>
                    </div>
                </form>

                <!-- Stats Cards -->
                <div class="row m-b-20">
                    <div class="col-lg-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-link"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Links</span>
                                <span class="info-box-number"><?= $stats['total_links']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Links Ativos</span>
                                <span class="info-box-number"><?= $stats['active_links']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-mouse-pointer"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Cliques</span>
                                <span class="info-box-number"><?= $stats['total_clicks']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="20">Ordem</th>
                            <th>Título</th>
                            <th>URL</th>
                            <th>Ícone</th>
                            <th>Cores</th>
                            <th width="80">Cliques</th>
                            <th width="80">Status</th>
                            <th width="120">Opções</th>
                        </tr>
                        </thead>
                        <tbody id="sortable">
                        <?php if (!empty($bioLinks)): ?>
                            <?php foreach ($bioLinks as $link): ?>
                                <tr data-id="<?= $link['id']; ?>">
                                    <td class="sortable-handle">
                                        <i class="fa fa-arrows" style="cursor: move;"></i>
                                        <?= $link['display_order']; ?>
                                    </td>
                                    <td><strong><?= esc($link['title']); ?></strong></td>
                                    <td>
                                        <a href="<?= esc($link['url']); ?>" target="_blank" class="text-primary">
                                            <?= character_limiter(esc($link['url']), 50); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($link['icon'])): ?>
                                            <i class="<?= esc($link['icon']); ?>"></i> <?= esc($link['icon']); ?>
                                        <?php else: ?>
                                            <em class="text-muted">Sem ícone</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="color-preview">
                                            <span class="color-box" style="background-color: <?= esc($link['button_color']); ?>;"></span>
                                            <span class="color-box" style="background-color: <?= esc($link['text_color']); ?>;"></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-blue"><?= $link['click_count']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($link['is_active'] == 1): ?>
                                            <span class="label label-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="label label-default">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                Ações <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="<?= adminUrl('bio-links/edit/' . $link['id']); ?>">
                                                        <i class="fa fa-edit"></i>&nbsp;&nbsp;Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= adminUrl('bio-links/toggle/' . $link['id']); ?>">
                                                        <i class="fa fa-<?= $link['is_active'] == 1 ? 'ban' : 'check'; ?>"></i>&nbsp;&nbsp;
                                                        <?= $link['is_active'] == 1 ? 'Desativar' : 'Ativar'; ?>
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteBioLink(<?= $link['id']; ?>, '<?= esc($link['title']); ?>')">
                                                        <i class="fa fa-trash"></i>&nbsp;&nbsp;Excluir
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p class="text-muted">Nenhum link cadastrado ainda.</p>
                                    <a href="<?= adminUrl('bio-links/add'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Adicionar Primeiro Link
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.color-preview .color-box {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
}
.sortable-handle {
    text-align: center;
}
</style>

<script>
$(document).ready(function() {
    // Sortable functionality
    $('#sortable').sortable({
        handle: '.sortable-handle',
        update: function(event, ui) {
            var orders = {};
            $('#sortable tr').each(function(index) {
                var id = $(this).data('id');
                if (id) {
                    orders[id] = index + 1;
                }
            });
            
            $.ajax({
                url: '<?= adminUrl('bio-links/update-order'); ?>',
                method: 'POST',
                data: JSON.stringify({orders: orders}),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        // Update display order numbers
                        $('#sortable tr').each(function(index) {
                            $(this).find('.sortable-handle').html('<i class="fa fa-arrows" style="cursor: move;"></i> ' + (index + 1));
                        });
                    }
                }
            });
        }
    });
});

function deleteBioLink(id, title) {
    if (confirm('Tem certeza que deseja excluir o link "' + title + '"?')) {
        $.ajax({
            type: 'POST',
            url: VrConfig.baseURL + '/admin/bio-links/delete/' + id,
            data: setAjaxData({}),
            success: function(response) {
                if (response.success) {
                    alert('Link removido com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao remover link: ' + (response.message || 'Erro desconhecido'));
                }
            },
            error: function() {
                alert('Erro de conexão. Tente novamente.');
            }
        });
    }
}
</script>

<?= view('admin/includes/_footer'); ?>