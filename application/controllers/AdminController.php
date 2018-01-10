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
        $this->initInstance();
        
        $this->s = new Flux_Site($this->idBase);
        $this->s->dbT = new Model_DbTable_Flux_Tag($this->s->db);
        $this->s->dbD = new Model_DbTable_Flux_Doc($this->s->db);
        $this->s->dbR = new Model_DbTable_Flux_Rapport($this->s->db);
        $this->s->dbM = new Model_DbTable_Flux_Monade($this->s->db);
        $this->s->dbA = new Model_DbTable_Flux_Acti($this->s->db);
        
        $arrayRes = $this->s->dbT->getRecidAll();
        
//         $arrayRes = $this->s->dbT->fetchAll($query)->toArray();
        $resJSON = json_encode($arrayRes, JSON_PRETTY_PRINT);
        
        $this->view->resJSON = $resJSON;
    }
    
    public function importcsvAction()
    {
        $this->initInstance();
        //get du parametre resJSON reçu après upload
        if ($this->_getparam('resJSON',false)){
            $this->view->resJSON = $this->_getparam('resJSON');
        }
        //get du parametre selection (sidebar) reçu après upload
        if ($this->_getparam('selection',false)){
            $this->view->selection = $this->_getparam('selection');
        }

        if ($_SESSION["user"])
        $this->view->user =  $this->_getParam('user', $_SESSION["user"] );

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
        //php file transfer
        // $uploaddir = getcwd().'/../data/upload/';
        // $uploadfile = $uploaddir . basename($_FILES['uploadedfile']['name']);
        
        // $newName = $this->incrementFileName( $uploaddir, $_FILES["uploadedfile"]["name"] );
        // // move_uploaded_file($_FILES["my_file"]["tmp_name"],"uploads/".$newName);
        
        // // if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadfile)) {
        // if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newName)) {
        //     echo "Le fichier est valide, et a été téléchargé
        //            avec succès. Voici plus d'informations :\n".$newName;
        // } else {
        //     echo "Attaque potentielle par téléchargement de fichiers.
        //           Voici plus d'informations :\n";
        // }
        
        // echo 'Voici quelques informations de débogage :';
        
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

    //jQuery file upload
        // $this->initInstance();
        
        // // if (($stream = fopen('php://delete', "r")) !== FALSE)
        // //         var_dump(stream_get_contents($stream));
            
        //     $auth = Zend_Auth::getInstance();
        // if ($auth->hasIdentity()) {
        //     $aFic = new Zend_File_Transfer_Adapter_Http();   		
        //     // $dbFic = new Model_DbTable_Iste_importfic();						
        //     //$ssUpload = new Zend_Session_Namespace('upload');
            
        //     $path = "/data/upload";//.$ssUpload->typeObj."_".$ssUpload->idObj."/";
        //     $webPath = "/admin/importcsv";
        //     $options = array('upload_dir' => ROOT_PATH.$path,'upload_url' => WEB_ROOT.$webPath
        //         ,'print_response'=>false);
        //     //$upload_handler = new UploadHandler($options);
        //     $upload_handler = new CustomUploadHandler($options);
        //         $response = $upload_handler->get_response();
        //         $this->view->json = json_encode($response);
        // } 
                      
        //redirection vers grid 
        $this->_forward('importcsv','Admin','default',array('resJSON' => $json,'selection' => $selection));
        // sleep(4);
        // $this->_redirect('admin/importcsv',array('resJSON' => $json));

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

        //DONE: utiliser le type pour différencier l'ajout d'étudiants ou d'enseignants 
        if($this->_getParam('type'))
            $typeSidebar = $this->_getParam('type');

        if($this->_getParam('resJSON')){
            if ($typeSidebar == 'etudiant'){

                $jsonrecu = $this->_getParam('resJSON');
                foreach ($jsonrecu as $ligne){
                    //si l'étudiant n'existe pas dans flux_uti
                    //TODO: flux_uti et obligation d'utilisation de l'email comme login ou le nom d'utilisateur
                    $idEtudiantUti = $this->s->dbU->ajouter(array("login" => $ligne["email"], "role" => "etudiant"));
                    $idEtudiantExi = $this->s->dbE->ajouter(array("uti_id" => $idEtudiantUti, "nom" => $ligne["nom"], "prenom" => $ligne["prenom"] )) ;
                    $idFormationExi = $this->s->dbE->ajouter(array("nom" => $ligne["formation"], "data" => "formation", "nait" => "2017-09-01" )) ;
                    $idGroupeExi = $this->s->dbE->ajouter(array("nom" => $ligne["groupe"], "data" => "groupe",
                    "nait" => "2017-09-01", "niveau" => $idFormationExi)) ;
                    $idRappEtuGr = $this->s->dbR->ajouter(array("monade_id"=> $this->idMonade, 
                    "src_id"=>$idEtudiantExi, "src_obj"=>"etudiant",
                        "dst_id"=>$idGroupeExi, "dst_obj"=>"groupe",
                        "pre_id"=>$idFormationExi, "pre_obj"=>"formation",
                        "valeur"=>"2017-09-01"));
                        
                        // echo implode(',',$ligne);
                        // echo ("\n".$ligne["nom"]." ".$ligne["prenom"]." ".$ligne["email"]."\n" );
                        
                }
            }
            if ($typeSidebar == 'enseignant'){

                $jsonrecu = $this->_getParam('resJSON');
                foreach ($jsonrecu as $ligne){
                    //si l'étudiant n'existe pas dans flux_uti
                    
                    $idEnseignantUti = $this->s->dbU->ajouter(array("login" => $ligne["email"], "role" => "enseignant"));
                    $idEnseignantExi = $this->s->dbE->ajouter(array("uti_id" => $idEnseignantUti, "nom" => $ligne["nom"], "prenom" => $ligne["prenom"] )) ;
                    $idFormationExi = $this->s->dbE->ajouter(array("nom" => $ligne["formation"], "data" => "formation", "nait" => "2017-09-01" )) ;
                    $idGroupeExi = $this->s->dbE->ajouter(array("nom" => $ligne["groupe"], "data" => "groupe",
                    "nait" => "2017-09-01", "niveau" => $idFormationExi)) ;
                    $idRappEtuGr = $this->s->dbR->ajouter(array("monade_id"=> $this->idMonade, 
                    "src_id"=>$idEnseignantExi, "src_obj"=>"enseignant",
                        "dst_id"=>$idGroupeExi, "dst_obj"=>"groupe",
                        "pre_id"=>$idFormationExi, "pre_obj"=>"formation",
                        "valeur"=>"2017-09-01"));
                        
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
 

    //pour l'authentification
    //TODO: gérer la session
    function initInstance($action=""){
        $session =  session_start();
        // $auth = Zend_Auth::getInstance();
        $role = $_SESSION["role"];
		// if ($role == "admin" && $session) {						
            //TODO: remettre la ligne au dessus (verif que le rôle == admin) après démo
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
}