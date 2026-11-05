<?php
/**
 * Catalogue Moudo Fashion (FR + EN).
 *
 * Les articles se gèrent depuis le tableau de bord : https://votre-site/admin
 * PRIX : nombre en FCFA (ex. 15000) ou null → « Prix sur WhatsApp »
 * COULEURS : liste des coloris disponibles (choisis par le client dans la fiche), ou [] si une seule.
 * PHOTOS (facultatif) : une photo par coloris, dans le même ordre que COULEURS ; elle s'affiche quand le client choisit la couleur.
 * Les champs *_en sont la version anglaise.
 */

const P = '/assets/img/p/';
const SHOP = '/assets/img/shop/';

const CATEGORIES = [
    'jeans'       => ['nom' => 'Jeans', 'nom_en' => 'Jeans', 'image' => P . 'jean-brode.jpg',
                      'texte' => 'Baggy, délavés, brodés', 'texte_en' => 'Baggy, washed, embroidered'],
    'vetements'   => ['nom' => 'Vêtements', 'nom_en' => 'Clothing', 'image' => P . 'chemise-crane-rouge.jpg',
                      'texte' => 'Tees, polos, chemises, ensembles', 'texte_en' => 'Tees, polos, shirts, sets'],
    'casquettes'  => ['nom' => 'Casquettes', 'nom_en' => 'Caps', 'image' => P . 'casquettes-cercle.jpg',
                      'texte' => 'Fitted et snapback', 'texte_en' => 'Fitted and snapback'],
    'chaussures'  => ['nom' => 'Chaussures', 'nom_en' => 'Footwear', 'image' => P . 'boots-ble.jpg',
                      'texte' => 'Boots et claquettes', 'texte_en' => 'Boots and slides'],
    'accessoires' => ['nom' => 'Accessoires', 'nom_en' => 'Accessories', 'image' => P . 'ceintures-boucle.jpg',
                      'texte' => 'Ceintures et boxers', 'texte_en' => 'Belts and boxers'],
];

/**
 * Catalogue de départ. Au premier lancement, il est copié dans storage/produits.json ;
 * ensuite, les articles se gèrent depuis le tableau de bord (/admin), plus besoin de toucher à ce fichier.
 */
