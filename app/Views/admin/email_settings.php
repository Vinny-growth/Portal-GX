<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('email_settings'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/emailSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_service'); ?></label>
                        <select name="mail_service" class="form-control" onchange="showSmtpOptions(this.value);">
                            <option value="swift" <?= $generalSettings->mail_service == "swift" ? 'selected' : ''; ?>>Swift Mailer</option>
                            <option value="php" <?= $generalSettings->mail_service == "php" ? 'selected' : ''; ?>>PHP Mailer</option>
                            <option value="mailjet" <?= $generalSettings->mail_service == "mailjet" ? 'selected' : ''; ?>>Mailjet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_protocol'); ?></label>
                        <select name="mail_protocol" class="form-control" id="protocol_selection">
                            <option value="smtp" <?= $generalSettings->mail_protocol == "smtp" ? 'selected' : ''; ?>>SMTP</option>
                            <option value="sendmail" <?= $generalSettings->mail_protocol == "sendmail" ? 'selected' : ''; ?>><?= trans('sendmail'); ?></option>
                            <option value="mail" <?= $generalSettings->mail_protocol == "mail" ? 'selected' : ''; ?>><?= trans('mail'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_title'); ?></label>
                        <input type="text" class="form-control" name="mail_title" placeholder="<?= trans('mail_title'); ?>" value="<?= esc($generalSettings->mail_title); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_host'); ?></label>
                        <input type="text" class="form-control" name="mail_host" placeholder="<?= trans('mail_host'); ?>" value="<?= esc($generalSettings->mail_host); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_port'); ?></label>
                        <input type="text" class="form-control" name="mail_port" placeholder="<?= trans('mail_port'); ?>" value="<?= esc($generalSettings->mail_port); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_username'); ?></label>
                        <input type="text" class="form-control" name="mail_username" placeholder="<?= trans('mail_username'); ?>" value="<?= esc($generalSettings->mail_username); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_password'); ?></label>
                        <input type="password" class="form-control" name="mail_password" placeholder="<?= trans('mail_password'); ?>" value="<?= esc($generalSettings->mail_password); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_encryption'); ?></label>
                        <select name="mail_encryption" class="form-control">
                            <option value="tls" <?= $generalSettings->mail_encryption == "tls" ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?= $generalSettings->mail_encryption == "ssl" ? 'selected' : ''; ?>>SSL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('mail_reply_to'); ?></label>
                        <input type="email" class="form-control" name="mail_reply_to" placeholder="<?= trans('mail_reply_to'); ?>" value="<?= esc($generalSettings->mail_reply_to); ?>">
                    </div>

                    <div class="callout callout-info" style="max-width: 500px;margin-top: 30px;">
                        <h4><?= trans('gmail_smtp'); ?></h4>
                        <p><?= trans('gmail_warning'); ?></p>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" name="submit" value="email" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('email_verification'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/emailVerificationPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label><?= trans('email_verification'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="email_verification" value="1" id="email_verification_1"
                                       class="square-purple" <?= $generalSettings->email_verification == 1 ? 'checked' : ''; ?>>
                                <label for="email_verification_1" class="option-label"><?= trans('enable'); ?></label>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 col-option">
                                <input type="radio" name="email_verification" value="0" id="email_verification_2"
                                       class="square-purple" <?= $generalSettings->email_verification != 1 ? 'checked' : ''; ?>>
                                <label for="email_verification_2" class="option-label"><?= trans('disable'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('send_test_email'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/sendTestEmailPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('email'); ?></label>
                        <input type="text" class="form-control" name="email" placeholder="<?= trans('email'); ?>" required>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('send_email'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showSmtpOptions(mailService) {
        if (mailService == "mailjet") {
            $("#protocol_selection").attr("disabled", true);
        } else {
            $("#protocol_selection").attr("disabled", false);
        }
    }
</script>