<?php
class AppController extends Controller {

	var $components = array('Auth');	

	function beforeFilter() 
	{
		$this->currentUser = $this->Auth->user();
		$this->isAuthed = !empty($this->currentUser);
	}

	function beforeRender() 
	{
   		$this->set('auth', $this->currentUser);
   		$this->set('isAuthed', $this->isAuthed);
	}
}

?>