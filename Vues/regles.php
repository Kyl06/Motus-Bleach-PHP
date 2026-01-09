<!DOCTYPE html>
<html xmlns="http://wwww3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <!-- métadonnées de la page des règles + feuille de styles globale du jeu -->
    <meta charset="UTF-8" />
    <title>Règles - Motus Bleach</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
    <!-- conteneur principal des règles, centré avec largeur limitée -->
    <div class="game-container" style="max-width: 600px; text-align: left;">
        <!-- titre des règles, centré visuellement -->
        <h1 style="text-align: center;">Règles du Combat</h1>

        <!-- explication générale de l'objectif du jeu -->
        <p>Vous devez deviner l'identité d'un personnage, d'une arme, d'un lieu ou d'un concept de l'univers Bleach.</p>

        <!-- section expliquant le code couleur des lettres -->
        <h3>Code Couleur :</h3>
        <!-- liste des exemples de lettres colorées avec leur signification -->
        <ul style="list-style: none; padding: 0;">
            <li>
                <!-- exemple de lettre bien placée (couleur verte/bonne) -->
                <span class="lettre bien-place"
                      style="width:30px; height:30px; line-height:30px; font-size:14px;">A</span>
                : La lettre est correcte et bien placée.
            </li>
            <li>
                <!-- exemple de lettre mal placée (bonne lettre, mauvaise position) -->
                <span class="lettre mal-place"
                      style="width:30px; height:30px; line-height:30px; font-size:14px;">B</span>
                : La lettre est dans le mot mais mal placée.
            </li>
            <li>
                <!-- exemple de lettre absente (n'apparaît pas dans le mot) -->
                <span class="lettre"
                      style="width:30px; height:30px; line-height:30px; font-size:14px; background:#333;">C</span>
                : La lettre n'existe pas dans le mot.
            </li>
        </ul>

        <!-- section décrivant le système de score -->
        <h3>Système de Points :</h3>
        <p>Votre score dépend de plusieurs facteurs :</p>
        <ul>
            <!-- influence du niveau de difficulté sur le score maximal -->
            <li>Le <strong>niveau choisi</strong> (score maximum plus élevé en EXPERT).</li>
            <!-- influence du nombre d'essais sur le score -->
            <li>Le <strong>nombre d'essais</strong> utilisés : moins vous faites d'essais, plus vous marquez de points.</li>
            <!-- pénalité liée à l'utilisation des indices -->
            <li>Les <strong>indices utilisés</strong> : chaque indice réduit votre score final.</li>
        </ul>

        <!-- phrase de flavor / ambiance autour de Bleach et du score -->
        <p>Plus vous libérez rapidement la bonne identité, plus votre Zanpakutō s'illumine.</p>

        <!-- bouton pour revenir à l'écran d'accueil -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn-shinigami" style="text-decoration:none;">RETOUR</a>
        </div>
    </div>
</body>
</html>
