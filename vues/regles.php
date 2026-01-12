<!DOCTYPE html>
<html xmlns="http://wwww3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Règles - Motus Bleach</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
    <div class="game-container" style="max-width: 600px; text-align: left;">
        <h1 style="text-align: center;">Règles du Combat</h1>

        <!-- explication générale du but du jeu -->
        <p>Vous devez deviner l'identité d'un personnage, d'une arme, d'un lieu ou d'un concept de l'univers Bleach.</p>

        <!-- section expliquant le code couleur des lettres -->
        <h3>Code Couleur :</h3>
        <ul style="list-style: none; padding: 0;">
            <li>
                <span class="lettre bien-place"
                    style="width:30px; height:30px; line-height:30px; font-size:14px;">A</span>
                : La lettre est correcte et bien placée.
            </li>
            <li>
                <span class="lettre mal-place"
                    style="width:30px; height:30px; line-height:30px; font-size:14px;">B</span>
                : La lettre est dans le mot mais mal placée.
            </li>
            <li>
                <span class="lettre"
                    style="width:30px; height:30px; line-height:30px; font-size:14px; background:#333;">C</span>
                : La lettre n'existe pas dans le mot.
            </li>
        </ul>

        <!-- section sur le système de score -->
        <h3>Système de Points :</h3>
        <p>Votre score dépend de plusieurs facteurs :</p>
        <ul>
            <li>Le <strong>niveau choisi</strong> (score maximum plus élevé en EXPERT qu'en SIMPLE).</li>
            <li>Le <strong>nombre d'essais</strong> utilisés : moins vous faites d'essais, plus vous marquez de points.
            </li>
            <li>Les <strong>indices utilisés</strong> : chaque indice réduit votre score final.</li>
        </ul>

        <!-- bouton pour revenir à l'accueil -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn-shinigami" style="text-decoration:none;">RETOUR</a>
        </div>
    </div>
</body>

</html>