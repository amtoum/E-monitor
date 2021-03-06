<?php
/**
 * CarteController
 *
 * Porte d'entrée du jardin des connaissances
 *
 * @author Samuel Szoniecky
 * @category   Zend
 * @package Zend\Controller\Projet
 * @license https://creativecommons.org/licenses/by-sa/2.0/fr/ CC BY-SA 2.0 FR
 * @version  $Id:$
 */
class CarteController extends Zend_Controller_Action
{

    var $idBase = "iutparishebddem";
    
    public function init()
    {
    	
    }

    public function indexAction()
    {
    	
    	
    }

    public function roueemotionAction(){
        $this->initInstance();
        $dt = "[
	      	{id:'tspan4022',en:'disapproval',fr:'désapprobation',color:'#ffffff',value:0}
	      	,{id:'tspan4026',en:'remorse',fr:'remord',color:'#ffffff',value:0}
	      	,{id:'tspan4030',en:'contempt',fr:'mépris',color:'#ffffff',value:0}
	      	,{id:'tspan4034',en:'awe',fr:'crainte',color:'#ffffff',value:0}
	      	,{id:'tspan4038',en:'submission',fr:'soumission',color:'#ffffff',value:0}
	      	,{id:'tspan4042',en:'love',fr:'amour',color:'#ffffff',value:0}
	      	,{id:'tspan4046',en:'optimism',fr:'optimisme',color:'#ffffff',value:0}
	      	,{id:'tspan4050',en:'aggressiveness',fr:'aggressivité',color:'#ffffff',value:0}
	      	,{id:'tspan3007',en:'pensiveness',fr:'songerie',color:'#8c8cff',value:0}
	      	,{id:'tspan3836',en:'annoyance',fr:'gêne',color:'#ff8c8c',value:0}
	      	,{id:'tspan3840',en:'anger',fr:'colère',color:'#ff0000',value:0}
	      	,{id:'tspan3844',en:'rage',fr:'rage',color:'#d40000',value:0}
	      	,{id:'tspan3891',en:'ecstasy',fr:'extase',color:'#ffe854',value:0}
	      	,{id:'tspan3895',en:'joy',fr:'joie',color:'#ffff54',value:0}
	      	,{id:'tspan3899',en:'serenity',fr:'sérénité',color:'#ffffb1',value:0}
	      	,{id:'tspan3903',en:'terror',fr:'terreur',color:'#008000',value:0}
	      	,{id:'tspan3907',en:'fear',fr:'peur',color:'#009600',value:0}
	      	,{id:'tspan3911',en:'apprehension',fr:'appréhension',color:'#8cc68c',value:0}
	      	,{id:'tspan3915',en:'admiration',fr:'adoration',color:'#00b400',value:0}
	      	,{id:'tspan3919',en:'trust',fr:'confiance',color:'#54ff54',value:0}
	      	,{id:'tspan3923',en:'acceptance',fr:'résignation',color:'#8cff8c',value:0}
	      	,{id:'tspan3927',en:'vigilance',fr:'vigilance',color:'#ff7d00',value:0}
	      	,{id:'tspan3931',en:'anticipation',fr:'excitation',color:'#ffa854',value:0}
	      	,{id:'tspan3935',en:'interest',fr:'intérêt',color:'#ffc48c',value:0}
	      	,{id:'tspan3939',en:'boredom',fr:'ennui',color:'#ffc6ff',value:0}
	      	,{id:'tspan3943',en:'disgust',fr:'dégoût',color:'#ff54ff',value:0}
	      	,{id:'tspan3947',en:'loathing',fr:'aversion',color:'#de00de',value:0}
	      	,{id:'tspan3951',en:'amazement',fr:'stupéfaction',color:'#0089e0',value:0}
	      	,{id:'tspan3955',en:'surprise',fr:'surprise',color:'#59bdff',value:0}
	      	,{id:'tspan3959',en:'distraction',fr:'distraction',color:'#a5dbff',value:0}
	      	,{id:'tspan3828',en:'sadness',fr:'tristesse',color:'#5151ff',value:0}
	      	,{id:'tspan3832',en:'grief',fr:'chagrin',color:'#0000c8',value:0}
	      	]";
        
