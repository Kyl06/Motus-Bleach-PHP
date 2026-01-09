<?php

class MotusModel
{
    public function getMot($niveau)
    {
        // si le fichier de mots n'existe pas on renvoie un mot d'erreur
        if (!file_exists(__DIR__ . '/../data/mots.json')) {
            return ['mot' => 'ERREUR', 'type' => 'INCONNU'];
        }

        // lecture et décodage du fichier JSON des mots
        $json  = file_get_contents(__DIR__ . '/../data/mots.json');
        $data  = json_decode($json, true);
        $liste = isset($data[$niveau]) ? $data[$niveau] : [];

        // si aucun mot pour ce niveau on renvoie un fallback
        if (empty($liste)) {
            return ['mot' => 'ICHIGO', 'type' => 'PERSONNAGE'];
        }

        // sélection d'un mot aléatoire dans la liste du niveau
        $choix = $liste[array_rand($liste)];

        // sécurité si jamais un ancien format contient encore des strings simples
        if (is_string($choix)) {
            return ['mot' => strtoupper(trim($choix)), 'type' => 'INCONNU'];
        }

        // on normalise le mot en majuscules sans espaces superflus
        $choix['mot'] = strtoupper(trim($choix['mot']));
        return $choix;
    }

    public function getScores()
    {
        // si aucun fichier de scores on renvoie un tableau vide
        if (!file_exists(__DIR__ . '/../data/scores.json')) {
            return [];
        }

        // lecture et décodage du fichier JSON des scores
        $json   = file_get_contents(__DIR__ . '/../data/scores.json');
        $scores = json_decode($json, true);

        // on garantit que le retour est toujours un tableau
        return is_array($scores) ? $scores : [];
    }

    public function saveScore($pseudo, $score, $niveau)
    {
        // on récupère les scores existants
        $scores   = $this->getScores();
        // on ajoute le nouveau score
        $scores[] = [
            'pseudo' => htmlspecialchars($pseudo),
            'score'  => (int) $score,
            'niveau' => $niveau,
            'date'   => date('d/m/Y H:i')
        ];

        // tri du meilleur score au plus faible
        usort($scores, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // on conserve uniquement les 10 meilleurs scores
        $scores = array_slice($scores, 0, 10);

        // on réécrit le fichier JSON des scores
        file_put_contents(__DIR__ . '/../data/scores.json', json_encode($scores));
    }

    public function getBestScore()
    {
        // récupère tous les scores
        $scores = $this->getScores();
        // si aucun score on renvoie null
        if (empty($scores)) {
            return null;
        }

        // on part du premier score comme meilleur
        $best = $scores[0]['score'];

        // on cherche le maximum à la main
        foreach ($scores as $s) {
            if ($s['score'] > $best) {
                $best = $s['score'];
            }
        }

        return $best;
    }

    public function getNbParties()
    {
        // nombre total de scores enregistrés = nombre de parties
        $scores = $this->getScores();
        return count($scores);
    }

    public function getTousLesMots()
    {
        // si le fichier de mots n'existe pas on renvoie un tableau vide
        if (!file_exists(__DIR__ . '/../data/mots.json')) {
            return [];
        }

        // lecture et décodage du dictionnaire complet
        $json = file_get_contents(__DIR__ . '/../data/mots.json');
        $data = json_decode($json, true);

        // on garantit que le retour est un tableau
        return is_array($data) ? $data : [];
    }
}
