<?php
$lb = array_merge(
    array_map(fn ($l) => $l + ['kind' => 'look'], LOOKS),
    array_map(fn ($b) => $b + ['kind' => 'shop'], BOUTIQUE)
);
?>
<section class="page-hero">
  <div class="container">
    <p class="eyebrow">Lookbook</p>
    <h1><?= tr('Des looks <em>complets</em>', 'Complete <em>looks</em>') ?></h1>
    <p><?= tr("Casquette, haut, jean, chaussures : on compose des tenues entières. Un look vous plaît ? Envoyez-nous la photo, on vous prépare le même à votre taille.",
              "Cap, top, jeans, shoes: we put whole outfits together. Like a look? Send us the photo and we'll put the same one together in your size.") ?></p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="looks">
      <?php foreach (LOOKS as $i => $l): ?>
      <figure class="look reveal">
        <button type="button" class="look__img" data-lb="<?= $i ?>" aria-label="<?= tr('Agrandir', 'Enlarge') ?> : <?= e(f($l, 'alt')) ?>">
          <img src="<?= e($l['src']) ?>" alt="<?= e(f($l, 'alt')) ?>" loading="lazy">
        </button>
        <figcaption>
          <span><?= e(f($l, 'alt')) ?></span>
          <a class="btn btn--wa btn--sm" target="_blank" rel="noopener" href="<?= e(wa(tr("Bonjour Moudo Fashion 👋\nJe voudrais ce look : « " . $l['alt'] . " ». Vous avez ma taille ?", "Hello Moudo Fashion 👋\nI'd like this look: \"" . $l['alt_en'] . "\". Do you have my size?"))) ?>"><?= tr('Je veux ce look', 'I want this look') ?></a>
        </figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--sand">
  <div class="container shopgrid">
    <div class="shopgrid__video reveal">
      <video controls muted playsinline preload="none" poster="<?= e(SHOP . 'video-9.jpg') ?>">
        <source src="/assets/video/boutique.mp4" type="video/mp4">
      </video>
    </div>
    <div class="shopgrid__txt reveal">
      <p class="eyebrow"><?= tr('La boutique', 'The store') ?></p>
      <h2><?= tr('Des étagères <em>pleines</em>', 'Shelves <em>packed</em> full') ?></h2>
      <p><?= tr("Jeans pliés par coupe, polos et chemises en portant, casquettes alignées : venez fouiller, essayer et repartir avec la tenue complète.", 'Jeans folded by fit, polos and shirts on rails, caps lined up: come and dig through, try on and leave with the full outfit.') ?></p>
      <div class="shopgrid__thumbs">
        <?php foreach (BOUTIQUE as $j => $b): ?>
        <button type="button" data-lb="<?= count(LOOKS) + $j ?>" aria-label="<?= tr('Agrandir', 'Enlarge') ?> : <?= e(f($b, 'alt')) ?>"><img src="<?= e($b['src']) ?>" alt="<?= e(f($b, 'alt')) ?>" loading="lazy"></button>
        <?php endforeach; ?>
      </div>
      <a href="<?= url('/contact') ?>" class="link-arrow"><?= tr('Adresse et horaires →', 'Address and hours →') ?></a>
    </div>
  </div>
</section>
