<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <h1 class="page-title">Agendar consultoria</h1>
            <div class="page-content">
                <?= loadView('partials/_messages'); ?>
                <form action="<?= base_url('wealth/agendar'); ?>" method="post" class="needs-validation">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="nome" value="<?= esc(user()->username ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>E-mail</label>
                        <input type="email" class="form-control" name="email" value="<?= esc(user()->email ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Telefone</label>
                        <input type="text" class="form-control" name="telefone" value="" placeholder="(11) 99999-9999">
                    </div>
                    <div class="mb-3">
                        <label>Preferência de horário</label>
                        <input type="text" class="form-control" name="preferencia_horario" placeholder="Ex: terça à tarde">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-lg btn-custom">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

