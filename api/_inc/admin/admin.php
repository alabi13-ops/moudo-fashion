<?php
/**
 * Tableau de bord Moudo Fashion — /admin
 *
 * Sécurité :
 * - mot de passe haché (password_hash), jamais stocké en clair ;
 * - création du mot de passe protégée par un code d'installation (config.php) ;
 * - session : cookie HttpOnly + SameSite=Strict (+ Secure en HTTPS), nouvel identifiant à la connexion,
 *   déconnexion automatique après 2 h d'inactivité ;
 * - jeton CSRF sur chaque formulaire ;
 * - blocage 15 min après 5 mots de passe faux depuis la même adresse ;
 * - pages non indexées et non mises en cache.
 */

const ADMIN_FILE    = STORAGE_DIR . '/admin.json';
const ATTEMPTS_FILE = STORAGE_DIR . '/connexions.json';
const SESSION_IDLE  = 7200;

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, max-age=0');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('mf_admin');
session_set_cookie_params(['lifetime' => 0, 'path' => '/admin', 'httponly' => true, 'samesite' => 'Strict', 'secure' => $https]);
ini_set('session.use_strict_mode', '1');
if (stockage_ok()) {
    $sessDir = STORAGE_DIR . '/sessions';
    if (!is_dir($sessDir)) @mkdir($sessDir, 0700, true);
    if (is_writable($sessDir)) session_save_path($sessDir);
}
session_start();

/* ---------- Outils ---------- */

function admin_cfg(): ?array
{
    if (!is_file(ADMIN_FILE)) return null;
    $c = json_decode((string) file_get_contents(ADMIN_FILE), true);
    return is_array($c) && !empty($c['hash']) ? $c : null;
}
function admin_cfg_save(array $c): bool
{
    return stockage_ok() && file_put_contents(ADMIN_FILE, json_encode($c), LOCK_EX) !== false;
}
function connecte(): bool
{
    if (empty($_SESSION['admin'])) return false;
    if (time() - ($_SESSION['vu'] ?? 0) > SESSION_IDLE) { $_SESSION = []; return false; }
    $cfg = admin_cfg();
    if (!$cfg || ($_SESSION['v'] ?? '') !== ($cfg['v'] ?? '')) { $_SESSION = []; return false; } // mot de passe changé ailleurs
    $_SESSION['vu'] = time();
    return true;
}
function csrf(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_champ(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_ok(): bool { return is_string($_POST['csrf'] ?? null) && hash_equals(csrf(), $_POST['csrf']); }
function go(string $to, string $flash = '', string $type = 'ok'): never
{
    if ($flash !== '') $_SESSION['flash'] = [$type, $flash];
    header('Location: ' . $to, true, 303);
    exit;
}
function flash(): string
{
    if (empty($_SESSION['flash'])) return '';
    [$t, $m] = $_SESSION['flash']; unset($_SESSION['flash']);
    return '<div class="flash flash--' . e($t) . '" role="status">' . e($m) . '</div>';
}
function ip(): string { return substr(hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '?') . 'mf'), 0, 16); }
function tentatives(): array
{
    $a = is_file(ATTEMPTS_FILE) ? json_decode((string) file_get_contents(ATTEMPTS_FILE), true) : [];
    $a = is_array($a) ? $a : [];
    return array_filter($a, fn ($t) => ($t['t'] ?? 0) > time() - 900);
}
function bloque(): int
{
    $t = tentatives()[ip()] ?? null;
    return ($t && $t['n'] >= 5) ? max(1, (int) ceil(($t['t'] + 900 - time()) / 60)) : 0;
}
function echec_connexion(): void
{
    $a = tentatives(); $k = ip();
    $a[$k] = ['n' => ($a[$k]['n'] ?? 0) + 1, 't' => time()];
    if (stockage_ok()) file_put_contents(ATTEMPTS_FILE, json_encode($a), LOCK_EX);
}
function reussite_connexion(): void
{
    $a = tentatives(); unset($a[ip()]);
    if (stockage_ok()) file_put_contents(ATTEMPTS_FILE, json_encode($a), LOCK_EX);
}
function liste_txt(string $s): array
{
    return array_values(array_filter(array_map('trim', preg_split('/[,;\n]+/', $s)), fn ($v) => $v !== ''));
}
function txt(string $k, int $max = 300): string
{
    return mb_substr(trim(str_replace("\r", '', (string) ($_POST[$k] ?? ''))), 0, $max);
}

