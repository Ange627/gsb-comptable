<?php

/* 
 * Vue appelée après avoir cliqué sur le bouton mettre en paiement. Elle confirme que a fiche a bien été mise en paiement.
 * 
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */

?>

<form method="post" action="index.php?uc=suivrePaiementFF&action=confirmationMiseEnPaiement"      
      role="form">
    
    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>">
    <input id="ok" type="submit" value="Mise en paiement" class="btn btn-success" name="mettreEnPaiement">
    
</form>