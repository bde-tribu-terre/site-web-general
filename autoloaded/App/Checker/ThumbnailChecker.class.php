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
        $modif = false;

        $thumbnailDirectory = $this->pathToLocation . "thumbnails/";

        if (!file_exists($thumbnailDirectory) || is_dir($thumbnailDirectory)) {
            mkdir($thumbnailDirectory);
        }

        foreach (scandir($this->pathToLocation) as $file) {
            $thumbnailFileName = $thumbnailDirectory . $file . ".webp";

            if (str_ends_with($file, ".pdf") && !file_exists($thumbnailFileName)) {

                $transloaditResult = TransloaditRequest::new([$this->pathToLocation . $file])->execute(TransloaditRequest::PDF_THUMBNAIL);

                $success = file_put_contents($thumbnailFileName, file_get_contents($transloaditResult[0]));

                if ($success) {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . "$this->pathToLocation$file' not found: created thumbnail on Transloadit and successfully downloaded at '$thumbnailFileName'");
                } else {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . "$this->pathToLocation$file' not found: created thumbnail on Transloadit but unsuccessfully downloaded at '$thumbnailFileName'");
                }

                $modif = true;
            }
        }

        return $modif;
    }
}