const BADGES = [
    ''              => ['', ''],
    'Nouveau'       => ['Nouveau', 'New'],
    'Best-seller'   => ['Best-seller', 'Best-seller'],
    'Tendance'      => ['Tendance', 'Trending'],
    'Coup de cœur'  => ['Coup de cœur', 'Staff pick'],
    'Promo'         => ['Promo', 'Sale'],
    'Pièce forte'   => ['Pièce forte', 'Statement'],
    'Ensemble'      => ['Ensemble', 'Set'],
    'Incontournable'=> ['Incontournable', 'Must-have'],
    'Coffret'       => ['Coffret', 'Box set'],
];

/* ---------- Routage ---------- */

$sub    = substr($path, strlen('/admin')) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$cfg    = admin_cfg();
$store  = stockage_ok();

if ($method === 'POST' && !csrf_ok()) {
    go('/admin', 'La page avait expiré. Recommencez.', 'err');
}

// 1. Première installation : création du mot de passe
if (!$cfg) {
    $err = '';
    if ($method === 'POST' && $sub === '/installer') {
        if (bloque()) $err = 'Trop d\'essais. Réessayez dans ' . bloque() . ' min.';
        elseif (!hash_equals(ADMIN_CODE_INSTALLATION, strtoupper(trim((string) ($_POST['code'] ?? ''))))) { echec_connexion(); $err = "Code d'installation incorrect."; }
        elseif (mb_strlen((string) ($_POST['mdp'] ?? '')) < 8) $err = 'Le mot de passe doit faire au moins 8 caractères.';
        elseif (($_POST['mdp'] ?? '') !== ($_POST['mdp2'] ?? '')) $err = 'Les deux mots de passe ne sont pas identiques.';
        elseif (!$store) $err = "L'hébergement ne permet pas d'enregistrer (dossier storage non accessible en écriture).";
        else {
            $v = bin2hex(random_bytes(8));
            admin_cfg_save(['hash' => password_hash((string) $_POST['mdp'], PASSWORD_DEFAULT), 'v' => $v, 'cree' => date('c')]);
            reussite_connexion();
            session_regenerate_id(true);
            $_SESSION = ['admin' => true, 'vu' => time(), 'v' => $v];
            if (!is_file(PRODUITS_FILE)) produits_enregistrer(produits_tous());
            go('/admin', 'Mot de passe créé. Bienvenue dans votre tableau de bord !');
        }
    }
    admin_page('Installation', function () use ($err, $store) { ?>
      <div class="auth">
        <img src="/assets/img/logo-dark.svg" alt="Moudo Fashion" width="96" height="96">
        <h1>Créer votre accès</h1>
        <p>Cette étape ne se fait qu'une fois. Entrez le code d'installation reçu de votre développeur, puis choisissez votre mot de passe.</p>
        <?php if (!$store): ?><div class="flash flash--err">Attention : l'hébergement ne permet pas d'enregistrer de fichiers. Le tableau de bord ne fonctionnera pas ici.</div><?php endif; ?>
        <?php if ($err): ?><div class="flash flash--err"><?= e($err) ?></div><?php endif; ?>
        <form method="post" action="/admin/installer" class="stack">
          <?= csrf_champ() ?>
          <label>Code d'installation<input name="code" required autocomplete="off" autocapitalize="characters" placeholder="MOUDO-XXXX-XXXX"></label>
          <label>Nouveau mot de passe <small>8 caractères minimum</small><input type="password" name="mdp" required minlength="8" autocomplete="new-password"></label>
          <label>Confirmer le mot de passe<input type="password" name="mdp2" required minlength="8" autocomplete="new-password"></label>
          <button class="btn btn--primary btn--block">Créer mon accès</button>
        </form>
      </div>
    <?php }, false);
    exit;
}

