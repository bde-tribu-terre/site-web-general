<?php

namespace App\Sitemap\Generator;

use App\Checker\Sitemap\Sitemap;

/**
 * Générateur de sitemap statique, qui détecte les pages Web statiques via les fichiers 'index.php' dans le système de
 * fichiers depuis une racine donnée.
 */
class SitemapGeneratorStatic implements SitemapGenerator {
    private string $pathToSearch;

    /**
     * Construit un générateur de sitemap statique selon un emplacement à partir duquel rechercher récursivement.
     * @param string $pathToSearch Chemin vers l'emplacement servant de racine à la recherche récursive.
     */
    public function __construct(string $pathToSearch) {
        $this->pathToSearch = $pathToSearch;
    }

    /**
     * Génère des couples (URL, dernière modification) référençables dans une sitemap en recherchant récursivement dans
     * le système de fichiers depuis un emplacement fourni à la construction, afin de détecter les pages Web fixes via
     * les fichiers 'index.php'.
     * @return array Tableau de couples (URL, dernière modification en timestamp), de la forme array[string, int].
     */
    public function getUrls(): array {
        return array_map(fn ($url) => ["loc" => $url, "lastmod" => time()], $this->listUrls());
    }

    private function listUrls(): array {
        $filesList = [$_SERVER['DOCUMENT_ROOT'] . $this->pathToSearch];

        $i = 0;
        while (isset($filesList[$i])) {
            if (is_dir($filesList[$i])) {
                $filesList = array_merge($filesList, array_map(
                    fn($fileName) => $filesList[$i] . "/" . $fileName,
                    array_filter(scandir($filesList[$i]), fn ($fileName) => !str_starts_with($fileName, "."))
                ));
            }
            $i++;
        }

        return array_map(
            fn ($fileLocation) => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . substr($fileLocation, strlen($_SERVER['DOCUMENT_ROOT']), -9),
            array_filter($filesList, fn ($fileName) => str_ends_with($fileName, "index.php")));
    }

    private function searchUrlList(): array {
        $urlList = array();

        // Recherche en profondeur dans un arbre.
        $stack = [$this->pathToSearch];
        while (!empty($stack)) {
            $vertice = array_pop($stack);
            // Pour chaque fichier/répertoire contenu dans le répertoire sauf ".." et "."...
            foreach (array_diff(scandir($vertice), array('..', '.')) as $wertice) {
                // Si c'est "index.php", alors le répertoire courant ($vertice) est une page.
                if ($wertice == "index.php") {
                    $urlList[] = $vertice;
                } elseif (is_dir($vertice . $wertice . "/")) {
                    $stack[] = $vertice . $wertice . "/";
                }
            }
        }

        return $urlList;
    }

    private function simplifyUrl(string $url): string {
        $path = array();
        $cursor = 0;
        foreach (explode("/", $url) as $dir) {
            if ($dir == "..")
                $cursor--;
            elseif ($dir == ".")
                continue;
            else {
                $path[$cursor] = $dir;
                $cursor++;
            }
        }

        $pathString = "";
        for ($i = 0; $i < $cursor; $i++) {
            if ($i != 0)
                $pathString .= "/";
            $pathString .= $path[$i];
        }

        return $pathString;
    }
}
