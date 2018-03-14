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

    public function visualisationAction(){
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);

        $arrayRes = $this->s->dbR->getEmotions();
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
        


        $message = "";
        // //chercher si combinaisons emotions+date est dans le tableau ???
        // foreach ($arrayEmotions as $emotion) {
        //     foreach ($arrayDate as $date ) {
        //         $test = array();
        //         $test = array_intersect_key($arrayRes, array(array("key" => $emotion,"value" => "", "date" => $date)));
        //         if(empty($test)){
        //             $message .= "pour emotion : ".$emotion." et date : ".$date." il faut ajouter 0 <br>";
        //         }
        //         else {
        //             $message .= "emotion : ".$emotion." et date : ".$date." présente <br>";
        //         }


        //     }
        // }
        
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
            $zeb = $arrayRes[$i]["date"]; 
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

        $arrayFin = array();



        $resultJSON = json_encode($arrayRes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $this->view->resultJSON = $resultJSON;
        $this->view->dateJSON = json_encode($arrayDate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->emotionsJSON = json_encode($arrayEmotions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->view->message = $message;

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
}