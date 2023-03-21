<?php

namespace App\Checker;

use App\Log;
use App\Request\TransloaditRequest;

class ThumbnailChecker {
    // Constructeur statique.
    public static function new(string $pathToLocation): ThumbnailChecker {
        return new ThumbnailChecker($pathToLocation);
    }

    // Attributs.
    private string $pathToLocation;

    // Constructeur.
    public function __construct(string $pathToLocation) {
        $this->pathToLocation = $pathToLocation;
    }

    // Méthodes.
    public function check(): bool {
        // Variable qui va savoir si quelque chose a été modifié ou non.
        $modif = false;

        // Nom du dossier recueillant les miniatures.
        $thumbnailDirectory = $this->pathToLocation . "thumbnails/";

        // Si ledit dossier n'existe pas, le créer.
        if (!file_exists($thumbnailDirectory) || !is_dir($thumbnailDirectory)) {
            mkdir($thumbnailDirectory);
        }

        // Tableau contenant les miniatures à générer.
        $missThnPdfFiles = array();

        // Pour chaque fichier dans le dossier des PDF...
        foreach (scandir($this->pathToLocation) as $file) {
            // Si le fichier se termine par .pdf et que le fichier de miniature n'existe pas déjà.
            if (str_ends_with($file, ".pdf") && (
                !file_exists($thumbnailDirectory . $file . ".jpg") ||
                !file_exists($thumbnailDirectory . $file . ".webp")
                )) {

                // Ajout des fichiers dans le tableau.
                $missThnPdfFiles[] = $file;

            }
        }

        if (!empty($missThnPdfFiles)) {
            // Execution de la requête sur l'API Transloadit.
            $transloaditResults = TransloaditRequest::new(array_map(fn($file): string => $this->pathToLocation . $file, $missThnPdfFiles))->executeTemplate(TransloaditRequest::PDF_THUMBNAIL);

            // Pour chaque résultat de requête manquante...
            foreach ($transloaditResults as $resultsOfStep) {
                foreach ($resultsOfStep as $result) {

                    $success = file_put_contents(
                        $thumbnailDirectory . $result->basename . ".pdf." . $result->ext,
                        file_get_contents($result->ssl_url)
                    );

                    // Log.
                    if ($success) {
                        Log::log("[thumbnail] Thumbnails images of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$result->basename' not found in directory of URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$thumbnailDirectory': created thumbnail of extension '$result->ext' on Transloadit and successfully downloaded");

                        // Quelque chose a été modifié.
                        $modif = true;
                    } else {
                        Log::log("[thumbnail] Thumbnails images of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$result->basename' not found in directory of URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$thumbnailDirectory': created thumbnail of extension '$result->ext' on Transloadit but unsuccessfully downloaded");
                    }
                }
            }
        }

        return $modif;
    }
}
