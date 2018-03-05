<?php

class VisualisationController extends Zend_Controller_Action
{

    var $idBase = "iutparishebddem";

    public function visualisationAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);

        $arrayRes = $this->s->dbR->getEmotions();

        $resultJSON = json_encode($arrayRes, JSON_PRETTY_PRINT);
        
        $this->view->resultJSON = $resultJSON;

    }

    function initInstance($action=""){
        $session =  session_start();
        // $auth = Zend_Auth::getInstance();
        $role = $_SESSION["role"];
        if ($role == "enseignant" && $session) {						
            
            if ($session) {						
            // l'identité existe ; on la récupère
            $this->view->identite = $_SESSION["user"];
            // $ssUti = new Zend_Session_Namespace('uti');
            // $this->view->uti = json_encode($ssUti->uti);
        }
        else if (!isset($role)){
            $this->_redirect('/auth/cas');
        }
        else{			
            //$this->view->uti = json_encode(array("login"=>"inconnu", "id_uti"=>0));
            $this->_redirect('/auth/finsession');		    
        }


        if($this->_getParam('idBase')) $this->idBase = $this->_getParam('idBase', $this->idBase);
        if($this->_getParam('idUti')) $this->idUti = $this->_getParam('idUti', 1);
        $this->idGeo = $this->_getParam('idGeo',-1);
        
        $this->view->idBase = $this->idBase;
        $this->view->idGeo = $this->idGeo;
        $this->view->langue = $this->_getParam('langue','fr');
                
        }
    }
}