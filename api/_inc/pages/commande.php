<?php /** @var array $commande  @var string $client */
$nbArticles = array_sum(array_column($commande, 'q')); ?>
<section class="page-hero page-hero--order">
  <div class="container">
    <p class="eyebrow"><?= tr('Récapitulatif de commande', 'Order summary') ?></p>
    <?php if ($commande): ?>
      <h1><?= $client !== '' ? tr('Commande de <em>', 'Order from <em>') . e($client) . '</em>' : tr('Commande <em>en photos</em>', 'Order <em>in pictures</em>') ?></h1>
      <p><?= $nbArticles ?> <?= $nbArticles > 1 ? tr('articles', 'items') : tr('article', 'item') ?> · <?= tr('envoyée depuis le site Moudo Fashion', 'sent from the Moudo Fashion website') ?></p>
    <?php else: ?>
      <h1><?= tr('Commande <em>introuvable</em>', 'Order <em>not found</em>') ?></h1>
      <p><?= tr("Ce lien de commande est incomplet ou a été modifié.", 'This order link is incomplete or has been changed.') ?></p>
    <?php endif; ?>
  </div>
</section>

<?php if ($commande): ?>
<section class="section section--tight">
  <div class="container">
    <ol class="order">
      <?php foreach ($commande as $i => $l): $p = $l['p']; ?>
      <li class="order__item">
        <span class="order__num"><?= $i + 1 ?></span>
        <figure class="order__img"><img src="<?= e(photo_couleur($p, $l['c'])) ?>" alt="<?= e(f($p, 'nom')) ?>" loading="<?= $i < 2 ? 'eager' : 'lazy' ?>"></figure>
        <div class="order__info">
          <p class="card__cat"><?= e(cat_nom($p['cat'])) ?></p>
          <h2 class="order__name"><?= e(f($p, 'nom')) ?></h2>
          <dl class="order__specs">
            <div><dt><?= tr('Quantité', 'Quantity') ?></dt><dd><?= (int) $l['q'] ?></dd></div>
            <?php if ($l['s'] !== ''): ?><div><dt><?= tr('Taille', 'Size') ?></dt><dd><?= e($l['s']) ?></dd></div><?php endif; ?>
            <?php if ($l['c'] !== ''): ?><div><dt><?= tr('Couleur', 'Colour') ?></dt><dd><?= e($l['c']) ?></dd></div><?php endif; ?>
            <div><dt><?= tr('Prix', 'Price') ?></dt><dd><?= e(prix($p['prix'])) ?></dd></div>
          </dl>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
    <p class="order__note"><?= tr("Cette page affiche ce que le client a choisi sur le site. La disponibilité et le prix final se confirment sur WhatsApp.", 'This page shows what the customer picked on the website. Availability and final price are confirmed on WhatsApp.') ?></p>
    <div class="center"><a href="<?= url('/collection') ?>" class="btn btn--dark"><?= tr('Voir toute la collection', 'Browse the whole collection') ?></a></div>
  </div>
</section>
<?php endif; ?>
