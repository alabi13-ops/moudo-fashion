<!-- HERO -->
<section class="hero">
  <picture class="hero__bg" aria-hidden="true">
    <source media="(max-width: 760px)" srcset="<?= e('/assets/img/hero-mobile.jpg') ?>">
    <img src="<?= e('/assets/img/hero-desktop.jpg') ?>" alt="" width="2400" height="1350" fetchpriority="high">
  </picture>
  <div class="container hero__in">
    <div class="hero__text">
      <p class="eyebrow eyebrow--gold reveal"><?= tr('Prêt-à-porter · Bohicon', 'Ready-to-wear · Bohicon') ?></p>
      <h1 class="reveal"><?= tr('Le style <br>qui <em>parle</em> <br>pour vous.', 'Style that <br><em>speaks</em> <br>for you.') ?></h1>
      <p class="hero__lead reveal"><?= tr("Jeans, chemises, t-shirts, casquettes, boots et ceintures. Pensés pour lui, et elle les porte aussi. Choisissez votre taille, on reçoit votre commande sur WhatsApp.",
                                          'Jeans, shirts, tees, caps, boots and belts. Made for him, and she wears them too. Pick your size and we get your order on WhatsApp.') ?></p>
      <div class="hero__cta reveal">
        <a href="<?= url('/collection') ?>" class="btn btn--gold"><?= tr('Voir la collection', 'Shop the collection') ?></a>
        <a href="<?= e(wa(tr("Bonjour Moudo Fashion 👋
Je cherche une tenue, vous pouvez me conseiller ?", "Hello Moudo Fashion 👋
I'm looking for an outfit, can you advise me?"))) ?>" class="btn btn--wa" target="_blank" rel="noopener"><?= tr('Se faire conseiller', 'Get advice') ?></a>
      </div>
      <ul class="hero__facts reveal">
        <li><strong><?= count(produits()) ?>+</strong><span><?= tr('modèles en ligne', 'styles online') ?></span></li>
        <li><strong>10h–22h</strong><span><?= tr('du lundi au samedi', 'Monday to Saturday') ?></span></li>
        <li><strong>S → XXL</strong><span><?= tr('jeans du 28 au 38', 'jeans 28 to 38') ?></span></li>
      </ul>
    </div>
  </div>
</section>

<!-- BANDEAU DÉFILANT -->
<div class="marquee" aria-hidden="true">
  <div class="marquee__track">
    <?php for ($i = 0; $i < 2; $i++): ?>
      <span><?= tr('Jeans', 'Jeans') ?></span><b>✦</b><span><?= tr('Chemises', 'Shirts') ?></span><b>✦</b><span>T-shirts</span><b>✦</b>
      <span><?= tr('Casquettes', 'Caps') ?></span><b>✦</b><span>Boots</span><b>✦</b><span><?= tr('Ceintures', 'Belts') ?></span><b>✦</b>
      <span><?= tr('Survêtements', 'Tracksuits') ?></span><b>✦</b><span><?= tr('Polos', 'Polos') ?></span><b>✦</b>
    <?php endfor; ?>
  </div>
</div>

<!-- CATÉGORIES -->
<section class="section">
  <div class="container">
    <div class="section__head section__head--row">
      <div>
        <p class="eyebrow"><?= tr('Les rayons', 'Departments') ?></p>
        <h2><?= tr('Trouvez votre <em>pièce</em>', 'Find your <em>piece</em>') ?></h2>
      </div>
      <a href="<?= url('/collection') ?>" class="link-arrow"><?= tr('Toute la collection →', 'Full collection →') ?></a>
    </div>
    <div class="cats">
      <?php foreach (CATEGORIES as $slug => $c): ?>
      <a href="<?= url('/collection') ?>#<?= e($slug) ?>" class="cat reveal">
        <img src="<?= e($c['image']) ?>" alt="<?= e(f($c, 'nom')) ?>" loading="lazy" width="600" height="800">
        <div class="cat__txt">
          <h3><?= e(f($c, 'nom')) ?></h3>
          <p><?= e(f($c, 'texte')) ?> · <?= count(produits_par_cat($slug)) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SÉLECTION -->
<section class="section section--sand">
  <div class="container">
    <div class="section__head section__head--row">
      <div>
        <p class="eyebrow"><?= tr('En ce moment', 'Right now') ?></p>
        <h2><?= tr('Les pièces <em>qui partent</em>', 'Pieces that <em>sell out</em>') ?></h2>
      </div>
      <a href="<?= url('/collection') ?>" class="link-arrow"><?= tr('Tout voir →', 'See all →') ?></a>
    </div>
    <div class="grid">
      <?php foreach (array_slice(array_filter(produits(), fn ($p) => $p['tag'] !== ''), 0, 8) as $p) include __DIR__ . '/../partials/produit.php'; ?>
    </div>
  </div>
</section>

