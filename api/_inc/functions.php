<?php

/* ---------- Langue ---------- */

$GLOBALS['LANG'] = 'fr';

function lang(): string { return $GLOBALS['LANG']; }

function set_lang(string $l): void
{
    $GLOBALS['LANG'] = in_array($l, ['fr', 'en'], true) ? $l : 'fr';
}

/** Texte bilingue : tr('Bonjour', 'Hello') */
function tr(string $fr, string $en): string { return lang() === 'en' ? $en : $fr; }

/** Champ traduit d'un tableau de données : f($produit, 'nom') */
function f(array $row, string $key)
{
    if (lang() === 'en' && !empty($row[$key . '_en'])) return $row[$key . '_en'];
    return $row[$key] ?? '';
}

/** URL interne dans la langue courante (ou une autre) */
function url(string $path = '/', ?string $l = null): string
{
    $l ??= lang();
    $path = '/' . ltrim($path, '/');
    if ($l === 'en') return $path === '/' ? '/en' : '/en' . $path;
    return $path;
}

/* ---------- Utilitaires ---------- */

function e($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

/** Lien WhatsApp avec message pré-rempli */
function wa(string $message = ''): string
{
    $base = 'https://wa.me/' . SITE['whatsapp'];
    return $message === '' ? $base : $base . '?text=' . rawurlencode($message);
}

function img(string $src): string { return $src; }

function prix(?int $p): string
{
    if ($p === null) return tr('Prix sur WhatsApp', 'Price on WhatsApp');
    return number_format($p, 0, lang() === 'en' ? '.' : ',', lang() === 'en' ? ',' : ' ') . ' FCFA';
}

/** Tailles d'un article : celles de l'article si elles sont définies, sinon celles de son rayon */
function tailles_de(array $p): array
{
    return !empty($p['tailles']) ? array_values($p['tailles']) : (TAILLES[$p['cat']] ?? []);
}

function en_stock(array $p): bool { return ($p['stock'] ?? true) !== false; }

function cat_nom(string $slug): string { return isset(CATEGORIES[$slug]) ? f(CATEGORIES[$slug], 'nom') : $slug; }

function produits_par_cat(?string $cat = null): array
{
    return array_values(array_filter(produits(), fn ($p) => $cat === null || $p['cat'] === $cat));
}

function heure(string $h): string
{
    if (lang() === 'en') {
        $t = DateTime::createFromFormat('H:i', $h);
        return str_replace(':00', '', $t->format('g:i a'));
    }
    return str_replace(':', 'h', str_replace(':00', 'h', $h));
}

function jour(int $n): string { return lang() === 'en' ? JOURS_EN[$n] : JOURS[$n]; }

/** Résumé des horaires pour l'affichage (« Lun – Sam · 10h – 22h ») */
function horaires_resume(): string
{
    if (!HORAIRES) return tr('Horaires sur WhatsApp', 'Opening hours on WhatsApp');
    return tr('Lundi – samedi · 10h – 22h', 'Monday – Saturday · 10 am – 10 pm');
}

/** Statut d'ouverture en temps réel (heure du Bénin). null si horaires non renseignés. */
function statut_ouverture(): ?array
{
    if (!HORAIRES) return null;
    $jour = (int) date('N');
    $now  = date('H:i');
    $h    = HORAIRES[$jour] ?? null;
    if ($h && $now >= $h[0] && $now < $h[1]) {
        return ['ouvert' => true, 'texte' => tr("Ouvert · jusqu'à ", 'Open · until ') . heure($h[1])];
    }
    for ($i = 0; $i < 8; $i++) {
        $j  = (($jour - 1 + $i) % 7) + 1;
        $hj = HORAIRES[$j] ?? null;
        if (!$hj) continue;
        if ($i === 0 && $now >= $hj[0]) continue;
        $quand = $i === 0 ? tr("aujourd'hui", 'today') : ($i === 1 ? tr('demain', 'tomorrow') : (lang() === 'en' ? jour($j) : strtolower(jour($j))));
        return ['ouvert' => false, 'texte' => tr('Fermé · ouvre ', 'Closed · opens ') . $quand . tr(' à ', ' at ') . heure($hj[0])];
    }
    return ['ouvert' => false, 'texte' => tr('Fermé', 'Closed')];
}

/**
 * Décode le paramètre ?c= d'un lien de commande WhatsApp.
 * Format : base64url( JSON [[id, quantité, taille, couleur], …] ). Les articles inconnus sont ignorés.
 */
function decode_commande(string $c): array
{
    $json = base64_decode(strtr($c, '-_', '+/'), true);
    $rows = $json ? json_decode($json, true) : null;
    if (!is_array($rows)) return [];
    $byId = array_column(produits_tous(), null, 'id');
    $out = [];
    foreach (array_slice($rows, 0, 40) as $r) {
        if (!is_array($r) || !isset($byId[$r[0] ?? ''])) continue;
        $out[] = [
            'p' => $byId[$r[0]],
            'q' => max(1, min(99, (int) ($r[1] ?? 1))),
            's' => mb_substr(trim((string) ($r[2] ?? '')), 0, 30),
            'c' => mb_substr(trim((string) ($r[3] ?? '')), 0, 30),
        ];
    }
    return $out;
}

/** Photo d'un article pour une couleur donnée (FR ou EN), sinon la photo principale */
function photo_couleur(array $p, string $couleur): string
{
    if ($couleur !== '' && !empty($p['photos'])) {
        foreach (['couleurs', 'couleurs_en'] as $k) {
            $i = array_search($couleur, $p[$k] ?? [], true);
            if ($i !== false && isset($p['photos'][$i])) return $p['photos'][$i];
        }
    }
    return $p['image'];
}

/** Rendu d'une page dans le layout */
function render(string $page, array $vars = []): void
{
    extract($vars);
    ob_start();
    include __DIR__ . "/pages/{$page}.php";
    $content = ob_get_clean();
    include __DIR__ . '/layout.php';
}

/** Logo texte : monogramme M + nom */
function logo(): string
{
    return '<span class="logo__mark" aria-hidden="true">M</span>'
        . '<span class="logo__txt"><span class="logo__name">Moudo</span><span class="logo__tag">Fashion · Bohicon</span></span>';
}

/** Icônes en ligne */
function icon(string $name): string
{
    $paths = [
        'home'  => '<path d="M4 11l8-7 8 7"/><path d="M6 9.5V20h12V9.5"/><path d="M10 20v-5h4v5"/>',
        'grid'  => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
        'bag'   => '<path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7V5a3 3 0 0 1 6 0v2"/>',
        'pin'   => '<path d="M12 21s-6.5-5.8-6.5-11a6.5 6.5 0 0 1 13 0c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.4"/>',
        'camera'=> '<path d="M4 8h3l2-2.5h6L17 8h3v11H4z"/><circle cx="12" cy="13" r="3.4"/>',
        'moon'  => '<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z"/>',
        'sun'   => '<circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M4.6 4.6 6 6M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4 6 18M18 6l1.4-1.4"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'tiktok'=> '<path d="M14 4v10.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 4c.4 2.4 2 4 4.5 4.2"/>',
    ];
    return '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
