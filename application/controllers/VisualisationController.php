<?php

class VisualisationController extends Zend_Controller_Action
{

    var $idBase = "iutparishebddem";

    public function cmp($a, $b)
    {
        $a = date('Y-m-d H:i:s', strtotime($a));
        $b = date('Y-m-d H:i:s', strtotime($b));

        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    /**
     * normalise le tableau des émotions en remplissant les dates manquantes 
     *
     * @param array $arrayRes
     * @return array tableau normalisé
     */
    public function formatEmotions($arrayRes){
        $arrayDate = array();
        $arrayEmotions = array();
        foreach ($arrayRes as $data) {
            if (!in_array($data['date'],$arrayDate)){
                array_push($arrayDate,$data['date']);
            }
            if (!in_array($data['key'],$arrayEmotions)){
                array_push($arrayEmotions,$data['key']);
            }
        }
        usort($arrayDate, array($this,"cmp"));
        
        $emStock = $arrayRes[0]["key"];
        $i = $j = 0;

        //ajouter les dates manquantes dans arrayRes
        while (($i < count($arrayRes) || $j < count($arrayDate))){
            if ($emStock != $arrayRes[$i]["key"]){
                if ( $j == count($arrayDate)){
                    $emStock = $arrayRes[$i]["key"];
                    $j = 0;
                }
            }
            
            if ($arrayRes[$i]["date"] != $arrayDate[$j] ){
                array_splice($arrayRes, $i, 0, array(array("key" => $emStock, "value"=>"0", "date" => $arrayDate[$j])));
                $i++;
                $j++;
            }
            else{
                $i++;
                $j++;
            }
        }
        return array ("arrayRes" => $arrayRes, "arrayDate" => $arrayDate, "arrayEmotions" => $arrayEmotions);
    }

    /**
     * récupère les données pour le streamgraph
     * Appelle getdatastreamAction si y a date début et fin 
     * sinon récupère toutes les infos 
     *
     * @return void
     */
    public function indexAction(){
        
        
        //si date début et date fin sont spécifiées (saisies dans la vue)
        if ($this->_getParam('dateDebut')&& $this->_getParam('dateFin')){
            $this->getdatastreamAction();
        }
        else {
            $this->initInstance();
            
            $this->s = new Flux_Site($this->idBase);
            $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
            $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
            $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
            $this->s->dbU = new Model_DbTable_Flux_Uti($this->s->db);
            $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
            
            if ($_SESSION["user"]){
                $utiId = $this->s->dbU->existe(array('login'=>$_SESSION["user"]));
                // $formation = $this->s->dbE->getFormationById($utiId);
                // $this->view->formation = $this->_getParam('formation', $formation );
                $infoExi = $this->s->dbE->findByUtiID($utiId);
                $nomPrenom = $infoExi["nom"]." ".$infoExi["prenom"];
                $this->view->user =  $this->_getParam('user', $nomPrenom );
                if ($_SESSION["role"]){
                    $this->view->role = $this->_getParam('role',$_SESSION["role"]);
                }
            }

            $arrayRes = $this->s->dbR->getEmotions();
            $now = new DateTime('now');
            $arrayFormation = $this->s->dbE->getFormationsAnnee($this->getNait($now));
            $this->view->formations = json_encode($arrayFormation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $arrayEmos = $this->s->dbT->getAllTags();
            $this->view->emos = json_encode($arrayEmos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $arr = $this->formatEmotions($arrayRes);
            
            $this->view->resultJSON = json_encode($arr["arrayRes"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $this->view->dateJSON = json_encode($arr["arrayDate"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $this->view->emotionsJSON = json_encode($arr["arrayEmotions"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }


    }

    function initInstance($action=""){
        $session =  session_start();
        // $auth = Zend_Auth::getInstance();
        $role = $_SESSION["role"];
        $user = $_SESSION["user"];
		// if ($role == "admin" && $session) {						
        if ((strpos($role,"enseignant") !== false || strpos($role,"admin") !== false) && $session) {						
            // if ($session) {						
			// l'identité existe ; on la récupère
		    $this->view->identite = $_SESSION["user"];
		    // $ssUti = new Zend_Session_Namespace('uti');
            // $this->view->uti = json_encode($ssUti->uti);
            if($this->_getParam('idBase')) $this->idBase = $this->_getParam('idBase', $this->idBase);
            if($this->_getParam('idUti')) $this->idUti = $this->_getParam('idUti', 1);
            $this->idGeo = $this->_getParam('idGeo',-1);
            
            $this->view->idBase = $this->idBase;
            $this->view->idGeo = $this->idGeo;
            $this->view->langue = $this->_getParam('langue','fr');
        }
        else{			
            $this->_redirect('/auth/cas');
        }           
    }

    /**
     * Récupère les données pour le streamgraph en utilisant les dates de 
     * début et de fin
     *
     * @return void
     */
    public function getdatastreamAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);

        $dateDebut = $this->_getParam('dateDebut');
        $dateFin = $this->_getParam('dateFin');
        // $formations = implode(",",$this->_getParam('formationSel'));
        $formations = $this->_getParam('formationSel');
        $emos = $this->_getParam('emos');
        $arrayRes = $this->s->dbR->getEmotions($dateDebut,$dateFin,$formations,$emos);

        $arr = $this->formatEmotions($arrayRes);
        
        $this->view->rs = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->view->resultJSON = json_encode($arr["arrayRes"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->dateJSON = json_encode($arr["arrayDate"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->emotionsJSON = json_encode($arr["arrayEmotions"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


    }


    /**
     * Récupère les données brutes pour l'export csv en utilisant les dates de 
     * début et de fin et autres filtres
     *
     * @return void
     */
    public function exportdatastreamAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);

        $dateDebut = $this->_getParam('dateDebut');
        $dateFin = $this->_getParam('dateFin');
        // $formations = implode(",",$this->_getParam('formationSel'));
        $formations = $this->_getParam('formationSel');
        $emos = $this->_getParam('emos');
        $resultJSON = $this->s->dbR->getEmotionsExport($dateDebut,$dateFin,$formations,$emos);

        $this->view->resultJSON = json_encode($resultJSON, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // $name = 'export'.time().'.csv';
        // $filename = getcwd().'/../data/export/'.$name;
        // // $f = fopen('php://output', 'w');
        // $f = fopen($filename, 'w');
        // $firstLineKeys = false;
        // foreach ($arrayRes as $line)
        // {
        //     if (empty($firstLineKeys))
        //     {
        //         $firstLineKeys = array_keys($line);
        //         fputcsv($f, $firstLineKeys);
        //         $firstLineKeys = array_flip($firstLineKeys);
        //     }
        //     // Using array_merge is important to maintain the order of keys acording to the first element
        //     fputcsv($f, array_merge($firstLineKeys, $line));
        // }

        
        // $this->_helper->viewRenderer->setNoRender(true);
        // header('Content-Description: File Transfer');
        // header('Content-Type: text/csv; charset=utf-8');
        // header("Content-Disposition: attachment; filename=" . $filename);
        // header("Content-Length: " . filesize( $filename ));
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-control: private, must-revalidate');
        // header("Pragma: public");
        
        // fclose($f);

        // $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender();
        // $this->getResponse()->setRawHeader( "Content-Type: application/csv; charset=UTF-8" )
        //     ->setRawHeader( "Content-Disposition: attachment; filename=".$filename )
        //     ->setRawHeader( "Content-Transfer-Encoding: binary" )
        //     ->setRawHeader( "Expires: 0" )
        //     ->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
        //     ->setRawHeader( "Pragma: public" )
        //     ->setRawHeader( "Content-Length: " . filesize( $filename ) )
        //     ->sendResponse();
        // readfile( $filename ); exit();

        // $arr = $this->formatEmotions($arrayRes);
        
        // $this->view->rs = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // $this->view->resultJSON = json_encode($arr["arrayRes"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // $this->view->dateJSON = json_encode($arr["arrayDate"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // $this->view->emotionsJSON = json_encode($arr["arrayEmotions"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


    }

    /**
     * récupère les infos sur une date et une émotion donnée et les renvoie à la vue
     * pour afficher dans le grid
     *
     * @return void
     */
    public function getemotiondateAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);

        $date = new DateTime($this->_getParam('date'));
        $emotion = $this->_getParam('emotion');

        //récupérer date de début et de fin de la demie journée pour une date donnée

        //récupérer temps de saisie autorisés
        $tempsSaisie = $this->s->dbA->findByCode("tempsSaisie");
        $tempsSaisie = explode(";",$tempsSaisie[0]["desc"]);
        $debutDemiJ;
        $finDemiJ;
        // check si dans les temps de saisie autorisés
        foreach($tempsSaisie as $partieTempsSaisie){
            $limite = explode(",",$partieTempsSaisie);
            // $date1 = DateTime::createFromFormat('H:i', strftime("%H:%M"));
            $date1 = DateTime::createFromFormat('H:i',$date->format("H:i"));
            $date2 = DateTime::createFromFormat('H:i', $limite[0]);
            $date3 = DateTime::createFromFormat('H:i', $limite[1]);
            if ($date1 >= $date2 && $date1 <= $date3){
                $debutDemiJ = $date2;
                $finDemiJ = $date3;
            }
        }

        $result = $this->s->dbR->getEmotionsByDate($date->format("Y-m-d"),$debutDemiJ->format("H:i"),$finDemiJ->format("H:i"),$emotion);
        $this->view->rs = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * renvoi des infos avec les étudiants pour une date et une émotion donnée
     * et le renvoi vers la vue pour un affichage dans la grid
     *
     * @return void
     */
    public function identifieretudiantsAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        $this->s->dbU = new Model_DbTable_Flux_Uti($this->s->db);

        $date = new DateTime($this->_getParam('date'));
        $emotion = $this->_getParam('emotion');

        //récupérer date de début et de fin de la demie journée pour une date donnée

        //récupérer temps de saisie autorisés
        $tempsSaisie = $this->s->dbA->findByCode("tempsSaisie");
        $tempsSaisie = explode(";",$tempsSaisie[0]["desc"]);
        $debutDemiJ;
        $finDemiJ;
        // check si dans les temps de saisie autorisés
        foreach($tempsSaisie as $partieTempsSaisie){
            $limite = explode(",",$partieTempsSaisie);
            // $date1 = DateTime::createFromFormat('H:i', strftime("%H:%M"));
            $date1 = DateTime::createFromFormat('H:i',$date->format("H:i"));
            $date2 = DateTime::createFromFormat('H:i', $limite[0]);
            $date3 = DateTime::createFromFormat('H:i', $limite[1]);
            if ($date1 >= $date2 && $date1 <= $date3){
                $debutDemiJ = $date2;
                $finDemiJ = $date3;
            }
        }

        $result = $this->s->dbR->getEmotionsByDate($date->format("Y-m-d"),$debutDemiJ->format("H:i"),$finDemiJ->format("H:i"),$emotion);

        $role = $_SESSION["role"];
        $user = $_SESSION["user"];

        //get id enseignant
        $utiId = $this->s->dbU->existe(array('login'=>$_SESSION["user"]));
        //get formations de l'enseignant pour la date donnée
        $nait = $this->getNait($date);
        $formations = $this->s->dbE->getFormationsProfById($utiId,$nait);

        //parcourir result et voir pour chacun des rapports si la formation de l'idEtu est dans
        //les formations de l'enseignant 
        foreach ($result as $key=>$entree){
            $found = false;
            $formEtu = $this->s->dbE->getIdFormationById($entree["idEtu"],$nait);
            if (strpos($role,"admin")!==false){ //l'admin peut lever l'anonymat sur tout
                $infoExi = $this->s->dbE->findByUtiID($entree["idEtu"]);
                $result[$key]["nom"] = $infoExi["nom"];
                $result[$key]["prenom"] =$infoExi["prenom"];
                $result[$key]["formation"] = $this->s->dbE->getFormationById($entree["idEtu"],$nait);
            }
            else if (strpos($role,"admin")!==false){
                foreach ($formations as $key1=>$field){
                    if ($formEtu == $field['pre_id']){
                        $found = $key1;
                    }
                }
                if ($found !== false){
                    $infoExi = $this->s->dbE->findByUtiID($entree["idEtu"]);
                    $result[$key]["nom"] = $infoExi["nom"];
                    $result[$key]["prenom"] =$infoExi["prenom"];
                    $result[$key]["formation"] = $this->s->dbE->getFormationById($entree["idEtu"],$nait);
                }
                else {
                    $result[$key]["nom"] = '*****';
                    $result[$key]["prenom"] ='*****';
                    $result[$key]["formation"] = '*****';
                }
            }
        }

        $this->view->rs = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);        

    }


    /**
     * renvoie la date de naissance (généralement pour une formation )
     *
     * @param datetime $date
     * @return datetime
     */
    private function getNait($date){
        $month = intval($date->format('n'),10);
        if ($month>=1 && $month<9 ){// si le mois est janvier ... aout
            $dateNait = new DateTime((intval($date->format('Y')-1,10)).'-09-01');
        } 
        else{
            $dateNait = new DateTime($date->format('Y').'-09-01');
        }
        return $dateNait->format('Y-m-d');
    }

}