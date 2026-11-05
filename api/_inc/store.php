<?php
/**
 * Stockage des articles (fichier JSON) et des photos envoyées depuis le tableau de bord.
 *
 * - storage/produits.json : la liste des articles (créée automatiquement au premier lancement
 *   à partir de PRODUITS_INIT dans data.php).
 * - uploads/p/            : les photos ajoutées par le client (redimensionnées et ré-encodées en JPEG).
 *
 * Fonctionne sur tout hébergement PHP classique (Hostinger, LWS, o2switch…).
 * Sur Vercel, le disque est en lecture seule : le site s'affiche, mais le tableau de bord ne peut pas enregistrer.
 */

const ROOT_DIR    = __DIR__ . '/../..';
const STORAGE_DIR = ROOT_DIR . '/storage';
const UPLOAD_DIR  = ROOT_DIR . '/uploads/p';
const UPLOAD_URL  = '/uploads/p/';
const PRODUITS_FILE = STORAGE_DIR . '/produits.json';

/** Tous les articles, dans l'ordre choisi (y compris ceux masqués) */
function produits_tous(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;
    $list = null;
    if (is_file(PRODUITS_FILE)) {
        $list = json_decode((string) file_get_contents(PRODUITS_FILE), true);
    }
    if (!is_array($list)) {
        $list = array_map(fn ($p) => $p + ['stock' => true, 'visible' => true], PRODUITS_INIT);
    }
    return $cache = array_values($list);
}

/** Articles affichés sur le site public */
function produits(): array
{
    return array_values(array_filter(produits_tous(), fn ($p) => ($p['visible'] ?? true) !== false));
}

function produit_par_id(string $id): ?array
{
    foreach (produits_tous() as $p) if ($p['id'] === $id) return $p;
    return null;
}

function stockage_ok(): bool
{
    if (!is_dir(STORAGE_DIR)) @mkdir(STORAGE_DIR, 0755, true);
    return is_dir(STORAGE_DIR) && is_writable(STORAGE_DIR);
}

/** Enregistre la liste complète (écriture atomique + copie de sauvegarde) */
function produits_enregistrer(array $list): bool
{
    if (!stockage_ok()) return false;
    $json = json_encode(array_values($list), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) return false;
    if (is_file(PRODUITS_FILE)) @copy(PRODUITS_FILE, STORAGE_DIR . '/produits.backup.json');
    $tmp = PRODUITS_FILE . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) return false;
    return rename($tmp, PRODUITS_FILE);
}

function nouvel_id(): string
{
    $ids = array_column(produits_tous(), 'id');
    do { $id = 'x' . bin2hex(random_bytes(3)); } while (in_array($id, $ids, true));
    return $id;
}

/**
 * Traite une photo envoyée ($_FILES['champ']).
 * Vérifie que c'est bien une image JPEG/PNG/WebP, la redimensionne (1000 px max), la ré-encode en JPEG
 * (ce qui supprime tout contenu caché) et renvoie son URL publique. Renvoie null si pas de fichier, ou lève une erreur lisible.
 */
function photo_recue(?array $f): ?string
{
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('La photo est trop lourde (10 Mo maximum).');
    }
    if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
        throw new RuntimeException("La photo n'a pas pu être envoyée. Réessayez.");
    }
    if ($f['size'] > 10 * 1024 * 1024) throw new RuntimeException('La photo est trop lourde (10 Mo maximum).');

    $info = @getimagesize($f['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpeg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]])) {
        throw new RuntimeException('Format non accepté. Envoyez une photo JPG, PNG ou WebP.');
    }
    if (!is_dir(UPLOAD_DIR) && !@mkdir(UPLOAD_DIR, 0755, true)) {
        throw new RuntimeException("Le dossier des photos n'est pas accessible sur l'hébergement.");
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.jpg';
    $dest = UPLOAD_DIR . '/' . $name;

    if (!function_exists('imagecreatetruecolor')) {
        throw new RuntimeException("L'hébergement ne permet pas de traiter les images (extension GD absente).");
    }
    $src = match ($types[$info[2]]) {
        'jpeg' => @imagecreatefromjpeg($f['tmp_name']),
        'png'  => @imagecreatefrompng($f['tmp_name']),
        'webp' => @imagecreatefromwebp($f['tmp_name']),
    };
    if (!$src) throw new RuntimeException('Cette image est illisible. Essayez une autre photo.');

    // Rotation selon l'orientation de l'appareil photo (photos de téléphone)
    if ($types[$info[2]] === 'jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($f['tmp_name']);
        $rot = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
        if ($rot) { $r = imagerotate($src, $rot, 0); if ($r) { imagedestroy($src); $src = $r; } }
    }

    $w = imagesx($src); $h = imagesy($src);
    $max = 1000; $k = min(1, $max / max($w, $h));
    $nw = max(1, (int) round($w * $k)); $nh = max(1, (int) round($h * $k));
    $dst = imagecreatetruecolor($nw, $nh);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // fond blanc pour les PNG transparents
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imageinterlace($dst, true);
    $ok = imagejpeg($dst, $dest, 84);
    imagedestroy($src); imagedestroy($dst);
    if (!$ok) throw new RuntimeException("La photo n'a pas pu être enregistrée.");
    return UPLOAD_URL . $name;
}

/** Supprime une photo envoyée par le client (jamais les photos d'origine du site) */
function photo_supprimer(?string $url): void
{
    if (!$url || !str_starts_with($url, UPLOAD_URL)) return;
    $file = UPLOAD_DIR . '/' . basename($url);
    if (is_file($file)) @unlink($file);
}
