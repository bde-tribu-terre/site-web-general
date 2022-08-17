<?php
########################################################################################################################
# Vérification du protocole (les deux fonctionnent, mais on veut forcer le passage par HTTPS)                           #
########################################################################################################################
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

########################################################################################################################
# Chargement des variables d'environnement                                                                             #
########################################################################################################################
require ROOT . "../secrets.php";

########################################################################################################################
# Chargement de l'autoloader                                                                                           #
########################################################################################################################
require ROOT . "autoloaded/Autoloader.class.php";

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
# Activation des checkers                                                                                              #
########################################################################################################################
App\Checker\SitemapChecker::new(ROOT)->check();
App\Checker\ThumbnailChecker::new(ROOT . "journaux/")->check();

########################################################################################################################
# Version du site                                                                                                      #
########################################################################################################################
define('VERSION_SITE', file_get_contents(ROOT . '/version.txt'));
