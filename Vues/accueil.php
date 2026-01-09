<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Accueil - Motus Bleach</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
    <div class="game-container">
        <h1>Motus Bleach</h1>

        <form method="post" action="index.php?page=jouer">
            <div style="margin-bottom: 20px;">
                <label for="pseudo">Nom du Shinigami :</label><br />
                <input
                    id="pseudo"
                    type="text"
                    name="pseudo"
                    required="required"
                    placeholder="Ex : Ichigo"
                    autocomplete="off"
                    style="width: 250px; font-size: 1.2em; border-bottom-color: #ff8c00;"
                />
            </div>

            <p>Choisissez votre rang de mission :</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="submit" name="niveau" value="debutant" class="btn-shinigami">SIMPLE</button>
                <button type="submit" name="niveau" value="moyen" class="btn-shinigami">MOYEN</button>
                <button type="submit" name="niveau" value="expert" class="btn-shinigami">EXPERT</button>
            </div>
        </form>

        <div style="margin-top: 30px;">
            <a href="index.php?page=scores" style="color: #ff8c00; text-decoration: none;">Hall of Fame</a> |
            <a href="index.php?page=regles" style="color: #ff8c00; text-decoration: none;">Règles</a>
        </div>
    </div>
</body>
</html>
