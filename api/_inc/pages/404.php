<section class="page-hero page-hero--404">
  <div class="container">
    <p class="eyebrow"><?= tr('Erreur 404', 'Error 404') ?></p>
    <h1><?= tr('Cette page est <em>en rupture</em>', 'This page is <em>out of stock</em>') ?></h1>
    <p><?= tr("La page que vous cherchez n'existe pas ou a été déplacée.", "The page you're looking for doesn't exist or has moved.") ?></p>
    <div class="hero__cta">
      <a href="<?= url('/') ?>" class="btn btn--dark"><?= tr("Retour à l'accueil", 'Back to home') ?></a>
      <a href="<?= url('/collection') ?>" class="btn btn--outline"><?= tr('Voir la collection', 'Browse the collection') ?></a>
    </div>
  </div>
</section>
