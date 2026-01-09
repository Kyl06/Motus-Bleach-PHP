<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Combat - Motus Bleach</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
<?php
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

// même calcul que dans le contrôleur :
$essais = $_SESSION['nb_essais'];
if ($essais < 1) {
    $essais = 1;
}
$score_affiche = $score_max - 100 * ($essais - 1);
if ($score_affiche < 0) {
    $score_affiche = 0;
}
if (!empty($_SESSION['score_nul'])) {
    $score_affiche = 0;
}
?>
    <div class="game-container">
        <h1>Trouvez le mot</h1>

        <div
            style="display: flex; justify-content: space-around; margin-bottom: 15px; font-size: 0.9em; color: #ccc; border-bottom: 1px solid #444; padding-bottom: 10px;">
            <span>Tentative :
                <strong style="color:#ff8c00">
                    <?= $_SESSION['nb_essais'] ?> / <?= $_SESSION['max_essais'] ?>
                </strong>
            </span>
            <span>Score :
                <strong style="color:#ffd700">
                    <?= $score_affiche ?>
                </strong>
            </span>
            <span>Niveau : <strong><?= strtoupper($_SESSION['niveau']) ?></strong></span>
        </div>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div style="margin-bottom:10px; padding:8px; background:#500; color:#ffd7d7; border:1px solid #f00;">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="grille">
            <?php foreach ($_SESSION['essais'] as $index => $ligne): ?>
                <div class="ligne" style="opacity: 0.8;">
                    <small style="color:#444; margin-right:5px;"><?= $index + 1 ?></small>
                    <?php foreach ($ligne as $c): ?>
                        <span class="lettre <?= $c['statut'] ?>"><?= $c['L'] ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <?php if (!$_SESSION['victoire'] && $_SESSION['nb_essais'] < $_SESSION['max_essais']): ?>
                <div class="ligne">
                    <small style="color:#ff8c00; margin-right:5px;">&gt;</small>
                    <?php
                    $mot = $_SESSION['mot_secret'];
                    for ($i = 0; $i < strlen($mot); $i++):
                        $revealed = in_array($i, $_SESSION['indices_reveles']);
                        ?>
                        <span class="lettre <?= $revealed ? 'bien-place' : '' ?>">
                            <?= $revealed ? $mot[$i] : '.' ?>
                        </span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['indice_type_utilise'])): ?>
            <p style="margin-top:10px; color:#ffd700;">
                Nature du mot : <strong><?= htmlspecialchars($_SESSION['type_mot_secret']) ?></strong>
            </p>
        <?php endif; ?>

        <?php if ($_SESSION['victoire'] || $_SESSION['nb_essais'] >= $_SESSION['max_essais']): ?>
            <div class="resultat-final" style="margin-top:20px; padding:15px; border:1px solid #ff8c00;">
                <h2 style="color:<?= $_SESSION['victoire'] ? '#ffd700' : '#ff0000' ?>">
                    <?= $_SESSION['victoire'] ? 'MISSION RÉUSSIE !' : 'K.O. - MISSION ÉCHOUÉE' ?>
                </h2>
                <p>L'identité était : <strong style="color:#ff8c00"><?= $_SESSION['mot_secret'] ?></strong></p>
                <div style="display:flex; gap:10px; justify-content:center">
                    <a href="<?= $this->getInfoLink($_SESSION['mot_secret']) ?>" target="_blank" class="btn-shinigami"
                       style="text-decoration:none">INFO</a>
                    <a href="index.php" class="btn-shinigami"
                       style="text-decoration:none; background:#fff; color:#000">REJOUER</a>
                </div>
            </div>
        <?php else: ?>
            <form method="post" action="index.php?page=jouer&amp;action=frapper">
                <input type="text" name="proposition" value="<?= $_SESSION['proposition_clavier'] ?>"
                       maxlength="<?= strlen($_SESSION['mot_secret']) ?>" autocomplete="off" autofocus="autofocus" />
                <br />
                <button type="submit" class="btn-shinigami">FRAPPER</button>
                <a href="index.php?page=jouer&amp;action=indice" class="btn-shinigami"
                   style="background:#444; text-decoration:none">
                    INDICE LETTRE (-2 essais / -200 pts)
                </a>
                <a href="index.php?page=jouer&amp;action=indiceType" class="btn-shinigami"
                   style="background:#555; text-decoration:none">
                    NATURE DU MOT (-3 essais / -300 pts)
                </a>
                <a href="index.php?page=jouer&amp;action=abandon" class="btn-shinigami"
                   style="background:#900; text-decoration:none">
                    ABANDONNER
                </a>
            </form>

            <div class="clavier">
                <?php foreach ([['A', 'Z', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'], ['Q', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'], ['W', 'X', 'C', 'V', 'B', 'N', 'M']] as $row): ?>
                    <div class="clavier-rangee">
                        <?php foreach ($row as $l): ?>
                            <a href="index.php?page=jouer&amp;char=<?= $l ?>"
                               class="touche <?= $_SESSION['clavier'][$l] ?>"><?= $l ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <a href="index.php?page=jouer&amp;action=del" class="touche"
                   style="width:120px; text-decoration:none">EFFACER</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bouton DICTIONNAIRE + panneau des mots -->
    <div style="position:fixed; top:50px; right:10px; text-align:right;">
        <a href="index.php?page=jouer&amp;action=antiseche" class="btn-shinigami"
           style="background:#1e90ff; text-decoration:none;">
            DICTIONNAIRE (score = 0)
        </a>

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
                        <strong style="color:#ff8c00; font-size:0.8em;"><?= strtoupper($niveau) ?></strong>
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
