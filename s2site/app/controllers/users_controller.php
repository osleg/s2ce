<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('Auth');
	var $helpers = array('Html', 'Form');
	
	function beforeFilter() 
	{
        $this->Auth->allow('home', 'register');
	}
	
	function home()
	{
						
	}
	
	function register()
	{
		if (!empty($this->data)) {
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash('Your post has been saved.');
				$this->redirect(array('action' => 'index'));
			}
		}
	}
}
?>