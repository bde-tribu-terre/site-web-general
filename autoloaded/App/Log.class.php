<?php
namespace App;

use App\Request\SqlRequest;
use Exception;

class Log {
    // Attribut statique
    private static string $script = "INSERT INTO general_log (service, message) VALUES (1, ?);";

    // Méthode statique
    public static function log(string $message): void {
        try {
            $repertory = explode(DIRECTORY_SEPARATOR, realpath(ROOT));
            SqlRequest::new(Log::$script)->execute(["[" . strtoupper(end($repertory)) . "]$message"]);
        } catch (Exception $exception) {
            ajouterMessage(600, $exception);
        }
    }
}
