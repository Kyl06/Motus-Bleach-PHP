<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Hall of Fame - Shinigami</title>
    <link rel="stylesheet" type="text/css" href="./public/motus.css" />
</head>

<body>
    <div class="game-container">
        <h1>Tableau des Scores</h1>

        <?php if (!empty($bestScore)): ?>
            <p>Meilleur score : <strong><?= (int) $bestScore ?></strong> points</p>
        <?php endif; ?>

        <p>Nombre de parties jouées : <strong><?= (int) $nbParties ?></strong></p>

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
                <?php foreach ($scores as $index => $s): ?>
                    <tr style="border-bottom: 1px solid #444;">
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($s['pseudo']) ?></td>
                        <td><?= (int) $s['score'] ?></td>
                        <td><?= htmlspecialchars(strtoupper($s['niveau'])) ?></td>
                        <td><?= htmlspecialchars($s['date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>
            <a href="index.php" class="btn-shinigami"
               style="text-decoration:none; margin-top:20px; display:inline-block;">
                RETOUR
            </a>
        </p>
    </div>
</body>
</html>
