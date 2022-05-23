<?php
/**
 * Vue Erreurs
 *
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */
?>
<div class="alert alert-danger" role="alert">
    <?php
    foreach ($_REQUEST['erreurs'] as $erreur) {
        echo '<p>' . htmlspecialchars($erreur) . '</p>';
    }
    ?>
</div>