<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <!-- métadonnées + titre de la page de combat + lien vers la feuille de styles principale -->
    <meta charset="UTF-8" />
    <title>Combat - Motus Bleach</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
<?php
// choix du score maximum en fonction du niveau stocké en session
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

// même logique de calcul du score que dans le contrôleur pour l'affichage temps réel
$essais = $_SESSION['nb_essais'];
if ($essais < 1) {
    $essais = 1;
}
$score_affiche = $score_max - 100 * ($essais - 1);
if ($score_affiche < 0) {
    $score_affiche = 0;
}
// si le dictionnaire a été utilisé, le score affiché est forcé à 0
if (!empty($_SESSION['score_nul'])) {
    $score_affiche = 0;
}
?>
    <!-- conteneur principal du jeu -->
    <div class="game-container">
        <!-- titre principal de la page de combat -->
        <h1>Trouvez le mot</h1>

        <!-- barre d'informations : numéro d'essai, score courant, niveau de difficulté -->
        <div
            style="display: flex; justify-content: space-around; margin-bottom: 15px; font-size: 0.9em; color: #ccc; border-bottom: 1px solid #444; padding-bottom: 10px;">
            <span>Tentative :
                <!-- affichage du nombre d'essais consommés sur le maximum -->
                <strong style="color:#ff8c00">
                    <?= $_SESSION['nb_essais'] ?> / <?= $_SESSION['max_essais'] ?>
                </strong>
            </span>
            <span>Score :
                <!-- affichage du score calculé pour la partie en cours -->
                <strong style="color:#ffd700">
                    <?= $score_affiche ?>
                </strong>
            </span>
            <!-- rappel du niveau de difficulté -->
            <span>Niveau : <strong><?= strtoupper($_SESSION['niveau']) ?></strong></span>
        </div>

        <!-- affichage d'un message d'erreur ponctuel si une action est impossible (indice, etc.) -->
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div style="margin-bottom:10px; padding:8px; background:#500; color:#ffd7d7; border:1px solid #f00;">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <!-- grille principale contenant l'historique des essais + éventuelle ligne d'indice -->
        <div class="grille">
            <!-- boucle sur chaque essai déjà effectué -->
            <?php foreach ($_SESSION['essais'] as $index => $ligne): ?>
                <div class="ligne" style="opacity: 0.8;">
                    <!-- numéro de la ligne (1er essai, 2ème, etc.) -->
                    <small style="color:#444; margin-right:5px;"><?= $index + 1 ?></small>
                    <!-- affichage de chaque lettre de l'essai avec son statut (bien/mal placé/absent) -->
                    <?php foreach ($ligne as $c): ?>
                        <span class="lettre <?= $c['statut'] ?>"><?= $c['L'] ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!-- ligne d'aide en cours de partie (lettres révélées par les indices) -->
            <?php if (!$_SESSION['victoire'] && $_SESSION['nb_essais'] < $_SESSION['max_essais']): ?>
                <div class="ligne">
                    <!-- petit chevron indiquant la ligne active -->
                    <small style="color:#ff8c00; margin-right:5px;">&gt;</small>
                    <?php
                    $mot = $_SESSION['mot_secret'];
                    for ($i = 0; $i < strlen($mot); $i++):
                        $revealed = in_array($i, $_SESSION['indices_reveles']);
                        ?>
                        <!-- si l'index est dans indices_reveles, on montre la lettre, sinon un point -->
                        <span class="lettre <?= $revealed ? 'bien-place' : '' ?>">
                            <?= $revealed ? $mot[$i] : '.' ?>
                        </span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- affichage de la nature du mot (personnage, lieu, etc.) si l'indice correspondant a été utilisé -->
        <?php if (!empty($_SESSION['indice_type_utilise'])): ?>
            <p style="margin-top:10px; color:#ffd700;">
                Nature du mot : <strong><?= htmlspecialchars($_SESSION['type_mot_secret']) ?></strong>
            </p>
        <?php endif; ?>

        <!-- bloc affiché en fin de partie : victoire ou échec -->
        <?php if ($_SESSION['victoire'] || $_SESSION['nb_essais'] >= $_SESSION['max_essais']): ?>
            <div class="resultat-final" style="margin-top:20px; padding:15px; border:1px solid #ff8c00;">
                <!-- titre variant selon le résultat -->
                <h2 style="color:<?= $_SESSION['victoire'] ? '#ffd700' : '#ff0000' ?>">
                    <?= $_SESSION['victoire'] ? 'MISSION RÉUSSIE !' : 'K.O. - MISSION ÉCHOUÉE' ?>
                </h2>
                <!-- rappel du mot à deviner -->
                <p>L'identité était : <strong style="color:#ff8c00"><?= $_SESSION['mot_secret'] ?></strong></p>
                <!-- actions possibles après la fin du combat (voir infos, rejouer) -->
                <div style="display:flex; gap:10px; justify-content:center">
                    <!-- lien vers une recherche d'infos sur le mot (Bleach + mot secret) -->
                    <a href="<?= $this->getInfoLink($_SESSION['mot_secret']) ?>" target="_blank" class="btn-shinigami"
                       style="text-decoration:none">INFO</a>
                    <!-- lien pour revenir à l'accueil et lancer une nouvelle partie -->
                    <a href="index.php" class="btn-shinigami"
                       style="text-decoration:none; background:#fff; color:#000">REJOUER</a>
                </div>
            </div>
        <?php else: ?>
            <!-- formulaire de proposition de mot tant que la partie n'est pas terminée -->
            <form method="post" action="index.php?page=jouer&amp;action=frapper">
                <input type="text" name="proposition" value="<?= $_SESSION['proposition_clavier'] ?>"
                       maxlength="<?= strlen($_SESSION['mot_secret']) ?>" autocomplete="off" autofocus="autofocus" />
                <br />
                <!-- envoi de la proposition -->
                <button type="submit" class="btn-shinigami">FRAPPER</button>
                <!-- demande d'un indice lettre (coût : -2 essais / -200 pts) -->
                <a href="index.php?page=jouer&amp;action=indice" class="btn-shinigami"
                   style="background:#444; text-decoration:none">
                    INDICE LETTRE (-2 essais / -200 pts)
                </a>
                <!-- demande de la nature du mot (coût : -3 essais / -300 pts) -->
                <a href="index.php?page=jouer&amp;action=indiceType" class="btn-shinigami"
                   style="background:#555; text-decoration:none">
                    NATURE DU MOT (-3 essais / -300 pts)
                </a>
                <!-- abandon immédiat de la partie -->
                <a href="index.php?page=jouer&amp;action=abandon" class="btn-shinigami"
                   style="background:#900; text-decoration:none">
                    ABANDONNER
                </a>
            </form>

            <!-- clavier virtuel pour entrer le mot via des liens au lieu du clavier physique -->
            <div class="clavier">
                <?php foreach ([['A', 'Z', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'], ['Q', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'], ['W', 'X', 'C', 'V', 'B', 'N', 'M']] as $row): ?>
                    <div class="clavier-rangee">
                        <!-- chaque lettre est un lien qui ajoute ce caractère à la proposition en cours -->
                        <?php foreach ($row as $l): ?>
                            <a href="index.php?page=jouer&amp;char=<?= $l ?>"
                               class="touche <?= $_SESSION['clavier'][$l] ?>"><?= $l ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <!-- bouton pour effacer complètement la proposition saisie via le clavier virtuel -->
                <a href="index.php?page=jouer&amp;action=del" class="touche"
                   style="width:120px; text-decoration:none">EFFACER</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- zone fixe à droite pour le bouton d'activation du dictionnaire et son panneau -->
    <div style="position:fixed; top:50px; right:10px; text-align:right;">
        <!-- bouton permettant d'ouvrir le dictionnaire (et de mettre le score à 0) -->
        <a href="index.php?page=jouer&amp;action=antiseche" class="btn-shinigami"
           style="background:#1e90ff; text-decoration:none;">
            DICTIONNAIRE (score = 0)
        </a>

        <!-- panneau des mots du dictionnaire affiché seulement si l'antiseche est active -->
        <?php if (!empty($_SESSION['antiseche_active'])): ?>
            <div style="
                margin-top:10px;
                padding:10px;
                background:#222;
                border:1px solid #ff8c00;
                width:300px;
                max-height:75vh;
                overflow-y:auto;
                font-size:0.8em;
            ">
                <h3 style="margin-top:0; font-size:0.9em;">Dictionnaire</h3>
                <?php
                $tous = $this->model->getTousLesMots();
                foreach ($tous as $niveau => $liste): ?>
                    <div style="margin-bottom:8px;">
                        <!-- titre de la section pour chaque niveau (simple, moyen, expert) -->
                        <strong style="color:#ff8c00; font-size:0.8em;"><?= strtoupper($niveau) ?></strong>
                        <!-- liste des mots du niveau sous forme de petits badges -->
                        <div style="display:flex; flex-wrap:wrap; gap:4px; margin-top:4px;">
                            <?php foreach ($liste as $item): ?>
                                <span style="padding:2px 4px; background:#333; border-radius:3px;">
                                    <?= htmlspecialchars($item['mot']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
