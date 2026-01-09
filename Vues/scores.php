<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <!-- métadonnées + titre de la page Hall of Fame + feuille de styles globale -->
    <meta charset="UTF-8" />
    <title>Hall of Fame - Shinigami</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
    <!-- conteneur principal du tableau des scores -->
    <div class="game-container">
        <!-- titre de la section scores -->
        <h1>Tableau des Scores</h1>

        <!-- affichage du meilleur score uniquement s'il existe au moins un enregistrement -->
        <?php if (!empty($bestScore)): ?>
            <p>Meilleur score : <strong><?= (int) $bestScore ?></strong> points</p>
        <?php endif; ?>

        <!-- nombre total de parties enregistrées -->
        <p>Nombre de parties jouées : <strong><?= (int) $nbParties ?></strong></p>

        <!-- tableau listant les meilleurs scores -->
        <table style="width:100%; border-collapse: collapse; margin-top:20px;">
            <thead>
                <tr style="border-bottom: 2px solid #ff8c00;">
                    <th>Rang</th>
                    <th>Joueur</th>
                    <th>Score</th>
                    <th>Niveau</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- boucle sur la liste de scores, déjà triée du meilleur au moins bon -->
                <?php foreach ($scores as $index => $s): ?>
                    <tr style="border-bottom: 1px solid #444;">
                        <!-- rang = index du tableau + 1 -->
                        <td><?= $index + 1 ?></td>
                        <!-- pseudo sécurisé pour éviter les injections HTML -->
                        <td><?= htmlspecialchars($s['pseudo']) ?></td>
                        <!-- score forcé en entier -->
                        <td><?= (int) $s['score'] ?></td>
                        <!-- niveau affiché en majuscules -->
                        <td><?= htmlspecialchars(strtoupper($s['niveau'])) ?></td>
                        <!-- date de la partie telle qu'enregistrée -->
                        <td><?= htmlspecialchars($s['date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- bouton de retour vers l'écran d'accueil -->
        <p>
            <a href="index.php" class="btn-shinigami"
               style="text-decoration:none; margin-top:20px; display:inline-block;">
                RETOUR
            </a>
        </p>
    </div>
</body>
</html>
