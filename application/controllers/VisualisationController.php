<?php

class VisualisationController extends Zend_Controller_Action
{

    public function visualisationAction(){
        $this->initInstance();
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