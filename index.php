<?php
// index.php : point d'entrée unique (front controller) du jeu

// démarrage de la session pour pouvoir utiliser $_SESSION partout dans l'app
session_start();

// inclusion du contrôleur principal du jeu
require_once 'controleurs/MotusController.php';

// instanciation du contrôleur
$controller = new MotusController();

// récupération de la page demandée via l'URL, par défaut "accueil"
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// routage simple en fonction du paramètre "page" pour appeler la bonne méthode du contrôleur
switch ($page) {
    case 'jouer':
        // page principale du jeu (logique de partie + affichage)
        $controller->jouer();
        break;
    case 'scores':
        // page du tableau des scores (Hall of Fame)
        $controller->scores();
        break;
    case 'regles':
        // page des règles du jeu
        $controller->regles();
        break;
    default:
        // page d'accueil (pseudo + choix du niveau)
        $controller->accueil();
        break;
}
