<?php
namespace App;

use App\Request\SqlRequest;
use Exception;

class Log {
    // Attribut statique
    private static string $script = "INSERT INTO general_log (service, message) VALUES (1, ?);";

    // Méthode statique
    public static function log(string $message) {
        try {
            SqlRequest::new(Log::$script)->execute([$message]);
        } catch (Exception $exception) {
            ajouterMessage(600, $exception);
        }
    }
}
