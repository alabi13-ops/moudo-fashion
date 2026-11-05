<?php
/**
 * Moudo Fashion — configuration générale du site.
 * Toutes les infos de la boutique se modifient ici.
 */

const SITE = [
    'nom'         => 'Moudo Fashion',
    'nom_court'   => 'Moudo',
    'url'         => 'https://moudo-fashion.vercel.app', // à remplacer par le lien Vercel final
    // Numéro donné : +229 67 75 22 43 → au format béninois à 10 chiffres (depuis 2024) : 01 67 75 22 43
    'telephone'   => '+229 01 67 75 22 43',
    'tel_href'    => '+2290167752243',
    'whatsapp'    => '2290167752243',               // format wa.me : indicatif + numéro, sans espaces ni +
    'quartier'    => 'À côté du bar Setondji, sur le pavé',
    'quartier_en' => 'Next to Setondji bar, on the paved road',
    'repere'      => 'Juste derrière le Lycée technique de Bohicon',
    'repere_en'   => 'Right behind the Bohicon Technical High School',
    'ville'       => 'Bohicon, Bénin',
    'ville_en'    => 'Bohicon, Benin',
    'maps_url'    => 'https://maps.app.goo.gl/svgoaSKYkVtux8VK7',   // fiche Google Maps de la boutique
    // Réseaux sociaux : laisser vide pour masquer
    'instagram'   => '',
    'facebook'    => '',
    'tiktok'      => 'https://www.tiktok.com/@moudofashion',
];

/**
 * Horaires : index 1 = lundi … 7 = dimanche. Exemple : 2 => ['09:00', '21:00'], ou null si fermé.
 * Laisser le tableau VIDE tant que les horaires ne sont pas confirmés : le site affiche alors
 * « Horaires sur WhatsApp » au lieu d'inventer des heures.
 */
const HORAIRES = [
    1 => ['10:00', '22:00'],
    2 => ['10:00', '22:00'],
    3 => ['10:00', '22:00'],
    4 => ['10:00', '22:00'],
    5 => ['10:00', '22:00'],
    6 => ['10:00', '22:00'],
    7 => null,
];

const JOURS = [1 => 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
const JOURS_EN = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

/**
 * Tableau de bord (/admin) : code d'installation demandé UNE SEULE FOIS, pour créer le mot de passe.
 * Une fois le mot de passe créé, ce code ne sert plus. Pour réinitialiser le mot de passe :
 * supprimer le fichier storage/admin.json sur l'hébergement, puis revenir sur /admin avec ce code.
 */
const ADMIN_CODE_INSTALLATION = 'MOUDO-9WTB-TZ9C';

/** Tailles proposées par catégorie (affichées dans la fiche produit, envoyées dans le message WhatsApp) */
const TAILLES = [
    'jeans'       => ['28', '29', '30', '31', '32', '33', '34', '36', '38'],
    'vetements'   => ['S', 'M', 'L', 'XL', 'XXL'],
    'casquettes'  => [],                        // taille unique / réglable
    'chaussures'  => ['39', '40', '41', '42', '43', '44', '45'],
    'accessoires' => [],
];

date_default_timezone_set('Africa/Porto-Novo');
