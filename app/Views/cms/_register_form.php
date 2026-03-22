<?php 
// Formulário simples de registro embutido para LP (CMS)
?>
<div class="cms-register" style="border:1px solid #eee; border-radius:10px; padding:16px; background:#fff;">
    <?= loadView('partials/_messages'); ?>
    <form action="<?= base_url('register-post'); ?>" method="post" class="needs-validation">
        <?= csrf_field(); ?>
        <input type="hidden" name="sys_lang_id" value="<?= isset($activeLang->id)? (int)$activeLang->id : 1; ?>">
        <div class="row">
            <div class="col-md-4 mb-2"><input type="text" name="username" class="form-control" placeholder="Seu nome" value="<?= old('username'); ?>" required autocomplete="off"></div>
            <div class="col-md-4 mb-2"><input type="email" name="email" class="form-control" placeholder="Seu e-mail" value="<?= old('email'); ?>" required></div>
            <div class="col-md-2 mb-2"><input type="password" name="password" class="form-control" placeholder="Senha" required></div>
            <div class="col-md-2 mb-2"><input type="password" name="confirm_password" class="form-control" placeholder="Confirmar" required></div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-2">
                <label style="font-weight:400;"><input type="checkbox" name="terms_conditions" value="1" required> Li e aceito os <a href="<?= getPageLinkByDefaultName('terms_conditions', $activeLang->id ?? 1); ?>" target="_blank"><strong>Termos</strong></a></label>
            </div>
        </div>
        <?php if (isRecaptchaEnabled($generalSettings)): ?>
            <div class="mb-3">
                <?php reCaptcha('generate', $generalSettings); ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-lg btn-custom">Criar minha conta grátis</button>
                <a href="<?= generateURL('register'); ?>" class="btn btn-outline-secondary" style="margin-left:6px;">Abrir página completa</a>
            </div>
        </div>
    </form>
</div>

