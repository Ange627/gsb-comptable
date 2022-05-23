<?php
/**
 * Classe d'accès aux données.
 *
 * @category  PPE
 * @package   GSB
 * @author    Salomé LILTI
 */


class PdoGsb
{
    private static $serveur='mysql:host=localhost';
    private static $bdd='dbname=gsbweb';   		
    private static $user='gsb-web' ;    		
    private static $mdp='arnold67' ;
    private static $monPdo;
    private static $monPdoGsb=null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(// méthode qui crée une instance de la classe Pdo. Chaque méthode est un obj de cette classe, 
				  //le constructeur sera exécuté donc à chaque fois qu'on appelle une méthode.
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,//3 paramètres : celui-ci est un param regroupé ;=> concaténation de la propriété $serveur et de la propriété $bdd
            PdoGsb::$user,//2ème param
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');//requete sql entre parenthèses, elle modifie le champ "CHARACTER" avec la valeur utf8
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()//le destructeur fait un peu d'ordre, il détruit la méthode dès qu'on n'en a plus besoin
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();// on affecte à la variable $instancePdoGsb le résultat de la méthode getPdoGsb().
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {//si la  classe PdoGsb (=la classe même) est nulle, on l'instancie

            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)//qd le viiteur rentre le login et le mot de passe, il va rechercher ds la BDD l'id, le nom et le prenom
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);//on dit que la variable unLogin va avec la variable $login
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();//exécuter la requete
        return $requetePrepare->fetch();//fetch()=>rentrer le résultat de la req sous forme de tableau
    }
    
    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     * @param String $mdp   Mot de passe du comptable
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosComptable($login, $mdp)//qd le comptable rentre le login et le mot de passe, il va rechercher ds la BDD l'id, le nom et le prenom
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
            . 'comptable.prenom AS prenom '
            . 'FROM comptable '
            . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);//on dit que la variable unLogin va avec la variable $login
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();//exécuter la requete
        return $requetePrepare->fetch();//fetch()=>rentrer le résultat de la req sous forme de tableau
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
   public function getLesFraisHorsForfait($idVisiteur, $mois)
   {
       $requetePrepare = PdoGsb::$monPdo->prepare(
           'SELECT * FROM lignefraishorsforfait '
           . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
           . 'AND lignefraishorsforfait.mois = :unMois'
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->execute();
       
       $lesLignes = $requetePrepare->fetchAll();
       for ($i = 0; $i < count($lesLignes); $i++) {
           $date = $lesLignes[$i]['date'];
           $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
       }
       return $lesLignes;
   }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch(); //fetch=> retourne le résultat de la requête
        return $laLigne['nb'];//retourne la ligne à l'indice nb
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
   public function getLesFraisForfait($idVisiteur, $mois)
   {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT fraisforfait.id as idfrais, '
           . 'fraisforfait.libelle as libelle, '
           . 'lignefraisforfait.quantite as quantite '
           . 'FROM lignefraisforfait '
           . 'INNER JOIN fraisforfait '
           . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
           . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
           . 'AND lignefraisforfait.mois = :unMois '
           . 'ORDER BY lignefraisforfait.idfraisforfait'
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();
   }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(//Cette requête classe les idFrais par id
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
   {
       $lesCles = array_keys($lesFrais);
       foreach ($lesCles as $unIdFrais) {
           $qte = $lesFrais[$unIdFrais];
           $requetePrepare = PdoGSB::$monPdo->prepare(
               'UPDATE lignefraisforfait '
               . 'SET lignefraisforfait.quantite = :uneQte '
               . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
               . 'AND lignefraisforfait.mois = :unMois '
               . 'AND lignefraisforfait.idfraisforfait = :idFrais'
           );
           $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
           $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
           $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
           $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
           $requetePrepare->execute();
       }
   }
    
    /**
    * Met à jour la table ligneFraisHorsForfait pour un visiteur et
    * un mois donné en enregistrant les nouveaux montants
    *
    * @param char $idVisiteur  ID du visiteur
    * @param int $leMois       Mois sous la forme aaaamm
    * @param char $libelleHF  
    * @param date $dateHF
    * @param int $montantHF
    * @return null
    */
    
    public function majFraisHorsForfait($idVisiteur,$leMois,$libelle,$date,$montant, $idFHF) {
       $date = dateFrancaisVersAnglais($date);
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'UPDATE lignefraishorsforfait '
               . 'SET lignefraishorsforfait.libelle = :unLibelle,lignefraishorsforfait.date = :uneDateHF,lignefraishorsforfait.montant = :unMontant '
               . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
               . 'AND lignefraishorsforfait.mois = :unMois '
               . 'AND id=:idFHF'
           );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
       $requetePrepare->bindParam(':uneDateHF', $date, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
       $requetePrepare->bindParam(':idFHF', $idFHF, PDO::PARAM_STR);
       $requetePrepare->execute();
   }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '//:unIdVisiteur est le correspondant sql de idvisiteur (=car idvisiteur ne passe pas en sql,
            						    // et cela "correspond" aussi au champ idvisiteur de la BDD)
	    . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;//par défaut c false => il y a une fiche de frais
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetchAll()) {
            $boolReturn = true;//true => il n'y a pas une fiche de frais
        }
        return $boolReturn;//retourne la valeur de la variable
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();//elle retourne le résultat dans la ligne
        $dernierMois = $laLigne['dernierMois'];//on met le résultat de la requête (qui est ds $la ligne) dans la variable $dernier mois
					       //'dernier mois' entre crochets correspond à l'alias du champ "mois" de la req sql (= cf SELECT)
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);//la variable $dernierMois est égale au résultat de la méthode dernierMoisSaisi()
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);//elle récupère le résultat de la méthode getLesInfosFicheFrais()
        if ($laDerniereFiche['idEtat'] == 'CR') { //si la dernière fiche de frais est à l'état CR = en cours
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL'); //alors il faudra appeler la méthode EtatFicheFrais() la mettre à jr => à l'état cloturé et ainsi le comptable pourrait continuer
            
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(//il crée une new fiche de frais
            'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
            . 'montantValide,dateModif,idEtat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        $lesIdFrais = $this->getLesIdFrais();//Cette req régénère l'association ligneFraisForfait : elle met une new page pr le prochain mois
        				     //(lien ac slam 3: association car 2 clés primaires)
	foreach ($lesIdFrais as $unIdFrais) {//boucle foreach => pr chaque ligne
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idFraisForfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant) 
    {
        $dateFr = dateFrancaisVersAnglais($date);//convetit la date du fr à l'anglais, et le résultat rentre dans $dateFr
        $requetePrepare = PdoGSB::$monPdo->prepare(//interroger la BDD PdoGSB
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '//ca va rentrer ces infos dans la table lignefraishorsforfait
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);//c pr faire la correspondance entre la "variable" en PHP et celle en sql
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $requetePrepare = PdoGSB::$monPdo->prepare(//elle va  récupérer le mois pr un visiteur donné
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                . 'ORDER BY fichefrais.mois desc'//trie les mois ds l'ordre décroissant (du plus recent au moins recent)
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute(); //ca exécute la req

        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {//exécuter
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4); //substr() c pr extraire l'année (en anglais c aaaa mm)
            $numMois = substr($mois, 4, 2); //on extrait le mois
            $lesMois[] = array(//tableau ac 3 colonnes   $lesMois['$mois'] = ar
                'mois' => $mois, //tte la date (aaaa mm)
                'numAnnee' => $numAnnee, //que l'année
                'numMois' => $numMois//que le mois
            );
        }
        return $lesMois;
    }
    
    /**
    * Retourne les informations d'une fiche de frais d'un visiteur pour un
    * mois donné, on s'en sert pour vérifier s'il y a des infos pour envoyer un msg d'erreur si pas de fiches, et pour changer l'etat de la fiche
    *
    * @param String $idVisiteur ID du visiteur
    * @param String $mois       Mois sous la forme aaaamm
    *
    * @return un tableau avec des champs de jointure entre une fiche de frais
    *         et la ligne d'état
    */
   public function getLesInfosFicheFrais($idVisiteur, $mois)
   {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT ficheFrais.idEtat as idEtat, '
           . 'ficheFrais.dateModif as dateModif,'
           . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
           . 'ficheFrais.montantValide as montantValide, '
           . 'etat.libelle as libEtat '
           . 'FROM fichefrais '
           . 'INNER JOIN Etat ON ficheFrais.idEtat = Etat.id '
           . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
           . 'AND fichefrais.mois = :unMois'
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);//permet de definir que le mdp et le login envoyés en paramètre correspondent à ceux récupérés de la bdd par la requete sql.
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->execute();
       $laLigne = $requetePrepare->fetch();
       return $laLigne;
   }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idEtat = :unEtat, dateModif = now() '//fonction php qui va chercher la date actuelle sur l'ordi
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
   /**
    * Permet de selectionner un visiteur
    * @return array              Retourne la liste des visiteurs sous forme de tableau
    */
    public function getChoisirVisiteur() //cette fonction permet au comptable de choisir un visiteur
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT *'
                . 'FROM visiteur'
                //. 'ORDER BY nom'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();

    }
    
    /**
     * Insère le montant valide dans la colonne montantvalide de la table fichefrais pour un visiteur et un mois donnés
     * @param char $idVisiteur
     * @param int $mois
     * @param float $montantValide
     */
    
    public function setMontantValide($idVisiteur, $mois, $montantValide)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(                 
            'UPDATE fichefrais'
            .' SET fichefrais.montantvalide = :montantValide'
            .' WHERE fichefrais.idvisiteur = :unIdVisiteur'
            .' AND mois = :unMois'
        );       
        $requetePrepare->bindParam(':montantValide', $montantValide, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    } 
   
    /**
     * Retourne la somme des montants FHF
     * @param int $idVisiteur ID du visiteur
     * @param int $mois       Mois sous la forme aaaamm
     * @return array          Montant des FHF
     */
    
    public function getMontantFHF($idVisiteur, $mois) 
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraishorsforfait.montant)'
            .' FROM lignefraishorsforfait'
            .' WHERE idvisiteur = :unIdVisiteur'
            .' AND mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $montantFHF=$requetePrepare->fetchAll();
        return $montantFHF;
    }
    
    /**
     * Retourne la somme des montants des frais forfaits pour un visiteur et un mois donnés
     * @param string $idVisiteur            ID du visiteur
     * @param string $mois                  Mois sous la forme aaaamm
     * @return array                        Montant des FF
     */
    
    public function getMontantFF($idVisiteur, $mois)
    {
         $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraisforfait.quantite * fraisforfait.montant)'
            .' FROM lignefraisforfait JOIN fraisforfait ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            .' WHERE idvisiteur = :unIdVisiteur'
            .' AND mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $montantFF=$requetePrepare->fetchAll();
        return $montantFF;
    }
    
    
    /**
     * Modifie le libellé des frais hors forfait, ajoute la mention "refusé" devant le libellé
     * @param char $idVisiteur Id du visiteur
     * @param int $leMois      Mois sous la forme aaaamm
     * @param int $date        Date au format français jj/mm/aaaa
     * @param int $idFHF       Id du FHF
     */
    
    public function majLibelleFraisHorsForfait($idVisiteur,$leMois, $idFHF) {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'UPDATE lignefraishorsforfait '
               . 'SET lignefraishorsforfait.libelle = CONCAT("Refusé: ", libelle)'
               . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
               . 'AND lignefraishorsforfait.mois = :unMois '
               . 'AND id=:idFHF'
           );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);
       $requetePrepare->bindParam(':idFHF', $idFHF, PDO::PARAM_INT);
       $requetePrepare->execute();
   }
   
   /**
    * Donne le type de véhicule, son id et le prix au Km
    * @param int $idVisiteur
    * @param int $idVehicule
    */
   
   public function calculPrixKm($idVisiteur, $idVehicule) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT idVehicule, type, prixKm '
                . 'FROM vehicule JOIN visiteur'
                . 'ON visiteur.idVehicule = vehicule.idVehic'
           ); 
        $requetePrepare->execute();

   }
   /**
    * Donne tous les mois dont la fiche est validée
    * @return array     Retourne sous la forme d'un tableau tous les moins dont la fiche est validée
    */
   public function getMoisDontFicheVA(){
       $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT mois '
             .' FROM fichefrais'
             .' WHERE idetat= "VA"'
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {//exécuter
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);//substr() c pr extraire l'année (en anglais c aaaa mm)
            $numMois = substr($mois, 4, 2);//on extrait le mois
            $lesMois[$mois] = array(//tableau ac 3 colonnes
                'mois' => $mois,//tte la date (aaaa mm)
                'numAnnee' => $numAnnee,//que l'année
                'numMois' => $numMois//que lel mois
            );
        }
        return $lesMois;
   }

   /**
    * Donne tous les visiteurs dont la fiche est validée
    * @return array    Retourne sous forme de tableau tous les visiteurs dont la fiche est validée
    */
   public function getVisiteurDontFicheVA(){
       $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT *'
               .' FROM visiteur JOIN fichefrais '
               . 'ON visiteur.id = fichefrais.idvisiteur'
               .' WHERE  idetat= "VA"'
             //.' GROUP BY visiteur.nom'
        );
        $requetePrepare->execute(); 
        return $requetePrepare->fetchAll();
   }
   
   /**
    * Insère le nombre de justificatifs rentré par le comptable,
    * dans la colonne nbjustificatifs de la table fichefrais 
    *
    * @param int $idVisiteur
    * @param int $mois
    * @param int $nbJustificatifs
    */
   public function setNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
        $requetePrepare = PdoGSB::$monPdo->prepare(
             'UPDATE fichefrais'
            .' SET fichefrais.nbjustificatifs= :leNbJustificatifs'
            .' WHERE idvisiteur = :unIdVisiteur'
            .' AND mois = :unMois'
        );       
        $requetePrepare->bindParam(':leNbJustificatifs', $nbJustificatifs, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

   }
}
