<?php
const ROOT = "../";
require ROOT . "controleur.php";

use App\Request\ApiSallesRequest;

// Liste des gabarits
const GABARIT_BARRE_DE_RECHERCHE = "gabaritBarreDeRecherche.php"; // Défaut
const GABARIT_AUCUN_RESULTAT = "gabaritAucunResultat.php";
const GABARIT_CHOIX_DE_SALLE = "gabaritChoixDeSalle.php";

// Gabarit par défaut
$gabarit = GABARIT_BARRE_DE_RECHERCHE;

if (isset($_GET["nom"])) {
    // Récupération des salles
    $salles = ApiSallesRequest::new()->requestByName($_GET["nom"]);

    if (empty($salles)) {
        $gabarit = GABARIT_AUCUN_RESULTAT;
    } else {
        $gabarit = GABARIT_CHOIX_DE_SALLE;
    }
}

// Appel du cadre
const TITLE = "Trouver une salle";
define("GABARIT", $gabarit);
const STYLES = ["buttons"];
const SCRIPTS = ["formulaire"];
require ROOT . "cadre.php";
