<?php
namespace App;

use App\Request\SqlRequest;
use Exception;

class Log {
    // Attribut statique
    private const SCRIPT = "INSERT INTO general_log (service, message) VALUES (1, ?);";

    // Méthode statique
    public static function log(string $message): void {
        try {
            $repertory = explode(DIRECTORY_SEPARATOR, realpath(ROOT));
            SqlRequest::new(Log::SCRIPT)->execute(["[" . strtoupper(end($repertory)) . "]$message"]);
        } catch (Exception $exception) {
            Message::add($exception);
        }
    }
}
