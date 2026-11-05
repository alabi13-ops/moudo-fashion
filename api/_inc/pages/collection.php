<section class="page-hero">
  <div class="container">
    <p class="eyebrow"><?= tr('La collection', 'The collection') ?></p>
    <h1><?= tr('Tout le <em>vestiaire</em>', 'The whole <em>wardrobe</em>') ?></h1>
    <p><?= tr('Ouvrez un article pour choisir taille et couleur, ajoutez-le à votre sélection, puis envoyez tout sur WhatsApp en un clic.',
              'Open an item to pick size and colour, add it to your selection, then send everything on WhatsApp in one tap.') ?></p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="toolbar">
      <div class="chips" role="tablist" aria-label="<?= tr('Filtrer par rayon', 'Filter by department') ?>">
        <button class="chip is-active" data-filter="all" role="tab" aria-selected="true"><?= tr('Tout', 'All') ?> <span><?= count(produits()) ?></span></button>
        <?php foreach (CATEGORIES as $slug => $c): ?>
          <button class="chip" data-filter="<?= e($slug) ?>" role="tab" aria-selected="false"><?= e(f($c, 'nom')) ?> <span><?= count(produits_par_cat($slug)) ?></span></button>
        <?php endforeach; ?>
      </div>
      <label class="search" for="search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
        <input type="search" id="search" placeholder="<?= tr('Jean, chemise, noir…', 'Jeans, shirt, black…') ?>" aria-label="<?= tr('Rechercher un article', 'Search for an item') ?>">
      </label>
    </div>

    <div class="grid" id="productGrid">
      <?php foreach (produits() as $p) include __DIR__ . '/../partials/produit.php'; ?>
    </div>

    <div class="empty" id="empty" hidden>
      <p><?= tr('Aucun article ne correspond à votre recherche.', 'No item matches your search.') ?></p>
      <a class="btn btn--wa" target="_blank" rel="noopener" href="<?= e(wa(tr("Bonjour 👋 Je cherche un article précis, vous pouvez m'aider ?", "Hello 👋 I'm looking for a specific item, can you help?"))) ?>"><?= tr('Demander sur WhatsApp', 'Ask on WhatsApp') ?></a>
    </div>

    <aside class="custom reveal">
      <div>
        <h2><?= tr('Pas trouvé <em>votre pièce ?</em>', "Can't find <em>your piece?</em>") ?></h2>
        <p><?= tr("Tout le stock n'est pas en ligne : nouveaux arrivages chaque semaine. Envoyez-nous une photo de ce que vous cherchez.", "Not all our stock is online: new arrivals every week. Send us a photo of what you're after.") ?></p>
      </div>
      <a class="btn btn--wa" target="_blank" rel="noopener" href="<?= e(wa(tr("Bonjour Moudo Fashion 👋\nJe cherche un article en particulier, je vous envoie une photo :", "Hello Moudo Fashion 👋\nI'm looking for a particular item, here's a photo:"))) ?>"><?= tr('Envoyer une photo', 'Send a photo') ?></a>
    </aside>
  </div>
</section>
