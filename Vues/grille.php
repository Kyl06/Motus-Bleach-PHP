<?php
if (isset($_GET['mot'])) {
  header('Content-Type: image/png');
  $mot = strtoupper($_GET['mot']);
  $im = imagecreatetruecolor(50 * strlen($mot), 50);
  $colors = [
    "rouge" => imagecolorallocate($im, 255, 0, 0),
    "jaune" => imagecolorallocate($im, 255, 215, 0),
    "gris" => imagecolorallocate($im, 180, 180, 180)
  ];
  $fontColor = imagecolorallocate($im, 0, 0, 0);
  for ($i = 0; $i < strlen($mot); $i++) {
    $color = $colors["gris"]; // Modifie selon résultat
    imagefilledrectangle($im, $i * 50, 0, ($i + 1) * 50 - 2, 48, $color);
    imagestring($im, 5, $i * 50 + 15, 15, $mot[$i], $fontColor);
  }
  imagepng($im);
  imagedestroy($im);
}
?>