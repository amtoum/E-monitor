<?php
class AuthController extends Zend_Controller_Action
{
    public function init()
	{
        $this->initView();
        //TODO : Revoir la base URL et la redirection 
		$this->baseUrl = $this->_request->getBaseUrl();
	}
 
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
            
            //TODO: modifier après création de session et login CAS
            //si le login correspond à un admin
            if ($result->isValid() && $loginForm->getValue('login')=="admin") {
                $this->_helper->FlashMessenger('Successful Login');
                $this->redirect('/admin/importcsv');
                return;
            }
            //si le login correspond à un utilisateur
            else if ($result->isValid()) {
                $this->_helper->FlashMessenger('Successful Login');
                $this->redirect('/carte/roueemotion');
                return;
            }
            else {
                $this->view->message = 'Login failed';
            }
 
        }
 
        $this->view->loginForm = $loginForm;
 
    }

    public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->redirect('/auth/login');
	}
 
}