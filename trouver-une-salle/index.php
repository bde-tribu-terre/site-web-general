<?php // TODO: Fix trouveur de salles
const ROOT = '../';
require_once(ROOT . 'controleur.php');
if (isset($_GET['nom'])) {
    try {
        if (
            !empty($_GET['nom'])
        ) {
            // Recherche des salles
            MdlRechercherSalle($_GET['nom']);

            // S'il y a des résultats...
            if ($GLOBALS['retoursModele']['salles']) {
                define('NOMBRE', count($GLOBALS['retoursModele']['salles']));

                if (NOMBRE > 1) {
                    $listeSalles = '';
                    foreach ($GLOBALS['retoursModele']['salles'] as $salle) {
                        $listeSalles .=
                            '
                            <div class="well">
                                <h4>' . $salle['nom'] . '</h4>
                                <p>Composante : ' . $salle['titreComposante'] . '</p>
                                <p>Bâtiment : ' . $salle['nomBatiment'] . '</p>
                                <p>Emplacement : ' . $salle['nomGroupe'] . '</p>
                                <p>
                                    <a
                                        href="/trouver-une-salle/?nom=' . preg_replace('/ /', '+', $salle['nom']) . '"
                                    >
                                        Voir le bâtiment sur le plan
                                    </a>
                                </p>
                            </div>
                            ';
                    }
                } else {
                    $listeSalles =
                        '
                        <div class="well">
                            <h4>' . $GLOBALS['retoursModele']['salles'][0]['nom'] . '</h4>
                            <p>Composante : ' . $GLOBALS['retoursModele']['salles'][0]['titreComposante'] . '</p>
                            <p>Bâtiment : ' . $GLOBALS['retoursModele']['salles'][0]['nomBatiment'] . '</p>
                            <p>Emplacement : ' . $GLOBALS['retoursModele']['salles'][0]['nomGroupe'] . '</p>
                        </div>
                        ';
                }
                define('SALLES', $listeSalles);
                define('CODE_COMPOSANTE', $GLOBALS['retoursModele']['salles'][0]['codeComposante']);
                define('ID_BATIMENT', $GLOBALS['retoursModele']['salles'][0]['idBatiment']);

                $gabarit = 'gabaritRecherche.php';
            }

            // S'il n'y a pas de résultat...
            else {
                ajouterMessage(604, 'Aucune salle de nom "' . $_GET['nom'] . '" n\'a été trouvée.');
            }
        } else {
            throw new Exception('Veuillez remplir tous les champs.', 400);
        }
    } catch (Exception $e) {
        ajouterMessage($e->getCode(), $e->getMessage());
        $gabarit = 'gabarit.php';
    }
} else {
    $gabarit = 'gabarit.php';
}

const TITLE = 'Trouver une salle';
define("GABARIT", $gabarit);
require_once(ROOT . 'cadre.php');
