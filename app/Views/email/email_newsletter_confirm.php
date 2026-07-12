<?= view('email/_header', ['subject' => $subject]); ?>
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <h1 style="font-size: 24px;line-height: 30px;font-weight: 700;text-align: left;text-transform: none;margin: 0 0 14px;color:#0a1a3a;">
                                <?= esc($subject); ?>
                            </h1>
                            <p style="font-size:15px;line-height:24px;color:#222;margin:0 0 24px;">
                                <?= esc($intro); ?>
                            </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 24px;">
                                <tr>
                                    <td style="background:#0a1a3a;text-align:center;">
                                        <a href="<?= esc($confirmUrl); ?>" style="display:inline-block;color:#fff;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:1.5px;text-transform:uppercase;padding:16px 32px;">
                                            <?= esc($buttonText); ?>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:12px;line-height:18px;color:#777;margin:24px 0 0;">
                                <?= lang('Newsletter.em_confirm_fallback'); ?><br>
                                <a href="<?= esc($confirmUrl); ?>" style="color:#b08a3a;word-break:break-all;"><?= esc($confirmUrl); ?></a>
                            </p>
                            <p style="font-size:12px;line-height:18px;color:#999;margin:18px 0 0;">
                                <?= lang('Newsletter.em_confirm_ignore'); ?>

                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= view('email/_footer', ['subscriber' => $subscriber, 'showUnsubscribeLink' => false]); ?>
