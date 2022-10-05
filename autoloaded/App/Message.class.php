<?php

namespace App;

class Message {
    // Attributs statiques
    private static array $messages = array();

    // Méthodes statiques
    public static function add(string $message): void {
        self::$messages[] = htmlentities($message, ENT_QUOTES, 'UTF-8');
    }

    public static function get(): array {
        return self::$messages;
    }

    public static function empty(): bool {
        return empty(self::$messages);
    }
}
