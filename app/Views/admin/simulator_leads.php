<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $title; ?></h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-filter-options">
                                    <button class="btn btn-danger btn-table-filter" onclick="deleteSelectedLeads();"><?= trans('delete'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped data_table" id="cs_datatable_lang" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th width="20"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('name'); ?></th>
                            <th><?= trans('email'); ?></th>
                            <th><?= "Telefone"; ?></th>
                            <th><?= "Status"; ?></th>
                            <th><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($leads)):
                            foreach ($leads as $item):?>
                                <tr>
                                    <td><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                    <td><?= esc($item->id); ?></td>
                                    <td><?= esc($item->name); ?></td>
                                    <td><?= esc($item->email); ?></td>
                                    <td><?= esc($item->phone); ?></td>
                                    <td>
                                        <select class="form-control lead-status-select" data-lead-id="<?= $item->id; ?>" onchange="updateLeadStatus(this)">
                                            <option value="new" <?= $item->status == 'new' ? 'selected' : ''; ?>>Novo</option>
                                            <option value="contacted" <?= $item->status == 'contacted' ? 'selected' : ''; ?>>Contatado</option>
                                            <option value="qualified" <?= $item->status == 'qualified' ? 'selected' : ''; ?>>Qualificado</option>
                                            <option value="proposal" <?= $item->status == 'proposal' ? 'selected' : ''; ?>>Proposta</option>
                                            <option value="converted" <?= $item->status == 'converted' ? 'selected' : ''; ?>>Convertido</option>
                                            <option value="lost" <?= $item->status == 'lost' ? 'selected' : ''; ?>>Perdido</option>
                                        </select>
                                    </td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <?php if (!empty($item->sim_data)): ?>
                                            <button class="btn btn-sm btn-info" onclick="showLeadDetails(<?= $item->id; ?>)"><i class="fa fa-info-circle"></i> Detalhes</button>
                                        <?php endif; ?>
                                        <a href="javascript:void(0)" onclick="deleteItem('Admin/deleteSimulatorLeadPost','<?= $item->id; ?>','<?= clrDQuotes(trans("confirm_delete")); ?>');" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?= trans('delete'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir detalhes do lead -->
<div id="leadDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalhes da Simulação</h4>
            </div>
            <div class="modal-body" id="leadDetailsContent">
                <!-- O conteúdo será carregado aqui dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function humanizeKey(key) {
        return String(key || '')
            .replace(/_/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .replace(/\b\w/g, function(char) { return char.toUpperCase(); });
    }

    function renderJsonValue(value) {
        if (value === null || value === undefined || value === '') {
            return '<span class="lead-json-muted">n/a</span>';
        }

        if (Array.isArray(value)) {
            if (!value.length) {
                return '<span class="lead-json-muted">n/a</span>';
            }

            return '<ul class="lead-json-list">' + value.map(function(item) {
                return '<li>' + renderJsonValue(item) + '</li>';
            }).join('') + '</ul>';
        }

        if (typeof value === 'object') {
            var rows = Object.keys(value).map(function(key) {
                return '<div class="lead-json-item"><strong>' + escapeHtml(humanizeKey(key)) + '</strong><div>' + renderJsonValue(value[key]) + '</div></div>';
            }).join('');
            return '<div class="lead-json-grid">' + rows + '</div>';
        }

        return escapeHtml(value);
    }

    function showLeadDetails(leadId) {
        var leads = <?= json_encode($leads); ?>;
        var lead = leads.find(function(item) {
            return item.id == leadId;
        });
        
        if (lead && lead.sim_data) {
            try {
                var data = JSON.parse(lead.sim_data);
                var content = '<div class="lead-detail-stack">';
                content += '<section class="lead-detail-section">';
                content += '<h4>Informações do Contato</h4>';
                content += '<div class="lead-json-grid">';
                content += '<div class="lead-json-item"><strong>Nome</strong><div>' + escapeHtml(lead.name || '') + '</div></div>';
                content += '<div class="lead-json-item"><strong>Email</strong><div>' + escapeHtml(lead.email || '') + '</div></div>';
                content += '<div class="lead-json-item"><strong>Telefone</strong><div>' + escapeHtml(lead.phone || '') + '</div></div>';
                content += '<div class="lead-json-item"><strong>Status</strong><div>' + escapeHtml(lead.status || '') + '</div></div>';
                if (lead.observations) {
                    content += '<div class="lead-json-item"><strong>Observações</strong><div>' + escapeHtml(lead.observations) + '</div></div>';
                }
                content += '</div>';
                content += '</section>';

                content += '<section class="lead-detail-section">';
                content += '<h4>Dados da Simulação</h4>';
                content += renderJsonValue(data);
                content += '</section>';
                content += '</div>';
                $('#leadDetailsContent').html(content);
            } catch (e) {
                $('#leadDetailsContent').html('<div class="alert alert-danger">Erro ao processar dados da simulação: ' + e.message + '</div>');
            }
        } else {
            $('#leadDetailsContent').html('<div class="alert alert-warning">Nenhum dado de simulação disponível para este lead.</div>');
        }
        
        $('#leadDetailsModal').modal('show');
    }
    
    function updateLeadStatus(selectElement) {
        var leadId = $(selectElement).data('lead-id');
        var status = $(selectElement).val();
        
        // Mostrar indicador de carregamento
        $(selectElement).addClass('disabled');
        
        // Criar e enviar formulário via AJAX
        var data = {
            'id': leadId,
            'status': status,
            [csrfName]: csrfHash
        };
        
        $.ajax({
            type: "POST",
            url: "<?= base_url('Admin/updateSimulatorLeadStatus'); ?>",
            data: data,
            success: function (response) {
                // Atualizar token CSRF se disponível no response
                if (response.csrf_hash) {
                    csrfHash = response.csrf_hash;
                }
                
                // Notificar usuário
                toastr.success('Status do lead atualizado com sucesso');
                
                // Remover indicador de carregamento
                $(selectElement).removeClass('disabled');
            },
            error: function() {
                // Notificar erro
                toastr.error('Erro ao atualizar status do lead');
                
                // Remover indicador de carregamento
                $(selectElement).removeClass('disabled');
            }
        });
    }
    
    function deleteSelectedLeads() {
        let itemsArray = [];
        $("input:checkbox[name='checkbox-table']:checked").each(function () {
            itemsArray.push(this.value);
        });
        if (itemsArray.length > 0) {
            let confirmText = "<?= trans("confirm_delete"); ?>";
            swalAreYouSure(confirmText, function () {
                let data = {
                    'lead_ids': itemsArray
                };
                data[csrfName] = csrfHash;
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('Admin/deleteSelectedSimulatorLeads'); ?>",
                    data: data,
                    success: function (response) {
                        location.reload();
                    }
                });
            });
        }
    }
    
    $(document).ready(function() {
        // Estilizar seletores de status com cores
        $('.lead-status-select').each(function() {
            updateStatusStyle(this);
        });
        
        $('.lead-status-select').on('change', function() {
            updateStatusStyle(this);
        });
        
        function updateStatusStyle(selectElement) {
            $(selectElement).removeClass('status-new status-contacted status-qualified status-proposal status-converted status-lost');
            
            var status = $(selectElement).val();
            $(selectElement).addClass('status-' + status);
        }
    });
</script>

<style>
    .lead-status-select {
        font-weight: bold;
    }
    .status-new {
        color: #3c8dbc;
        border-color: #3c8dbc;
    }
    .status-contacted {
        color: #f39c12;
        border-color: #f39c12;
    }
    .status-qualified {
        color: #00a65a;
        border-color: #00a65a;
    }
    .status-proposal {
        color: #605ca8;
        border-color: #605ca8;
    }
    .status-converted {
        color: #00a65a;
        border-color: #00a65a;
        background-color: #dff0d8;
    }
    .status-lost {
        color: #dd4b39;
        border-color: #dd4b39;
    }
    .lead-detail-stack {
        display: grid;
        gap: 18px;
    }
    .lead-detail-section h4 {
        margin: 0 0 10px;
        font-weight: 700;
    }
    .lead-json-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }
    .lead-json-item {
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fafbfd;
    }
    .lead-json-item strong {
        display: block;
        margin-bottom: 6px;
    }
    .lead-json-list {
        margin: 0;
        padding-left: 18px;
    }
    .lead-json-list li + li {
        margin-top: 6px;
    }
    .lead-json-muted {
        color: #9ca3af;
    }
</style>
