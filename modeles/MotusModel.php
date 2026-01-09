<?php

class MotusModel
{
    public function getMot($niveau)
    {
        if (!file_exists(__DIR__ . '/../data/mots.json')) {
            return ['mot' => 'ERREUR', 'type' => 'INCONNU'];
        }

        $json  = file_get_contents(__DIR__ . '/../data/mots.json');
        $data  = json_decode($json, true);
        $liste = isset($data[$niveau]) ? $data[$niveau] : [];

        if (empty($liste)) {
            return ['mot' => 'ICHIGO', 'type' => 'PERSONNAGE'];
        }

        $choix = $liste[array_rand($liste)];

        // sécurité si jamais il reste des simples strings
        if (is_string($choix)) {
            return ['mot' => strtoupper(trim($choix)), 'type' => 'INCONNU'];
        }

        $choix['mot'] = strtoupper(trim($choix['mot']));
        return $choix;
    }

    public function getScores()
    {
        if (!file_exists(__DIR__ . '/../data/scores.json')) {
            return [];
        }
        $json   = file_get_contents(__DIR__ . '/../data/scores.json');
        $scores = json_decode($json, true);
        return is_array($scores) ? $scores : [];
    }

    public function saveScore($pseudo, $score, $niveau)
    {
        $scores   = $this->getScores();
        $scores[] = [
            'pseudo' => htmlspecialchars($pseudo),
            'score'  => (int) $score,
            'niveau' => $niveau,
            'date'   => date('d/m/Y H:i')
        ];

        // tri du meilleur au pire
        usort($scores, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // on garde uniquement les 10 meilleurs
        $scores = array_slice($scores, 0, 10);

        file_put_contents(__DIR__ . '/../data/scores.json', json_encode($scores));
    }

    public function getBestScore()
    {
        $scores = $this->getScores();
        if (empty($scores)) {
            return null;
        }
        $best = $scores[0]['score'];
        foreach ($scores as $s) {
            if ($s['score'] > $best) {
                $best = $s['score'];
            }
        }
        return $best;
    }

    public function getNbParties()
    {
        $scores = $this->getScores();
        return count($scores);
    }

    public function getTousLesMots()
    {
        if (!file_exists(__DIR__ . '/../data/mots.json')) {
            return [];
        }

        $json = file_get_contents(__DIR__ . '/../data/mots.json');
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
}
