<?php
########################################################################################################################
# Vérification du protocole (les deux fonctionnent, mais on veut forcer le passage par HTTPS)                          #
########################################################################################################################
if (!isset($_SERVER['HTTPS']) && preg_match("/^.*\.\d*$/", $_SERVER["HTTP_HOST"]) && $_SERVER["SERVER_NAME"] != "localhost") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

########################################################################################################################
# Chargement des variables d'environnement                                                                             #
########################################################################################################################
require $_SERVER['DOCUMENT_ROOT'] . "/../secrets.php";

########################################################################################################################
# Chargement de l'autoloader                                                                                           #
########################################################################################################################
require $_SERVER['DOCUMENT_ROOT'] . "/autoloaded/Autoloader.class.php";

########################################################################################################################
# Activation des checkers                                                                                              #
########################################################################################################################
App\Sitemap\SitemapManager::get()->checkAndUpdate("sitemap-static.xml", 7776000, new App\Sitemap\Generator\SitemapGeneratorStatic(""));
App\Checker\ThumbnailChecker::new($_SERVER['DOCUMENT_ROOT'] . "/association/statuts/")->check();

########################################################################################################################
# Version du site                                                                                                      #
########################################################################################################################
define('VERSION_SITE', file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/version.txt'));
