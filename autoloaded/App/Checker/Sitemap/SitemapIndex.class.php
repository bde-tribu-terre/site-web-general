<?php

namespace App\Checker\Sitemap;

use App\Log;
use Throwable;

class SitemapIndex {
    private static bool|SitemapIndex $sitemapIndex = false;

    public static function getSitemapIndex(string $pathToRoot): SitemapIndex {
        if (!SitemapIndex::$sitemapIndex) {
            SitemapIndex::$sitemapIndex = new SitemapIndex($pathToRoot);
        }
        return SitemapIndex::$sitemapIndex;
    }

    private string $pathToRoot;
    private array $sitemaps = array(); // [url => lastmod]

    private function __construct(string $pathToRoot) {
        $this->pathToRoot = $pathToRoot;

        if (file_exists($this->pathToRoot)) {
            try {
                $sitemapIndexXmlElement = simplexml_load_file($this->pathToRoot);

                foreach ($sitemapIndexXmlElement as $sitemapXmlElement) {
                    $this->sitemaps[$sitemapXmlElement->loc] = strtotime($sitemapXmlElement->lastmod);
                }
            } catch (Throwable $e) {
                Log::log("[sitemap] Sitemaps index at path '" . $this->pathToRoot . "' exists but an exception occurred trying to read the file: " . $e->getMessage() . "; the file has been regenerated from scratch");
            }
        } else {
            Log::log("[sitemap] No sitemaps index at path '" . $this->pathToRoot . "'; the file has been regenerated from scratch");
        }
    }

    public function updateSitemap($url): void {
        $this->sitemaps[$url] = time();
    }

    public function getSitemaps(): array {
        return $this->sitemaps;
    }

    public function saveFile(): void {
        $sitemapIndexXmlElement = simplexml_load_string(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>
EOF
        );

        foreach ($this->sitemaps as $url => $lastmod) {
            $urlXmlElement = $sitemapIndexXmlElement->addChild("sitemap");
            $urlXmlElement->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . "/" . $url);
            $urlXmlElement->addChild("lastmod", date("Y-m-d\TH:i:sP", $lastmod));
        }

        $sitemapIndexXmlElement->asXML($this->pathToRoot);
    }
}