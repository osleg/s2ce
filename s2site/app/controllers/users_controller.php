<?php
class UsersController extends AppController {

	var $name = 'Users';	
	var $helpers = array('Html', 'Form');
	
	function beforeFilter() 
	{
		parent::beforeFilter();
        $this->Auth->allow('index', 'register', 'login');
	}
	
	function index()
	{
						
	}
	
	function register()
	{
		if (!empty($this->data)) {
            // Turn the supplied password into the correct Hash.
            // and move into the ‘password’ field so it will get saved.
            $this->data['User']['password'] = $this->Auth->password($this->data['User']['passwrd']);

			if ($this->User->save($this->data)) {
				$this->Session->setFlash('Your account has been created.');
				$this->redirect(array('action' => 'index'));
			}
			
			$this->data['User']['passwrd'] = null;
		}
	}
	
	function login()
	{			
			
	}
	
	function logout() {
		$this->redirect($this->Auth->logout());
	}	
}
?>