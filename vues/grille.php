<?php
// si un mot est passé en GET, génère PNG
if (isset($_GET['mot'])) {
  // indique au navigateur le PNG
  header('Content-Type: image/png');

  // création d'image
  $im = imagecreatetruecolor(50 * strlen($mot), 50);

  // couleurs possibles
  $colors = [
    "rouge" => imagecolorallocate($im, 255, 0, 0),
    "jaune" => imagecolorallocate($im, 255, 215, 0),
    "gris" => imagecolorallocate($im, 180, 180, 180)
  ];

  // couleur police
  $fontColor = imagecolorallocate($im, 255, 255, 255);

  // boucle sur chaque lettre du mot
  for ($i = 0; $i < strlen($mot); $i++) {
    // couleur de la case, par défaut gris
    $color = $colors["gris"];

    // dessin d'un rectangle plein pour case de la lettre
    imagefilledrectangle($im, $i * 50, 0, ($i + 1) * 50 - 2, 48, $color);

    // dessin de lettre au ~centre de la case
    imagestring($im, 5, $i * 50 + 15, 15, $mot[$i], $fontColor);
  }

  // envoi PNG au navigateur
  imagepng($im);

  // libération mémoire
  imagedestroy($im);
}
?>