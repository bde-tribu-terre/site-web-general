<?php
const ROOT = './';
require_once(ROOT . 'controleur.php');

use App\Request\SqlRequest;

// Récupération des journaux
$journaux = SqlRequest::new(<<< EOF
SELECT
    idJournal AS id,
    titreJournal AS titre,
    dateJournal AS date,
    pdfJournal AS pdf
FROM
    website_journaux
ORDER BY
    dateJournal
    DESC
LIMIT 3;
EOF
)->execute();

// Appel du cadre
const TITLE = 'Accueil';
const GABARIT = 'gabarit.php';
require_once(ROOT . 'cadre.php');
