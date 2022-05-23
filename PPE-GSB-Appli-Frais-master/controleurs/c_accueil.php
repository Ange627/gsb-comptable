<?php
/**
 * Gestion de l'accueil
 *
 *
 * @category  PPE
 * @package   GSB
 * @author   Salomé LILTI

 */
$estVisiteurConnecte=estVisiteurConnecte();
$estComptableConnecte=estComptableConnecte();
if ($estVisiteurConnecte) {
    include 'vues/v_accueilVisiteur.php';
    }elseif ($estComptableConnecte){
        include 'vues/v_accueilComptable.php';
    } else {
    include 'vues/v_connexion.php';
}
