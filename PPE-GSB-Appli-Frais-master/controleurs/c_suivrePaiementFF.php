<?php

/** 
 * Suivi du paiement des fiches de frais par le comptable
 * @category  PPE
 * @package   GSB
 * @author   Salomé LILTI
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
case 'choisirFicheFrais':
    //choisir une fiche validee: 2 listes deroulantes: visiteur dont la fiche est validée et mois dont la fiche est validée.
    $visiteurs= $pdo->getVisiteurDontFicheVA();
    $lesClesV = array_keys($visiteurs);
    $visiteurASelectionner = $lesClesV;
    $lesMois=$pdo->getMoisDontFicheVA(); 
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles;
    
    $test_if_array_is_empty = array_keys( $visiteurs, true ); 
    if (!$test_if_array_is_empty){
        ajouterErreur('Pas de fiches validées à mettre en paiement.');
        include 'vues/v_erreurs.php';
    }else{
    $visiteurs= $pdo->getVisiteurDontFicheVA();
    $lesClesV = array_keys($visiteurs);
    $visiteurASelectionner = $lesClesV;
    $lesMois=$pdo->getMoisDontFicheVA(); 
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles;
    include 'vues/v_suivrePaiementFF.php';
    }
    break;
case 'miseEnPaiement':
    $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $visiteurs= $pdo->getVisiteurDontFicheVA();
    $visiteurASelectionner = $idVisiteur;
    $lstMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $lesMois=$pdo->getMoisDontFicheVA(); 
    $moisASelectionner = $lstMois;
    //if ($visiteurs==0){
    //   echo 'pas de fiche a mettre en paiement';
    //}
    
    $lesFraisForfait=$pdo->getLesFraisForfait($idVisiteur, $lstMois);
    $lesFraisHorsForfait=$pdo->getLesFraisHorsForfait($idVisiteur, $lstMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $lstMois);

    $numAnnee = substr($lstMois, 0, 4);
    $numMois = substr($lstMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    
    $etat='RB';
    $pdo->majEtatFicheFrais($idVisiteur, $lstMois, $etat);
  
    include 'vues/v_etatFrais.php';
    include 'vues/v_miseEnPaiement.php';
    break;
    case 'confirmationMiseEnPaiement':
    ajouterErreur('La fiche a bien été mise en paiement.');
        include 'vues/v_messageConfirmation.php';

}