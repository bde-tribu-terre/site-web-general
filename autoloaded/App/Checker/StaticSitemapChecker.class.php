<?php

namespace App\Checker;

use App\Checker\Sitemap\Sitemap;
use App\Checker\Sitemap\SitemapIndex;
use App\Log;

class StaticSitemapChecker {
    // Constructeur statique.
    public static function new(string $pathToRoot, string $pathToSearch, int $ttlOfSitemap): StaticSitemapChecker {
        return new StaticSitemapChecker($pathToRoot, $pathToSearch, $ttlOfSitemap);
    }

    // Attributs.
    private string $pathToRoot;
    private string $pathToSearch;
    private int $ttlOfSitemap; // 7776000 = 90 jours.

    // Constructeur.
    public function __construct(string $pathToRoot, string $pathToSearch, $ttlOfSitemap) {
        $this->pathToRoot = $pathToRoot;
        $this->pathToSearch = $pathToSearch;
        $this->ttlOfSitemap = $ttlOfSitemap;
    }

    public function check(): bool {
        $sitemapIndex = SitemapIndex::getSitemapIndex($this->pathToRoot . "sitemapindex.xml");

        // Vérification de la date de dernière modification pour MÀJ.
        $outOfDateSeconds = -1;
        foreach ($sitemapIndex->getSitemaps() as $sitemapUrl => $sitemapLastmod) {
            $outOfDateSeconds = time() - $sitemapLastmod;
            if ($sitemapUrl == $this->pathToSearch . "sitemap-static.xml" && $this->ttlOfSitemap < $outOfDateSeconds)
                return false;
        }

        $sitemap = new Sitemap($this->pathToSearch . "sitemap-static.xml");
        foreach ($this->searchUrlList() as $url)
            $sitemap->addUrl($this->simplifyUrl($url));

        $sitemapIndex->updateSitemap($this->simplifyUrl($this->pathToSearch . "sitemap-static.xml"));

        $sitemap->saveFile();
        $sitemapIndex->saveFile();

        if ($outOfDateSeconds == -1)
            Log::log("[sitemap] Sitemap of path '$this->pathToSearch' was not indexed in the sitemap index and thus has been added");
        else
            Log::log("[sitemap] Sitemap of path '$this->pathToSearch' was outdated by $outOfDateSeconds seconds and has been updated");

        return true;
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
            elseif ($dir != ".") {
                $path[$cursor] = $dir;
                $cursor++;
            }
        }

        $pathString = "";
        for ($i = 0; $i < $cursor; $i++) {
            $pathString .= $path[$i];
            if ($i < $cursor - 1)
                $pathString .= "/";
        }

        return $pathString;
    }
}
