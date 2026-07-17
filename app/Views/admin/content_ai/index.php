<?php
$allowedCategoryIds = [];
if (!empty($settings->allowed_category_ids)) {
    $decoded = json_decode($settings->allowed_category_ids, true);
    if (is_array($decoded)) {
        $allowedCategoryIds = $decoded;
    }
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Central de Conteudos IA</h1>
    </section>

    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_calendar" data-toggle="tab">Calendario</a></li>
                <li><a href="#tab_trends" data-toggle="tab">Tendencias</a></li>
                <li><a href="#tab_settings" data-toggle="tab">Configuracoes</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_calendar">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Adicionar item ao calendario</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= adminUrl('content-ai/calendar/add'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Titulo base</label>
                                            <input type="text" class="form-control" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Categoria</label>
                                            <select class="form-control select2" name="category_id">
                                                <option value="">Selecione</option>
                                                <?php if (!empty($categories)):
                                                    foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat->id; ?>"><?= esc($cat->name); ?></option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Instrucoes para a IA</label>
                                    <textarea class="form-control" name="instructions" rows="3" placeholder="Ex.: foco em cambio comercial, publico C-level, incluir dados recentes."></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tom</label>
                                            <select name="tone" class="form-control">
                                                <option value="">Padrao</option>
                                                <option value="professional">Profissional</option>
                                                <option value="formal">Formal</option>
                                                <option value="inspirational">Inspiracional</option>
                                                <option value="persuasive">Persuasivo</option>
                                                <option value="academic">Academico</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tamanho</label>
                                            <select name="length" class="form-control">
                                                <option value="">Padrao</option>
                                                <option value="short">Curto</option>
                                                <option value="medium">Medio</option>
                                                <option value="long">Longo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Publicar em</label>
                                            <input type="datetime-local" class="form-control" name="publish_at">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Gerar em</label>
                                            <input type="datetime-local" class="form-control" name="generate_at">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Adicionar</button>
                            </form>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Itens do calendario
                                <?php if (!empty($calendarPagination)): ?>
                                    <small class="text-muted" style="margin-left:10px;">
                                        <?= number_format((int) $calendarPagination['total'], 0, ',', '.'); ?> itens no total
                                    </small>
                                <?php endif; ?>
                            </h3>
                            <div class="pull-right">
                                <form action="<?= adminUrl('content-ai/run-now'); ?>" method="post" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-primary btn-sm">Gerar agora</button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php
                            $fStatus = $calendarFilters['status'] ?? '';
                            $fSource = $calendarFilters['source_type'] ?? '';
                            $fDate   = $calendarFilters['date'] ?? '';
                            $fQuery  = $calendarFilters['q'] ?? '';
                            $statusCounts = $calendarStatusCounts ?? [];
                            $sourceCounts = $calendarSourceCounts ?? [];
                            $statusOptions = [
                                'planned'      => 'Planejado',
                                'queued'       => 'Na fila',
                                'generating'   => 'Gerando',
                                'generated'    => 'Gerado',
                                'needs_review' => 'Aguardando revisao',
                                'failed'       => 'Falhou',
                            ];
                            $sourceOptions = [
                                'manual'  => 'Manual',
                                'trend'   => 'Tendencia',
                                'popular' => 'Popular',
                            ];
                            ?>
                            <form method="get" action="<?= adminUrl('content-ai'); ?>" class="form-inline" style="margin-bottom:15px;">
                                <div class="form-group" style="margin-right:8px;">
                                    <select name="status" class="form-control input-sm">
                                        <option value="">Todos os status</option>
                                        <?php foreach ($statusOptions as $k => $label): ?>
                                            <option value="<?= $k; ?>" <?= $fStatus === $k ? 'selected' : ''; ?>>
                                                <?= $label; ?><?= isset($statusCounts[$k]) ? ' (' . $statusCounts[$k] . ')' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-right:8px;">
                                    <select name="source_type" class="form-control input-sm">
                                        <option value="">Todas as origens</option>
                                        <?php foreach ($sourceOptions as $k => $label): ?>
                                            <option value="<?= $k; ?>" <?= $fSource === $k ? 'selected' : ''; ?>>
                                                <?= $label; ?><?= isset($sourceCounts[$k]) ? ' (' . $sourceCounts[$k] . ')' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-right:8px;">
                                    <input type="date" name="date" class="form-control input-sm" value="<?= esc($fDate); ?>" placeholder="Data">
                                </div>
                                <div class="form-group" style="margin-right:8px;">
                                    <input type="text" name="q" class="form-control input-sm" value="<?= esc($fQuery); ?>" placeholder="Buscar titulo" style="width:200px;">
                                </div>
                                <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-filter"></i> Filtrar</button>
                                <?php if ($fStatus || $fSource || $fDate || $fQuery): ?>
                                    <a href="<?= adminUrl('content-ai'); ?>" class="btn btn-sm btn-link">Limpar</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width:50px;">ID</th>
                                    <th>Titulo</th>
                                    <th style="width:140px;">Status</th>
                                    <th style="width:160px;">Publicar em</th>
                                    <th style="width:100px;">Origem</th>
                                    <th style="width:90px;">Post</th>
                                    <th style="width:160px;">Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $statusLabels = [
                                    'planned'      => ['default', 'Planejado'],
                                    'queued'       => ['primary', 'Na fila'],
                                    'generating'   => ['info',    'Gerando'],
                                    'generated'    => ['success', 'Gerado'],
                                    'needs_review' => ['warning', 'Revisao'],
                                    'failed'       => ['danger',  'Falhou'],
                                ];
                                $srcLabels = [
                                    'manual'  => ['default', 'Manual'],
                                    'trend'   => ['info',    'Tendencia'],
                                    'popular' => ['warning', 'Popular'],
                                ];
                                $todayStr = date('Y-m-d');
                                ?>
                                <?php if (!empty($calendarItems)):
                                    foreach ($calendarItems as $item):
                                        [$stCls, $stTxt] = $statusLabels[$item->status] ?? ['default', ucfirst((string) $item->status)];
                                        [$srcCls, $srcTxt] = $srcLabels[$item->source_type] ?? ['default', ucfirst((string) ($item->source_type ?? '—'))];
                                        $isToday = !empty($item->publish_at) && strpos($item->publish_at, $todayStr) === 0;
                                        $isPast = !empty($item->publish_at) && $item->publish_at < date('Y-m-d H:i:s');
                                        ?>
                                        <tr<?= $isToday ? ' style="background:#fff8e1;"' : ''; ?>>
                                            <td><?= $item->id; ?></td>
                                            <td>
                                                <?= esc($item->title); ?>
                                                <?php if (!empty($item->source_url)): ?>
                                                    <br><small class="text-muted" title="<?= esc($item->source_url); ?>"><i class="fa fa-link"></i> <?= esc(mb_substr((string) $item->source_url, 0, 60)); ?><?= mb_strlen((string) $item->source_url) > 60 ? '…' : ''; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item->status === 'failed' && !empty($item->last_error)): ?>
                                                    <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#errorModal-<?= (int) $item->id; ?>" title="Ver detalhes do erro">
                                                        <i class="fa fa-exclamation-triangle"></i> <?= esc($stTxt); ?>
                                                    </button>
                                                    <div class="modal fade" id="errorModal-<?= (int) $item->id; ?>" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title"><i class="fa fa-exclamation-triangle text-danger"></i> Erro na geração — item #<?= (int) $item->id; ?></h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Título:</strong> <?= esc($item->title); ?></p>
                                                                    <p><strong>Última tentativa:</strong> <?= esc($item->last_run_finished_at ?? '—'); ?></p>
                                                                    <p><strong>Status do run:</strong> <span class="label label-danger"><?= esc($item->last_run_status ?? '—'); ?></span></p>
                                                                    <p><strong>Mensagem de erro:</strong></p>
                                                                    <pre style="white-space:pre-wrap; word-break:break-word; background:#f9f2f2; border-left:3px solid #d9534f; padding:10px; max-height:300px; overflow:auto;"><?= esc($item->last_error); ?></pre>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <form action="<?= adminUrl('content-ai/calendar/retry'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Reenviar este item para a fila e tentar gerar novamente agora?');">
                                                                        <?= csrf_field(); ?>
                                                                        <input type="hidden" name="id" value="<?= (int) $item->id; ?>">
                                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Gerar novamente</button>
                                                                    </form>
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="label label-<?= $stCls; ?>"><?= esc($stTxt); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= esc($item->publish_at); ?>
                                                <?php if ($isToday): ?>
                                                    <br><small class="text-warning"><strong>HOJE</strong></small>
                                                <?php elseif ($isPast && in_array($item->status, ['planned', 'queued'], true)): ?>
                                                    <br><small class="text-danger"><strong>ATRASADO</strong></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="label label-<?= $srcCls; ?>"><?= esc($srcTxt); ?></span></td>
                                            <td>
                                                <?php if (!empty($item->post_id)): ?>
                                                    <a href="<?= adminUrl('edit-post/' . (int) $item->post_id); ?>" target="_blank" class="btn btn-xs btn-default" title="Editar post #<?= (int) $item->post_id; ?>">
                                                        <i class="fa fa-edit"></i> #<?= (int) $item->post_id; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item->status === 'needs_review'): ?>
                                                    <form action="<?= adminUrl('content-ai/calendar/approve'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Aprovar e publicar este post agora?');">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                        <button type="submit" class="btn btn-xs btn-warning"><i class="fa fa-check"></i> Aprovar</button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($item->status === 'failed'): ?>
                                                    <form action="<?= adminUrl('content-ai/calendar/retry'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Reenviar este item para a fila e tentar gerar novamente agora?');">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                        <button type="submit" class="btn btn-xs btn-primary" title="Gerar novamente"><i class="fa fa-refresh"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?= adminUrl('content-ai/calendar/delete'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Excluir este item do calendario? Esta acao nao pode ser desfeita.');">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                    <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr><td colspan="7" class="text-center text-muted" style="padding:30px;">Nenhum item encontrado com os filtros atuais.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>

                            <?php if (!empty($calendarPagination) && $calendarPagination['pages'] > 1):
                                $cp = $calendarPagination;
                                $qs = array_filter([
                                    'status'      => $fStatus,
                                    'source_type' => $fSource,
                                    'date'        => $fDate,
                                    'q'           => $fQuery,
                                ], fn($v) => $v !== '' && $v !== null);
                                $buildUrl = function ($p) use ($qs) {
                                    $qs['page'] = $p;
                                    return adminUrl('content-ai') . '?' . http_build_query($qs) . '#tab_calendar';
                                };
                                $cur = (int) $cp['page'];
                                $last = (int) $cp['pages'];
                                $start = max(1, $cur - 3);
                                $end = min($last, $cur + 3);
                                ?>
                                <nav style="margin-top:10px;">
                                    <ul class="pagination pagination-sm" style="margin:0;">
                                        <li class="<?= $cur <= 1 ? 'disabled' : ''; ?>">
                                            <?php if ($cur > 1): ?><a href="<?= $buildUrl($cur - 1); ?>">&laquo;</a><?php else: ?><span>&laquo;</span><?php endif; ?>
                                        </li>
                                        <?php if ($start > 1): ?>
                                            <li><a href="<?= $buildUrl(1); ?>">1</a></li>
                                            <?php if ($start > 2): ?><li class="disabled"><span>…</span></li><?php endif; ?>
                                        <?php endif; ?>
                                        <?php for ($p = $start; $p <= $end; $p++): ?>
                                            <li class="<?= $p === $cur ? 'active' : ''; ?>">
                                                <?php if ($p === $cur): ?><span><?= $p; ?></span><?php else: ?><a href="<?= $buildUrl($p); ?>"><?= $p; ?></a><?php endif; ?>
                                            </li>
                                        <?php endfor; ?>
                                        <?php if ($end < $last): ?>
                                            <?php if ($end < $last - 1): ?><li class="disabled"><span>…</span></li><?php endif; ?>
                                            <li><a href="<?= $buildUrl($last); ?>"><?= $last; ?></a></li>
                                        <?php endif; ?>
                                        <li class="<?= $cur >= $last ? 'disabled' : ''; ?>">
                                            <?php if ($cur < $last): ?><a href="<?= $buildUrl($cur + 1); ?>">&raquo;</a><?php else: ?><span>&raquo;</span><?php endif; ?>
                                        </li>
                                    </ul>
                                    <small class="text-muted" style="margin-left:10px;">Pagina <?= $cur; ?> de <?= $last; ?> · <?= $cp['per_page']; ?> por pagina</small>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_trends">
                    <?php if (!empty($xPulseItems)): ?>
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-twitter"></i> X Pulse — temas quentes nas ultimas 24h</h3>
                                <div class="pull-right">
                                    <form action="<?= adminUrl('content-ai/x-pulse/run'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Disparar X Pulse agora? Isso usa creditos da API Grok.');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-warning btn-sm"><i class="fa fa-refresh"></i> Rodar X Pulse</button>
                                    </form>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-condensed" style="margin:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:30px;">#</th>
                                            <th>Tema</th>
                                            <th style="width:80px;">Sentimento</th>
                                            <th style="width:100px;">Menções~</th>
                                            <th style="width:90px;">Relevância</th>
                                            <th>Tickers / Entidades</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($xPulseItems as $xp):
                                            $sentMap = ['positive'=>'success','negative'=>'danger','neutral'=>'default','mixed'=>'warning'];
                                            $sentCls = $sentMap[$xp->sentiment] ?? 'default';
                                            $tickers = json_decode((string) ($xp->tickers_json ?? '[]'), true) ?: [];
                                            $entities = json_decode((string) ($xp->entities_json ?? '[]'), true) ?: [];
                                        ?>
                                            <tr>
                                                <td><?= $xp->rank; ?></td>
                                                <td>
                                                    <strong><?= esc(mb_substr($xp->theme, 0, 110)); ?></strong>
                                                    <?php if (!empty($xp->summary)): ?>
                                                        <br><small class="text-muted"><?= esc(mb_substr($xp->summary, 0, 200)); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="label label-<?= $sentCls; ?>"><?= esc($xp->sentiment); ?></span></td>
                                                <td><?= number_format((int) $xp->mentions_estimate, 0, ',', '.'); ?></td>
                                                <td><?= $xp->relevance_score; ?>/100</td>
                                                <td>
                                                    <?php foreach (array_slice($tickers, 0, 4) as $tk): ?>
                                                        <small class="label label-primary">$<?= esc($tk); ?></small>
                                                    <?php endforeach; ?>
                                                    <?php foreach (array_slice($entities, 0, 4) as $en): ?>
                                                        <small class="label label-default"><?= esc($en); ?></small>
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif (!empty($settings->x_pulse_enabled)): ?>
                        <div class="callout callout-warning">
                            <h4><i class="fa fa-twitter"></i> X Pulse habilitado, sem snapshot ainda</h4>
                            <p>Rode <code>php spark x:pulse</code> ou clique abaixo para coletar agora.</p>
                            <form action="<?= adminUrl('content-ai/x-pulse/run'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Disparar X Pulse agora? Isso usa creditos da API Grok.');">
                                <?= csrf_field(); ?>
                                <button class="btn btn-warning btn-sm"><i class="fa fa-refresh"></i> Rodar X Pulse</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($sourceHealth)): ?>
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-heartbeat"></i> Saude das fontes (ultimas 24h)</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-condensed" style="margin:0;">
                                    <thead>
                                        <tr>
                                            <th>Fonte</th>
                                            <th style="width:90px;">Status</th>
                                            <th style="width:90px;">Tentativas</th>
                                            <th style="width:90px;">Sucessos</th>
                                            <th style="width:90px;">Falhas</th>
                                            <th style="width:90px;">Itens</th>
                                            <th style="width:110px;">Latencia</th>
                                            <th>Ultima</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sourceHealth as $sh):
                                            $statusCls = $sh['status'] === 'healthy' ? 'success' : ($sh['status'] === 'down' ? 'danger' : 'warning');
                                            $statusLbl = $sh['status'] === 'healthy' ? 'OK' : ($sh['status'] === 'down' ? 'OFFLINE' : 'DEGRADADA');
                                        ?>
                                            <tr>
                                                <td><code><?= esc($sh['source']); ?></code></td>
                                                <td><span class="label label-<?= $statusCls; ?>"><?= $statusLbl; ?></span></td>
                                                <td><?= $sh['attempts']; ?></td>
                                                <td><?= $sh['successes']; ?> <small class="text-muted">(<?= $sh['success_rate']; ?>%)</small></td>
                                                <td><?= $sh['failures']; ?></td>
                                                <td><?= $sh['items']; ?></td>
                                                <td><?= $sh['avg_latency_ms']; ?> ms</td>
                                                <td><small><?= esc($sh['last_attempt']); ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="box box-solid" id="popular-control">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-shield"></i> Controle de Populares (anti-repeticao / blocklist)</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <p class="text-muted" style="margin-top:0;">Posts que ja alimentaram a esteira de populares. Quem passa do limite de derivacoes e <strong>auto-excluido</strong>; voce tambem pode excluir/reabilitar manualmente para dar espaco a outras verticais.</p>
                            <form action="<?= adminUrl('content-ai/popular/block'); ?>" method="post" class="form-inline" style="margin-bottom:12px;" onsubmit="return confirm('Excluir este post da esteira de populares?');">
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <label>Excluir post por ID&nbsp;</label>
                                    <input type="number" class="form-control input-sm" name="post_id" min="1" placeholder="ID do post" required>
                                </div>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-ban"></i> Excluir dos populares</button>
                            </form>
                            <?php if (!empty($popularControl)): ?>
                            <table class="table table-condensed table-hover" style="margin:0;">
                                <thead>
                                    <tr>
                                        <th style="width:70px;">Post</th>
                                        <th>Titulo</th>
                                        <th style="width:110px;">Derivacoes</th>
                                        <th style="width:150px;">Ultima</th>
                                        <th style="width:140px;">Status</th>
                                        <th style="width:120px;">Acao</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($popularControl as $pc): ?>
                                        <tr>
                                            <td><code>#<?= (int) $pc->post_id; ?></code></td>
                                            <td><?= esc($pc->title ?? '—'); ?></td>
                                            <td><span class="badge"><?= (int) $pc->derived_count; ?></span></td>
                                            <td><small><?= esc($pc->last_derived_at ?? '—'); ?></small></td>
                                            <td>
                                                <?php if (!empty($pc->blocked)): ?>
                                                    <span class="label label-danger">Excluido<?= ($pc->blocked_reason ?? '') === 'auto_cap' ? ' (auto)' : ''; ?></span>
                                                <?php else: ?>
                                                    <span class="label label-success">Ativo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($pc->blocked)): ?>
                                                    <form action="<?= adminUrl('content-ai/popular/unblock'); ?>" method="post" style="display:inline;">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="post_id" value="<?= (int) $pc->post_id; ?>">
                                                        <button class="btn btn-xs btn-default"><i class="fa fa-undo"></i> Reabilitar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?= adminUrl('content-ai/popular/block'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Excluir este post da esteira de populares?');">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="post_id" value="<?= (int) $pc->post_id; ?>">
                                                        <input type="hidden" name="title" value="<?= esc($pc->title ?? '', 'attr'); ?>">
                                                        <button class="btn btn-xs btn-warning"><i class="fa fa-ban"></i> Excluir</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p class="text-muted" style="margin:0;">Nenhum post derivado ainda. Assim que a esteira de populares rodar, o historico aparece aqui.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tendencias</h3>
                            <div class="pull-right">
                                <form action="<?= adminUrl('content-ai/trends/fetch'); ?>" method="post" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-info btn-sm"><i class="fa fa-refresh"></i> Buscar tendencias</button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <form id="form-trends" action="<?= adminUrl('content-ai/trends/add'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width:30px"><input type="checkbox" id="check-all-trends"></th>
                                        <th>Titulo</th>
                                        <th>Fonte</th>
                                        <th style="width:60px">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($trendItems)):
                                        foreach ($trendItems as $trend): ?>
                                            <tr class="<?= !empty($trend->used) ? 'text-muted' : ''; ?>">
                                                <td>
                                                    <?php if (empty($trend->used)): ?>
                                                        <input type="checkbox" name="trend_ids[]" value="<?= $trend->id; ?>" class="trend-check">
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($trend->title); ?></td>
                                                <td><small class="label label-default"><?= esc($trend->source); ?></small></td>
                                                <td>
                                                    <?php if (!empty($trend->used)): ?>
                                                        <small class="label label-success">Publicado</small>
                                                    <?php else: ?>
                                                        <small class="label label-info">Disponivel</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    endif; ?>
                                    </tbody>
                                </table>
                                <div style="margin-top:15px;">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-send"></i> Enviar para publicacao</button>
                                    <span class="text-muted" style="margin-left:10px;" id="trends-count">0 selecionadas</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var checkAll = document.getElementById('check-all-trends');
                    var checks = document.querySelectorAll('.trend-check');
                    var countEl = document.getElementById('trends-count');
                    function updateCount() {
                        var c = document.querySelectorAll('.trend-check:checked').length;
                        countEl.textContent = c + ' selecionada' + (c !== 1 ? 's' : '');
                    }
                    if (checkAll) {
                        checkAll.addEventListener('change', function() {
                            checks.forEach(function(cb) { cb.checked = checkAll.checked; });
                            updateCount();
                        });
                    }
                    checks.forEach(function(cb) { cb.addEventListener('change', updateCount); });
                });
                </script>

                <div class="tab-pane" id="tab_settings">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Configuracoes da rotina</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= adminUrl('content-ai/settings'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Publicacao direta</label>
                                            <select class="form-control" name="auto_publish">
                                                <option value="1" <?= !empty($settings->auto_publish) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->auto_publish) ? 'selected' : ''; ?>>Nao (revisao)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Posts por dia</label>
                                            <input type="number" class="form-control" name="posts_per_day" min="1" value="<?= (int) ($settings->posts_per_day ?? 1); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Usuario padrao (ID)</label>
                                            <input type="number" class="form-control" name="default_user_id" min="1" value="<?= (int) ($settings->default_user_id ?? 1); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 1</label>
                                            <input type="time" class="form-control" name="run_time_1" value="<?= !empty($settings->run_time_1) ? substr($settings->run_time_1, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 2</label>
                                            <input type="time" class="form-control" name="run_time_2" value="<?= !empty($settings->run_time_2) ? substr($settings->run_time_2, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 3</label>
                                            <input type="time" class="form-control" name="run_time_3" value="<?= !empty($settings->run_time_3) ? substr($settings->run_time_3, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tom padrao</label>
                                            <select name="default_tone" class="form-control">
                                                <option value="professional" <?= ($settings->default_tone ?? '') == 'professional' ? 'selected' : ''; ?>>Profissional</option>
                                                <option value="formal" <?= ($settings->default_tone ?? '') == 'formal' ? 'selected' : ''; ?>>Formal</option>
                                                <option value="inspirational" <?= ($settings->default_tone ?? '') == 'inspirational' ? 'selected' : ''; ?>>Inspiracional</option>
                                                <option value="persuasive" <?= ($settings->default_tone ?? '') == 'persuasive' ? 'selected' : ''; ?>>Persuasivo</option>
                                                <option value="academic" <?= ($settings->default_tone ?? '') == 'academic' ? 'selected' : ''; ?>>Academico</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tamanho padrao</label>
                                            <select name="default_length" class="form-control">
                                                <option value="short" <?= ($settings->default_length ?? '') == 'short' ? 'selected' : ''; ?>>Curto</option>
                                                <option value="medium" <?= ($settings->default_length ?? '') == 'medium' ? 'selected' : ''; ?>>Medio</option>
                                                <option value="long" <?= ($settings->default_length ?? '') == 'long' ? 'selected' : ''; ?>>Longo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Idioma</label>
                                            <select name="lang_id" class="form-control">
                                                <?php if (!empty($activeLanguages)):
                                                    foreach ($activeLanguages as $lang): ?>
                                                        <option value="<?= $lang->id; ?>" <?= ($settings->lang_id ?? $activeLang->id) == $lang->id ? 'selected' : ''; ?>><?= esc($lang->name); ?></option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (curto)</label>
                                            <input type="number" class="form-control" name="length_short_words" min="200" value="<?= (int) ($settings->length_short_words ?? 900); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (medio)</label>
                                            <input type="number" class="form-control" name="length_medium_words" min="400" value="<?= (int) ($settings->length_medium_words ?? 1400); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (longo)</label>
                                            <input type="number" class="form-control" name="length_long_words" min="600" value="<?= (int) ($settings->length_long_words ?? 2000); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Categorias permitidas</label>
                                    <select name="allowed_category_ids[]" class="form-control select2" multiple>
                                        <?php if (!empty($categories)):
                                            foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->id; ?>" <?= in_array($cat->id, $allowedCategoryIds, true) ? 'selected' : ''; ?>><?= esc($cat->name); ?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Guidelines de voz</label>
                                    <textarea class="form-control" name="voice_guidelines" rows="3"><?= esc($settings->voice_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines de SEO</label>
                                    <textarea class="form-control" name="seo_guidelines" rows="3"><?= esc($settings->seo_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Prompt template (com placeholders)</label>
                                    <textarea class="form-control" name="prompt_template" rows="8"><?= esc($settings->prompt_template ?? ''); ?></textarea>
                                    <p class="help-block">Use {category_name}, {category_guidelines}, {title}, {instructions}, {tone}, {length_words}, {voice}, {seo}, {rules}.</p>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines de imagem</label>
                                    <textarea class="form-control" name="image_guidelines" rows="3"><?= esc($settings->image_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Prompt template de imagem</label>
                                    <textarea class="form-control" name="image_prompt_template" rows="6"><?= esc($settings->image_prompt_template ?? ''); ?></textarea>
                                    <p class="help-block">Use {title}, {summary}, {category_name}, {category_guidelines}, {image_prompt}, {image_guidelines}.</p>
                                </div>
                                <div class="form-group">
                                    <label>Regras de categoria (JSON)</label>
                                    <textarea class="form-control" name="category_rules_json" rows="5"><?= esc($settings->category_rules_json ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines por categoria (JSON)</label>
                                    <textarea class="form-control" name="category_guidelines_json" rows="5"><?= esc($settings->category_guidelines_json ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Auto adicionar tendencias</label>
                                            <select class="form-control" name="auto_add_trends">
                                                <option value="1" <?= !empty($settings->auto_add_trends) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->auto_add_trends) ? 'selected' : ''; ?>>Nao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tendencias por dia</label>
                                            <input type="number" class="form-control" name="trends_per_day" min="1" value="<?= (int) ($settings->trends_per_day ?? 3); ?>">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4><i class="fa fa-tags"></i> Palavras-chave de relevancia (filtro de trends)</h4>
                                <p class="text-muted">
                                    Define o vocabulario que decide se uma trend e "do nosso escopo" antes de entrar no calendario.
                                    Uma palavra/frase por linha. Linhas iniciando com <code>#</code> sao ignoradas (comentarios).
                                    Tudo e normalizado (lowercase, sem acentos) — pode escrever com ou sem acento.
                                </p>
                                <?php
                                $tkRaw = $settings->trend_keywords_json ?? '';
                                $tk = !empty($tkRaw) ? json_decode($tkRaw, true) : null;
                                if (!is_array($tk)) $tk = \App\Models\ContentAISettingsModel::getDefaultTrendKeywords();
                                $tkPhrases  = implode("\n", $tk['phrases'] ?? []);
                                $tkWords    = implode("\n", $tk['words'] ?? []);
                                $tkContext  = implode("\n", $tk['context_words'] ?? []);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Frases (match por substring)</label>
                                            <textarea class="form-control" name="trend_keywords_phrases" rows="10" style="font-family:monospace;font-size:12px;"><?= esc($tkPhrases); ?></textarea>
                                            <p class="help-block">Use para combinacoes de 2+ palavras (ex: <code>mercado financeiro</code>). Ja sao especificas o suficiente para nao precisar de palavra-inteira.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (match palavra inteira)</label>
                                            <textarea class="form-control" name="trend_keywords_words" rows="10" style="font-family:monospace;font-size:12px;"><?= esc($tkWords); ?></textarea>
                                            <p class="help-block">Palavras simples — o sistema usa borda de palavra (\b) para evitar falsos positivos. Ex: <code>credito</code> nao casa em "creditorio".</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contextuais (siglas curtas)</label>
                                            <textarea class="form-control" name="trend_keywords_context" rows="10" style="font-family:monospace;font-size:12px;"><?= esc($tkContext); ?></textarea>
                                            <p class="help-block">Siglas/abreviacoes (PIB, Fed, CDI, ETF...). Mesmo tratamento de palavra inteira mas listadas separadas para clareza.</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-info"><i class="fa fa-info-circle"></i> Mudancas afetam apenas trends coletadas a partir do proximo cron — trends ja armazenadas mantem o estado anterior.</p>

                                <hr>
                                <h4><i class="fa fa-twitter"></i> X Pulse (analise de tendencias no X via Grok)</h4>
                                <p class="text-muted">
                                    Usa a API do <strong>Grok (xAI)</strong> com busca em tempo real no X para identificar temas financeiros quentes.
                                    Funciona como <strong>3a fonte de sinal</strong> ao lado de trends (RSS) e populares (analytics do portal).
                                    Quando habilitado, alimenta o editor IA com convergencia de sinais — temas que aparecem tanto em trends quanto no X
                                    sao priorizados (sinal duplo de demanda real).
                                </p>
                                <p>
                                    <strong>Custo:</strong> ~$0,025 por execucao (cada chamada com live search). Rodando 1x/dia = ~$0,75/mes.
                                    <br><strong>Setup:</strong> definir <code>GROK_API_KEY</code> ou <code>XAI_API_KEY</code> no <code>.env</code>.
                                    Sem a chave, o sistema apenas pula a etapa silenciosamente.
                                </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Habilitar X Pulse</label>
                                            <select class="form-control" name="x_pulse_enabled">
                                                <option value="1" <?= !empty($settings->x_pulse_enabled) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->x_pulse_enabled) ? 'selected' : ''; ?>>Nao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Janela de analise</label>
                                            <?php $xw = (int) ($settings->x_window_hours ?? 6); ?>
                                            <select class="form-control" name="x_window_hours">
                                                <?php foreach ([3,6,12,24] as $h): ?>
                                                    <option value="<?= $h; ?>" <?= $xw === $h ? 'selected' : ''; ?>>Ultimas <?= $h; ?>h</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Temas por execucao</label>
                                            <input type="number" class="form-control" name="x_themes_per_day" min="3" max="30" value="<?= (int) ($settings->x_themes_per_day ?? 10); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Menções minimas</label>
                                            <input type="number" class="form-control" name="x_min_mentions" min="0" value="<?= (int) ($settings->x_min_mentions ?? 100); ?>">
                                            <p class="help-block">Filtra temas com pouco volume.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Modelo Grok</label>
                                            <?php
                                            $xm = (string) ($settings->x_grok_model ?? 'grok-4.3');
                                            $grokModels = [
                                                'grok-4.3'                       => 'grok-4.3 (recomendado — 1M ctx)',
                                                'grok-4.20-0309-reasoning'       => 'grok-4.20 reasoning',
                                                'grok-4.20-0309-non-reasoning'   => 'grok-4.20 non-reasoning',
                                                'grok-4.20-multi-agent-0309'     => 'grok-4.20 multi-agent',
                                                'grok-build-0.1'                 => 'grok-build-0.1 (256k, mais barato)',
                                            ];
                                            // Preserve any custom value the user already saved (forward-compat)
                                            if ($xm && !isset($grokModels[$xm])) {
                                                $grokModels = [$xm => $xm . ' (custom)'] + $grokModels;
                                            }
                                            ?>
                                            <select class="form-control" name="x_grok_model">
                                                <?php foreach ($grokModels as $k => $label): ?>
                                                    <option value="<?= $k; ?>" <?= $xm === $k ? 'selected' : ''; ?>><?= $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="help-block">Doc oficial: <a href="https://docs.x.ai/developers/models" target="_blank">docs.x.ai/developers/models</a></p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Ultima execucao</label>
                                            <input type="text" class="form-control" value="<?= esc($settings->last_run_x_pulse ?? '—'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Prompt do X Pulse Analyzer</label>
                                    <textarea class="form-control" name="x_pulse_prompt" rows="10" style="font-family:monospace;font-size:12px;"><?= esc($settings->x_pulse_prompt ?? ''); ?></textarea>
                                    <p class="help-block">Placeholders: <code>{themes_per_day}</code>, <code>{window_hours}</code>, <code>{min_mentions}</code>. Use para refinar escopo, fontes preferidas, criterios de relevancia.</p>
                                </div>
                                <p class="text-muted"><strong>X como fonte de pauta</strong> — alem de repriorizar tendencias, os temas quentes do X viram artigos proprios (o editor IA transforma cada tema em pauta).</p>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Habilitar X como fonte de pauta</label>
                                            <select class="form-control" name="x_seed_enabled">
                                                <option value="1" <?= !empty($settings->x_seed_enabled) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->x_seed_enabled) ? 'selected' : ''; ?>>Nao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Artigos do X por dia</label>
                                            <input type="number" class="form-control" name="x_seed_per_day" min="0" max="20" value="<?= (int) ($settings->x_seed_per_day ?? 0); ?>">
                                            <p class="help-block">Quantos artigos serao criados a partir dos temas quentes do X. 0 = desligado.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ultima execucao (X seed)</label>
                                            <input type="text" class="form-control" value="<?= esc($settings->last_run_x_seed ?? '—'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4><i class="fa fa-fire"></i> Conteudos Populares (Derivados do Portal)</h4>
                                <p class="text-muted">
                                    Analisa diariamente os posts com mais trafego e engajamento do portal e o editor-chefe IA propoe NOVAS pautas derivadas (aprofundamentos, atualizacoes, angulos novos).
                                    Esses artigos sao <strong>ADICIONADOS</strong> ao total diario — somam aos posts da linha editorial (tendencias). Se nao houver dados suficientes na janela escolhida, esta rotina e pulada silenciosamente.
                                </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Habilitar conteudos populares</label>
                                            <select class="form-control" name="popular_enabled">
                                                <option value="1" <?= !empty($settings->popular_enabled) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->popular_enabled) ? 'selected' : ''; ?>>Nao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Posts populares por dia</label>
                                            <input type="number" class="form-control" name="popular_posts_per_day" min="0" max="20" value="<?= (int) ($settings->popular_posts_per_day ?? 0); ?>">
                                            <p class="help-block">Soma ao total da linha editorial.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Janela de analise</label>
                                            <?php $popWin = (int) ($settings->popular_window_days ?? 7); ?>
                                            <select class="form-control" name="popular_window_days">
                                                <option value="1"  <?= $popWin === 1  ? 'selected' : ''; ?>>Ultimas 24h</option>
                                                <option value="3"  <?= $popWin === 3  ? 'selected' : ''; ?>>Ultimos 3 dias</option>
                                                <option value="7"  <?= $popWin === 7  ? 'selected' : ''; ?>>Ultimos 7 dias</option>
                                                <option value="30" <?= $popWin === 30 ? 'selected' : ''; ?>>Ultimos 30 dias</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Metrica</label>
                                            <?php $popMetric = (string) ($settings->popular_metric ?? 'mixed'); ?>
                                            <select class="form-control" name="popular_metric">
                                                <option value="mixed"      <?= $popMetric === 'mixed'      ? 'selected' : ''; ?>>Mista (60% views + 40% engajamento)</option>
                                                <option value="pageviews"  <?= $popMetric === 'pageviews'  ? 'selected' : ''; ?>>Pageviews</option>
                                                <option value="engagement" <?= $popMetric === 'engagement' ? 'selected' : ''; ?>>Engajamento (comentarios + reacoes)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pageviews minimos para considerar popular</label>
                                            <input type="number" class="form-control" name="popular_min_pageviews" min="0" value="<?= (int) ($settings->popular_min_pageviews ?? 5); ?>">
                                            <p class="help-block">Posts abaixo desse limiar sao ignorados na selecao.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Ultima execucao da rotina popular</label>
                                            <input type="text" class="form-control" value="<?= esc($settings->last_run_popular ?? '—'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted" style="margin-top:6px;"><strong>Anti-repeticao / diversidade</strong> — evita minerar o mesmo hit infinitamente e da espaco as outras verticais.</p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Max. derivacoes por post</label>
                                            <input type="number" class="form-control" name="popular_max_derivations" min="0" value="<?= (int) ($settings->popular_max_derivations ?? 2); ?>">
                                            <p class="help-block">Ao atingir, o post e auto-excluido dos populares. 0 = sem limite.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cooldown (dias)</label>
                                            <input type="number" class="form-control" name="popular_cooldown_days" min="0" value="<?= (int) ($settings->popular_cooldown_days ?? 14); ?>">
                                            <p class="help-block">Nao re-derivar o mesmo post dentro dessa janela.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Diversidade por vertical</label>
                                            <select class="form-control" name="popular_diversity_enabled">
                                                <option value="1" <?= !empty($settings->popular_diversity_enabled) ? 'selected' : ''; ?>>Ligada</option>
                                                <option value="0" <?= empty($settings->popular_diversity_enabled) ? 'selected' : ''; ?>>Desligada</option>
                                            </select>
                                            <p class="help-block">Aplica os pesos de topico aos populares.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Max. por categoria (cardapio)</label>
                                            <input type="number" class="form-control" name="popular_per_category_cap" min="0" value="<?= (int) ($settings->popular_per_category_cap ?? 2); ?>">
                                            <p class="help-block">Limita candidatos por vertical. 0 = sem limite.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Prompt do Editor-Chefe IA (Populares)</label>
                                    <textarea class="form-control" name="popular_editor_prompt" rows="8"><?= esc($settings->popular_editor_prompt ?? ''); ?></textarea>
                                    <p class="help-block">Placeholders: {popular_per_day}, {window_days}, {metric}, {categories}, {recent_titles}.</p>
                                </div>

                                <hr>
                                <h4><i class="fa fa-newspaper-o"></i> Editor-Chefe IA (Distribuicao de Topicos)</h4>
                                <p class="text-muted">Configure o peso de cada topico. A IA editora vai distribuir os posts diarios respeitando esses percentuais.</p>

                                <?php
                                $topicWeights = ['cambio' => 18, 'credito' => 18, 'consorcio' => 15, 'seguro' => 15, 'investimentos' => 12, 'economia' => 22];
                                if (!empty($settings->topic_weights_json)) {
                                    $decoded = json_decode($settings->topic_weights_json, true);
                                    if (is_array($decoded)) $topicWeights = $decoded;
                                }
                                $topicLabels = [
                                    'cambio' => 'Cambio e Trade Finance',
                                    'credito' => 'Credito Empresarial',
                                    'consorcio' => 'Consorcio',
                                    'seguro' => 'Seguro de Vida',
                                    'investimentos' => 'Investimentos',
                                    'economia' => 'Economia e Mercado',
                                    'tecnologia' => 'Tecnologia para Negocios',
                                ];
                                ?>
                                <div class="row">
                                    <?php foreach ($topicLabels as $key => $label): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?= $label; ?> <span class="text-muted">(%)</span></label>
                                            <input type="number" class="form-control topic-weight" name="topic_weights[<?= $key; ?>]" min="0" max="100" step="5" value="<?= (int) ($topicWeights[$key] ?? 0); ?>">
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <p id="weight-total" class="text-info" style="font-weight:bold;"></p>

                                <div class="form-group">
                                    <label>Prompt do Editor-Chefe IA</label>
                                    <textarea class="form-control" name="editor_prompt" rows="8"><?= esc($settings->editor_prompt ?? ''); ?></textarea>
                                    <p class="help-block">Placeholders: {posts_per_day}, {topic_weights}, {categories}, {recent_titles}. A IA usara este prompt para decidir quais artigos produzir automaticamente.</p>
                                </div>

                                <button type="submit" class="btn btn-success">Salvar configuracoes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var weights = document.querySelectorAll('.topic-weight');
    var totalEl = document.getElementById('weight-total');
    function updateTotal() {
        var sum = 0;
        weights.forEach(function(w) { sum += parseInt(w.value) || 0; });
        totalEl.textContent = 'Total: ' + sum + '%';
        totalEl.style.color = sum === 100 ? '#00a65a' : '#dd4b39';
    }
    weights.forEach(function(w) { w.addEventListener('input', updateTotal); });
    updateTotal();
});
</script>
