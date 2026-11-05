# Moudo Fashion — site vitrine (PHP + Vercel)

Prêt-à-porter à Bohicon (à côté du bar Setondji, derrière le Lycée technique). Catalogue avec choix de taille et couleur, sélection multi-articles, commande via WhatsApp, français/anglais, mode sombre.

## Lancer en local
```bash
php -S localhost:8000 api/index.php
```

## Mettre en ligne sur Hostinger (recommandé)
1. Hostinger → Sites web → ajouter le site / le domaine. Dans **Avancé → Configuration PHP**, choisir **PHP 8.2 ou plus**.
2. **Gestionnaire de fichiers** → dossier `public_html` : y envoyer **le contenu** du dossier `moudo-fashion` (pas le dossier lui-même) : `api/`, `assets/`, `storage/`, `uploads/`, `.htaccess`, `.user.ini`…
   (Ou bien : Hostinger → Git → connecter le dépôt GitHub, branche `main`, dossier `public_html`.)
3. **SSL** : activer le certificat gratuit (Sécurité → SSL). Le `.htaccess` force ensuite le HTTPS.
4. Mettre l'adresse définitive dans `api/_inc/config.php` (ligne `'url'`).
5. Ouvrir `https://votre-domaine/admin` et créer le mot de passe avec le code d'installation.

> Le site s'affiche aussi sur Vercel, mais le tableau de bord ne peut pas enregistrer là-bas (disque en lecture seule).

## Tableau de bord (/admin)
- **Code d'installation** (une seule fois, pour créer le mot de passe) : voir `ADMIN_CODE_INSTALLATION` dans `api/_inc/config.php`. Mettez le dépôt GitHub en **privé** et créez le mot de passe dès la mise en ligne.
- Ajouter / modifier / supprimer un article, photo principale + une photo par couleur, prix, badge, tailles, description FR/EN.
- Boutons **En stock / Épuisé** et **Visible / Masqué**, flèches ↑ ↓ pour l'ordre d'affichage.
- Les articles sont enregistrés dans `storage/produits.json` (une copie de sauvegarde `produits.backup.json` est faite à chaque modification), les photos dans `uploads/p/`.
- **Mot de passe oublié** : supprimer `storage/admin.json` via le gestionnaire de fichiers, puis revenir sur `/admin` avec le code d'installation.
- **Sauvegarde** : télécharger de temps en temps `storage/produits.json` et le dossier `uploads/`.
- ⚠️ Lors d'une mise à jour du site, **ne pas écraser** `storage/` ni `uploads/p/` sur l'hébergement (le `.gitignore` les exclut déjà de Git).

### Sécurité en place
Mot de passe haché · code d'installation · session HttpOnly/SameSite/Secure, expiration après 2 h · jeton anti-CSRF sur chaque action · blocage 15 min après 5 essais ratés · photos vérifiées, redimensionnées et ré-encodées (aucun fichier piégé possible) · aucun script exécutable dans `uploads/` · dossiers `storage/` et `api/_inc/` interdits d'accès · HTTPS forcé + en-têtes de sécurité.

## Où modifier quoi
| Fichier | Contenu |
|---|---|
| `api/_inc/config.php` | Nom, WhatsApp, adresse, **lien Google Maps** (à mettre quand la fiche est validée), horaires, réseaux, tailles par rayon |
| `api/_inc/data.php` | Rayons, catalogue de départ, looks, photos boutique (les articles se gèrent ensuite depuis /admin) |
| `api/_inc/pages/*.php` | Textes des pages |
| `assets/css/style.css` | Couleurs (variables en haut) et mise en page |
| `assets/js/app.js` | Fiche produit, tailles, sélection, messages WhatsApp |

- Ajouter un article : depuis le tableau de bord `/admin`.
- Prix : `'prix' => 15000` (FCFA) ou `null` → « Prix sur WhatsApp ».
- Logo : `assets/img/logo-dark.svg` / `logo-light.svg` (vectoriel), `logo-512.png`.

## Commande en photos
Chaque message WhatsApp contient un lien `…/commande?c=…&n=Prénom`. Il ouvre une page récapitulative avec la photo, la taille, la couleur et la quantité de chaque article, et WhatsApp en affiche l'aperçu avec la photo du premier article.
⚠️ Le lien utilise `SITE['url']` (dans `config.php`) : mettre l'adresse Vercel définitive, sinon les liens ne s'ouvriront pas.
Pour qu'une couleur affiche sa propre photo, renseigner `'photos'` dans `data.php` (même ordre que `'couleurs'`).

## Pages
`/` · `/collection` (filtres, recherche, `#jeans` filtre directement) · `/lookbook` · `/contact` — version anglaise sous `/en/…`
