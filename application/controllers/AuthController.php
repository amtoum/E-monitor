<?php
class AuthController extends Zend_Controller_Action
{
 
    public function loginAction()
    {
        $db = $this->_getParam('db');
 
        $loginForm = new Application_Form_Auth_Login();
 
        if ($loginForm->isValid($_POST)) {
 
            $adapter = new Zend_Auth_Adapter_DbTable(
                $db,
                'flux_uti',
                'login',
                'mdp'
                );
 
            $adapter->setIdentity($loginForm->getValue('login'));
            $adapter->setCredential($loginForm->getValue('mdp'));
 
            $auth   = Zend_Auth::getInstance();
            $result = $auth->authenticate($adapter);
 
            if ($result->isValid()) {
                $this->_helper->FlashMessenger('Successful Login');
                $this->_redirect('/carte/roueemotion');
                return;
            }
 
        }
 
        $this->view->loginForm = $loginForm;
 
    }
 
}