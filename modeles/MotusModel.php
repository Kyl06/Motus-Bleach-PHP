<?php

class MotusModel {
    public function getMot($niveau) {
        // lecture et décodage du JSON
        $json = file_get_contents(__DIR__ . '/../data/mots.json');
        $data = json_decode($json, true);
        $liste = isset($data[$niveau]) ? $data[$niveau] : [];

        // sélection aléatoire dans la liste du niveau
        $choix = $liste[array_rand($liste)];

        // on normalise le mot en majuscules sans espaces superflus
        $choix['mot'] = strtoupper(trim($choix['mot']));
        return $choix;
    }

    public function getScores() {
        // si aucun fichier de scores on renvoie un tableau vide
        if (!file_exists(__DIR__ . '/../data/scores.json')) {
            return [];
        }

        // lecture et décodage du fichier JSON des scores
        $json = file_get_contents(__DIR__ . '/../data/scores.json');
        $scores = json_decode($json, true);

        // le return est toujours un tableau
        return is_array($scores) ? $scores : [];
    }

    public function saveScore($pseudo, $score, $niveau) {
        // récupère scores existants
        $scores = $this->getScores();
        // ajoute le nouveau score
        $scores[] = [
            'pseudo' => htmlspecialchars($pseudo),
            'score' => (int) $score,
            'niveau' => $niveau,
            'date' => date('d/m/Y H:i')
        ];

        // tri du meilleur score au plus faible
        usort($scores, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // conserve les 20 meilleurs scores
        $scores = array_slice($scores, 0, 20);

        // réecrit JSON des scores
        file_put_contents(__DIR__ . '/../data/scores.json', json_encode($scores));
    }

    public function getTousLesMots() {

        // lecture et décodage du dictionnaire
        $json = file_get_contents(__DIR__ . '/../data/mots.json');
        $data = json_decode($json, true);

        // on garantit que le return est un tableau
        return is_array($data) ? $data : [];
    }
}
