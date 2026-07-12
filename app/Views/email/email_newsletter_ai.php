<?= view('email/_header', ['subject' => $subject]); ?>
    <?php if (!empty($preheader)): ?>
        <span class="preheader"><?= esc($preheader); ?></span>
    <?php endif; ?>
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <h1 style="font-size: 24px;line-height: 30px;font-weight: 700;text-align: left;text-transform: none;margin: 0 0 12px;color:#0a1a3a;">
                                <?= esc($subject); ?>
                            </h1>
                            <?php if (!empty($intro)): ?>
                                <p style="font-size:15px;line-height:24px;color:#222;margin:0 0 20px;">
                                    <?= esc($intro); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($postsList)): foreach ($postsList as $idx => $item): ?>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin:0 0 22px;border-top:1px solid #ececec;padding-top:18px;">
                                    <tr>
                                        <?php if (!empty($item['image_url'])): ?>
                                            <td style="width:160px;padding-right:14px;vertical-align:top;" class="first">
                                                <a href="<?= esc($item['url']); ?>" style="display:block;">
                                                    <img src="<?= esc($item['image_url']); ?>" alt="" style="display:block;width:160px;height:auto;border-radius:2px;">
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                        <td style="vertical-align:top;">
                                            <h2 style="font-size:17px;line-height:22px;font-weight:700;margin:0 0 8px;color:#0a1a3a;">
                                                <a href="<?= esc($item['url']); ?>" style="color:#0a1a3a;text-decoration:none;">
                                                    <?= esc($item['title']); ?>
                                                </a>
                                            </h2>
                                            <?php if (!empty($item['summary'])): ?>
                                                <p style="font-size:14px;line-height:21px;color:#444;margin:0 0 10px;">
                                                    <?= esc($item['summary']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <a href="<?= esc($item['url']); ?>" style="display:inline-block;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#b08a3a;text-decoration:none;">
                                                <?= esc($item['cta_label'] ?? lang('Newsletter.em_ai_readmore')); ?> &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            <?php endforeach; endif; ?>
                            <?php if (!empty($ctaText) && !empty($ctaUrl)): ?>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin:24px 0 0;">
                                    <tr>
                                        <td style="background:#0a1a3a;border-radius:0;text-align:center;">
                                            <a href="<?= esc($ctaUrl); ?>" style="display:inline-block;color:#fff;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:1.5px;text-transform:uppercase;padding:14px 28px;">
                                                <?= esc($ctaText); ?>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif; ?>
                            <?php if (!empty($pixelUrl)): ?>
                                <img src="<?= esc($pixelUrl); ?>" alt="" width="1" height="1" style="display:block;border:0;width:1px;height:1px;opacity:0;">
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= view('email/_footer', ['subscriber' => $subscriber, 'showUnsubscribeLink' => true]); ?>
