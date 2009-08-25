<?php
class AppController extends Controller {

	var $components = array('Auth');	

	function beforeFilter() 
	{
		$user = $this->Auth->user();
		$this->isAuthed = !empty($user);
		
		if($this->isAuthed) {
			// Load current user including buddy list		
			$this->User->id = $user['User']['id'];
			$this->User->recursive = 1;
			$this->currentUser = $this->User->read();				
		}
	}

	function beforeRender() 
	{
   		$this->set('auth', $this->currentUser);
   		$this->set('isAuthed', $this->isAuthed);
	}
}

?>