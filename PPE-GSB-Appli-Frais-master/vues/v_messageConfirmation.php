<?php

/** 
 * Vue affichant un message de confirmation en bleu
 * 
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */
?>
<div class="alert alert-info" role="alert" color "blue">
  <?php
    foreach ($_REQUEST['erreurs'] as $erreur) {
        echo '<p>' . htmlspecialchars($erreur) . '</p>';
    }
    ?>  
</div>
<?php
//header("Refresh: 10;URL=index.php");
//<p>La fiche a bien été validée! <a href="index.php">Cliquez ici</a>pour revenir à la page de connexion.</p>
