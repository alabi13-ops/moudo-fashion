<?php $aujourdhui = (int) date('N'); $statut = statut_ouverture(); ?>
<section class="page-hero">
  <div class="container">
    <p class="eyebrow"><?= tr('La boutique', 'The store') ?></p>
    <h1><?= tr('Passez <em>nous voir</em>', 'Come <em>see us</em>') ?></h1>
    <p><?= tr('Essayer, comparer les coupes, demander conseil : on vous attend à Bohicon.', 'Try on, compare fits, ask for advice: we are waiting for you in Bohicon.') ?></p>
  </div>
</section>

<section class="section section--tight">
  <div class="container contact">
    <div class="contact__info">
      <figure class="contact__photo reveal">
        <img src="<?= e(SHOP . 'video-21.jpg') ?>" alt="<?= tr("L'intérieur de la boutique Moudo Fashion", 'Inside the Moudo Fashion store') ?>" loading="lazy" width="720" height="1280">
      </figure>
      <div class="info-card reveal">
        <h3><?= tr('Comment nous trouver', 'How to find us') ?></h3>
        <ul class="landmarks">
          <li><?= icon('pin') ?><span><strong><?= e(f(SITE, 'quartier')) ?></strong><?= e(f(SITE, 'repere')) ?> · <?= e(f(SITE, 'ville')) ?></span></li>
        </ul>
        <a href="<?= e(SITE['maps_url']) ?>" target="_blank" rel="noopener" class="link-arrow"><?= tr('Ouvrir dans Google Maps →', 'Open in Google Maps →') ?></a>
      </div>
    </div>

    <div class="contact__side">
      <div class="info-card reveal">
        <h3><?= tr('Horaires', 'Opening hours') ?>
          <?php if ($statut): ?><span class="badge <?= $statut['ouvert'] ? 'badge--open' : '' ?>"><?= $statut['ouvert'] ? tr('Ouvert', 'Open') : tr('Fermé', 'Closed') ?></span><?php endif; ?>
        </h3>
        <?php if (HORAIRES): ?>
        <table class="hours">
          <?php foreach (array_keys(JOURS) as $n): $h = HORAIRES[$n] ?? null; ?>
          <tr class="<?= $n === $aujourdhui ? 'is-today' : '' ?>">
            <td><?= e(jour($n)) ?><?php if ($n === $aujourdhui): ?> <small><?= tr("· aujourd'hui", '· today') ?></small><?php endif; ?></td>
            <td><?= $h ? heure($h[0]) . ' – ' . heure($h[1]) : tr('Fermé', 'Closed') ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p><?= e(horaires_resume()) ?></p>
        <?php endif; ?>
      </div>

      <div class="info-card reveal">
        <h3><?= tr('Téléphone, WhatsApp, TikTok', 'Phone, WhatsApp, TikTok') ?></h3>
        <p class="info-card__big"><a href="tel:<?= e(SITE['tel_href']) ?>"><?= e(SITE['telephone']) ?></a></p>
        <div class="hero__cta">
          <a href="tel:<?= e(SITE['tel_href']) ?>" class="btn btn--outline btn--sm"><?= tr('Appeler', 'Call') ?></a>
          <a href="<?= e(wa()) ?>" class="btn btn--wa btn--sm" target="_blank" rel="noopener">WhatsApp</a>
          <?php if (SITE['tiktok']): ?><a href="<?= e(SITE['tiktok']) ?>" class="btn btn--dark btn--sm" target="_blank" rel="noopener"><?= icon('tiktok') ?> TikTok</a><?php endif; ?>
        </div>
      </div>

      <form class="form reveal" id="contactForm">
        <h2><?= tr('Écrivez-<em>nous</em>', 'Write <em>to us</em>') ?></h2>
        <p><?= tr("Votre message s'ouvre directement dans WhatsApp, prêt à être envoyé.", 'Your message opens directly in WhatsApp, ready to send.') ?></p>
        <label for="cName"><?= tr('Votre prénom', 'Your first name') ?><input id="cName" name="nom" required autocomplete="given-name" placeholder="<?= tr('Ex. Kévin', 'e.g. Kevin') ?>"></label>
        <label for="cSubject"><?= tr('Vous voulez', 'You would like') ?>
          <select id="cSubject" name="sujet">
            <option><?= tr('Savoir si un article est disponible', 'To know if an item is in stock') ?></option>
            <option><?= tr('Un conseil de taille', 'Sizing advice') ?></option>
            <option><?= tr('Une tenue complète', 'A full outfit') ?></option>
            <option><?= tr('Une livraison', 'A delivery') ?></option>
            <option><?= tr('Autre chose', 'Something else') ?></option>
          </select>
        </label>
        <label for="cMessage"><?= tr('Votre message', 'Your message') ?><textarea id="cMessage" name="message" rows="4" required placeholder="<?= tr('Article, taille, couleur, budget…', 'Item, size, colour, budget…') ?>"></textarea></label>
        <button class="btn btn--wa btn--block" type="submit"><?= tr('Envoyer sur WhatsApp', 'Send on WhatsApp') ?></button>
      </form>
    </div>
  </div>
</section>
