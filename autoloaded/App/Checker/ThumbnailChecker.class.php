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
        foreach (scandir($this->pathToLocation) as $file) {
            if (str_ends_with($file, ".pdf") && !file_exists($this->pathToLocation . $file . ".jpg")) {

                $success = file_put_contents($this->pathToLocation . $file . ".jpg", file_get_contents(TransloaditRequest::new([$this->pathToLocation . $file])->execute(TransloaditRequest::PDF_THUMBNAIL)[0]));

                if ($success) {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . "$this->pathToLocation$file' not found: created thumbnail on Transloadit and successfully downloaded at '$this->pathToLocation$file.jpg'");
                } else {
                    Log::log("[thumbnail] Thumbnail image of PDF file at URI 'https://" . $_SERVER["HTTP_HOST"] . "$this->pathToLocation$file' not found: created thumbnail on Transloadit but unsuccessfully downloaded at '$this->pathToLocation$file.jpg'");
                }
            }
        }
        return false;
    }
}
