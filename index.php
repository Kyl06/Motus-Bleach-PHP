<?php
// index.php
session_start();

require_once 'controleurs/MotusController.php';

$controller = new MotusController();
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';


switch ($page) {
    case 'jouer':
        $controller->jouer();
        break;
    case 'scores':
        $controller->scores();
        break;
    case 'regles':
        $controller->regles();
        break;
    default:
        $controller->accueil();
        break;
}
