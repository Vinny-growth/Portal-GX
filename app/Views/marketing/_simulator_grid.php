<?php
$showPath = $showPath ?? false;
$showLegacyBadge = $showLegacyBadge ?? true;
?>
<?php if (!empty($simulators)): ?>
    <div class="gx-simulator-grid">
        <?php foreach ($simulators as $simulator): ?>
            <article class="gx-simulator-card">
                <div class="gx-simulator-top">
                    <span class="gx-simulator-mark"><?= esc($simulator['label']); ?></span>
                    <?php if ($showLegacyBadge): ?>
                        <span class="gx-legacy-pill">URL preservada</span>
                    <?php endif; ?>
                </div>
                <p class="gx-card-kicker"><?= esc($simulator['eyebrow']); ?></p>
                <h3 class="gx-simulator-title"><?= esc($simulator['title']); ?></h3>
                <p class="gx-simulator-meta"><?= esc($simulator['description']); ?></p>
                <div class="gx-simulator-footer">
                    <?php if (!empty($showPath)): ?>
                        <span class="gx-simulator-path">/<?= esc($simulator['slug']); ?></span>
                    <?php endif; ?>
                    <a href="<?= esc($simulator['url']); ?>" class="gx-text-link"><?= esc($simulator['cta']); ?></a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="gx-empty-state">
        Nenhum simulador público foi encontrado para o idioma atual. O hub continua preparado para receber novas entradas sem mexer nas rotas existentes.
    </div>
<?php endif; ?>
