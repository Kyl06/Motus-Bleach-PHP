<?php
require_once __DIR__ . '/../config/db.php';
class MotusModel
{
    public function getMot(string $niveau): array
    {
        $pdo = getPDO();

        // Récupère tous les mots pour ce niveau
        $sql = "SELECT mot, type_mot
            FROM mots
            WHERE niveau = :niveau";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':niveau' => $niveau]);
        $mots = $stmt->fetchAll();

        // Choix aléatoire dans les résultats
        $choix = $mots[array_rand($mots)];

        // Normalisation en majuscules / trimming
        $mot = strtoupper(trim($choix['mot']));
        $type = isset($choix['type_mot']) ? $choix['type_mot'] : 'INCONNU';

        return [
            'mot' => $mot,
            'type' => $type
        ];
    }

    public function getScores(): array
    {
        $pdo = getPDO();

        $sql = "SELECT pseudo, score, niveau, date_partie
                FROM scores
                ORDER BY score DESC, date_partie DESC
                LIMIT 20";

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();

        // On renvoie toujours un tableau
        return is_array($rows) ? $rows : [];

    }

    public function saveScore(string $pseudo, int $score, string $niveau): void
    {
        $pdo = getPDO();

        $sql = "INSERT INTO scores (pseudo, score, niveau, date_partie)
                VALUES (:pseudo, :score, :niveau, :date_partie)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':score' => $score,
            ':niveau' => $niveau,
            ':date_partie' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getTousLesMots(): array
    {
        $pdo = getPDO();

        $sql = "SELECT mot, type_mot, niveau
            FROM mots
            ORDER BY niveau, mot";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();

        $resultat = [
            'debutant' => [],
            'moyen' => [],
            'expert' => []
        ];

        foreach ($rows as $row) {
            $niv = $row['niveau'];
            if (!isset($resultat[$niv])) {
                $resultat[$niv] = [];
            }
            $resultat[$niv][] = [
                'mot' => strtoupper(trim($row['mot'])),
                'type' => $row['type_mot']
            ];
        }

        return $resultat;
    }

}