// 2. Connexion / déconnexion
if ($sub === '/deconnexion' && $method === 'POST') {
    $_SESSION = []; session_destroy();
    go('/admin', 'Vous êtes déconnecté.');
}
if (!connecte()) {
    $err = '';
    if ($method === 'POST' && $sub === '/connexion') {
        if ($m = bloque()) $err = "Trop d'essais. Réessayez dans {$m} min.";
        elseif (password_verify((string) ($_POST['mdp'] ?? ''), $cfg['hash'])) {
            reussite_connexion();
            if (password_needs_rehash($cfg['hash'], PASSWORD_DEFAULT)) { $cfg['hash'] = password_hash((string) $_POST['mdp'], PASSWORD_DEFAULT); admin_cfg_save($cfg); }
            session_regenerate_id(true);
            $_SESSION = ['admin' => true, 'vu' => time(), 'v' => $cfg['v']];
            go('/admin');
        } else { echec_connexion(); usleep(400000); $err = 'Mot de passe incorrect.'; }
    }
    admin_page('Connexion', function () use ($err) { ?>
      <div class="auth">
        <img src="/assets/img/logo-dark.svg" alt="Moudo Fashion" width="96" height="96">
        <h1>Tableau de bord</h1>
        <p>Connectez-vous pour gérer vos articles.</p>
        <?= flash() ?>
        <?php if ($err): ?><div class="flash flash--err"><?= e($err) ?></div><?php endif; ?>
        <form method="post" action="/admin/connexion" class="stack">
          <?= csrf_champ() ?>
          <label>Mot de passe<input type="password" name="mdp" required autocomplete="current-password" autofocus></label>
          <button class="btn btn--primary btn--block">Se connecter</button>
        </form>
        <a class="back" href="/">← Retour au site</a>
      </div>
    <?php }, false);
    exit;
}

