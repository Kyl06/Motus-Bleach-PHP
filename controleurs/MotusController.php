<?php
require_once 'modeles/MotusModel.php';

class MotusController
{
    // Modèle qui gère les données (mots, scores, dictionnaire, etc.)
    private $model;

    public function __construct()
    {
        $this->model = new MotusModel();

        // Toujours initialiser la proposition du clavier virtuel
        if (!isset($_SESSION['proposition_clavier'])) {
            $_SESSION['proposition_clavier'] = '';
        }
    }


    // Page d'accueil avec choix du pseudo + difficulté
    public function accueil()
    {
        include 'vues/accueil.php';
    }

    // Page des règles du jeu
    public function regles()
    {
        include 'vues/regles.php';
    }

    // Page des scores (Hall of Fame)
    public function scores()
    {
        // Récupère tous les scores enregistrés
        $scores = $this->model->getScores();

        // Meilleur score global
        $bestScore = null;
        if (!empty($scores)) {
            $bestScore = max(array_column($scores, 'score'));
        }

        // Nombre total de parties jouées
        $nbParties = count($scores);

        include 'vues/scores.php';
    }


    public function jouer()
    {
        //1. DÉMARRAGE D'UNE NOUVELLE PARTIE
        if (isset($_POST['niveau']) && isset($_POST['pseudo'])) {

            // Enregistre le pseudo et le niveau choisis
            $_SESSION['pseudo'] = strtoupper(trim($_POST['pseudo']));
            $_SESSION['niveau'] = $_POST['niveau'];

            // Détermine le nombre max d'essais en fonction de la difficulté
            switch ($_SESSION['niveau']) {
                case 'moyen':
                    $_SESSION['max_essais'] = 8;
                    break;
                case 'expert':
                    $_SESSION['max_essais'] = 10;
                    break;
                case 'simple':
                default:
                    $_SESSION['max_essais'] = 6;
                    break;
            }

            // Récupère un mot adapté au niveau
            $info = $this->model->getMot($_SESSION['niveau']);

            // Initialisation complète de l'état de la partie
            $_SESSION['mot_secret'] = $info['mot'];
            $_SESSION['type_mot_secret'] = $info['type'];
            $_SESSION['essais'] = [];
            $_SESSION['victoire'] = false;
            $_SESSION['nb_essais'] = 0;// "unités" d'essais : frappes + indices
            $_SESSION['score_nul'] = false;// passe à true si dictionnaire utilisé
            $_SESSION['proposition_clavier'] = '';
            $_SESSION['clavier'] = array_fill_keys(range('A', 'Z'), 'inconnu');
            $_SESSION['indices_reveles'] = [0];// première lettre révélée
            $_SESSION['indice_type_utilise'] = false;
            $_SESSION['antiseche_active'] = false;

            // Redirection pour éviter le rechargement de formulaire
            header('Location: index.php?page=jouer');
            exit;
        }

        //2. SÉCURITÉ : AUCUN MOT EN SESSION => RETOUR ACCUEIL
        if (!isset($_SESSION['mot_secret'])) {
            header('Location: index.php');
            exit;
        }

        //3. ACTION : DICTIONNAIRE (ANTISÈCHE)
// Active le dictionnaire et annule le score final (score = 0), sans consommer d'essais
        if (isset($_GET['action']) && $_GET['action'] === 'antiseche' && !$_SESSION['victoire']) {
            $_SESSION['antiseche_active'] = true;
            $_SESSION['score_nul'] = true; // toutes les parties avec dictionnaire auront 0 point

            header('Location: index.php?page=jouer&antiseche=1');
            exit;
        }

        //4. ACTION : INDICE LETTRE
// Coût : +2 essais, donc -200 points. Ne s'active que s'il reste au moins 1 essai après.
        if (isset($_GET['action']) && $_GET['action'] === 'indice' && !$_SESSION['victoire']) {

            // Vérifie qu'on ne tombe pas à 0 essai (on veut garder au moins 1 tentative)
            if ($_SESSION['nb_essais'] + 2 < $_SESSION['max_essais']) {
                $mot = $_SESSION['mot_secret'];

                // Cherche la prochaine lettre non révélée (on commence à 1, la 0 est toujours révélée)
                for ($i = 1; $i < strlen($mot); $i++) {
                    if (!in_array($i, $_SESSION['indices_reveles'])) {
                        $_SESSION['indices_reveles'][] = $i;
                        $_SESSION['nb_essais'] += 2; // consomme 2 essais
                        break;
                    }
                }
            } else {
                // Message d'erreur affiché une fois dans la vue (flash message)
                $_SESSION['flash_error'] = "Impossible : cet indice vous laisserait sans essais.";
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        //5. ACTION : ABANDON
// Met immédiatement la partie en échec (nb_essais = max_essais)
        if (isset($_GET['action']) && $_GET['action'] === 'abandon' && !$_SESSION['victoire']) {
            $_SESSION['victoire'] = false;
            $_SESSION['nb_essais'] = $_SESSION['max_essais'];

            header('Location: index.php?page=jouer');
            exit;
        }

        //6. ACTION : INDICE NATURE DU MOT
// Coût : +3 essais (−300 points), utilisable une seule fois et seulement si 1 essai restera.
        if (isset($_GET['action']) && $_GET['action'] === 'indiceType' && !$_SESSION['victoire']) {

            // Indice non encore utilisé ET il restera au moins 1 essai après
            if (!$_SESSION['indice_type_utilise'] && $_SESSION['nb_essais'] + 3 < $_SESSION['max_essais']) {
                $_SESSION['indice_type_utilise'] = true;
                $_SESSION['nb_essais'] += 3; // consomme 3 essais
            } else {
                $_SESSION['flash_error'] = "Impossible : cet indice vous laisserait sans essais.";
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        //7. ACTION : EFFACER LA SAISIE CLAVIER VIRTUEL
        if (isset($_GET['action']) && $_GET['action'] === 'del' && !$_SESSION['victoire']) {
            $_SESSION['proposition_clavier'] = '';

            header('Location: index.php?page=jouer');
            exit;
        }

        //8. ACTION : AJOUT LETTRE VIA CLAVIER VIRTUEL
        if (isset($_GET['char']) && !$_SESSION['victoire']) {

            // Empêche de dépasser la longueur du mot secret
            if (strlen($_SESSION['proposition_clavier']) < strlen($_SESSION['mot_secret'])) {
                $lettre = strtoupper(substr($_GET['char'], 0, 1));
                $_SESSION['proposition_clavier'] .= $lettre;
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        //9. ACTION : PROPOSITION DU JOUEUR (FRAPPER)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proposition'])) {

            $prop = strtoupper(trim($_POST['proposition']));

            // On n'accepte la proposition que si elle a la bonne longueur
// et qu'il reste des essais
            if (
                strlen($prop) === strlen($_SESSION['mot_secret']) &&
                $_SESSION['nb_essais'] < $_SESSION['max_essais']
            ) {
                // Analyse de la proposition lettre par lettre
                $analyse = $this->analyserMot($prop, $_SESSION['mot_secret']);

                // Ajout de cette ligne d'essai à l'historique
                $_SESSION['essais'][] = $analyse;
                $_SESSION['nb_essais'] += 1; // chaque frappe consomme 1 essai
                $_SESSION['proposition_clavier'] = '';

                // Mise à jour des états des lettres du clavier (bien/mal placé, absent)
                foreach ($analyse as $item) {
                    $L = $item['L'];
                    $S = $item['statut'];

                    if ($S === 'bien-place') {
                        $_SESSION['clavier'][$L] = 'bien-place';
                    } elseif ($S === 'mal-place' && $_SESSION['clavier'][$L] !== 'bien-place') {
                        $_SESSION['clavier'][$L] = 'mal-place';
                    } elseif ($S === 'absent' && $_SESSION['clavier'][$L] === 'inconnu') {
                        $_SESSION['clavier'][$L] = 'absent';
                    }
                }

                // Vérifie la victoire (mot trouvé)
                if ($prop === $_SESSION['mot_secret']) {
                    $_SESSION['victoire'] = true;

                    // Score max selon la difficulté
                    switch ($_SESSION['niveau']) {
                        case 'moyen':
                            $score_max = 1200;
                            break;
                        case 'expert':
                            $score_max = 1500;
                            break;
                        case 'simple':
                        default:
                            $score_max = 1000;
                            break;
                    }

                    // Score de base : -100 points par "essai" (frappes + indices)
                    $essais = $_SESSION['nb_essais'];
                    if ($essais < 1) {
                        $essais = 1;
                    }

                    $score = $score_max - 100 * ($essais - 1);
                    if ($score < 0) {
                        $score = 0;
                    }

                    // Si le dictionnaire a été utilisé, le score est forcé à 0
                    if (!empty($_SESSION['score_nul'])) {
                        $score = 0;
                    }

                    // Sauvegarde du score
                    $this->model->saveScore($_SESSION['pseudo'], $score, $_SESSION['niveau']);
                }
            }

            // On redirige toujours après traitement du POST
            header('Location: index.php?page=jouer');
            exit;
        }

        // 10. AFFICHAGE DE LA VUE DU JEU
        include 'vues/jeu.php';
    }


    // Compare une proposition avec le mot secret et retourne un tableau
// de cases : ['L' => lettre, 'statut' => 'bien-place' | 'mal-place' | 'absent']
    private function analyserMot($proposition, $secret)
    {
        $pA = str_split($proposition);
        $sA = str_split($secret);
        $taille = count($pA);

        // Initialisation : tout est "absent" par défaut
        $res = array_fill(0, $taille, ['L' => '', 'statut' => 'absent']);
        $disp = [];

        // 1ère passe : on marque les lettres bien placées
// et on compte les lettres restantes du mot secret.
        for ($i = 0; $i < $taille; $i++) {
            $lettre = $sA[$i];
            if ($pA[$i] === $lettre) {
                $res[$i] = ['L' => $lettre, 'statut' => 'bien-place'];
            } else {
                if (!isset($disp[$lettre])) {
                    $disp[$lettre] = 0;
                }
                $disp[$lettre]++;
            }
        }

        // 2ème passe : on gère les "mal placées" en respectant les quantités
        for ($i = 0; $i < $taille; $i++) {
            if ($res[$i]['statut'] === 'bien-place') {
                continue;
            }
            $lettre = $pA[$i];
            $res[$i]['L'] = $lettre;

            if (isset($disp[$lettre]) && $disp[$lettre] > 0) {
                $res[$i]['statut'] = 'mal-place';
                $disp[$lettre]--;
            }
        }

        return $res;
    }


    // Génère un lien Google Images pour le mot (Bleach + nom)
    public function getInfoLink($mot)
    {
        return 'https://www.google.com/search?q=' . urlencode('Bleach ' . $mot) . '&tbm=isch';
    }
}
