<?php
require ROOT . "../secrets.php";
require ROOT . "autoloaded/Autoloader.class.php";

use App\Checker\SitemapChecker;
use App\Checker\ThumbnailChecker;

########################################################################################################################
# Vérification du protocole (les deux fonctionnent, mais on veut forcer le passage par HTTPS)                           #
########################################################################################################################
if ($_SERVER["HTTPS"] != "on") {
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
# Activation des checkers                                                                                              #
########################################################################################################################
SitemapChecker::new(ROOT)->check();
ThumbnailChecker::new(ROOT . "journaux/")->check();

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
