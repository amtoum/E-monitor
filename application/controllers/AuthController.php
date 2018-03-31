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
    
    public function casAction()
    {
		$ssExi = new Zend_Session_Namespace('uti');
    	
    	$this->cas();
    	// at this step, the user has been authenticated by the CAS server
    	// and the user's login name can be read with phpCAS::getUser().
    	
    	// logout if desired
    	if (isset($_REQUEST['logout'])) {
    		phpCAS::logout();
    	}    	    	
		$this->view->user = phpCAS::getUser();	

		
	

		//paramètres de redirection
		$dbNom = $this->_getParam('idBase');
		$redir = $this->_getParam('redir');
		$s = new Flux_Site($dbNom);	
		
		//met en sessions les informations de l'existence
		$dbUti = new Model_DbTable_Flux_Uti($s->db);
		$id = $dbUti->existe(array("login"=>$this->view->user),true,false);
		if($id != false){
			$role = $dbUti->getRoleById($id) ;
			$auth = phpCAS::checkAuthentication();
			if (!$auth) {
				$_SESSION["service_id_auth"] = $GLOBALS["TSFE"]->id;
				header('Location: ' . t3lib_div::locationHeaderUrl($this->pi_getPageLink($idPageAuth, "", array("action" => "auth"))));
				exit;
			} else {
				$_SESSION["user"] = phpCAS::getUser();
				$_SESSION["role"] = $role;
			}
			switch (true) {
				case strpos($role,"enseignant") !== false :
					$this->redirect('/visualisation/visualisation');
					break;
				case strpos($role,"etudiant") !== false :
					// $this->view->role = "ETUDIANT";
					$this->redirect('/carte/emotions');
					break;
				case strpos($role,"admin") !== false :
					// $this->view->role = "ADMIN";
					$this->redirect('/admin/importcsv');
					

					break;
			
			
				
			
			}
		}
	}

	public function deconnexionAction()
	{
		// Zend_Session::destroy($remove_cookie = true, $readonly = true);
		// $auth = Zend_Auth::getInstance();
		// $auth->clearIdentity();
		$this->cas();
		phpCAS::logout();
		session_unset();
session_destroy();
		// $this->clearConnexion();
	}

	public function finsessionAction()
	{
		
	}


	private function cas(){
		/**
    	 * The purpose of this central config file is configuring all examples
    	 * in one place with minimal work for your working environment
    	 * Just configure all the items in this config according to your environment
    	 * and rename the file to config.php
    	 *
    	 * PHP Version 5
    	 *
    	 * @file     config.php
    	 * @category Authentication
    	 * @package  PhpCAS
    	 * @author   Joachim Fritschi <jfritschi@freenet.de>
    	 * @author   Adam Franco <afranco@middlebury.edu>
    	 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
    	 * @link     https://wiki.jasig.org/display/CASC/phpCAS
    	 */    	 
    	
		///////////////////////////////////////
    	// Basic Config of the phpCAS client //
    	///////////////////////////////////////
    	
    	// Full Hostname of your CAS Server
    	$cas_host = 'cas.univ-paris8.fr';
    	
    	// Context of the CAS Server
    	$cas_context = '/cas';
    	
    	// Port of your CAS server. Normally for a https server it's 443
    	$cas_port = 443;
    	
    	// Path to the ca chain that issued the cas server certificate
    	$cas_server_ca_cert_path = '/path/to/cachain.pem';
    	    	
    	// Client config for cookie hardening
    	$client_domain = '127.0.0.1';
    	$client_path = 'phpcas';
    	$client_secure = true;
    	$client_httpOnly = true;
    	$client_lifetime = 0;
    	
    	// Database config for PGT Storage
    	$db = 'pgsql:host=localhost;dbname=phpcas';
    	//$db = 'mysql:host=localhost;dbname=phpcas';
    	$db_user = 'phpcasuser';
    	$db_password = 'mysupersecretpass';
    	$db_table = 'phpcastabel';
    	$driver_options = '';
    	
    	///////////////////////////////////////////
    	// End Configuration -- Don't edit below //
    	///////////////////////////////////////////
    	
    	// Generating the URLS for the local cas example services for proxy testing
    	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    		$curbase = 'https://' . $_SERVER['SERVER_NAME'];
    	} else {
    		$curbase = 'http://' . $_SERVER['SERVER_NAME'];
    	}
    	if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
    		$curbase .= ':' . $_SERVER['SERVER_PORT'];
    	}
    	
    	$curdir = dirname($_SERVER['REQUEST_URI']) . "/";
    	
    	//CAS client nodes for rebroadcasting pgtIou/pgtId and logoutRequest
    	$rebroadcast_node_1 = 'https://cas.univ-paris8.fr/cas';
    	$rebroadcast_node_2 = 'https://cas.univ-paris8.fr/cas';
    	
    	// access to a single service
    	$serviceUrl = $curbase . $curdir . 'auth/cas';
    	// access to a second service
    	$serviceUrl2 = $curbase . $curdir . 'auth/cas';
    	 
    	
    	$pgtBase = preg_quote(preg_replace('/^http:/', 'https:', $curbase . $curdir), '/');
    	$pgtUrlRegexp = '/^' . $pgtBase . '.*$/';
    	
    	$cas_url = 'https://' . $cas_host;
    	if ($cas_port != '443') {
    		$cas_url = $cas_url . ':' . $cas_port;
    	}
    	$cas_url = $cas_url . $cas_context;
    	
    	// Set the session-name to be unique to the current script so that the client script
    	// doesn't share its session with a proxied script.
    	// This is just useful when running the example code, but not normally.
    	// session_name(
    	// 		'session_for:'
    	// 		. preg_replace('/[^a-z0-9-]/i', '_', basename($_SERVER['SCRIPT_NAME']))
    	// 		);
    	    	 
    	// Set an UTF-8 encoding header for internation characters (User attributes)
    	header('Content-Type: text/html; charset=utf-8');
    	
    	// Enable debugging
    	phpCAS::setDebug("../tmp/phpCAS.log");
    	// Enable verbose error messages. Disable in production!
    	phpCAS::setVerbose(true);
    	
    	// Initialize phpCAS
    	phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
    	
    	// For production use set the CA certificate that is the issuer of the cert
    	// on the CAS server and uncomment the line below
    	// phpCAS::setCasServerCACert("cacert.pem");//($cas_server_ca_cert_path);
    	
    	// For quick testing you can disable SSL validation of the CAS server.
    	// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
    	// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
    	phpCAS::setNoCasServerValidation();
    	
    	// force CAS authentication
    	phpCAS::forceAuthentication();
	}

	function clearConnexion(){
		$ssExi = new Zend_Session_Namespace('uti'); 		   	
		$redir = $ssExi->redir;
		Zend_Session::namespaceUnset('uti');
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_redirect($redir);            	
	} 

}