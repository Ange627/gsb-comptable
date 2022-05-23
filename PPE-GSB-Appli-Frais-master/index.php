<?php
/**
 * Index du projet

 GSB
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */

require_once 'includes/fct.inc.php';//require = > erreur fatale , arrêt de l'exécution du programme, once=> on l'ajoute une seule fois
				    //fct => fct simples
require_once 'includes/class.pdogsb.inc.php';//fichier où on a besoin d'une partie, fichier intégré ds ce fichier
					     //fichier qui contient des fonctions ac des requetes sql qui vont interagir dans la BDD					     
session_start();//methode qui va ouvrir la session => variable super globale, elle contient plusieurs variables du projet
$pdo = PdoGsb::getPdoGsb();//elle permet de connecter le code à la BDD PdoGsb. On appelle le résultat de la méthode getGsb() qui vient de la classe PdoGsb.
$estConnecte = estConnecte();
$estComptableConnecte = estComptableConnecte();
$estVisiteurConnecte = estVisiteurConnecte();
require 'vues/v_entete.php';//require=> pr lancer le fichier "v_entete.php", il va l'afficher sur l'utilisateur. Si ca marche pas, l'execution s'arrete (a la difference de include)
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);//méthode qui permet de filtrer= vérifier le contenu de la variable sur laquelle elle sera appliquée.
							    //$uc et $action sont des variables utilisées tt au long du projet et changeant de valeur
if ($uc && !$estConnecte) {//si il a reçu le résultat de !$estConnecte, 
    $uc = 'connexion';//alors $uc prend la valeur connexion
} elseif (empty($uc)) {//si $uc est vide, on affecte la valeur accueil à $uc
    $uc = 'accueil';
}
switch ($uc) {//sur la variable $uc
case 'connexion'://si valeur $uc = connexion
    include 'controleurs/c_connexion.php';//il faut lancer le fichier "c_connexion.php"
    break;
case 'accueil'://sinon, si elle prend la valeur "accueil"
    include 'controleurs/c_accueil.php';
    break;
case 'gererFrais':
    include 'controleurs/c_gererFrais.php';
    break;
case 'etatFrais':
    include 'controleurs/c_etatFrais.php';
    break;
case 'valideFrais'://je crée ce controleur qui va permettre au comptable de faire son boulot càd valider les frais
    include 'controleurs/c_valideFrais.php';
    break;
case 'suivrePaiementFF':
    include 'controleurs/c_suivrePaiementFF.php';
    break;
case 'deconnexion':
    include 'controleurs/c_deconnexion.php';
    break;

}
require 'vues/v_pied.php';//on lance le pied de page
