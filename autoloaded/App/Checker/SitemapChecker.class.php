<?php

namespace App\Checker;

use App\Log;
use App\Request\SqlRequest;
use Throwable;

class SitemapChecker {
    // Attributs statiques.
    // Liste des sitemaps dynamiques qu'il faut mettre à jour.
    // [URI de la sitemap, délai avant MÀJ en secondes, fonction pour récupérer une liste d'URI, arguments de la fonction, préfixe des URI des éléments]
    private const DYNAMIC_SITEMAPS = [
        ["/sitemap-static.xml", 7776000 /* 90 jours */, "getSitemapUriByIndexSearchFromRoot", [], ""]
    ];

    // Méthodes statiques.
    private function getSitemapUriListBySql(string $sqlRequest): array {
        $uriList = array();
        foreach (SqlRequest::new($sqlRequest)->execute() as $result) $uriList[] = $result->uri;
        return $uriList;
    }

    private function getSitemapUriByIndexSearchFromRoot(): array {
        $uriList = array();

        // Recherche en profondeur dans un arbre.
        $stack = [$this->root];
        while (!empty($stack)) {
            $vertice = array_pop($stack);
            foreach (array_diff(scandir($vertice), array('..', '.')) as $wertice) {
                if ($wertice == "index.php") {
                    // Simplification de l'URI.
                    $r = array();
                    foreach (explode("/", $vertice) as $p) {
                        if ($p == "..") array_pop($r);
                        elseif ($p != '.' && strlen($p)) $r[] = $p;
                    }
                    $uriList[] =  ($vertice[0] == "/" ? "/" : "") . implode("/", $r) . "/";
                } elseif (is_dir($vertice . $wertice . "/")) {
                    $stack[] = $vertice . $wertice . "/";
                }
            }
        }

        return $uriList;
    }

    // Constructeur statique.
    public static function new(string $root): SitemapChecker {
        return new SitemapChecker($root);
    }

    // Attributs.
    private string $root;

    // Constructeur.
    public function __construct(string $root) {
        $this->root = $root;
    }

    public function check(): bool {
        $modified = false;

        // Seulement si le fichier existe alors, on peut tenter de le lire.
        if (file_exists($this->root . "sitemapindex.xml")) {
            try {
                // Chargement de la sitemapindex.
                $sitemapIndexXmlElement = simplexml_load_file($this->root . "sitemapindex.xml");

                // Vérification que toutes les sitemaps dynamiques sont bien présentes.
                // Listage des sitemaps dynamiques pas indexées.
                $notIndexedSitemapsIndexes = array();
                for ($i = 0; $i < count(SitemapChecker::DYNAMIC_SITEMAPS); $i++) $notIndexedSitemapsIndexes[] = $i;
                foreach ($sitemapIndexXmlElement as $sitemapIndexSitemapXmlElement) {
                    // Pour chaque sitemap on parcourt la liste des sitemaps dynamiques à MÀJ pour voir s'il y a correspondance.
                    foreach (SitemapChecker::DYNAMIC_SITEMAPS as $i => $dynamicSitemap) {
                        // Vérification qu'il s'agit d'une correspondance en utilisant la localisation du fichier.
                        if ($sitemapIndexSitemapXmlElement->loc == "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]) {
                            unset($notIndexedSitemapsIndexes[$i]);
                        }
                    }
                }

                // Ajout des sitemaps dynamiques pas indexées.
                foreach ($notIndexedSitemapsIndexes as $i) {
                    $newURI = $sitemapIndexXmlElement->addChild("sitemap");
                    $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . SitemapChecker::DYNAMIC_SITEMAPS[$i][0]);
                    $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP", 0));
                }

                // Log
                if (count($notIndexedSitemapsIndexes) != 0) {
                    $modified = true;
                    Log::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' was incomplete: added " . count($notIndexedSitemapsIndexes) . " new sitemaps URI; new sitemaps index contains " . count(SitemapChecker::DYNAMIC_SITEMAPS) . " sitemap URLs");
                }
            } catch (Throwable $e) {
                // Erreur de lecture du fichier, donc on le supprime pour le regénérer.
                unlink($this->root . "sitemapindex.xml");

                // Log
                Log::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' exists and is incomplete, but an exception occurred trying to read the file: " . $e->getMessage() . "; deleting the file in order to validly regenerate");
            }
        }

        // Si on n'est pas parvenu à valider le fichier sitemap.
        if (!file_exists($this->root . "sitemapindex.xml")) {
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
            foreach (SitemapChecker::DYNAMIC_SITEMAPS as $dynamicSitemap) {
                $newURI = $sitemapIndexXmlElement->addChild("url");
                $newURI->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]);
                $newURI->addChild("lastmod", date("Y-m-d\TH:i:sP", 0));
            }

            // Log
            $modified = true;
            Log::log("[sitemap] Sitemaps index at URI 'https://" . $_SERVER["HTTP_HOST"] . "/sitemapindex.xml' not found: regenerated one from scratch using dynamic sitemaps informations; new sitemaps index contains " . count(SitemapChecker::DYNAMIC_SITEMAPS) . " sitemap URLs");
        }

        // Vérification des dates de dernières modifications des sitemaps pour MÀJ.
        // Parcours des sitemaps.
        foreach ($sitemapIndexXmlElement as $sitemapIndexSitemapXmlElement) {
            // Pour chaque sitemap on parcourt la liste des sitemaps dynamiques à MÀJ pour voir s'il y a correspondance.
            foreach (SitemapChecker::DYNAMIC_SITEMAPS as $dynamicSitemap) {
                // Vérification qu'il s'agit d'une correspondance en utilisant la localisation du fichier.
                if ($sitemapIndexSitemapXmlElement->loc == "https://" . $_SERVER["HTTP_HOST"] . $dynamicSitemap[0]) {
                    // Est-ce que le fichier de sitemap existe.
                    $sitemapExists = file_exists(ROOT . $dynamicSitemap[0]);

                    // Est-ce que le délai est dépassé ou pas. Si oui, alors on doit mettre à jour, sinon non.
                    $lastmodTime = strtotime($sitemapIndexSitemapXmlElement->lastmod);
                    $outOfDateSeconds = time() - $lastmodTime;
                    if (!$sitemapExists || $outOfDateSeconds > $dynamicSitemap[1]) {
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
                        $uriList = call_user_func_array(array($this, $dynamicSitemap[2] ), $dynamicSitemap[3]);

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
                        $modified = true;
                        Log::log("[sitemap] Sitemap at URI '$sitemapIndexSitemapXmlElement->loc'" . ($sitemapExists ? " out of date by $outOfDateSeconds seconds" : "not found") . ": updated by " . ($sitemapExists ? "replacing existing file" : "creating previously inexistant file") . "; new sitemap contains " . count($uriList) . " URLs");
                    }
                }
            }
        }

        // Mise à jour de la sitemapindex avec les nouveaux attributs lastmod.
        $sitemapIndexXmlElement->asXML(ROOT . "sitemapindex.xml");

        return $modified;
    }
}
