<?php

namespace App\Checker\Sitemap;

class Sitemap {
    private string $path;
    private array $urls = array();

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function addUrl(string $url): void {
        $this->urls[] = $url;
    }

    public function saveFile(): void {
        $sitemapXmlElement = simplexml_load_string(<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>
EOT
        );

        foreach ($this->urls as $url) {
            $urlXmlElement = $sitemapXmlElement->addChild("url");
            $urlXmlElement->addChild("loc", "https://" . $_SERVER["HTTP_HOST"] . "/" . $url);
            $urlXmlElement->addChild("lastmod", date("Y-m-d\TH:i:sP"));
        }

        $sitemapXmlElement->asXML($this->path);
    }
}