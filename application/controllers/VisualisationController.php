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
    public function visualisationAction(){
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
            $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
            $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
    
            $arrayRes = $this->s->dbR->getEmotions();
            $now = new DateTime('now');
            $arrayFormation = $this->s->dbE->getFormationsAnnee($this->getNait($now));
            $this->view->formations = json_encode($arrayFormation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
        if ($session && ($user=="atoumia" || $user=="louaprem")) {						
            //TODO: modifier la ligne au dessus après création espace enseignant
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
        $arrayRes = $this->s->dbR->getEmotions($dateDebut,$dateFin,$formations);

        $arr = $this->formatEmotions($arrayRes);
        
        $this->view->rs = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->view->resultJSON = json_encode($arr["arrayRes"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->dateJSON = json_encode($arr["arrayDate"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->emotionsJSON = json_encode($arr["arrayEmotions"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


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