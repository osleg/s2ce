<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		'email' => array('email'),
		'nickname' => array('maxlength'),
		'password' => array('minlength')
	);

}
?>