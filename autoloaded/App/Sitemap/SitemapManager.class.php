<?php

namespace App\Sitemap;

use App\Log;
use App\Sitemap\Generator\SitemapGenerator;
use Throwable;

/**
 * Gestionnaire de sitemap. Il s'agit d'une classe singleton : instance obtenue par la méthode statique get.
 */
class SitemapManager {
    // Static
    private static SitemapManager $instance;

    /**
     * Renvoie une référence à l'instance singleton du gestionnaire de sitemap.
     * @return SitemapManager Instance singleton du gestionnaire de sitemap.
     */
    public static function get(): SitemapManager {
        if (!isset(self::$instance)) {
            self::$instance = new SitemapManager();
        }

        return self::$instance;
    }

    // Singleton
    private string $sitemapIndexLocation; // Initialisé dans le constructeur.
    private bool $sitemapIndexUpdated = false;
    private array $sitemaps = [];

    private function __construct() {
        // Localisation normale : à la racine du serveur.
        $this->sitemapIndexLocation = $_SERVER['DOCUMENT_ROOT'] . "/sitemapindex.xml";

        // Si l'index existe, alors on le lit pour lister les sitemaps.
        if (file_exists($this->sitemapIndexLocation)) {
            try {
                // Lecture du fichier au format XML.
                $sitemapIndexXmlElement = simplexml_load_file($this->sitemapIndexLocation);

                // Pour chaque sitemap, on l'ajoute à la liste des sitemaps.
                foreach ($sitemapIndexXmlElement as $sitemapXmlElement) {
                    $fileLocation = $_SERVER['DOCUMENT_ROOT'] . substr($sitemapXmlElement->loc, strlen((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"]));

                    if (file_exists($fileLocation)) {
                        $this->sitemaps[] = [
                            "fileLocation" => $fileLocation,
                            "urlLocation" => (string) $sitemapXmlElement->loc,
                            "lastmod" =>  strtotime((string) $sitemapXmlElement->lastmod),
                            "updated" => false
                        ];
                    } else {
                        $this->sitemapIndexUpdated = true;

                        Log::log("[sitemap] Sitemap index references sitemap at path '" . $fileLocation . "' that doesn't exist; the path will be removed from sitemap index");
                    }
                }
            } catch (Throwable $e) {
                Log::log("[sitemap] Sitemaps index at path '" . $this->sitemapIndexLocation . "' exists but an exception occurred trying to read the file: " . $e->getMessage() . "; the file will be regenerated");
            }
        }

        // Sinon si l'index n'existe pas, alors il n'y a aucune sitemap à lire, mais il faudra générer l'index.
        else {
            $this->sitemapIndexUpdated = true;

            Log::log("[sitemap] No sitemaps index at path '" . $this->sitemapIndexLocation . "'; the file will be regenerated");
        }
    }

    public function __destruct() {
        if ($this->sitemapIndexUpdated) {
            $this->saveSitemapIndexFile();
        }
    }

    /**
     * Vérifie l'intégrité d'une sitemap : existence et durée de vie. Si la sitemap est invalidée, alors elle est mise à
     * jour en utilisant un générateur de sitemap (à choisir en fonction de l'objectif de la sitemap), ce qui mettra
     * implicitement à jour les fichiers de la sitemap et de l'index de sitemap.
     *
     * Tous les fichiers sitemaps sont enregistrés à la racine du serveur PHP, et sont référencés par le fichier index
     * des sitemaps, dont le nom est fixé à 'sitemapindex.xml'.
     * @param string $fileName Nom du fichier de la sitemap, incluant l'extension '.xml'.
     * @param int $ttl Durée de vie en secondes de la sitemap : si la sitemap a été générée précédemment dans cet
     * intervalle, alors elle n'est pas mise à jour.
     * @param SitemapGenerator $generator Générateur de sitemap, à choisir en fonction de l'objectif de la sitemap, et
     * générant les couples (URL, dernière modification) à référencer dans la sitemap.
     * @return bool Est-ce que le fichier de la sitemap a été mis à jour.
     */
    public function checkAndUpdate(string $fileName, int $ttl, SitemapGenerator $generator): bool {
        $sitemap = $this->getSitemap($_SERVER['DOCUMENT_ROOT'] . "/" . $fileName);

        if (!$sitemap || time() - $sitemap["lastmod"] > $ttl) {
            if (!$sitemap) {
                Log::log("[sitemap] Sitemap of path '" . $_SERVER['DOCUMENT_ROOT'] . "/" . $fileName . "' was not indexed in the sitemap index and will be added");
            } else {
                Log::log("[sitemap] Sitemap of path '" . $_SERVER['DOCUMENT_ROOT'] . "/" . $fileName . "' was outdated by " . time() - $sitemap["lastmod"] - $ttl . " seconds and will be updated");
            }

            $sitemap = [
                "fileLocation" => $_SERVER['DOCUMENT_ROOT'] . "/" . $fileName,
                "urlLocation" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . "/" . $fileName,
                "lastmod" => time(),
                "updated" => true
            ];

            $this->addSitemap($sitemap);
            $this->saveSitemapFile($sitemap, $generator->getUrls());

            return true;
        } else {
            return false;
        }
    }

    private function addSitemap(array $newSitemap): void {
        foreach ($this->sitemaps as $i => $sitemap) {
            if ($sitemap["fileLocation"] == $newSitemap["fileLocation"]) {
                unset($this->sitemaps[$i]);
                break;
            }
        }
        $this->sitemaps[] = $newSitemap;
        $this->sitemapIndexUpdated = true;
    }

    private function getSitemap(string $fileLocation): ?array {
        foreach ($this->sitemaps as $sitemap) {
            if ($sitemap["fileLocation"] == $fileLocation) {
                return $sitemap;
            }
        }
        return null;
    }

    private function saveSitemapIndexFile(): void {
        if (file_exists($this->sitemapIndexLocation)) {
            unlink($this->sitemapIndexLocation);
        }

        $sitemapIndexXmlElement = simplexml_load_string(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="https://www.sitemaps.org/schemas/sitemap/siteindex.xsd"/>
EOF
        );

        foreach ($this->sitemaps as $sitemap) {
            $urlXmlElement = $sitemapIndexXmlElement->addChild("sitemap");
            $urlXmlElement->addChild("loc", $sitemap["urlLocation"]);
            $urlXmlElement->addChild("lastmod", date("Y-m-d\TH:i:sP", $sitemap["lastmod"]));
        }

        $sitemapIndexXmlElement->asXML($this->sitemapIndexLocation);
    }

    private function saveSitemapFile($sitemap, $urls): void {
        if (file_exists($sitemap["fileLocation"])) {
            unlink($sitemap["fileLocation"]);
        }

        $sitemapXmlElement = simplexml_load_string(<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/sitemap.xsd"/>
EOT
        );

        foreach ($urls as $url) {
            $urlXmlElement = $sitemapXmlElement->addChild("url");
            $urlXmlElement->addChild("loc", $url["loc"]);
            $urlXmlElement->addChild("lastmod", date("Y-m-d\TH:i:sP", $url["lastmod"]));
        }

        $sitemapXmlElement->asXML($sitemap["fileLocation"]);
    }
}