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
        if (!file_exists($thumbnailDirectory) || is_dir($thumbnailDirectory)) {
            mkdir($thumbnailDirectory);
        }

        // Tableau contenant les miniatures à générer.
        $missThnPdfFiles = array();

        // Pour chaque fichier dans le dossier des PDF...
        foreach (scandir($this->pathToLocation) as $file) {
            // Si le fichier se termine par .pdf et que le fichier de miniature n'existe pas déjà.
            if (str_ends_with($file, ".pdf") && !file_exists($thumbnailDirectory . $file . ".webp")) {

                // Ajout des fichiers dans le tableau.
                $missThnPdfFiles[] = $file;

            }
        }

        if (!empty($missThnPdfFiles)) {
            // Execution de la requête sur l'API Transloadit.
            $transloaditResults = TransloaditRequest::new(array_map(fn($file): string => $this->pathToLocation . $file, $missThnPdfFiles))->execute(TransloaditRequest::PDF_THUMBNAIL);

            // Pour chaque résultat de requête manquante...
            foreach ($transloaditResults as $index => $transloaditResult) {

                $success = file_put_contents(
                    $thumbnailDirectory . $missThnPdfFiles[$index] . ".webp",
                    file_get_contents($transloaditResult)
                );

                // Log.
                if ($success) {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$this->pathToLocation$missThnPdfFiles[$index]' not found at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$thumbnailDirectory$missThnPdfFiles[$index].webp': created thumbnail on Transloadit and successfully downloaded");

                    // Quelque chose a été modifié.
                    $modif = true;
                } else {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$this->pathToLocation$missThnPdfFiles[$index]' not found at URI 'https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "$thumbnailDirectory$missThnPdfFiles[$index].webp': created thumbnail on Transloadit but unsuccessfully downloaded");
                }
            }
        }

        return $modif;
    }
}