<!-- POUR LUI, POUR ELLE -->
<section class="unisex">
  <div class="container unisex__grid">
    <figure class="unisex__img reveal"><img src="<?= e(P . 'look-assis.jpg') ?>" alt="<?= e(f(LOOKS[0], 'alt')) ?>" loading="lazy" width="866" height="1540"></figure>
    <div class="unisex__txt reveal">
      <p class="eyebrow eyebrow--accent"><?= tr('Coupes larges, tailles du S au XXL', 'Relaxed fits, sizes S to XXL') ?></p>
      <h2><?= tr('Pour lui.<br><em>Pour elle aussi.</em>', 'For him.<br><em>For her too.</em>') ?></h2>
      <p><?= tr("Nos jeans baggy, chemises oversize, polos et casquettes sont coupés pour les hommes, et les femmes se les approprient. Dites-nous pour qui c'est, on vous aide à choisir la bonne taille.",
                "Our baggy jeans, oversized shirts, polos and caps are cut for men, and women make them their own. Tell us who it's for and we'll help you pick the right size.") ?></p>
      <a href="<?= url('/lookbook') ?>" class="btn btn--gold"><?= tr('Voir les looks', 'See the looks') ?></a>
    </div>
    <figure class="unisex__img unisex__img--small reveal"><img src="<?= e(P . 'look-miroir.jpg') ?>" alt="<?= e(f(LOOKS[2], 'alt')) ?>" loading="lazy" width="866" height="1540"></figure>
  </div>
</section>

<!-- COMMENT COMMANDER -->
<section class="section">
  <div class="container">
    <div class="section__head section__head--center">
      <p class="eyebrow"><?= tr('Simple comme un message', 'As simple as a message') ?></p>
      <h2><?= tr('Commander en <em>3 étapes</em>', 'Order in <em>3 steps</em>') ?></h2>
    </div>
    <ol class="steps">
      <li class="reveal"><span>01</span><h3><?= tr('Choisissez', 'Choose') ?></h3><p><?= tr('Ouvrez un article, prenez votre taille et votre couleur, ajoutez-le à votre sélection.', 'Open an item, pick your size and colour, add it to your selection.') ?></p></li>
      <li class="reveal"><span>02</span><h3><?= tr('Envoyez', 'Send') ?></h3><p><?= tr('Un clic : toute votre sélection part sur notre WhatsApp, avec les tailles, déjà rédigée.', 'One tap: your whole selection goes to our WhatsApp, sizes included, already written.') ?></p></li>
      <li class="reveal"><span>03</span><h3><?= tr('Récupérez', 'Collect') ?></h3><p><?= tr('On confirme prix et disponibilité, puis vous passez en boutique ou on organise la livraison.', 'We confirm price and stock, then you come to the store or we arrange delivery.') ?></p></li>
    </ol>
    <div class="center"><a href="<?= url('/collection') ?>" class="btn btn--dark"><?= tr('Commencer ma sélection', 'Start my selection') ?></a></div>
  </div>
</section>

<!-- TIKTOK -->
<?php if (SITE['tiktok']): ?>
<section class="social">
  <div class="container social__in">
    <div>
      <p class="eyebrow eyebrow--accent"><?= tr('Les nouveautés arrivent d’abord ici', 'New arrivals land here first') ?></p>
      <h2><?= tr('Suivez <em>@moudofashion</em> sur TikTok', 'Follow <em>@moudofashion</em> on TikTok') ?></h2>
    </div>
    <a class="btn btn--light" href="<?= e(SITE['tiktok']) ?>" target="_blank" rel="noopener"><?= icon('tiktok') ?> <?= tr('Ouvrir TikTok', 'Open TikTok') ?></a>
  </div>
</section>
<?php endif; ?>

<!-- VISITE -->
<section class="section section--sand">
  <div class="container visit">
    <div class="visit__txt reveal">
      <p class="eyebrow"><?= tr('Venez essayer', 'Come and try on') ?></p>
      <h2><?= tr('La boutique à <em>Bohicon</em>', 'The store in <em>Bohicon</em>') ?></h2>
      <p class="visit__lead"><?= tr("Des étagères de jeans pliés par coupe, des portants de polos et de chemises, des casquettes alignées. Venez fouiller, essayer et repartir avec la tenue complète.", 'Shelves of jeans folded by fit, rails of polos and shirts, caps lined up. Come and dig through, try on and leave with the full outfit.') ?></p>
      <ul class="landmarks">
        <li><?= icon('pin') ?><span><strong><?= e(f(SITE, 'quartier')) ?></strong><?= e(f(SITE, 'repere')) ?></span></li>
        <li><?= icon('clock') ?><span><strong><?= e(horaires_resume()) ?></strong><?= tr('Dimanche : fermé', 'Sunday: closed') ?></span></li>
      </ul>
      <div class="hero__cta">
        <a href="<?= e(SITE['maps_url']) ?>" class="btn btn--dark" target="_blank" rel="noopener"><?= tr('Itinéraire', 'Directions') ?></a>
        <a href="tel:<?= e(SITE['tel_href']) ?>" class="btn btn--outline"><?= tr('Appeler', 'Call') ?></a>
      </div>
    </div>
    <div class="visit__video reveal">
      <video autoplay muted loop playsinline preload="metadata" poster="<?= e(SHOP . 'video-21.jpg') ?>" aria-label="<?= tr('Visite de la boutique Moudo Fashion', 'A walk through the Moudo Fashion store') ?>">
        <source src="/assets/video/boutique.mp4" type="video/mp4">
      </video>
      <span class="visit__live"><i></i><?= tr('La boutique, en vrai', 'The store, for real') ?></span>
    </div>
  </div>
</section>
