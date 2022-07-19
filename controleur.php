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
// [URI de la sitemap, délai avant MÀJ en secondes, fonction pour récupérer une liste d'URI, arguments de la fonction, préfixe des URI des éléments]
$dynamicSitemaps = [
    ["/sitemap-static.xml", 2592000, "getSitemapUriByIndexSearch", [ROOT], ""],
    ["/journaux/sitemap-journaux.xml", 86400, "getSitemapUriListBySql", ["SELECT pdfJournal AS uri FROM website_journaux"], "/journaux"]
];

// Déclaration des fonctions de mise à jour.
function getSitemapUriListBySql(string $sqlRequest): array {
    $uriList = array();
    foreach (SqlSimpleRequest::new($sqlRequest)->execute() as $result) $uriList[] = $result->uri;
    return $uriList;
}

function getSitemapUriByIndexSearch(string $root): array {
    $uriList = array();

    // Recherche en profondeur dans un arbre.
    $stack = [$root];
    while (!empty($stack)) {
        $vertice = array_pop($stack);
        foreach (array_diff(scandir($vertice), array('..', '.')) as $wertice) {
            if ($wertice == "index.php") {
                // Simplification de l'URI.
                $r = array();
                foreach (explode("/", $vertice . $wertice) as $p) {
                    if ($p == "..") array_pop($r);
                    elseif ($p != '.' && strlen($p)) $r[] = $p;
                }
                $uriList[] =  ($vertice[0] == "/" ? "/" : "") . implode("/", $r);
            } elseif (is_dir($vertice . $wertice . "/")) {
                $stack[] = $vertice . $wertice . "/";
            }
        }
    }

    return $uriList;
}

// Vérification de l'existence de la sitemapindex.
$sitemapIndexExists = file_exists(ROOT . "sitemapindex.xml");
if (file_exists(ROOT . "sitemapindex.xml")) {
    // Chargement de la sitemapindex.
    $sitemapIndexXmlElement = simplexml_load_file(ROOT . "sitemapindex.xml");

    // Vérification que toutes les sitemaps dynamiques sont bien présentes.
    // Listage des sitemaps dynamiques pas indexées.
    $notIndexedSitemapsIndexes = array();
    for ($i = 0; $i < count($dynamicSitemaps); $i++) $notIndexedSitemapsIndexes[] = $i;
    foreach ($sitemapIndexXmlElement as $sitemapIndexSitemapXmlElement) {
        // Pour chaque sitemap on parcourt la liste des sitemaps dynamiques à MÀJ pour voir s'il y a correspondance.
        foreach ($dynamicSitemaps as $i=>$dynamicSitemap) {
            // Vérification qu'il s'agit d'une correspondance en utilisant la localisation du fichier.
            if ($sitemapIndexSitemapXmlElement->loc == "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]) {
                unset($notIndexedSitemapsIndexes[$i]);
            }
        }
    }

    // Ajout des sitemaps dynamiques pas indexées.
    foreach ($notIndexedSitemapsIndexes as $i) {
        $newURI = $sitemapIndexXmlElement->addChild("sitemap");
        $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemaps[$i][0]);
        $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP", 0));
    }

    // Log
    if (count($notIndexedSitemapsIndexes) != 0) {
        SqlLog::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' was incomplete: added " . count($notIndexedSitemapsIndexes) . " new sitemaps URI; new sitemaps index contains " . count($dynamicSitemaps) . " sitemap URLs");
    }
} elseif (!$sitemapIndexExists) {
    // Regénération de la sitemapindex à partir des données des sitemaps dynamiques.
    $sitemapIndexXmlElement = simplexml_load_string(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex
        xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9
            https://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
</sitemapindex>
EOF
    );

    // Insertion des URI des sitemaps à indexer dans la sitemapindex.
    foreach ($dynamicSitemaps as $dynamicSitemap) {
        $newURI = $sitemapIndexXmlElement->addChild("url");
        $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]);
        $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP", 0));
    }

    // Log
    SqlLog::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' not found: regenerated one from scratch using dynamic sitemaps informations; new sitemaps index contains " . count($dynamicSitemaps) . " sitemap URLs");
}


// Vérification des dates de dernières modifications des sitemaps pour MÀJ.
// Parcours des sitemaps.
foreach ($sitemapIndexXmlElement as $sitemapIndexSitemapXmlElement) {
    // Pour chaque sitemap on parcourt la liste des sitemaps dynamiques à MÀJ pour voir s'il y a correspondance.
    foreach ($dynamicSitemaps as $dynamicSitemap) {
        // Vérification qu'il s'agit d'une correspondance en utilisant la localisation du fichier.
        if ($sitemapIndexSitemapXmlElement->loc == "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]) {
            // Est-ce que le fichier de sitemap existe.
            $sitemapExists = file_exists(ROOT . $dynamicSitemap[0]);

            // Est-ce que le délai est dépassé ou pas. Si oui, alors on doit mettre à jour, sinon non.
            $lastmodTime = strtotime($sitemapIndexSitemapXmlElement->lastmod);
            $outOfDateSeconds = time() - $lastmodTime;
            if (!$sitemapExists || $outOfDateSeconds > 86400) {
                // Lancement de la MÀJ.

                // Recopie de l'en-tête d'un fichier de sitemap. L'objet est de classe SimpleXMLElement.
                $sitemapXmlElement = simplexml_load_string(<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9
    https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
</urlset>
EOT
                );

                // Récupération des URI à insérer dans la sitemap via la fonction fournie.
                $uriList = call_user_func_array($dynamicSitemap[2], $dynamicSitemap[3]);

                // Insertion des URI dans la sitemap.
                foreach ($uriList as $uri) {
                    $newURI = $sitemapXmlElement->addChild("url");
                    $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[4] . "/" . $uri);
                    $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP"));
                }

                // Écriture du fichier (pour appliquer les MÀJ des lastmod).
                $sitemapXmlElement->asXML(ROOT . $dynamicSitemap[0]);

                // Mise à jour de l'attribut lastmod.
                $sitemapIndexSitemapXmlElement->lastmod = date("Y-m-d\TH:i:sP");

                // Log.
                SqlLog::log("[sitemap] Sitemap at URI '$sitemapIndexSitemapXmlElement->loc'" . ($sitemapExists ? " out of date by $outOfDateSeconds seconds" : "not found") . ": updated by " . ($sitemapExists ? "replacing existing file" : "creating previously inexistant file") . "; new sitemap contains " . count($uriList) . " URLs");
            }
        }
    }
}

// Mise à jour de la sitemapindex avec les nouveaux attributs lastmod.
$sitemapIndexXmlElement->asXML(ROOT . "sitemapindex.xml");

########################################################################################################################
# Version du site                                                                                                      #
########################################################################################################################
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
            substr($date, 8, 2) . '/' .
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
