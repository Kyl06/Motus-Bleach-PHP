<?php
require_once 'modeles/MotusModel.php';

class MotusController
{
    // Modèle
    private $model;

    public function __construct()
    {
        $this->model = new MotusModel();

        // propos du clavier virtuel
        if (!isset($_SESSION['pclavier'])) {
            $_SESSION['pclavier'] = '';
        }
    }

    // Page d'accueil
    public function accueil()
    {
        include 'vues/accueil.php';
    }

    // Page des règles
    public function regles()
    {
        include 'vues/regles.php';
    }

    // Page des scores
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

        // Protection silencieuse : si on arrive ici sans avoir démarré de partie
        if (
            !isset($_SESSION['mot_secret']) &&
            $_SERVER['REQUEST_METHOD'] !== 'POST' // autorise seulement la création de partie par POST
        ) {
            // renvoie vers l'accueil
            header('Location: index.php');
            exit;
        }

        // Nouvelle partie
        if (isset($_POST['niveau']) && isset($_POST['pseudo'])) {

            // Enregistre pseudo et niveau choisis
            $_SESSION['pseudo'] = strtoupper(trim($_POST['pseudo']));
            $_SESSION['niveau'] = $_POST['niveau'];

            // Nombre max d'essais en fonction de difficulté
            switch ($_SESSION['niveau']) {
                case 'moyen':
                    $_SESSION['max_essais'] = 8;
                    break;
                case 'expert':
                    $_SESSION['max_essais'] = 10;
                    break;
                default:
                case 'simple':
                    $_SESSION['max_essais'] = 6;
                    break;
            }

            // mot seloon niveau
            $info = $this->model->getMot($_SESSION['niveau']);

            // Initialisation état de la partie
            $_SESSION['mot_secret'] = $info['mot'];
            $_SESSION['typemot'] = $info['type'];
            $_SESSION['essais'] = [];
            $_SESSION['victoire'] = false;
            $_SESSION['nb_essais'] = 0;
            $_SESSION['score_nul'] = false;
            $_SESSION['pclavier'] = '';
            $_SESSION['clavier'] = array_fill_keys(range('A', 'Z'), 'inconnu');
            $_SESSION['indlettre'] = [0];
            $_SESSION['indtype'] = false;
            $_SESSION['antiseche'] = false;

            // Redirection pour éviter le rechargement de formulaire
            header('Location: index.php?page=jouer');
            exit;
        }

        // INDICE DICTIONNAIRE
        // Active le dictionnaire et annule le score final (score = 0), sans consommer d'essais
        if (isset($_GET['action']) && $_GET['action'] === 'antiseche' && !$_SESSION['victoire']) {
            $_SESSION['antiseche'] = true;
            $_SESSION['score_nul'] = true;

            header('Location: index.php?page=jouer&antiseche=1');
            exit;
        }

        // INDICE LETTRE
        // Coût : +2 essais, donc -200 points. Pas possible si reste qu'un essai.
        if (isset($_GET['action']) && $_GET['action'] === 'indice' && !$_SESSION['victoire']) {

            // Vérifie qu'on ne tombe pas à 0 essai
            if ($_SESSION['nb_essais'] + 2 < $_SESSION['max_essais']) {
                $mot = $_SESSION['mot_secret'];

                // Cherche la prochaine lettre non révélée
                for ($i = 1; $i < strlen($mot); $i++) {
                    if (!in_array($i, $_SESSION['indlettre'])) {
                        $_SESSION['indlettre'][] = $i;
                        $_SESSION['nb_essais'] += 2;
                        break;
                    }
                }
            } else {
                $_SESSION['flash_error'] = "Impossible : cet indice vous laisserait sans essais.";
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        // ABANDON
        // Met la partie en échec
        if (isset($_GET['action']) && $_GET['action'] === 'abandon' && !$_SESSION['victoire']) {
            $_SESSION['victoire'] = false;
            $_SESSION['nb_essais'] = $_SESSION['max_essais'];

            header('Location: index.php?page=jouer');
            exit;
        }

        // INDICE NATURE DU MOT
        // Coût : +3 essais (−300 points), utilisable une seule fois et seulement si 1 ou 2 essai restant.
        if (isset($_GET['action']) && $_GET['action'] === 'indiceType' && !$_SESSION['victoire']) {

            if (!$_SESSION['indtype'] && $_SESSION['nb_essais'] + 3 < $_SESSION['max_essais']) {
                $_SESSION['indtype'] = true;
                $_SESSION['nb_essais'] += 3;
            } else {
                $_SESSION['flash_error'] = "Impossible : cet indice vous laisserait sans essais.";
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        // EFFACER LA SAISIE
        if (isset($_GET['action']) && $_GET['action'] === 'del' && !$_SESSION['victoire']) {
            $_SESSION['pclavier'] = '';

            header('Location: index.php?page=jouer');
            exit;
        }

        // AJOUT LETTRE VIA CLAVIER VIRTUEL
        if (isset($_GET['char']) && !$_SESSION['victoire']) {

            // Empêche de dépasser la longueur du mot secret
            if (strlen($_SESSION['pclavier']) < strlen($_SESSION['mot_secret'])) {
                $lettre = strtoupper(substr($_GET['char'], 0, 1));
                $_SESSION['pclavier'] .= $lettre;
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        // propos DU JOUEUR
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['propos'])) {

            $prop = strtoupper(trim($_POST['propos']));

            // accepte la propos que si elle a la bonne longueur et qu'il reste des essais
            if (
                strlen($prop) === strlen($_SESSION['mot_secret']) &&
                $_SESSION['nb_essais'] < $_SESSION['max_essais']
            ) {
                // Analyse de la propos lettre par lettre
                $analyse = $this->analyserMot($prop, $_SESSION['mot_secret']);

                // Ajout de cette ligne d'essai à l'historique
                $_SESSION['essais'][] = $analyse;
                $_SESSION['nb_essais'] += 1;
                $_SESSION['pclavier'] = '';

                // Mise à jour des états des lettres du clavier
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

                // Vérifie la victoire
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
                        default:
                        case 'simple':
                            $score_max = 1000;
                            break;
                    }

                    // -100 points par essai
                    $essais = $_SESSION['nb_essais'];
                    if ($essais < 1) {
                        $essais = 1;
                    }

                    $score = $score_max - 100 * ($essais - 1);
                    if ($score < 0) {
                        $score = 0;
                    }

                    // Si dictionnaire utilisé, le score est forcé à 0
                    if (!empty($_SESSION['score_nul'])) {
                        $score = 0;
                    }

                    // Sauvegarde du score
                    $this->model->saveScore($_SESSION['pseudo'], $score, $_SESSION['niveau']);
                }
            }

            header('Location: index.php?page=jouer');
            exit;
        }

        include 'vues/jeu.php';
    }


    // Compare une propos avec le mot secret et retourne un tableau de cases
    private function analyserMot($propos, $secret)
    {
        $pA = str_split($propos);
        $sA = str_split($secret);
        $taille = count($pA);

        $res = array_fill(0, $taille, ['L' => '', 'statut' => 'absent']);
        $disp = [];

        // 1ère passe : lettres bien placées
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

        // 2ème passe : lettres mal placées
        for ($i = 0; $i < $taille; $i++) {
            if ($res[$i]['statut'] === 'bien-place') {
                continue; // on saute les bien placé
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


    // Génère un lien Google Images : "Bleach + nom"
    public function getInfoLink($mot)
    {
        return 'https://www.google.com/search?q=' . urlencode('Bleach ' . $mot) . '&tbm=isch';
    }
}
