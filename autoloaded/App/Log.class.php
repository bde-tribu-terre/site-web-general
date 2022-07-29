<?php
namespace App;

use App\Request\SqlRequest;
use Exception;

class Log {
    // Attribut statique
    private static string $script = "INSERT INTO general_log (service, message) VALUES (1, :message);";

    // Méthode statique
    public static function log(string $message) {
        try {
            $pdo = getConnect();
            $prepare = $pdo->prepare(SqlLog::$script);
            $prepare->bindValue(":message", $message);
            $prepare->execute();
            $prepare->closeCursor();
        } catch (Exception $exception) {
            ajouterMessage(600, $exception);
        }
    }
}