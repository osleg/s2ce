<?php
class ServersController extends AppController {

	var $name = 'Servers';
	var $helpers = array('Html', 'Form');
	
	function index() {
		$this->set('servers', $this->Server->find('all'));
	}

}
?>