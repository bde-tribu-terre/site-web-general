<?php

class SqlSimpleRequest {
    // Constructeur statique
    public static function new(string $sqlScript): SqlSimpleRequest {
        return new SqlSimpleRequest($sqlScript);
    }

    // Attributs
    private PDO $pdo;
    private string $sqlScript;

    // Constructeur
    public function __construct(string $sqlScript) {
        try {
            $this->pdo = getConnect();
        } catch (Exception $exception) {
            ajouterMessage(600, $exception->getMessage());
        }
        $this->sqlScript = $sqlScript;
    }

    // Getteurs
    public function getSqlScript(): string {
        return $this->sqlScript;
    }

    // Méthodes
    public function execute(int $max = 0): array {
        $prepare = $this->pdo->prepare($this->sqlScript . ($max != 0 ? " LIMIT $max" : ""));

        try {
            $prepare->execute();
            $count = 0;
            foreach ($prepare->fetchAll() as $item) {
                $item["count"] = $count++;
                $results[] = (object) $item;
            }
        } catch (Exception $exception) {
            ajouterMessage(600, $exception->getMessage());
            $results = array();
        } finally {
            $prepare->closeCursor();
        }

        return $results;
    }
}