        $this->view->data =  $this->_getParam('data', $dt);
        $this->view->w =  $this->_getParam('w', 0);
        $this->view->h =  $this->_getParam('h', 0);
        $this->view->langue =  $this->_getParam('langue', "fr");
        $this->view->titre =  $this->_getParam('titre', "Roue des émotions");
    }  
    
    public function emotionsAction(){
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        $this->s->dbu = new Model_DbTable_Flux_Uti($this->s->db);
        // $this->view->data =  $this->_getParam('data', $dt);
        // $this->view->w =  $this->_getParam('w', 0);
        // $this->view->h =  $this->_getParam('h', 0);
        // $this->view->langue =  $this->_getParam('langue', "fr");
        // $this->view->titre =  $this->_getParam('titre', "Roue des émotions");
        if ($_SESSION["user"]){
            $utiId = $this->s->dbu->existe(array('login'=>$_SESSION["user"]));
            $formation = $this->s->dbE->getFormationById($utiId,$this->getNait(New DateTime('now')));
            $this->view->formation = $this->_getParam('formation', $formation );
            $infoExi = $this->s->dbE->findByUtiID($utiId);
            $nomPrenom = $infoExi["nom"]." ".$infoExi["prenom"];
            $this->view->user =  $this->_getParam('user', $nomPrenom );
        }
    }

    public function saverepquestAction(){
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        
        
        $this->idMonade = $this->s->dbM->ajouter(array("titre"=>"E-monitor"),true,false);
        $this->idDocEvalRoot = $this->s->dbD->ajouter(array("titre"=>"évaluations"));
        
        //enregistre l'émotion évaluée
        if($this->_getParam('emo')){
            $this->saveRepEmo($this->_getParam('emo'),$this->idDocEvalRoot);
            $this->view->message = "Emotion enregistrée.";
        }
        
        //enregistre toutes les émotions du donuts
        if($this->_getParam('roueData')){
            $data = $this->_getParam('roueData');
            //enregistre chaque émotion
            $idDocEval = $this->s->dbD->ajouter(array("titre"=>"Evaluation roue émotion","parent"=>$this->idDocEvalRoot,"data"=>json_encode($data)));
            foreach ($data as $emo) {
                $this->saveRepEmo($emo,$idDocEval);
            }
            $this->view->message = "Emotions enregistrées.";
        }
       
    }
    

    public function saverepd3Action(){
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbU = new Model_DbTable_Flux_Uti($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        
        
        $this->idMonade = $this->s->dbM->ajouter(array("titre"=>"E-monitor"),true,false);
        $this->idDocEvalRoot = $this->s->dbD->ajouter(array("titre"=>"évaluationsSVG"));
        
        $checkSave = $this->autoriserSave();
        if ($checkSave[0] && $this->_getParam('emotions')){
            $data = $this->_getParam('emotions');
            
            //enregistre chaque émotion
            foreach ($data as $emo=>$value) {
                $this->saveRepEmoD3($emo,$value);
            }
        }
        $this->view->message = $checkSave[1];

       
    }
    function saveRepEmoD3($emo,$value){
        $idTag = $this->s->dbT->ajouter(array("code"=>$emo));
        //enregistre la réponse à la question par l'utilistaeur
        // $intensite = preg_replace('/[^0-9]/', '', $emo["name"]);
        $idRapRep = $this->s->dbR->ajouter(array("monade_id"=>$this->idMonade,"geo_id"=>$this->idGeo
            ,"src_id"=>$idTag,"src_obj"=>"tag"
            ,"pre_id"=>$this->idDocEvalRoot,"pre_obj"=>"doc"
            ,"dst_id"=>$this->s->dbU->existe(array("login" => $_SESSION["user"])),"dst_obj"=>"uti"
            ,"niveau"=>$value
        ),false);
    }

    function saveRepEmo($emo,$idDocEval){
        $idTag = $this->s->dbT->ajouter(array("code"=>$emo["fr"]));
        //enregistre la réponse à la question par l'utilistaeur
        $idRapRep = $this->s->dbR->ajouter(array("monade_id"=>$this->idMonade,"geo_id"=>$this->idGeo
            ,"src_id"=>$idTag,"src_obj"=>"tag"
            ,"pre_id"=>$idDocEval,"pre_obj"=>"doc"
            ,"dst_id"=>$this->idDocEvalRoot,"dst_obj"=>"doc"
            ,"niveau"=>$emo["value"]
        ),false);
    }

    function autoriserSave(){
        //récupérer le jour de la semaine en français
        setlocale(LC_TIME, "fr_FR");
        
        //récupérer jours de saisie autorisés
        $dansJours = false;
        $joursSaisie = $this->s->dbA->findByCode("joursSaisie");
        $joursSaisie = explode(";",$joursSaisie[0]["desc"]);
        // check si dans les jours de saisie autorisés
        $jour = strftime("%A");
        if (in_array($jour,$joursSaisie))
            $dansJours = true;
            
            // echo strftime(" in French %A and");
            $heure =  strftime("%H:%M");
            $dansTemps = false;
            //récupérer temps de saisie autorisés
            $tempsSaisie = $this->s->dbA->findByCode("tempsSaisie");
            $tempsSaisie = explode(";",$tempsSaisie[0]["desc"]);
            $debutDemiJ;
            $finDemiJ;
            // check si dans les temps de saisie autorisés
            foreach($tempsSaisie as $partieTempsSaisie){
                $limite = explode(",",$partieTempsSaisie);
                $date1 = DateTime::createFromFormat('H:i', $heure);
                $date2 = DateTime::createFromFormat('H:i', $limite[0]);
                $date3 = DateTime::createFromFormat('H:i', $limite[1]);
                if ($date1 > $date2 && $date1 < $date3){
                    $dansTemps = true;
                    $debutDemiJ = $date2;
                    $finDemiJ = $date3;
                }
            }
            
        if (!$dansJours) 
            return array(false, "Jour de saisie non autorisé.\nReviens lundi matin pour enregistrer tes émotions du jour.\nVoir l'aide pour plus d'informations.");
        else if (!$dansTemps)    
            return array(false, "Saisie non autorisée.\nReviens à la prochaine demie-journée pour enregistrer tes émotions.\nVoir l'aide pour plus d'informations.");
        
        //récupérer dernière date de saisie
        $utiId = $this->s->dbU->existe(array("login" => $_SESSION["user"]));
        $lastEntry = $this->s->dbR->getTimeMostRecentEntryByUtiId($utiId);
        $jour2 = "";
        if ($lastEntry != 0){
            $lastEntry = new DateTime($lastEntry);
            $jour2 = strftime("%A",$lastEntry->getTimeStamp());
        }
        if($jour2 == $jour && $lastEntry > $debutDemiJ && $lastEntry < $finDemiJ  )
            return array(false, "Saisie déjà faite pour cette demie-journée.\nReviens à la prochaine demie-journée pour enregistrer tes émotions.\nVoir l'aide pour plus d'informations.");

        return array(true, "Emotions enregistrées.");
    }
    
    public function aideAction(){
        //aide
    }

    //pour l'authentification
    function initInstance($action=""){

        $session =  session_start();
        // $auth = Zend_Auth::getInstance();
        $role = $_SESSION["role"];
		if (strpos($role,"etudiant") !== false && $session) {						
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



