<?php
/**
 * Gestion de la connexion
 *
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 * 
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if (!$uc) {
    $uc = 'demandeconnexion';
}

switch ($action) {
case 'demandeConnexion':
    include 'vues/v_connexion.php';
    break;
case 'valideConnexion':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
    $visiteur = $pdo->getInfosVisiteur($login, $mdp);
    $comptable = $pdo->getInfosComptable($login, $mdp);
    if (!is_array($visiteur)&& !is_array($comptable)) { //si les identifiants sont faux
        ajouterErreur('Login ou mot de passe incorrect');
        include 'vues/v_erreurs.php';
        include 'vues/v_connexion.php';
    } else {
        if (is_array($visiteur)){
            $id = $visiteur['id'];
            $nom = $visiteur['nom'];
            $prenom = $visiteur['prenom'];
            $statut='visiteur';
        }
        elseif (is_array($comptable)){
            $id = $comptable['id'];
            $nom = $comptable['nom'];
            $prenom = $comptable['prenom'];
            $statut='comptable';
        }
        connecter($id, $nom, $prenom, $statut); // j'ai ajouté la variable statut
        header('Location: index.php'); //g un doute sur ce que ca veut dire
    }
    break;
default:
    include 'vues/v_connexion.php';
    break;
}
