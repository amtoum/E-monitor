<?php

/**
 * @author Amri Toumia
 * @category   Zend
 * @package Zend\Controller\Projet
 * @license https://creativecommons.org/licenses/by-sa/2.0/fr/ CC BY-SA 2.0 FR
 * @version  $Id:$
 */
class AdminController extends Zend_Controller_Action {
 

    
    
    var $idBase = "iutparishebddem";
    
    public function init()
    {
        
    }
    
    public function indexAction()
    {

        $this->redirect('/admin/importcsv');

//         $this->initInstance();
        
//         $this->s = new Flux_Site($this->idBase);
//         $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
//         $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
//         $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
//         $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
//         $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
//         $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
//         $this->s->dbu = new Model_DbTable_Flux_Uti($this->s->db);
        
//         $arrayRes = $this->s->dbT->getRecidAll();
        
// //         $arrayRes = $this->s->dbT->fetchAll($query)->toArray();
//         $resJSON = json_encode($arrayRes, JSON_PRETTY_PRINT);
        
//         $this->view->resJSON = $resJSON;
//         if ($_SESSION["user"]){
//             $utiId = $this->s->dbu->existe(array('login'=>$_SESSION["user"]));
//             $infoExi = $this->s->dbE->findByUtiID($utiId);
//             $nomPrenom = $infoExi["nom"]." ".$infoExi["prenom"];
//             $this->view->user =  $this->_getParam('user', $nomPrenom );
//         }
    }
    
    public function importcsvAction()
    {
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        $this->s->dbu = new Model_DbTable_Flux_Uti($this->s->db);
        
        //get du parametre resJSON reçu après upload
        if ($this->_getparam('resJSON',false)){
            $this->view->resJSON = $this->_getparam('resJSON');
        }
        //get du parametre selection (sidebar) reçu après upload
        if ($this->_getparam('selection',false)){
            $this->view->selection = $this->_getparam('selection');
        }

        if ($_SESSION["user"]){
            $utiId = $this->s->dbu->existe(array('login'=>$_SESSION["user"]));
            $infoExi = $this->s->dbE->findByUtiID($utiId);
            $nomPrenom = $infoExi["nom"]." ".$infoExi["prenom"];
            $this->view->user =  $this->_getParam('user', $nomPrenom );
        }
        // Import csv et echo
        
        // $this->view->titre =  $this->_getParam('titre', "Page Test admin");
        // $file="table.csv";
        // $delimiter = ",";
        // // $csv= file_get_contents($file);
        // // $array = array_map("str_getcsv", explode("\n", $csv));
        // // $json = json_encode($array);
        // $json = $this->csvtojson($file,$delimiter);
        // echo $json;
    }

