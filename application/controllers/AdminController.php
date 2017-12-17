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

    function csvtojson($file,$delimiter)
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
        }
    
        fclose($handle);
        return json_encode($csv_json);
    }


    //pour l'authentification
    function initInstance($action=""){
        if($this->_getParam('idBase')) $this->idBase = $this->_getParam('idBase', $this->idBase);
        if($this->_getParam('idUti')) $this->idUti = $this->_getParam('idUti', 1);
        $this->idGeo = $this->_getParam('idGeo',-1);
        
        $this->view->idBase = $this->idBase;
        $this->view->idGeo = $this->idGeo;
        $this->view->langue = $this->_getParam('langue','fr');
                
    }
}