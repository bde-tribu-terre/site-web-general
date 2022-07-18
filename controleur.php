<?php
require_once ROOT . "../connect.php";
require_once ROOT . "modele/SqlLog.class.php";
require_once ROOT . "modele/SqlSimpleRequest.class.php";

########################################################################################################################
# Vérification du protocole (les deux fonctionnent, mais on veut forcer le passage par HTTPS)                           #
########################################################################################################################
if($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

########################################################################################################################
# Initialisation des tableaux globaux                                                                                  #
########################################################################################################################
# Messages
$GLOBALS['messages'] = array();

########################################################################################################################
# Initialisation du tableau formulaire                                                                                 #
########################################################################################################################
$form = array();
foreach ($_POST as $keyInput => $valInput) {
    $arrayInput = explode('_', $keyInput);
    if (isset($form['_name']) && $form['_name'] != $arrayInput[0]) {
        ajouterMessage(502, 'Attention : la convention d\'attribut "name" des inputs n\'est pas respectée.');
    } else {
        $form['_name'] = $arrayInput[0];
    }
    if (isset($arrayInput[2]) && $arrayInput[2] == 'submit') {
        $form['_submit'] = $arrayInput['1'];
    } else {
        $form[explode('_', $keyInput)[1]] = $valInput;
    }
}

if (count($form) == 0) {
    $form['_name'] = NULL;
    $form['_submit'] = NULL;
}

########################################################################################################################
# DEBUG pour pendant le développement                                                                                  #
# /!\ Tout ce qui suit doit être en commentaire dans la version définitive du site /!\                                 #
########################################################################################################################
# Visualisation du formulaire POST
##ajouterMessage(0, print_r($form, true));

########################################################################################################################
# Fonctions d'ajout dans les tableaux globaux (pour la lisibilité)                                                     #
########################################################################################################################
function ajouterMessage($code, $texte): void {
    $GLOBALS['messages'][] = [$code, htmlentities($texte, ENT_QUOTES, 'UTF-8')];
}

########################################################################################################################
# Mise à jour des sitemaps                                                                                             #
########################################################################################################################
// Liste des sitemaps dynamiques qu'il faut mettre à jour.
// [URI de la sitemap, délai avant MÀJ en secondes, requête SQL pour obtenir les URI des éléments, préfixe des URI des éléments]
$dynamicSitemaps = [
    ["/journaux/sitemap-journaux.xml", 86400, "SELECT pdfJournal AS uri FROM website_journaux", "/journaux"]
];

// Vérification de l'existence de la sitemapindex.
if (!file_exists(ROOT . "sitemapindex.xml")) {
    SqlLog::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' not found: reading of 'lastmod' attributes is impossible");
}

// Vérification des dates de dernières modifications des sitemaps pour MÀJ.
else {
    // Chargement de la sitemapindex.
    $sitemapIndexXmlElement = simplexml_load_file(ROOT . "sitemapindex.xml");

    // Parcours des sitemaps.
    foreach ($sitemapIndexXmlElement as $sitemapIndexSitemapXmlElement) {
        // Pour chaque sitemap on parcourt la liste des sitemaps dynamiques à MÀJ pour voir s'il y a correspondance.
        foreach ($dynamicSitemaps as $dynamicSitemap) {
            // Vérification qu'il s'agit d'une correspondance en utilisant la localisation du fichier.
            if ($sitemapIndexSitemapXmlElement->loc == "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]) {
                // Est-ce que le délai est dépassé ou pas. Si oui, alors on doit mettre à jour, sinon non.
                $lastmodTime = strtotime($sitemapIndexSitemapXmlElement->lastmod);
                $outOfDateSeconds = time() - $lastmodTime;
                if ($outOfDateSeconds > 86400) {
                    // Lancement de la MÀJ.

                    // Recopie de l'en-tête d'un fichier de sitemap. L'objet est de classe SimpleXMLElement.
                    $sitemap = simplexml_load_string(<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9
        https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
</urlset>
EOT
                    );

                    // Récupération des URI à insérer dans la sitemap.
                    $uriList = array();
                    foreach (SqlSimpleRequest::new($dynamicSitemap[2])->execute() as $result) $uriList[] = $result->uri;

                    foreach ($uriList as $uri) {
                        $newURI = $sitemap->addChild("url");
                        $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[3] . "/" . $uri);
                        $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP"));
                    }

                    // Vérification que le fichier existe.
                    $fileAlreadyExists = file_exists(ROOT . $dynamicSitemap[0]);
                    $sitemap->asXML(ROOT . $dynamicSitemap[0]);

                    // Mise à jour de l'attribut lastmod.
                    $sitemapIndexSitemapXmlElement->lastmod = date("Y-m-d\TH:i:sP");

                    // Log.
                    SqlLog::log("[sitemap] Sitemap at URI '$sitemapIndexSitemapXmlElement->loc' out of date by $outOfDateSeconds seconds: updated by " . ($fileAlreadyExists ? "replacing existing file" : "creating previously inexistant file") . "; new sitemap contains " . count($uriList) . " URLs");
                }
            }
        }
    }

    // Mise à jour de la sitemapindex avec les nouveaux attributs lastmod.
    $sitemapIndexXmlElement->asXML(ROOT . "sitemapindex.xml");
}

########################################################################################################################
# Version du site                                                                                                      #
########################################################################################################################
/**
 * Variable contenant la version actuelle du site indiquée dans le fichier ."../version.txt".
 */
define('VERSION_SITE', file_get_contents(ROOT . '/version.txt'));

########################################################################################################################
# Fonctions d'affichage                                                                                                #
########################################################################################################################
/**
 * Génère une date dans un format convivial.
 * @param string $date
 * La date à convertir au format aaaa-mm-jj (info : format standard en SQL).
 * @param bool $numerique
 * Faut-il privilégier un format numérique ? Si le paramètre n'est pas renseigné alors la date sera au format développé
 * en français. Exemples :
 * <ul>
 * <li>Développé : 1<sup>er</sup> Janvier 2020</li>
 * <li>Numérique : 01/01/2020</li>
 * </ul>
 * @return string
 * <strong>HTML</strong> La date au format choisi, sous forme de chaîne de caractère.
 */
function genererDate(string $date, bool $numerique = false): string {
    if ($numerique) {
        return
            substr($date, 8, 2) . 'vue.php/' .
            substr($date, 5, 2) . '/' .
            substr($date, 0, 4);
    } else {
        $arrayMois = [
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
        ];

        return
            (substr($date, 8, 2) == '01' ? '1<sup>er</sup>' : intval(substr($date, 8, 2))) .
            ' ' .
            $arrayMois[substr($date, 5, 2)] .
            ' ' .
            substr($date, 0, 4);
    }
}
