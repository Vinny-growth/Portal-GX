<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Diagnósticos</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <p><strong>Database selecionado (SELECT DATABASE()):</strong> <?= esc($database ?? ''); ?></p>
                <p><strong>Tabelas wm_% encontradas:</strong> 
                    <?php if (!empty($wm_tables)): ?>
                        <?= esc(implode(', ', $wm_tables)); ?>
                    <?php else: ?>
                        nenhuma
                    <?php endif; ?>
                </p>
                <p><strong>Users (count):</strong> <?= esc((string)($users_count ?? '')); ?></p>
                <p><strong>Tabela migrations existe?</strong> <?= esc($has_migrations ?? ''); ?></p>
                <hr>
                <p>Se as tabelas wm_% não aparecem:
                    <br>- Confirme o banco (acima) é o mesmo em que importou o SQL.
                    <br>- Se preciso, reimporte: <code>app/Database/SQL/wealth_manager_schema.sql</code> no banco mostrado acima.</p>
                <p>Após as tabelas existirem, teste:
                    <br>- <a href="<?= base_url('wealth'); ?>" target="_blank">/wealth</a>
                    <br>- <a href="<?= base_url('wealth/conversa'); ?>" target="_blank">/wealth/conversa</a>
                    <br>- <a href="<?= base_url('wealth/resultado'); ?>" target="_blank">/wealth/resultado</a>
                </p>
            </div>
        </div>
    </div>
</div>