// 3. Actions (connecté)
if ($method === 'POST') {
    if (!$store) go('/admin', "Impossible d'enregistrer : l'hébergement est en lecture seule.", 'err');
    $list = produits_tous();
    $id   = (string) ($_POST['id'] ?? '');
    $idx  = array_search($id, array_column($list, 'id'), true);

    switch ($sub) {
        case '/enregistrer':
            $nouveau = $idx === false;
            $p = $nouveau ? ['id' => nouvel_id(), 'stock' => true, 'visible' => true, 'image' => ''] : $list[$idx];
            try {
                $nom = txt('nom', 80);
                if ($nom === '') throw new RuntimeException("Donnez un nom à l'article.");
                $cat = (string) ($_POST['cat'] ?? '');
                if (!isset(CATEGORIES[$cat])) throw new RuntimeException('Choisissez un rayon.');
                $prixTxt = preg_replace('/\D/', '', (string) ($_POST['prix'] ?? ''));
                $badge = (string) ($_POST['tag'] ?? '');
                $badge = isset(BADGES[$badge]) ? $badge : '';

                $photo = photo_recue($_FILES['photo'] ?? null);
                if ($photo) { photo_supprimer($p['image'] ?? null); $p['image'] = $photo; }
                if (empty($p['image'])) throw new RuntimeException("Ajoutez une photo de l'article.");

                // Couleurs (+ photo par couleur facultative)
                $cFr = $_POST['c_fr'] ?? []; $cEn = $_POST['c_en'] ?? []; $cOld = $_POST['c_photo'] ?? [];
                $couleurs = []; $couleursEn = []; $photos = []; $anyPhoto = false;
                foreach ((array) $cFr as $i => $c) {
                    $c = mb_substr(trim((string) $c), 0, 30);
                    $old = (string) ($cOld[$i] ?? '');
                    if ($c === '') { photo_supprimer($old); continue; }
                    $file = isset($_FILES['c_file']['name'][$i]) ? [
                        'name' => $_FILES['c_file']['name'][$i], 'type' => $_FILES['c_file']['type'][$i],
                        'tmp_name' => $_FILES['c_file']['tmp_name'][$i], 'error' => $_FILES['c_file']['error'][$i], 'size' => $_FILES['c_file']['size'][$i],
                    ] : null;
                    $new = photo_recue($file);
                    if ($new) { photo_supprimer($old); $old = $new; }
                    $couleurs[] = $c;
                    $couleursEn[] = mb_substr(trim((string) ($cEn[$i] ?? '')), 0, 30) ?: $c;
                    $photos[] = $old ?: $p['image'];
                    if ($old) $anyPhoto = true;
                }

                // Photos de couleurs retirées : supprimées de l'hébergement
                foreach (array_diff($p['photos'] ?? [], $photos, [$p['image']]) as $orphan) photo_supprimer($orphan);

                $p['cat']  = $cat;
                $p['nom']  = $nom;
                $p['nom_en'] = txt('nom_en', 80);
                $p['prix'] = $prixTxt === '' ? null : (int) $prixTxt;
                $p['tag']  = BADGES[$badge][0];
                $p['tag_en'] = BADGES[$badge][1];
                $p['desc'] = txt('desc', 600);
                $p['desc_en'] = txt('desc_en', 600);
                $p['couleurs'] = $couleurs;
                $p['couleurs_en'] = $couleursEn;
                if ($anyPhoto) $p['photos'] = $photos; else unset($p['photos']);
                $tailles = liste_txt(txt('tailles', 200));
                if ($tailles) $p['tailles'] = array_slice($tailles, 0, 20); else unset($p['tailles']);
                $p['stock'] = !empty($_POST['stock']);
                $p['visible'] = !empty($_POST['visible']);

                if ($nouveau) array_unshift($list, $p); else $list[$idx] = $p;
                if (!produits_enregistrer($list)) throw new RuntimeException("L'enregistrement a échoué. Réessayez.");
                go('/admin', $nouveau ? "« {$nom} » est en ligne." : "« {$nom} » a été mis à jour.");
            } catch (RuntimeException $ex) {
                $_SESSION['form'] = $_POST;
                go($nouveau ? '/admin/nouveau' : '/admin/article?id=' . rawurlencode($id), $ex->getMessage(), 'err');
            }

        case '/supprimer':
            if ($idx === false) go('/admin', 'Article introuvable.', 'err');
            $p = $list[$idx];
            photo_supprimer($p['image'] ?? null);
            foreach ($p['photos'] ?? [] as $ph) photo_supprimer($ph);
            array_splice($list, $idx, 1);
            produits_enregistrer($list);
            go('/admin', "« {$p['nom']} » a été supprimé.");

        case '/stock':
        case '/visible':
            if ($idx === false) go('/admin', 'Article introuvable.', 'err');
            $k = $sub === '/stock' ? 'stock' : 'visible';
            $list[$idx][$k] = !(($list[$idx][$k] ?? true) !== false);
            produits_enregistrer($list);
            $n = $list[$idx]['nom'];
            $msg = $k === 'stock'
                ? ($list[$idx]['stock'] ? "« {$n} » est de nouveau disponible." : "« {$n} » est marqué épuisé.")
                : ($list[$idx]['visible'] ? "« {$n} » est visible sur le site." : "« {$n} » est masqué du site.");
            go('/admin#' . $id, $msg);

        case '/deplacer':
            if ($idx === false) go('/admin', 'Article introuvable.', 'err');
            $to = $idx + (($_POST['sens'] ?? '') === 'haut' ? -1 : 1);
            if ($to >= 0 && $to < count($list)) {
                [$list[$idx], $list[$to]] = [$list[$to], $list[$idx]];
                produits_enregistrer($list);
            }
            go('/admin#' . $id);

        case '/compte':
            if (!password_verify((string) ($_POST['actuel'] ?? ''), $cfg['hash'])) go('/admin/compte', 'Mot de passe actuel incorrect.', 'err');
            if (mb_strlen((string) ($_POST['mdp'] ?? '')) < 8) go('/admin/compte', 'Le nouveau mot de passe doit faire au moins 8 caractères.', 'err');
            if (($_POST['mdp'] ?? '') !== ($_POST['mdp2'] ?? '')) go('/admin/compte', 'Les deux mots de passe ne sont pas identiques.', 'err');
            $cfg['hash'] = password_hash((string) $_POST['mdp'], PASSWORD_DEFAULT);
            $cfg['v'] = bin2hex(random_bytes(8)); // déconnecte les autres appareils
            admin_cfg_save($cfg);
            $_SESSION['v'] = $cfg['v'];
            session_regenerate_id(true);
            go('/admin', 'Mot de passe modifié.');
    }
    go('/admin');
}