    function csvtojson($file,$delimiter,$addrecid = false)
    {
        if (($handle = fopen($file, "r")) === false)
        {
                die("can't open the file.");
        }
    
        $csv_headers = fgetcsv($handle, 4000, $delimiter);
        $csv_json = array();
        while ($row = fgetcsv($handle, 4000, $delimiter))
        {
        
            $csv_json[] = array_combine($csv_headers, $row);
            // $csv_json["id"]= $i;
            // $recid = '{"recid" : "$i"}';
            // $temprecid = json_decode ($recid,true)
            // $csv_json[] = array_unshift($csv_json[], '{ "recid" : "'.$i.'" }');
        }
        
        fclose($handle);
        if ($addrecid){
            $recid_json = array();
            $i=1;
            foreach ($csv_json as $array) {
                // echo $array."<br>";
                // $array = array_push($array,'{ "recid" : "'.$i.'" }');
                
                $array = array("recid" => "$i") +$array;
                // $array["recid"] = "$i";

                // echo "<br>****".implode(" ; ",$array)."<br>";
                // echo "$key => ".implode($array)."<br>";
                $recid_json[]= $array;
                $i++;
            }
            return json_encode($recid_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        return json_encode($csv_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function uploadAction()
    {
        $this->initInstance();
        
        
        //ZEND FILE TRANSFER
        $selection = $_POST['selection'];
        // echo "selection :".$selection;
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $dir = getcwd().'/../data/upload/';
        
        $adapter->setDestination($dir);
        
        //renommer le fichier s'il existe dans le répertoire 
        $fileName = $adapter->getFileName('uploadedfile');
        $pos = strpos($fileName,'upload/');
        $fileName = substr($fileName,$pos+strlen('upload/'));
        $newFileName = $this->incrementFileName($dir,$fileName);
        //filtre pour le renommage
        $adapter->addFilter('Rename', array('target' => $dir.$newFileName, 'overwrite' => true));

        if ($adapter->receive()) {
            // $newFileName = $this->incrementFileName($dir, $adapter->getFileName('uploadedfile'));
            //TODO: ajouter trace upload dans la bdd
            // echo ($newFileName.'<br>');
            $fileContents = file_get_contents($dir.$newFileName);
            // echo ($fileContents);
            // echo '<br><br><br>';
            $json = $this->csvtojson($dir.$newFileName, ',',true);
            // echo ($json);
        } 
        else {
           $messages = $adapter->getMessages();
           echo implode("\n", $messages);
        }

   
                      
        //redirection vers grid 
        $this->_forward('importcsv','Admin','default',array('resJSON' => $json,'selection' => $selection));
       

    }

    public function updategrfrmAction(){
        $this->initInstance();
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        if($this->_getParam("changes")){
            $changes = $this->_getParam("changes");
            foreach ($changes as $element ) {
                $this->s->dbE->edit($element["recid"], array("mort"=>(($element["mort"] =="") ? null : $element["mort"])));
            }
        }
    }

    public function savecontroledataAction(){
        $this->initInstance();
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        if($this->_getParam('tempsSaisie')){
            $id = $this->s->dbA->existe(array("code"=>"tempsSaisie"));
            $this->s->dbA->edit($id,array("desc"=>$this->_getParam('tempsSaisie')));
        }
        if($this->_getParam('joursSaisie')){
            $id = $this->s->dbA->existe(array("code"=>"joursSaisie"));
            $this->s->dbA->edit($id,array("desc"=>$this->_getParam('joursSaisie')));
        }
    }

    public function savejsonintodbAction(){
        
        $this->initInstance();

        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        $this->s->dbU = new Model_DbTable_Flux_Uti($this->s->db);
        
        $this->idMonade = $this->s->dbM->ajouter(array("titre"=>"E-monitor"),true,false);
        $nait = $this->getNait(new DateTime('now'));
        
        if($this->_getParam('type'))
            $typeSidebar = $this->_getParam('type');

        if($this->_getParam('resJSON')){
            if ($typeSidebar == 'etudiant'){

                $jsonrecu = $this->_getParam('resJSON');
                foreach ($jsonrecu as $ligne){
                    //si l'étudiant n'existe pas dans flux_uti
                    
                    $idEtudiantUti = $this->s->dbU->ajouter(array("login" => $ligne["login"], "role" => "etudiant"));
                    $idEtudiantExi = $this->s->dbE->ajouter(array("uti_id" => $idEtudiantUti, "nom" => $ligne["nom"], "prenom" => $ligne["prenom"] )) ;
                    $idFormationExi = $this->s->dbE->ajouter(array("nom" => $ligne["formation"], "data" => "formation", "nait" => $nait )) ;
                    $idGroupeExi = $this->s->dbE->ajouter(array("nom" => $ligne["groupe"], "data" => "groupe",
                    "nait" => $nait, "niveau" => $idFormationExi)) ;
                    $idRappEtuGr = $this->s->dbR->ajouter(array("monade_id"=> $this->idMonade, 
                    "src_id"=>$idEtudiantUti, "src_obj"=>"etudiant",
                        "dst_id"=>$idGroupeExi, "dst_obj"=>"groupe",
                        "pre_id"=>$idFormationExi, "pre_obj"=>"formation",
                        "valeur"=>$nait));
                        
                        // echo implode(',',$ligne);
                        // echo ("\n".$ligne["nom"]." ".$ligne["prenom"]." ".$ligne["email"]."\n" );
                        
                }
            }
            if ($typeSidebar == 'enseignant'){

                $jsonrecu = $this->_getParam('resJSON');
                foreach ($jsonrecu as $ligne){
                    //si l'étudiant n'existe pas dans flux_uti
                    
                    $idEnseignantUti = $this->s->dbU->ajouter(array("login" => $ligne["login"], "role" => "enseignant"));
                    $idEnseignantExi = $this->s->dbE->ajouter(array("uti_id" => $idEnseignantUti, "nom" => $ligne["nom"], "prenom" => $ligne["prenom"] )) ;
                    $idFormationExi = $this->s->dbE->ajouter(array("nom" => $ligne["formation"], "data" => "formation", "nait" => $nait )) ;
                    $idGroupeExi = $this->s->dbE->ajouter(array("nom" => $ligne["groupe"], "data" => "groupe",
                    "nait" => $nait, "niveau" => $idFormationExi)) ;
                    $idRappEtuGr = $this->s->dbR->ajouter(array("monade_id"=> $this->idMonade, 
                    "src_id"=>$idEnseignantExi, "src_obj"=>"enseignant",
                        "dst_id"=>$idGroupeExi, "dst_obj"=>"groupe",
                        "pre_id"=>$idFormationExi, "pre_obj"=>"formation",
                        "valeur"=>$nait));
                        
                        // echo implode(',',$ligne);
                        // echo ("\n".$ligne["nom"]." ".$ligne["prenom"]." ".$ligne["email"]."\n" );
                        
                }
            }
        }    

        $this->view->message = "Données enregistrées!!!";
    }

    public function getdatagroupeformationAction(){
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbE = new Model_DbTable_Flux_Exi($this->s->db);
        $this->s->dbU = new Model_DbTable_Flux_Uti($this->s->db);
        if($this->_getParam('type'))
            $typeSidebar = $this->_getParam('type');

        $this->view->rs = json_encode($this->s->dbE->getByChamp("data",$typeSidebar));

    }

    public function getcontroledataAction(){
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);

        
        $joursSaisie = $this->s->dbA->findByCode("joursSaisie");
        $tempsSaisie = $this->s->dbA->findByCode("tempsSaisie");
        $this->view->joursSaisie = $joursSaisie[0]["desc"];
        $this->view->tempsSaisie = $tempsSaisie[0]["desc"];
    }
 

    //pour l'authentification
    function initInstance($action=""){
        $session =  session_start();
        // $auth = Zend_Auth::getInstance();
        $role = $_SESSION["role"];
        $user = $_SESSION["user"];
		// if ($role == "admin" && $session) {						
        if (strpos($role,"admin") !== false && $session) {						
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

    function incrementFileName($file_path,$filename)
    {
        if(count(glob($file_path.$filename))>0)
        {
            $file_ext = end(explode(".", $filename));
            $file_name = str_replace(('.'.$file_ext),"",$filename);
            $newfilename = $file_name.'_'.count(glob($file_path."$file_name*.$file_ext")).'.'.$file_ext;
            return $newfilename;
         }
         else
         {
            return $filename;
         }
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