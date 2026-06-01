<form method="post" action="<?= base_url('contact-post'); ?>" class="form-inline gx-inline-contact-form" style="margin:12px 0;">
    <?= csrf_field(); ?>
    <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
    <input type="hidden" name="lead_origin" value="Bloco de formulário CMS">
    <input type="hidden" name="message" value="Contato iniciado pelo formulário rápido da página CMS.">
    <input type="text" name="message_content" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">
    <div class="gx-inline-contact-grid">
        <input type="text" class="form-control" name="name" placeholder="Seu nome" value="<?= esc(old('name')); ?>" required>
        <input type="email" class="form-control" name="email" placeholder="Seu e-mail" value="<?= esc(old('email')); ?>" required>
        <?= view('partials/_lead_phone_field', [
            'fieldIdPrefix' => 'cms-contact-phone',
            'wrapperClass' => 'gx-inline-contact-phone',
            'label' => false,
            'countryClass' => 'form-control',
            'inputClass' => 'form-control',
            'countryValue' => old('phone_country'),
            'phoneValue' => old('phone'),
        ]); ?>
        <button class="btn btn-custom">Enviar</button>
    </div>
</form>
<style>
    .gx-inline-contact-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
        gap: 10px;
        align-items: start;
    }

    .gx-inline-contact-grid .form-control,
    .gx-inline-contact-grid .btn {
        width: 100%;
    }

    .gx-inline-contact-phone {
        margin: 0;
    }

    @media (max-width: 991px) {
        .gx-inline-contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
