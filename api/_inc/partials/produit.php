<?php /** @var array $p */
$pNom = f($p, 'nom'); $pCat = cat_nom($p['cat']); $pTag = f($p, 'tag'); $pCols = f($p, 'couleurs') ?: []; $dispo = en_stock($p);
if (!$dispo) $pTag = tr('Épuisé', 'Sold out'); ?>
<article class="card reveal<?= $dispo ? '' : ' card--out' ?>" data-cat="<?= e($p['cat']) ?>" data-name="<?= e(mb_strtolower($pNom . ' ' . $pCat . ' ' . $p['nom'] . ' ' . implode(' ', $pCols))) ?>">
  <button class="card__media" data-view="<?= e($p['id']) ?>" aria-label="<?= tr('Voir', 'View') ?> <?= e($pNom) ?>">
    <img src="<?= e($p['image']) ?>" alt="<?= e($pNom) ?>" loading="lazy" width="600" height="750">
    <?php if ($pTag): ?><span class="card__tag<?= $dispo ? '' : ' card__tag--out' ?>"><?= e($pTag) ?></span><?php endif; ?>
    <?php if (count($pCols) > 1): ?><span class="card__colors"><?= count($pCols) ?> <?= tr('coloris', 'colours') ?></span><?php endif; ?>
  </button>
  <div class="card__body">
    <p class="card__cat"><?= e($pCat) ?></p>
    <h3 class="card__title"><?= e($pNom) ?></h3>
    <p class="card__price"><?= e(prix($p['prix'])) ?></p>
    <div class="card__actions">
      <button class="btn btn--outline btn--sm card__pick" data-view="<?= e($p['id']) ?>"><?= !$dispo ? tr('Voir', 'View') : (tailles_de($p) ? tr('Choisir <span class="hide-xs">ma </span>taille', 'Choose<span class="hide-xs"> size</span>') : tr('Choisir', 'Choose')) ?></button>
      <a class="btn btn--wa btn--sm btn--icon" target="_blank" rel="noopener" aria-label="<?= tr('Demander sur WhatsApp', 'Ask on WhatsApp') ?>"
         href="<?= e(wa(tr("Bonjour Moudo Fashion 👋\nJe suis intéressé(e) par : *{$pNom}* ({$pCat}).\nC'est disponible ? Merci !",
                          "Hello Moudo Fashion 👋\nI'm interested in: *{$pNom}* ({$pCat}).\nIs it available? Thank you!"))) ?>"></a>
    </div>
  </div>
</article>
