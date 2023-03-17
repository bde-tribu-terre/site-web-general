<?php
const ROOT = "../../";
require ROOT . "controleur.php";

use App\Request\ApiSallesRequest;

if (!isset($_GET["id"])) {
    header("Location: " . "/trouver-une-salle/");
}

// Récupération de la salle
$salle = ApiSallesRequest::new()->requestById($_GET["id"]);

if (empty($salle)) {
    header("Location: " . "/trouver-une-salle/");
}

// Appel du cadre
define("TITLE", $salle->name);
const GABARIT = "gabarit.php";
require ROOT . "cadre.php";
