<form method="post" action="<?= base_url('contact-post'); ?>" class="form-inline" style="margin:12px 0;">
    <?= csrf_field(); ?>
    <input type="text" class="form-control" name="name" placeholder="Seu nome" required>
    <input type="email" class="form-control" name="email" placeholder="Seu e-mail" required>
    <button class="btn btn-custom">Enviar</button>
</form>

