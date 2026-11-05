<?php
/**
 * Moudo Fashion — point d'entrée unique (routeur).
 * Français : /, /collection…   Anglais : /en, /en/collection…
 * Local : php -S localhost:8000 api/index.php  (depuis la racine du projet)
 */

require __DIR__ . '/_inc/config.php';
require __DIR__ . '/_inc/data.php';
require __DIR__ . '/_inc/functions.php';
require __DIR__ . '/_inc/store.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . trim(rawurldecode($path), '/');

// Dossiers privés : jamais servis (en plus du .htaccess)
if (preg_match('#^/(storage|api/_inc|\.git)(/|$)#', $path)) { http_response_code(404); exit; }

// Serveur PHP local : laisser passer les fichiers statiques (css, js, images, vidéo…)
if (PHP_SAPI === 'cli-server' && $path !== '/' && is_file(dirname(__DIR__) . $path)) {
    return false;
}

// Tableau de bord du vendeur
if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require __DIR__ . '/_inc/admin/admin.php';
    exit;
}

if ($path === '/en' || str_starts_with($path, '/en/')) {
    set_lang('en');
    $path = '/' . trim(substr($path, 3), '/');
}

$routes = [
    '/'           => ['accueil',    ['Prêt-à-porter à Bohicon · Jeans, vêtements, casquettes', 'Streetwear store in Bohicon · Jeans, clothing, caps'],
                      ["Moudo Fashion, boutique de prêt-à-porter à Bohicon : jeans, chemises, t-shirts, casquettes, chaussures et accessoires. Pour lui, et pour elle aussi. Commande directe sur WhatsApp.",
                       "Moudo Fashion, ready-to-wear store in Bohicon: jeans, shirts, tees, caps, footwear and accessories. Made for him, worn by her too. Order directly on WhatsApp."]],
    '/collection' => ['collection', ['La collection', 'The collection'],
                      ["Tous les articles Moudo Fashion : jeans, vêtements, casquettes, chaussures et accessoires. Choisissez votre taille et commandez sur WhatsApp.",
                       "Every Moudo Fashion piece: jeans, clothing, caps, footwear and accessories. Pick your size and order on WhatsApp."]],
    '/lookbook'   => ['lookbook',   ['Lookbook', 'Lookbook'],
                      ["Des looks complets Moudo Fashion et la boutique en images.", "Full Moudo Fashion looks and the store in pictures."]],
    '/contact'    => ['contact',    ['Boutique & contact', 'Store & contact'],
                      ["Adresse, horaires et contact de Moudo Fashion à Bohicon, à côté du bar Setondji.", "Address, opening hours and contact for Moudo Fashion in Bohicon, next to Setondji bar."]],
];

if ($path === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nAllow: /\nSitemap: " . SITE['url'] . "/sitemap.xml\n";
    exit;
}

if ($path === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach (array_keys($routes) as $r) {
        foreach (['fr', 'en'] as $l) echo '<url><loc>' . e(SITE['url'] . url($r, $l)) . '</loc></url>';
    }
    echo '</urlset>';
    exit;
}

if ($path === '/commande') {
    $commande = decode_commande((string) ($_GET['c'] ?? ''));
    $client   = mb_substr(trim(strip_tags((string) ($_GET['n'] ?? ''))), 0, 40);
    $nb       = array_sum(array_column($commande, 'q'));
    $titre    = $client !== '' ? tr('Commande de ', 'Order from ') . $client : tr('Commande', 'Order');
    $desc     = $nb . ' ' . ($nb > 1 ? tr('articles', 'items') : tr('article', 'item')) . ' · '
              . implode(', ', array_map(fn ($l) => f($l['p'], 'nom') . ($l['s'] !== '' ? ' (' . $l['s'] . ')' : ''), array_slice($commande, 0, 4)));
    render('commande', [
        'titre' => $titre, 'description' => $desc, 'actif' => '/commande', 'noindex' => true,
        'ogImage' => $commande ? SITE['url'] . photo_couleur($commande[0]['p'], $commande[0]['c']) : null,
        'commande' => $commande, 'client' => $client,
    ]);
    exit;
}

if (isset($routes[$path])) {
    [$page, $titres, $descs] = $routes[$path];
    render($page, ['titre' => tr(...$titres), 'description' => tr(...$descs), 'actif' => $path]);
} else {
    http_response_code(404);
    render('404', ['titre' => tr('Page introuvable', 'Page not found'), 'description' => '', 'actif' => $path]);
}
