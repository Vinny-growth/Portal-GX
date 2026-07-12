<?= view('email/_header', ['subject' => $subject]); ?>
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p style="font-size:11px;line-height:14px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#b08a3a;margin:0 0 10px;">
                                <?= brandLang('Newsletter.lay_copyright'); ?>

                            </p>
                            <h1 style="font-size: 26px;line-height: 32px;font-weight: 700;text-align: left;text-transform: none;margin: 0 0 14px;color:#0a1a3a;">
                                <?= esc($subject); ?>
                            </h1>
                            <p style="font-size:15px;line-height:24px;color:#222;margin:0 0 28px;">
                                <?= esc($intro); ?>
                            </p>

                            <?php if (!empty($magnets)): ?>
                                <p style="font-size:11px;line-height:14px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#0a1a3a;margin:0 0 14px;">
                                    <?= lang('Newsletter.em_welcome_material'); ?>

                                </p>
                                <?php foreach ($magnets as $m): ?>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin:0 0 20px;border:1px solid #ececec;">
                                        <tr>
                                            <?php if (!empty($m['cover_image'])): ?>
                                                <td style="width:140px;padding:14px;vertical-align:top;">
                                                    <img src="<?= esc($m['cover_image']); ?>" alt="" style="display:block;width:140px;height:auto;">
                                                </td>
                                            <?php endif; ?>
                                            <td style="padding:18px;vertical-align:top;">
                                                <p style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#b08a3a;margin:0 0 6px;">
                                                    <?= esc($m['line_name']); ?>
                                                </p>
                                                <h3 style="font-size:17px;line-height:22px;font-weight:700;margin:0 0 8px;color:#0a1a3a;">
                                                    <?= esc($m['title']); ?>
                                                </h3>
                                                <?php if (!empty($m['description'])): ?>
                                                    <p style="font-size:13px;line-height:20px;color:#555;margin:0 0 12px;">
                                                        <?= esc($m['description']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <a href="<?= esc($m['url']); ?>" style="display:inline-block;background:#0a1a3a;color:#fff;text-decoration:none;font-weight:700;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;padding:12px 22px;">
                                                    <?= esc($m['cta_text']); ?> &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="font-size:14px;line-height:22px;color:#555;margin:0 0 16px;">
                                    <?= lang('Newsletter.em_welcome_first'); ?>

                                </p>
                            <?php endif; ?>

                            <p style="font-size:12px;line-height:18px;color:#999;margin:32px 0 0;border-top:1px solid #ececec;padding-top:16px;">
                                <?= brandLang('Newsletter.em_welcome_footer'); ?>

                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= view('email/_footer', ['subscriber' => $subscriber, 'showUnsubscribeLink' => true]); ?>
