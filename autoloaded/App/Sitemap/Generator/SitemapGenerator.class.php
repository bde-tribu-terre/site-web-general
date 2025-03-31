<?php

namespace App\Sitemap\Generator;

/**
 * Générateur de sitemap, qui génère des couples (URL, dernière modification) référençables dans une sitemap.
 */
interface SitemapGenerator {
    /**
     * Génère des couples (URL, dernière modification) référençables dans une sitemap selon un procédé dépendant de la
     * classe implémentant l'interface.
     * @return array Tableau de couples (URL, dernière modification en timestamp), de la forme array[string, int].
     */
    function getUrls(): array;
}