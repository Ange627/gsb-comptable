<?php
/**
 * Gestion des frais
 *
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */

$idVisiteur = $_SESSION['idUtilisateur']; //g modifie de idVisiteur à idUtilisateur chepa si c une bne idee oui c une bonne idee!
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
case 'saisirFrais':   
    if ($pdo->estPremierFraisMois($idVisiteur, $mois)) {
        $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
    }   
    break;
case 'validerMajFraisForfait': //ajout frais
    $lesFrais = filter_input(INPUT_POST, 'lesFrais',FILTER_DEFAULT, FILTER_FORCE_ARRAY); //jai mis FILTER_FORCE_ARRAY au lieu de FILTER SANITIZE STRING, pr récupérer tt le tableau des frais
    if (lesQteFraisValides($lesFrais)) {
        $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
    } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
    }
    break;
case 'validerCreationFrais':
    $dateFrais = filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_STRING);
    $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
    $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
    valideInfosFrais($dateFrais, $libelle, $montant);
    if (nbErreurs() != 0) {
        include 'vues/v_erreurs.php';
    } else {
        $pdo->creeNouveauFraisHorsForfait(
            $idVisiteur,
            $mois,
            $libelle,
            $dateFrais,
            $montant
        );
    }
    break;
case 'supprimerFrais':
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    $pdo->supprimerFraisHorsForfait($idFrais);
    break;
}
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
