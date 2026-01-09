<?php
// si un mot est passé en paramètre GET, on génère une image PNG
if (isset($_GET['mot'])) {
  // on indique au navigateur que la sortie sera une image PNG
  header('Content-Type: image/png');

  // mot à afficher, converti en majuscule
  $mot = strtoupper($_GET['mot']);

  // création d'une image vraie couleur : largeur = 50px par lettre, hauteur = 50px
  $im = imagecreatetruecolor(50 * strlen($mot), 50);

  // définition des couleurs de fond possibles (rouge, jaune, gris)
  $colors = [
    "rouge" => imagecolorallocate($im, 255, 0, 0),
    "jaune" => imagecolorallocate($im, 255, 215, 0),
    "gris"  => imagecolorallocate($im, 180, 180, 180)
  ];

  // couleur de la police (noir)
  $fontColor = imagecolorallocate($im, 0, 0, 0);

  // boucle sur chaque lettre du mot
  for ($i = 0; $i < strlen($mot); $i++) {
    // couleur de la case, par défaut gris (à adapter selon le résultat du jeu)
    $color = $colors["gris"];

    // dessin d'un rectangle plein pour la case de la lettre
    imagefilledrectangle($im, $i * 50, 0, ($i + 1) * 50 - 2, 48, $color);

    // dessin de la lettre au centre approximatif de la case
    imagestring($im, 5, $i * 50 + 15, 15, $mot[$i], $fontColor);
  }

  // envoi de l'image PNG au navigateur
  imagepng($im);

  // libération des ressources mémoire
  imagedestroy($im);
}
?>
