<?php
/**
 * Vue du bouton Valider les fiches de frais
 * 
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */
//recuperer les 2 listes deroulantes de v_choisirVisiteurMois et les elements forfaitisés et hors frais de v_etatFrais 
?>

<form method="post" 
      action="index.php?uc=valideFrais&action=corrigerFraisForfait"      
      role="form">

    <div class="row">
        <div class="col-md-4">

            <!-- liste déroulante visiteur-->
            <div class="form-group"
                 style="display: inline-block">
                <label for="lstVisiteurs" accesskey="n">Choisir le Visiteur : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
<?php
foreach ($visiteurs as $unVisiteur) {
    $id = $unVisiteur['id'];
    $nom = $unVisiteur['nom'];
    $prenom = $unVisiteur['prenom'];
    if ($nom == $visiteurASelectionner) { //pour que le nom selectionne s'affiche
        ?>
                            <option selected value="<?php echo $id ?>">
                            <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $id ?>">
                                <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        }
                    }
                    ?>  

                </select>
            </div>
            <!-- liste déroulante mois-->
            &nbsp;<div class="form-group"
                       style="display: inline-block">
                <label for="mois" accesskey="n">Mois : </label>
                <select id="mois" name="mois" class="form-control">
<?php
foreach ($lesMois as $unMois) {
    $mois = $unMois['mois'];
    $numAnnee = $unMois['numAnnee'];
    $numMois = $unMois['numMois'];
    if ($mois == $moisASelectionner) {
        ?>
                            <option selected value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?> 
                </select>          
            </div>         
        </div><br><br>




        <!-- affichage des frais forfait-->
        <div class="row">
            <br>
            <h2 style="color:orange;"> Valider la fiche de frais</h2>
            <h3>Eléments forfaitisés</h3>
            <div class="col-md-4">

                <fieldset>
<?php
foreach ($lesFraisForfait as $unFrais) {
    $idFrais = $unFrais['idfrais'];
    $libelle = htmlspecialchars($unFrais['libelle']);
    $quantite = $unFrais['quantite'];
    ?>
                        <div class="form-group">
                            <label for="idFrais"><?php echo $libelle ?></label>
                            <input type="text" id="idFrais" 
                                   name="lesFrais[<?php echo $idFrais ?>]"
                                   size="10" maxlength="5" 
                                   value="<?php echo $quantite ?>" 
                                   class="form-control">
                        </div>
    <?php }
?>

                    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
                    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>"> 

                    <button class="btn btn-success" type="submit">Corriger</button>
                    <button class="btn btn-danger" type="reset">Réinitialiser</button>
                </fieldset>
            </div>
        </div> 
    </div>    
</form>  


<hr>
<br>
<div>
    <form method="post" 
          action="index.php?uc=valideFrais&action=corrigerFraisHorsForfait"      
          accesskey=""role="form">
        <!-- affichage des frais hors forfait-->
        <div>
            <div class="row">
                <div class="panel panel-info-comptable">
                    <div class="panel-heading">Descriptif des éléments hors forfait</div>
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th class="date">Date</th>
                                <th class="libelle">Libellé</th>  
                                <th class="montant">Montant</th>  
                                <th class="action">&nbsp;</th> 
                            </tr>
                        </thead>  
                        <tbody>
<?php
foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
    $date = $unFraisHorsForfait['date'];
    $montant = $unFraisHorsForfait['montant'];
    $idFHF = $unFraisHorsForfait['id'];
    ?>  
                            <input type="hidden" name="idFHF" id="frais" size="10" value="<?php echo $idFHF ?>" class="form-control"/>
                            <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
                            <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>">
                            <tr name="tableauFHF">
                                <td> <input name="date" type="text" class="form-control" value="<?php echo $date ?>"> </td>
                                <td> <input name="libelle" type="text" class="form-control" value="<?php echo $libelle ?>"> </td>
                                <td> <input name="montant" type="text" class="form-control" value="<?php echo $montant ?>"> </td>
                                <td>
                                    <button class="btn btn-success" type="submit" name='corriger' value="refuser" >Corriger</button>
                                    <button class="btn btn-danger" type="reset" name="reinitialiser" value="reinitialiser" >Réinitialiser</button>
                                    <button class="btn btn-success" type="submit" name='reporter' value="reporter" >Reporter </button>
                                </td>
                            </tr>

    <?php
}
?>  
                        </tbody>  
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<form method="post"
      accept-charset=""action="index.php?uc=valideFrais&action=validerFiche"
      accesskey=""role="form">
    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>">
    Nombre de justificatifs: <input type="text" id="nbJust" name="nbJust" class="form-control-me" value="<?php echo $nbJustificatifs ?>"><br><br>
    <input id="ok" type="submit" value="Valider" class="btn btn-success"
           accept=""role="button">
    <button class="btn btn-danger" type="reset">Reinitialiser</button>
</form>
