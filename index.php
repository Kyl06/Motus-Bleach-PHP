<?php
// index.php : point d'entrée unique du jeu
// Session pour pouvoir utiliser $_SESSION partout dans l'app
session_start();
require_once 'controleurs/MotusController.php';

// instance du contrôleur
$controller = new MotusController();

// récupère la page demandée via l'URL, par défaut "accueil"
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// routage en fonction du paramètre "page" pour méthode du contrôleur
switch ($page) {
    case 'jouer':
        // page du jeu
        $controller->jouer();
        break;
    case 'scores':
        //tableau des scores
        $controller->scores();
        break;
    case 'regles':
        // règles du jeu
        $controller->regles();
        break;
    default:
        // page d'accueil (pseudo et choix du niveau)
        $controller->accueil();
        break;
}