const PRODUITS_INIT = [
    // ---------- JEANS ----------
    ['id' => 'j01', 'cat' => 'jeans', 'prix' => null, 'image' => P . 'jean-brode.jpg', 'tag' => 'Pièce forte', 'tag_en' => 'Statement',
     'nom' => 'Jean brodé fleurs & grues', 'nom_en' => 'Floral & Crane Embroidered Jeans', 'couleurs' => [],
     'desc' => "Coupe large, délavage gris-bleu, broderies de fleurs et de grues sur la jambe et la poche. Le jean qu'on remarque.",
     'desc_en' => "Wide fit, grey-blue wash, flower and crane embroidery down the leg and on the pocket. Jeans that get noticed."],
    ['id' => 'j02', 'cat' => 'jeans', 'prix' => null, 'image' => P . 'jean-delave.jpg', 'tag' => 'Best-seller', 'tag_en' => 'Best-seller',
     'nom' => 'Jean baggy délavé vintage', 'nom_en' => 'Vintage Washed Baggy Jeans', 'couleurs' => [],
     'desc' => "Délavage sable et bleu, jambe ample qui casse sur la chaussure. À porter avec des boots ou des sneakers.",
     'desc_en' => "Sand-and-blue wash with a loose leg that breaks over the shoe. Wear it with boots or sneakers."],
    ['id' => 'j03', 'cat' => 'jeans', 'prix' => null, 'image' => P . 'jean-peinture.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Jean bootcut taches de peinture', 'nom_en' => 'Paint-Splatter Bootcut Jeans', 'couleurs' => [],
     'desc' => "Bleu clair, coupe évasée et éclaboussures de peinture blanches pour un look artiste.",
     'desc_en' => "Light blue, flared cut with white paint splatters for an artsy look."],
    ['id' => 'j04', 'cat' => 'jeans', 'prix' => null, 'image' => P . 'jean-strass.jpg', 'tag' => 'Nouveau', 'tag_en' => 'New',
     'nom' => 'Jean croix en strass', 'nom_en' => 'Rhinestone Cross Jeans', 'couleurs' => [],
     'desc' => "Délavage vintage et croix en strass sur les côtés. Brille juste ce qu'il faut.",
     'desc_en' => "Vintage wash with rhinestone crosses down the sides. Just the right amount of shine."],
    ['id' => 'j05', 'cat' => 'jeans', 'prix' => null, 'image' => P . 'jean-script-bleu.jpg', 'tag' => '', 'tag_en' => '',
     'photos' => [P . 'jean-script-bleu.jpg', P . 'jean-script-noir.jpg'],
     'nom' => 'Jean droit lettrage brodé', 'nom_en' => 'Script-Embroidered Straight Jeans', 'couleurs' => ['Bleu', 'Noir'], 'couleurs_en' => ['Blue', 'Black'],
     'desc' => "Coupe droite, grand lettrage brodé rouge et lilas, chaîne colorée fournie. Existe en bleu et en noir.",
     'desc_en' => "Straight cut, large red and lilac embroidered script, colourful chain included. Available in blue and black."],

    // ---------- VÊTEMENTS ----------
    ['id' => 'v01', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'chemise-crane-rouge.jpg', 'tag' => 'Coup de cœur', 'tag_en' => 'Staff pick',
     'photos' => [P . 'chemise-crane-rouge.jpg', P . 'chemise-crane-noir.jpg', P . 'chemise-crane-blanc.jpg'],
     'nom' => 'Chemise Crâne couronné', 'nom_en' => 'Crowned Skull Shirt', 'couleurs' => ['Rouge', 'Noir', 'Blanc'], 'couleurs_en' => ['Red', 'Black', 'White'],
     'desc' => "Chemise manches longues, grand imprimé crânes, corbeau et couronne. Trois coloris.",
     'desc_en' => "Long-sleeve shirt with a large skulls, raven and crown print. Three colours."],
    ['id' => 'v02', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'chemise-parrain.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Chemise noire « Le Parrain »', 'nom_en' => '"The Godfather" Black Shirt', 'couleurs' => [],
     'desc' => "Chemise noire coupe ample, imprimé cinéma noir et blanc avec rose rouge.",
     'desc_en' => "Loose black shirt with a black-and-white film print and a red rose."],
    ['id' => 'v03', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'chemise-croix.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Chemisettes motif croix', 'nom_en' => 'Cross Pattern Short-Sleeve Shirts', 'couleurs' => ['Noir', 'Blanc'], 'couleurs_en' => ['Black', 'White'],
     'desc' => "Manches courtes, croix brodées rouges devant ou grand motif au dos.",
     'desc_en' => "Short sleeves, red embroidered crosses on the front or a large print on the back."],
    ['id' => 'v04', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'chemise-peinte.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Chemises illustrées', 'nom_en' => 'Illustrated Shirts', 'couleurs' => ['Blanc', 'Rouge'], 'couleurs_en' => ['White', 'Red'],
     'desc' => "Manches longues, illustration façon nuit étoilée au dos ou petit motif devant.",
     'desc_en' => "Long sleeves, starry-night style illustration on the back or a small front print."],
    ['id' => 'v05', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'polo-raye-vert.jpg', 'tag' => 'Tendance', 'tag_en' => 'Trending',
     'photos' => [P . 'polo-raye-vert.jpg', P . 'polo-raye-beige.jpg'],
     'nom' => 'Polo rayé effet superposé', 'nom_en' => 'Layered-Look Striped Polo', 'couleurs' => ['Vert/bleu', 'Beige'], 'couleurs_en' => ['Green/blue', 'Beige'],
     'desc' => "Polo oversize rayé avec manches longues blanches intégrées et imprimé vintage « 1998 ».",
     'desc_en' => "Oversized striped polo with built-in white long sleeves and a vintage \"1998\" print."],
    ['id' => 'v06', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'polo-zip.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Polos zippés écusson', 'nom_en' => 'Crest Zip Polos', 'couleurs' => ['Rose', 'Noir', 'Gris', 'Rouge', 'Blanc', 'Beige'], 'couleurs_en' => ['Pink', 'Black', 'Grey', 'Red', 'White', 'Beige'],
     'desc' => "Col zippé, écusson brodé, lettrage vertical. Six coloris, pour lui comme pour elle.",
     'desc_en' => "Zip collar, embroidered crest, vertical lettering. Six colours, for him and for her."],
    ['id' => 'v07', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'tshirt-raye-ml.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'T-shirts rayés manches longues', 'nom_en' => 'Striped Long-Sleeve Tees', 'couleurs' => ['Blanc', 'Noir', 'Vert', 'Rouge'], 'couleurs_en' => ['White', 'Black', 'Green', 'Red'],
     'desc' => "Fines rayures, petit logo brodé sur la poitrine. Une base facile à porter.",
     'desc_en' => "Thin stripes with a small embroidered chest logo. An easy everyday basic."],
    ['id' => 'v08', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'tshirt-alien.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'T-shirt « Take me back »', 'nom_en' => '"Take me back" Tee', 'couleurs' => [],
     'desc' => "T-shirt blanc oversize, grand imprimé alien au dos, petit rappel devant.",
     'desc_en' => "Oversized white tee with a large alien print on the back and a small one on the front."],
    ['id' => 'v09', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'tshirt-dream.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'T-shirt « Dream Chasers »', 'nom_en' => '"Dream Chasers" Tee', 'couleurs' => [],
     'desc' => "Lettrage graffiti violet devant, trio de squelettes au dos.",
     'desc_en' => "Purple graffiti lettering on the front, skeleton trio on the back."],
    ['id' => 'v10', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'tshirt-eternal.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'T-shirt « Eternal »', 'nom_en' => '"Eternal" Tee', 'couleurs' => [],
     'desc' => "Typo brush devant, calligraphie et silhouettes au dos.",
     'desc_en' => "Brush type on the front, calligraphy and silhouettes on the back."],
    ['id' => 'v11', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'tshirt-bratz.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'T-shirt lettrage fourrure', 'nom_en' => 'Fuzzy Lettering Tee', 'couleurs' => [],
     'desc' => "Lettrage effet fourrure et auréole devant, étoiles chromées au dos.",
     'desc_en' => "Fuzzy-effect lettering and halo on the front, chrome stars on the back."],
    ['id' => 'v12', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'survet-marron.jpg', 'tag' => 'Ensemble', 'tag_en' => 'Set',
     'photos' => [P . 'survet-marron.jpg', P . 'survet-noir.jpg', P . 'survet-gris.jpg'],
     'nom' => 'Survêtement monogramme', 'nom_en' => 'Monogram Tracksuit', 'couleurs' => ['Marron', 'Noir', 'Gris'], 'couleurs_en' => ['Brown', 'Black', 'Grey'],
     'desc' => "Veste zippée et pantalon assortis, empiècements monogramme. Trois coloris.",
     'desc_en' => "Matching zip jacket and trousers with monogram panels. Three colours."],
    ['id' => 'v13', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'jogger-lisere.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Jogger droit liseré', 'nom_en' => 'Piped Straight Joggers', 'couleurs' => ['Marron', 'Noir'], 'couleurs_en' => ['Brown', 'Black'],
     'desc' => "Jambe droite, liseré blanc sur le côté, cordon contrasté.",
     'desc_en' => "Straight leg, white side piping, contrast drawstring."],
    ['id' => 'v14', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'jogger-trio.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Jogger nervuré à rivets', 'nom_en' => 'Seamed Joggers with Studs', 'couleurs' => ['Marron', 'Violet', 'Noir'], 'couleurs_en' => ['Brown', 'Purple', 'Black'],
     'desc' => "Coutures nervurées devant, petits rivets métal, bas réglable.",
     'desc_en' => "Front seam detail, small metal studs, adjustable hem."],
    ['id' => 'v15', 'cat' => 'vetements', 'prix' => null, 'image' => P . 'jogger-gris-noir.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Jogger épais minimal', 'nom_en' => 'Heavyweight Minimal Joggers', 'couleurs' => ['Gris', 'Noir'], 'couleurs_en' => ['Grey', 'Black'],
     'desc' => "Molleton épais, coupe droite, petit logo discret.",
     'desc_en' => "Heavy fleece, straight cut, small discreet logo."],

    // ---------- CASQUETTES ----------
    ['id' => 'c01', 'cat' => 'casquettes', 'prix' => null, 'image' => P . 'casquettes-cercle.jpg', 'tag' => 'Best-seller', 'tag_en' => 'Best-seller',
     'nom' => 'Casquettes fitted brodées', 'nom_en' => 'Embroidered Fitted Caps', 'couleurs' => [],
     'desc' => "Visière plate, logos d'équipes brodés, patchs et dessous de visière colorés. Dites-nous laquelle vous voulez.",
     'desc_en' => "Flat brim, embroidered team logos, patches and coloured undervisors. Tell us which one you want."],
    ['id' => 'c02', 'cat' => 'casquettes', 'prix' => null, 'image' => P . 'casquettes-snapback.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Casquettes snapback', 'nom_en' => 'Snapback Caps', 'couleurs' => [],
     'desc' => "Réglables à l'arrière, logos et lettrages brodés, plein de couleurs au choix.",
     'desc_en' => "Adjustable at the back, embroidered logos and lettering, lots of colours to choose from."],
    ['id' => 'c03', 'cat' => 'casquettes', 'prix' => null, 'image' => P . 'casquettes-equipes.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Casquettes équipes US', 'nom_en' => 'US Team Caps', 'couleurs' => [],
     'desc' => "Une trentaine d'équipes disponibles. Envoyez-nous le nom de la vôtre.",
     'desc_en' => "Around thirty teams available. Send us the name of yours."],

    // ---------- CHAUSSURES ----------
    ['id' => 's01', 'cat' => 'chaussures', 'prix' => null, 'image' => P . 'boots-ble.jpg', 'tag' => 'Incontournable', 'tag_en' => 'Must-have',
     'photos' => [P . 'boots-ble.jpg', P . 'boots-noir.jpg', P . 'boots-orange.jpg'],
     'nom' => 'Boots montantes nubuck', 'nom_en' => 'Nubuck Ankle Boots', 'couleurs' => ['Blé', 'Noir', 'Orange'], 'couleurs_en' => ['Wheat', 'Black', 'Orange'],
     'desc' => "Nubuck, col rembourré, semelle crantée. La paire qui va avec tous les jeans.",
     'desc_en' => "Nubuck leather, padded collar, lug sole. The pair that goes with every pair of jeans."],
    ['id' => 's02', 'cat' => 'chaussures', 'prix' => null, 'image' => P . 'sandale-camel.jpg', 'tag' => '', 'tag_en' => '',
     'photos' => [P . 'sandale-camel.jpg', P . 'sandale-noire.jpg', P . 'sandale-grise.jpg'],
     'nom' => 'Claquettes brodées squelette', 'nom_en' => 'Skeleton Embroidered Slides', 'couleurs' => ['Camel', 'Noir', 'Gris'], 'couleurs_en' => ['Camel', 'Black', 'Grey'],
     'desc' => "Semelle liège confortable, bride daim avec os brodés.",
     'desc_en' => "Comfortable cork sole, suede strap with embroidered bones."],

    // ---------- ACCESSOIRES ----------
    ['id' => 'a01', 'cat' => 'accessoires', 'prix' => null, 'image' => P . 'ceintures-boucle.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Ceintures boucle ovale', 'nom_en' => 'Oval Buckle Belts', 'couleurs' => ['Noir/blanc', 'Camel', 'Rouge', 'Blanc'], 'couleurs_en' => ['Black/white', 'Camel', 'Red', 'White'],
     'desc' => "Grande boucle ovale métal, bande imprimée lettrage. Le détail qui finit la tenue.",
     'desc_en' => "Large metal oval buckle, printed lettering strap. The detail that finishes the outfit."],
    ['id' => 'a02', 'cat' => 'accessoires', 'prix' => null, 'image' => P . 'ceintures-lettrage.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Ceintures lettrage', 'nom_en' => 'Lettering Belts', 'couleurs' => ['Camouflage', 'Blanc', 'Noir', 'Rouge'], 'couleurs_en' => ['Camo', 'White', 'Black', 'Red'],
     'desc' => "Boucle à ardillon, lettrage imprimé sur toute la longueur.",
     'desc_en' => "Pin buckle with lettering printed along the full length."],
    ['id' => 'a03', 'cat' => 'accessoires', 'prix' => null, 'image' => P . 'ceintures-motif.jpg', 'tag' => '', 'tag_en' => '',
     'nom' => 'Ceintures toile à motif', 'nom_en' => 'Patterned Canvas Belts', 'couleurs' => ['Jaune', 'Rouge', 'Bleu', 'Gris/camel', 'Noir'], 'couleurs_en' => ['Yellow', 'Red', 'Blue', 'Grey/tan', 'Black'],
     'desc' => "Toile enduite à motif chevrons, passants cuir, boucle argentée.",
     'desc_en' => "Coated canvas with a chevron pattern, leather loops, silver buckle."],
    ['id' => 'a04', 'cat' => 'accessoires', 'prix' => null, 'image' => P . 'boxers-coffret.jpg', 'tag' => 'Coffret', 'tag_en' => 'Box set',
     'nom' => 'Coffret de boxers', 'nom_en' => 'Boxer Gift Box', 'couleurs' => [],
     'desc' => "Boxers coton ceinture élastique logo, en coffret. Idée cadeau facile.",
     'desc_en' => "Cotton boxers with logo waistband, in a gift box. An easy gift idea."],
];

/** Looks portés (page Lookbook + accueil) */
const LOOKS = [
    ['src' => P . 'look-assis.jpg',   'alt' => 'Look casquette, polo rayé à carreaux et jean baggy', 'alt_en' => 'Cap, plaid-striped polo and baggy jeans look'],
    ['src' => P . 'look-capuche.jpg', 'alt' => 'Look sweat à capuche gris, jean délavé et boots orange', 'alt_en' => 'Grey hoodie, washed jeans and orange boots look'],
    ['src' => P . 'look-miroir.jpg',  'alt' => 'Look polo rayé et casquette crème', 'alt_en' => 'Striped polo and cream cap look'],
];

/** Photos de la boutique */
const BOUTIQUE = [
    ['src' => SHOP . 'video-21.jpg',       'alt' => 'Les étagères de la boutique', 'alt_en' => 'The shop shelves'],
    ['src' => SHOP . 'video-9.jpg',        'alt' => 'Portant de polos en boutique', 'alt_en' => 'Polo rack in the shop'],
    ['src' => P . 'boutique-coffret.jpg',  'alt' => 'Coffret de boxers présenté en boutique', 'alt_en' => 'Boxer box set shown in the shop'],
];
