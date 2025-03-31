<?php
require $_SERVER['DOCUMENT_ROOT'] . "/controleur.php";

// Quels PDF utiliser ? (sans extension .pdf)
const STATUTS = "Statuts-2023-03-18";
const RI = "RI-2023-11-05";

const TITLE = "Statuts";
const GABARIT = "gabarit.php";
const STYLES = ["pdf-viewer", "buttons"];
require $_SERVER['DOCUMENT_ROOT'] . "/cadre.php";
