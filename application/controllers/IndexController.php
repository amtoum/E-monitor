<?php
/**
 * IndexController
 *
 * Porte d'entrée du jardin des connaissances
 *
 * @author Samuel Szoniecky
 * @category   Zend
 * @package Zend\Controller\Projet
 * @license https://creativecommons.org/licenses/by-sa/2.0/fr/ CC BY-SA 2.0 FR
 * @version  $Id:$
 */
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    	
    }

    public function indexAction()
    {
    	$this->_redirect('/auth/cas');
    	
    }
    
}