// 4. Pages (connecté)
if ($sub === '/nouveau' || $sub === '/article') {
    $p = $sub === '/article' ? produit_par_id((string) ($_GET['id'] ?? '')) : null;
    if ($sub === '/article' && !$p) go('/admin', 'Article introuvable.', 'err');
    $old = $_SESSION['form'] ?? null; unset($_SESSION['form']);
    require __DIR__ . '/form.php';
    exit;
}
if ($sub === '/compte') {
    admin_page('Mon compte', function () { ?>
      <a class="back" href="/admin">← Mes articles</a>
      <h1>Changer le mot de passe</h1>
      <?= flash() ?>
      <form method="post" action="/admin/compte" class="card stack">
        <?= csrf_champ() ?>
        <label>Mot de passe actuel<input type="password" name="actuel" required autocomplete="current-password"></label>
        <label>Nouveau mot de passe <small>8 caractères minimum</small><input type="password" name="mdp" required minlength="8" autocomplete="new-password"></label>
        <label>Confirmer<input type="password" name="mdp2" required minlength="8" autocomplete="new-password"></label>
        <button class="btn btn--primary btn--block">Enregistrer</button>
        <p class="hint">Les autres appareils connectés seront déconnectés.</p>
      </form>
    <?php });
    exit;
}

// Liste des articles
$list = produits_tous();
admin_page('Mes articles', function () use ($list, $store) {
    $nb = count($list); $out = count(array_filter($list, fn ($p) => ($p['stock'] ?? true) === false));
    $hid = count(array_filter($list, fn ($p) => ($p['visible'] ?? true) === false)); ?>
  <div class="head">
    <div>
      <h1>Mes articles</h1>
      <p class="hint"><?= $nb ?> articles<?= $out ? " · {$out} épuisé" . ($out > 1 ? 's' : '') : '' ?><?= $hid ? " · {$hid} masqué" . ($hid > 1 ? 's' : '') : '' ?></p>
    </div>
    <a class="btn btn--primary" href="/admin/nouveau">+ Ajouter</a>
  </div>
  <?php if (!$store): ?><div class="flash flash--err">L'hébergement est en lecture seule : les modifications ne peuvent pas être enregistrées ici.</div><?php endif; ?>
  <?= flash() ?>
  <div class="tools">
    <input type="search" id="q" placeholder="Rechercher un article…" aria-label="Rechercher un article">
    <select id="fcat" aria-label="Filtrer par rayon">
      <option value="">Tous les rayons</option>
      <?php foreach (CATEGORIES as $k => $c): ?><option value="<?= e($k) ?>"><?= e($c['nom']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <ul class="items" id="items">
    <?php foreach ($list as $i => $p): $vis = ($p['visible'] ?? true) !== false; $st = ($p['stock'] ?? true) !== false; ?>
    <li class="item<?= $vis ? '' : ' is-hidden-item' ?>" id="<?= e($p['id']) ?>" data-cat="<?= e($p['cat']) ?>" data-q="<?= e(mb_strtolower($p['nom'] . ' ' . implode(' ', $p['couleurs'] ?? []))) ?>">
      <a class="item__main" href="/admin/article?id=<?= rawurlencode($p['id']) ?>">
        <img src="<?= e($p['image']) ?>" alt="" loading="lazy" width="64" height="80">
        <span class="item__txt">
          <strong><?= e($p['nom']) ?></strong>
          <span><?= e(CATEGORIES[$p['cat']]['nom'] ?? $p['cat']) ?> · <?= $p['prix'] === null ? 'Prix sur WhatsApp' : e(number_format($p['prix'], 0, ',', ' ') . ' FCFA') ?></span>
          <span class="pills">
            <?php if (!$st): ?><em class="pill pill--out">Épuisé</em><?php endif; ?>
            <?php if (!$vis): ?><em class="pill">Masqué</em><?php endif; ?>
            <?php if (!empty($p['tag'])): ?><em class="pill pill--gold"><?= e($p['tag']) ?></em><?php endif; ?>
          </span>
        </span>
      </a>
      <div class="item__actions">
        <form method="post" action="/admin/stock"><?= csrf_champ() ?><input type="hidden" name="id" value="<?= e($p['id']) ?>">
          <button class="chip<?= $st ? '' : ' chip--on' ?>" title="<?= $st ? 'Marquer épuisé' : 'Remettre en stock' ?>"><?= $st ? 'En stock' : 'Épuisé' ?></button></form>
        <form method="post" action="/admin/visible"><?= csrf_champ() ?><input type="hidden" name="id" value="<?= e($p['id']) ?>">
          <button class="chip<?= $vis ? '' : ' chip--on' ?>"><?= $vis ? 'Visible' : 'Masqué' ?></button></form>
        <span class="order-btns">
          <form method="post" action="/admin/deplacer"><?= csrf_champ() ?><input type="hidden" name="id" value="<?= e($p['id']) ?>"><input type="hidden" name="sens" value="haut">
            <button class="icon" aria-label="Monter" <?= $i === 0 ? 'disabled' : '' ?>>↑</button></form>
          <form method="post" action="/admin/deplacer"><?= csrf_champ() ?><input type="hidden" name="id" value="<?= e($p['id']) ?>"><input type="hidden" name="sens" value="bas">
            <button class="icon" aria-label="Descendre" <?= $i === count($list) - 1 ? 'disabled' : '' ?>>↓</button></form>
        </span>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <p class="empty-msg" id="none" hidden>Aucun article ne correspond.</p>
  <script>
    (function () {
      var q = document.getElementById('q'), c = document.getElementById('fcat'), none = document.getElementById('none');
      function f() {
        var v = q.value.trim().toLowerCase(), k = c.value, n = 0;
        document.querySelectorAll('#items .item').forEach(function (li) {
          var ok = (!k || li.dataset.cat === k) && (!v || li.dataset.q.indexOf(v) > -1);
          li.hidden = !ok; if (ok) n++;
        });
        none.hidden = n > 0;
      }
      q.addEventListener('input', f); c.addEventListener('change', f);
    })();
  </script>
<?php });

/* ---------- Gabarit ---------- */

function admin_page(string $titre, callable $body, bool $nav = true): void
{ ?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= e($titre) ?> — Moudo Fashion · Gestion</title>
<link rel="icon" href="/assets/img/favicon-64.png" type="image/png">
<link rel="stylesheet" href="/assets/css/admin.css?v=1">
</head>
<body>
<?php if ($nav): ?>
<header class="bar">
  <a class="bar__brand" href="/admin"><img src="/assets/img/logo-light.svg" alt="" width="34" height="34"><span>Moudo · Gestion</span></a>
  <nav class="bar__nav">
    <a href="/" target="_blank" rel="noopener">Voir le site</a>
    <a href="/admin/compte">Compte</a>
    <form method="post" action="/admin/deconnexion"><?= csrf_champ() ?><button>Déconnexion</button></form>
  </nav>
</header>
<?php endif; ?>
<main class="wrap"><?php $body(); ?></main>
</body>
</html>
<?php }
